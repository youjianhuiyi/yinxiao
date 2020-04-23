<?php

namespace app\admin\controller\sysconfig;

use app\admin\model\sysconfig\Rypay as RypayModel;
use app\admin\model\team\Team as TeamModel;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 如意付管理
 *
 * @icon fa fa-circle-o
 */
class Rypay extends Backend
{
    
    /**
     * Rypay模型对象
     */
    protected $model = null;
    protected $teamModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new RypayModel();
        $this->teamModel = new TeamModel();

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
     * 添加
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['status'] = 1;
            if ($params) {
                $teamName = $this->teamModel->where('id',$params['team_id'])->find()['name'];
                $params['team_name'] = $teamName ? $teamName :'未知团队';
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
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
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
            if ($params) {
                $params['status'] = 1;
                //如果有提交team_id，表示是编辑 操作，否则是x-editable操作。
                if (isset($params['team_id'])) {
                    $teamName = $this->teamModel->where('id',$params['team_id'])->find()['name'];
                    $params['team_name'] = $teamName ? $teamName :'未知团队';
                }
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
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
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

}
