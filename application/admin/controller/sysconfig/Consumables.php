<?php

namespace app\admin\controller\sysconfig;

use app\admin\model\team\Team as TeamModel;
use app\common\controller\Backend;

/**
 * 炮灰域名
 *
 * @icon fa fa-circle-o
 */
class Consumables extends Backend
{
    
    /**
     * Consumables模型对象
     * @var \app\admin\model\sysconfig\Consumables
     */
    protected $model = null;
    protected $teamModel = null;


    public function _initialize()
    {
        parent::_initialize();
        $this->teamModel = new TeamModel();
        $this->model = new \app\admin\model\sysconfig\Consumables;

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


}
