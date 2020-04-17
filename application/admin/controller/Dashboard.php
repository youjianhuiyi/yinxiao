<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\admin\model\order\Order as OrderModel;
/**
 * 数据报表
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    protected $orderModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
    }

    /**
     * 查看
     */
    public function index()
    {
        $userInfo = $this->adminInfo;
        if ($userInfo['id'] == 1) {
            //表示是平台总管理员，可以查看所有记录
            $orderData = collection($this->orderModel->select());
        } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
            //老板查看团队所有人员的数据
            $orderData = collection($this->orderModel->where(['team_id'=>$this->adminInfo['team_id']])->select());
        } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
            //组长查看自己及以下员工的数据
            $orderData = collection($this->orderModel->where(['team_id'=>$this->adminInfo['team_id']])->select());
        } else {
            //业务员只能查看自己的订单数据
            $orderData = collection($this->orderModel->where(['admin_id'=>$this->adminInfo['id']])->select());
        }

        return $this->view->fetch();
    }

}
