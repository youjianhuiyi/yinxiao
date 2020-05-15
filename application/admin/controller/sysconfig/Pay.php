<?php

namespace app\admin\controller\sysconfig;

use app\admin\model\data\PayRecord as PayRecordModel;
use app\admin\model\sysconfig\Payset as PaySetModel;
use app\common\controller\Backend;
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
    protected $payRecordMode = null;
    protected $paysetModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->teamModel = new TeamModel();
        $this->model = new \app\admin\model\sysconfig\Pay;
        $this->wxdomainModel = new WxDomainModel();
        $this->payRecordMode = new PayRecordModel();
        $this->paysetModel = new PaySetModel();

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
            if ($this->adminInfo['id'] == 1) {
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


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 新增支付同步更新支付管理表
     * @param $data array   更新数据
     * @param $payId    integer     支付ID
     * @return bool
     */
    protected function addPayManagement($data,$payId)
    {
        $newArr = [
            'type'              =>  0,/*表示微信支付类型*/
            'pay_id'            =>  $payId,
            'pay_channel'       =>  $data['pay_name'],
            'team_id'           =>  $data['team_id'],
            'team_name'         =>  $this->teamModel->get($data['team_id'])['name'],
            'is_multiple'       =>  1,
            'status'            =>  1,
        ];
        $result = $this->paysetModel->isUpdate(false)->save($newArr);
        return $result ? true : false;
    }

    /**
     * 编辑同步更新支付管理表
     * @param $data array   更新数据
     * @param $payId    integer     支付ID
     * @return bool
     */
    protected function editPayManagement($data,$payId)
    {
        if ($data['status'] == 0) {
            //表示当前是禁用操作。
            $result = $this->paysetModel->destroy(['pay_id'=>$payId,'type'=>0]);
        } else {
            $result = $this->addPayManagement($data,$payId);
        }
        return $result;
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['status'] = 1;
            $teamName = $this->teamModel->where('id',$params['team_id'])->find()['name'];
            $params['team_name'] = $teamName ? $teamName :'未知团队';
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = $result1 = $result2 = false;
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
                    //同步添加到支付管理列表。
                    //同步将商户添加到商户收款表里面
                    $newData = [
                        'date'          => date('m-d',time()),
                        'team_id'       => $params['team_id'],
                        'pay_id'        => $this->model->id,
                        'pay_type'      => 0,
                        'use_count'     => 0,
                        'pay_nums'      => 0,
                        'money'         => 0.00,
                    ];
                    $result1 = $this->payRecordMode->isUpdate(false)->save($newData);
                    //如果是新加商户，直接同步到支付管理表。并开启
                    $result2 = $this->addPayManagement($params,$this->model->id);
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
                if ($result !== false && $result1 !== false && $result2 !== false) {
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
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\DbException
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
                $result = $result1 = false;
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
                    //修改支付数据，需要同步到支付管理里面，如果是禁用修改的话，直接将支付管理里面关闭或者删除
                    $params['team_id'] = $row['team_id'];
                    $params['pay_name'] = $row['pay_name'];
                    $result1 = $this->editPayManagement($params,$ids);
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
                if ($result !== false && $result1 !== false) {
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

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();

            $count = 0;
            Db::startTrans();
            try {
                foreach ($list as $k => $v) {
                    $count += $v->delete();
                }
                //删除支付管理里面的商户号
                $this->editPayManagement(['status'=>0],$ids);
                Db::commit();
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }
}
