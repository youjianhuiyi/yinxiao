<?php

namespace app\admin\controller\production;

use app\common\controller\Backend;

/**
 * 产品文案库
 *
 * @icon fa fa-cubes
 */
class Production extends Backend
{
    
    /**
     * Production模型对象
     * @var \app\admin\model\production\Production
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\production\Production;

    }

}
