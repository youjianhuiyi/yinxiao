<?php

namespace app\admin\controller\auth;

use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use app\admin\model\team\Team;
use app\common\controller\Backend;
use Endroid\QrCode\QrCode;
use fast\Random;
use fast\Tree;
use think\Response;
use think\Validate;

/**
 * 管理员管理
 *
 * @icon fa fa-users
 * @remark 一个管理员可以有多个角色组,左侧的菜单根据管理员所拥有的权限进行生成
 */
class Admin extends Backend
{

    /**
     * @var \app\admin\model\Admin
     */
    protected $model = null;
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Admin');

        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);

        $groupList = collection(AuthGroup::where('id', 'in', $this->childrenGroupIds)->select())->toArray();

        Tree::instance()->init($groupList);
        $groupdata = [];
        if ($this->auth->isSuperAdmin()) {
            $result = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0));
            foreach ($result as $k => $v) {
                $groupdata[$v['id']] = $v['name'];
            }
        } else {
            $result = [];
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n) {
                $childlist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['id']));
                $temp = [];
                foreach ($childlist as $k => $v) {
                    $temp[$v['id']] = $v['name'];
                }
                $result[__($n['name'])] = $temp;
            }
            $groupdata = $result;
        }

        //团队数据
        $teamData = collection(Team::column('name','id'))->toArray();
        if ($this->adminInfo['id'] == 1 ) {
            $teamData[0] = '请选择';
            ksort($teamData);
            $newTeamData = $teamData;
        } else {
            $newTeamData[0] = $teamData[$this->adminInfo['team_id']];
        }
        //团队关系
        $adminData = \app\admin\model\Admin::where('level','in',[0,1,2])
            ->where(['team_id'=>$this->adminInfo['team_id']])
            ->column('nickname','id');
        $adminData[0] = '添加账号属于谁的下级，就选择谁，没有下级就选择本项';

        ksort($adminData);
        $this->view->assign('adminData',$adminData);
        $this->view->assign('groupdata', $groupdata);
        $this->view->assign('teamData', $newTeamData);
        $this->assignconfig("admin", ['id' => $this->auth->id]);
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            $childrenGroupIds = $this->childrenGroupIds;
            $groupName = AuthGroup::where('id', 'in', $childrenGroupIds)
                ->column('id,name');
            $authGroupList = AuthGroupAccess::where('group_id', 'in', $childrenGroupIds)
                ->field('uid,group_id')
                ->select();

            $adminGroupName = [];
            foreach ($authGroupList as $k => $v) {
                if (isset($groupName[$v['group_id']])) {
                    $adminGroupName[$v['uid']][$v['group_id']] = $groupName[$v['group_id']];
                }
            }
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n) {
                $adminGroupName[$this->auth->id][$n['id']] = $n['name'];
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if ($this->adminInfo['id'] == 1) {
                $total = $this->model
                    ->where($where)
                    ->where('id', 'in', $this->childrenAdminIds)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->where('id', 'in', $this->childrenAdminIds)
                    ->field(['password', 'salt', 'token'], true)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            } else {
                $total = $this->model
                    ->where($where)
                    ->where('id', 'in', $this->childrenAdminIds)
                    ->where(['team_id'=>$this->adminInfo['team_id']])
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->where('id', 'in', $this->childrenAdminIds)
                    ->where(['team_id'=>$this->adminInfo['team_id']])
                    ->field(['password', 'salt', 'token'], true)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            }

            foreach ($list as $k => &$v) {
                $groups = isset($adminGroupName[$v['id']]) ? $adminGroupName[$v['id']] : [];
                $v['groups'] = implode(',', array_keys($groups));
                $v['groups_text'] = implode(',', array_values($groups));
            }
            unset($v);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            if ($params) {
                if (!Validate::is($params['password'], '\S{6,16}')) {
                    $this->error(__("Please input correct password"));
                }
                $params['salt'] = Random::alnum();
                $params['password'] = md5(md5($params['password']) . $params['salt']);
                $params['avatar'] = '/assets/img/avatar.png'; //设置新管理员默认头像。
                $teamName = Team::where('id',$params['team_id'])->value('name');
                $params['team_name'] = $teamName ?:'未知团队';
                //设置团队关系级别
                if ($params['pid'] < 3) {
                    //表示用户不是第一层级
                    $level = \app\admin\model\Admin::where('id',$params['pid'])->value('level') + 1;
                } else {
                    $level = 3;
                }
                $params['level'] = $level;
                $result = $this->model->validate('Admin.add')->save($params);
                if ($result === false) {
                    $this->error($this->model->getError());
                }
                $group = $this->request->post("group/a");

                //过滤不允许的组别,避免越权
                $group = array_intersect($this->childrenGroupIds, $group);
                $dataset = [];
                foreach ($group as $value) {
                    $dataset[] = ['uid' => $this->model->id, 'group_id' => $value];
                }
                model('AuthGroupAccess')->saveAll($dataset);
                $this->success();
            }
            $this->error();
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if (!in_array($row->id, $this->childrenAdminIds)) {
            $this->error(__('You have no permission'));
        }

        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            $teamName = Team::where('id',$params['team_id'])->value('name');
            $params['team_name'] = $teamName  != '' ? $teamName : '未知团队';
            //设置团队关系级别
            if ($params['pid'] < 3) {
                //表示用户不是第一层级
                $level = \app\admin\model\Admin::where('id',$params['pid'])->value('level') + 1;
            } else {
                $level = 3;
            }
            $params['level'] = $level;
            if ($params) {
                if ($params['password']) {
                    if (!Validate::is($params['password'], '\S{6,16}')) {
                        $this->error(__("Please input correct password"));
                    }
                    $params['salt'] = Random::alnum();
                    $params['password'] = md5(md5($params['password']) . $params['salt']);
                } else {
                    unset($params['password'], $params['salt']);
                }
                //这里需要针对username和email做唯一验证
                $adminValidate = \think\Loader::validate('Admin');
                $adminValidate->rule([
                    'username' => 'require|regex:\w{3,12}|unique:admin,username,' . $row->id,
                    'email'    => 'require|email|unique:admin,email,' . $row->id,
                    'password' => 'regex:\S{32}',
                ]);
                $result = $row->validate('Admin.edit')->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }

                // 先移除所有权限
                model('AuthGroupAccess')->where('uid', $row->id)->delete();

                $group = $this->request->post("group/a");

                // 过滤不允许的组别,避免越权
                $group = array_intersect($this->childrenGroupIds, $group);

                $dataset = [];
                foreach ($group as $value) {
                    $dataset[] = ['uid' => $row->id, 'group_id' => $value];
                }
                model('AuthGroupAccess')->saveAll($dataset);
                $this->success();
            }
            $this->error();
        }
        $grouplist = $this->auth->getGroups($row['id']);
        $groupids = [];
        foreach ($grouplist as $k => $v) {
            $groupids[] = $v['id'];
        }
        $this->view->assign("row", $row);
        $this->view->assign("groupids", $groupids);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $ids = array_intersect($this->childrenAdminIds, array_filter(explode(',', $ids)));
            // 避免越权删除管理员
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this->model->where('id', 'in', $ids)->where('id', 'in', function ($query) use ($childrenGroupIds) {
                $query->name('auth_group_access')->where('group_id', 'in', $childrenGroupIds)->field('uid');
            })->select();
            if ($adminList) {
                $deleteIds = [];
                foreach ($adminList as $k => $v) {
                    $deleteIds[] = $v->id;
                }
                $deleteIds = array_values(array_diff($deleteIds, [$this->auth->id]));
                if ($deleteIds) {
                    $this->model->destroy($deleteIds);
                    model('AuthGroupAccess')->where('uid', 'in', $deleteIds)->delete();
                    $this->success();
                }
            }
        }
        $this->error(__('You have no permission'));
    }

    /**
     * 批量更新
     * @internal
     */
    public function multi($ids = "")
    {
        // 管理员禁止批量操作
        $this->error();
    }

    /**
     * 下拉搜索
     */
    public function selectpage()
    {
        $this->dataLimit = 'auth';
        $this->dataLimitField = 'id';
        return parent::selectpage();
    }

    /**
     * 获取登录地址
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function url($ids = null)
    {
        $data = $this->model->get(['id' => $ids]);
        \think\Cache::set('url_data'.$data['id'],$data['username']);
        $sn = urlencode(base64_encode($data['username']));
        $data['login_url'] = $this->request->domain().$this->request->baseFile().'/index/login?sn='.$sn;
        $this->assign('data',$data);
        return $this->view->fetch();
    }

    /**
     * 生成二维码
     * @return Response
     * @throws \Endroid\QrCode\Exceptions\DataDoesntExistsException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionFailedException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionUnknownException
     * @throws \Endroid\QrCode\Exceptions\ImageTypeInvalidException
     */
    public function build()
    {
        $text = $this->request->get('text', 'hello world');
        $size = $this->request->get('size', 250);
        $padding = $this->request->get('padding', 15);
        $errorcorrection = $this->request->get('errorcorrection', 'medium');
        $foreground = $this->request->get('foreground', "#ffffff");
        $background = $this->request->get('background', "#000000");
        $logo = $this->request->get('logo');
        $logosize = $this->request->get('logosize');
        $label = $this->request->get('label');
        $labelfontsize = $this->request->get('labelfontsize');
        $labelhalign = $this->request->get('labelhalign');
        $labelvalign = $this->request->get('labelvalign');

        // 前景色
        list($r, $g, $b) = sscanf($foreground, "#%02x%02x%02x");
        $foregroundcolor = ['r' => $r, 'g' => $g, 'b' => $b];

        // 背景色
        list($r, $g, $b) = sscanf($background, "#%02x%02x%02x");
        $backgroundcolor = ['r' => $r, 'g' => $g, 'b' => $b];

        $qrCode = new QrCode();
        $qrCode
            ->setText($text)
            ->setSize($size)
            ->setPadding($padding)
            ->setErrorCorrection($errorcorrection)
            ->setForegroundColor($foregroundcolor)
            ->setBackgroundColor($backgroundcolor)
            ->setLogoSize($logosize)
            ->setLabel($label)
            ->setLabelFontSize($labelfontsize)
            ->setLabelHalign($labelhalign)
            ->setLabelValign($labelvalign)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);
        $fontPath = ROOT_PATH . 'public/assets/fonts/SourceHanSansK-Regular.ttf';
        if (file_exists($fontPath)) {
            $qrCode->setLabelFontPath($fontPath);
        }
        if ($logo) {
            $qrCode->setLogo(ROOT_PATH . 'public/assets/img/qrcode.png');
        }
        //也可以直接使用render方法输出结果
        //$qrCode->render();
        return new Response($qrCode->get(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }

}
