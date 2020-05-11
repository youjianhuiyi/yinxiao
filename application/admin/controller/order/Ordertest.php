<?php

namespace app\admin\controller\order;

use app\admin\model\Admin as AdminModel;
use app\common\controller\Backend;

/**
 * 测试商户订单
 *
 * @icon fa fa-circle-o
 */
class Ordertest extends Backend
{
    
    /**
     * Ordertest模型对象
     * @var \app\admin\model\order\Ordertest
     */
    protected $model = null;
    protected $adminModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\order\Ordertest;
        $this->adminModel = new AdminModel();
        $pid = $this->adminInfo['pid'];

        if ($pid == 0) {
            //表示是老板级别，可以查看所有信息
            $this->assignconfig('admin_level',0);
        } elseif ($pid != 0 ) {
            //表示没权限，列表显示多少看多少
            $this->assignconfig('admin_level',1);
        } else {
            $this->assignconfig('admin_level',2);
        }
        $this->assignconfig('show_column',true);


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
            //表示是组长级别账号。可以查看到自己及自己员工下所有订单
            //客服组需要单独查看团队所有人员的订单权限
            $id = $this->adminInfo['id'];
            $allIds = collection($this->adminModel->field('id')->where('pid',$id)->select())->toArray();
            $newArr = [];
            foreach ($allIds as $value) {
                $newArr[] = $value['id'];
            }
            array_push($newArr,$id);
            if ($this->adminInfo['id'] == 1) {
                //表示当前用户为总平台管理层
                $total = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            } elseif ($this->adminInfo['pid'] == 0) {
                //表示是老板级别账号。可以查看到平台下所有订单
                $total = $this->model
                    ->where($where)
                    ->where(['team_id' => $this->adminInfo['team_id']])
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->where(['team_id' => $this->adminInfo['team_id']])
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            } elseif ($this->adminInfo['pid'] != 0 && count($newArr) > 1) {
                $total = $this->model
                    ->where($where)
                    ->where('admin_id','in',$newArr)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->where('admin_id','in',$newArr)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            } else {
                //表示最低级别，就是只能查看自己的订单
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
        $this->error('暂时不支持后台添加订单~');
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
