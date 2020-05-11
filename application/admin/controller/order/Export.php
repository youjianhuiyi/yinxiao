<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;

/**
 * 订单导出
 *
 * @icon fa fa-first-order
 */
class Export extends Backend
{

    /**
     * Order模型对象
     * @var \app\admin\model\order\Order
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\order\Order;

    }

    /**
     * 查看
     */
    public function index()
    {
        return $this->view->fetch();
    }

}
