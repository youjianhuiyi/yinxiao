<?php

namespace app\admin\controller\data;

use app\common\controller\Backend;

/**
 * 访问记录
 *
 * @icon fa fa-circle-o
 */
class Visit extends Backend
{
    
    /**
     * Visit模型对象
     * @var \app\admin\model\data\Visit
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\data\Visit;

    }

}
