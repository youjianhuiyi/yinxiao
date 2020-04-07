<?php

namespace app\admin\controller\sysconfig;

use app\common\controller\Backend;

/**
 * 落地域名
 *
 * @icon fa fa-diamond
 */
class Ground extends Backend
{
    
    /**
     * Ground模型对象
     * @var \app\admin\model\sysconfig\Ground
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\sysconfig\Ground;

    }

    

}
