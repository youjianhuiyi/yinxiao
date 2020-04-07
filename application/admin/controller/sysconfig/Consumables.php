<?php

namespace app\admin\controller\sysconfig;

use app\common\controller\Backend;

/**
 * 炮灰域名
 *
 * @icon fa fa-bomb
 */
class Consumables extends Backend
{
    
    /**
     * Consumables模型对象
     * @var \app\admin\model\sysconfig\Consumables
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\sysconfig\Consumables;

    }


}
