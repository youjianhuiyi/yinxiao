<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;

/**
 * 订单管理
 *
 * @icon fa fa-first-order
 */
class Order extends Backend
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
     * 添加
     * @internal
     */
    public function add()
    {
        return $this->error('暂时不支持后台添加订单~');
    }


    /**
     * 订单详情
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function detail($ids=null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }
}
