<?php

namespace app\admin\controller\sysconfig;

use app\common\controller\Backend;
use think\Cache;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\model\team\Team as TeamModel;
use app\admin\model\sysconfig\Wxdomain as WxDomainModel;

/**
 * 支付设置
 *
 * @icon fa fa-weixin
 */
class Pay extends Backend
{
    
    /**
     * Pay模型对象
     * @var \app\admin\model\sysconfig\Pay
     */
    protected $model = null;
    protected $teamModel = null;
    protected $wxdomainModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->teamModel = new TeamModel();
        $this->model = new \app\admin\model\sysconfig\Pay;
        $this->wxdomainModel = new WxDomainModel();
        //团队数据
        $teamData = collection($this->teamModel->column('name','id'))->toArray();
        if ($this->adminInfo['id'] == 1 ) {
            $teamData[0] = '自动新增团队请选择我(新加老板账号),否则下拉选择(新加非老板账号)';
            ksort($teamData);
            $newTeamData = $teamData;
        } else {
            $newTeamData[$this->adminInfo['team_id']] = $teamData[$this->adminInfo['team_id']];
        }
        $this->view->assign('teamData', $newTeamData);
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if ($this->adminInfo['id'] == 1) {
                $total = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            } else {
                $total = $this->model
                    ->where($where)
                    ->where('team_id',$this->adminInfo['team_id'])
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->where('team_id',$this->adminInfo['team_id'])
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            }

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * 回收站
     */
    public function recyclebin()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->onlyTrashed()
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->onlyTrashed()
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

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
            $params = $this->request->post("row/a");
            $teamName = $this->teamModel->where('id',$params['team_id'])->find()['name'];
            $params['team_name'] = $teamName ? $teamName :'未知团队';
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    //更新配置到微信域名配置表
                    $payId = $this->model->id;
                    $res = $this->addWechatDomain($params,$payId);
                    $this->wxdomainModel->isUpdate(false)->allowField(true)->saveAll($res[0]);
                    $this->wxdomainModel->isUpdate(false)->allowField(true)->saveAll($res[1]);
                    //将本团队的商品数据缓存起来
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            //如果有提交team_id，表示是编辑 操作，否则是x-editable操作。
            if (isset($params['team_id'])) {
                $teamName = $this->teamModel->where('id',$params['team_id'])->find()['name'];
                $params['team_name'] = $teamName ? $teamName :'未知团队';
            }
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    //同步更新微信配置表
                    if (isset($params['team_id'])) {
                        $res = $this->editWechatDomain($params,$ids);
                        $this->wxdomainModel->allowField(true)->saveAll($res[0]);
                        $this->wxdomainModel->allowField(true)->saveAll($res[1]);
                    }
                    //将本团队的商品数据缓存起来
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 处理微信域名
     * @param $data
     * @param $id
     * @return array
     * @internal
     */
    public function addWechatDomain($data,$id)
    {
        $payDomain = $grantDomain = [];

        for ($i = 1;$i<=5;$i++) {
            $temp = [
                'team_id' => $data['team_id'],
                'pay_id'  => $id,
                'domain'  => $data['pay_domain'.$i],
                'type'    => 1,
                'is_inuse'=> 1
            ];
            //表示里面没有存在
            if (!in_array($temp,$payDomain)) {
                array_push($payDomain,$temp);
            }
        }

        for ($i =1;$i <= 3;$i++) {
            $tmp = [
                'team_id' => $data['team_id'],
                'pay_id'  => $id,
                'domain'  => $data['grant_domain_'.$i],
                'type'    => 0,
                'is_inuse'=> 1
            ];

            //表示里面没有存在
            if (!in_array($tmp,$payDomain)) {
                array_push($grantDomain,$tmp);
            }
        }

        return [$payDomain,$grantDomain];
    }


    /**
     * 处理微信域名
     * @param $data
     * @param $ids
     * @return array
     * @internal
     */
    public function editWechatDomain($data,$ids)
    {
        $oldDataPay = $this->wxdomainModel->field(['id','team_id','pay_id','domain','type','is_inuse'])->where(['type'=>1,'pay_id'=>$ids])->select();
        $oldDataGrant = $this->wxdomainModel->field(['id','team_id','pay_id','domain','type','is_inuse'])->where(['type'=>0,'pay_id'=>$ids])->select();
        $newPay = $newGrant = [];

        for ($i = 1;$i<=5;$i++) {
            $newPay[$i-1] = [
                'id'      => isset($oldDataPay[$i-1]['id']) ? $oldDataPay[$i-1]['id'] : null,
                'team_id' => $data['team_id'],
                'pay_id'  => $ids,
                'domain'  => $data['pay_domain'.$i],
                'type'    => 1,
                'is_inuse'=> 1
            ];

        }

        for ($i =1;$i <= 3;$i++) {
            $newGrant[$i-1] = [
                'id'      => isset($oldDataGrant[$i-1]) ? $oldDataGrant[$i-1]['id'] : null,
                'team_id' => $data['team_id'],
                'pay_id'  => $ids,
                'domain'  => $data['grant_domain_'.$i],
                'type'    => 0,
                'is_inuse'=> 1
            ];

        }
        return [$newPay,$newGrant];
    }

}
