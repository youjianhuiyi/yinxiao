<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;

/**
 * 分享有礼管理
 *
 * @icon fa fa-circle-o
 */
class Sharedata extends Backend
{
    
    /**
     * Sharedata模型对象
     * @var \app\admin\model\order\Sharedata
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\order\Sharedata;

    }

    

}
