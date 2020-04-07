<?php

namespace app\admin\controller\team;

use app\common\controller\Backend;

/**
 * 团队管理
 *
 * @icon fa fa-sitemap
 */
class Team extends Backend
{
    
    /**
     * Team模型对象
     * @var \app\admin\model\team\Team
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\team\Team;

    }


}
