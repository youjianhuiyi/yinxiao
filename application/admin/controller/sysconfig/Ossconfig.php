<?php

namespace app\admin\controller\sysconfig;

use app\admin\model\team\Team as TeamModel;
use app\common\controller\Backend;

/**
 * oss配置
 *
 * @icon fa fa-circle-o
 */
class Ossconfig extends Backend
{
    
    /**
     * Ossconfig模型对象
     * @var \app\admin\model\sysconfig\Ossconfig
     */
    protected $model = null;
    protected $teamModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\sysconfig\Ossconfig;
        $this->teamModel = new TeamModel();
        //团队数据
        if ($this->request->action() == 'add' || $this->request->action() == 'edit' || $this->request->action() == 'index' ) {
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

}
