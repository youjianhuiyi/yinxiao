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
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            //获取需要查询订单的用户, 平台需要查看所有订单。基本老板只能查看自己平台的订单，下面员工只能看到自己的订单。
            //admin_id = 0 查看全站
            //假如admin_id = 3 是老板号 4是经理号，5是业务员号，
            //3可以查看所有 3为团队的订单。即以团队id=1.
            //
            if ($this->adminInfo['id'] == 0) {
                //表示当前用户为总平台管理层
                $total = $this->model
                    ->where($where)
                    ->where(['admin_id'=>$this->adminInfo['id']])
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->where(['admin_id'=>$this->adminInfo['id']])
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            }


            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
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
