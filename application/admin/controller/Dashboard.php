<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\data\Visit as VisitModel;
use app\admin\model\production\url as UrlModel;
use app\admin\model\Admin as AdminModel;
use fast\Tree;

/**
 * 数据报表
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    protected $orderModel = null;
    protected $visitModel = null;
    protected $urlModel = null;
    protected $adminModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
        $this->urlModel = new UrlModel();
        $this->visitModel = new VisitModel();
        $this->adminModel = new AdminModel();
    }


    /**
     * 获取当天0点到当天23点59分59秒的时间戳
     */
    public function getBeginEndTime()
    {
        $ytime = strtotime(date("Y-m-d",strtotime("-1 day")));//昨天开始时间戳
        $zerotime = $ytime+24 * 60 * 60;//昨天23点59分59秒+1秒
        $totime = $zerotime+24 * 60 * 60-1;//今天结束时间戳 23点59分59秒。
        return [$zerotime,$totime];
    }

    /**
     * 查看
     */
    public function index()
    {
        //获取当前用户信息
        $userInfo = $this->adminInfo;
        $timeData = $this->getBeginEndTime();

        if ($userInfo['id'] == 1) {
            //表示是平台总管理员，可以查看所有记录
            $userData = collection($this->adminModel->select())->toArray();
            //获取当天时间 0点到23点59分59秒的订单数量。
            $orderDoneData = collection($this->orderModel->where('updatetime','>',$timeData[0])->where('updatetime','<',$timeData[1])->select())->toArray();
            $visitData = collection($this->visitModel->select())->toArray();

        } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
            //老板查看团队所有人员的数据
            //获取团队下所有的用户数据
            $uesrData = $this->adminModel->where('team_id',$userInfo['team_id'])->select();
            $userData  = collection($uesrData)->toArray();
            //获取当前此团队下订单的所有数量
            $orderDoneData = $this->orderModel
                ->where('updatetime','>',$timeData[0])
                ->where('updatetime','<',$timeData[1])
                ->where('team_id',$userInfo['team_id'])
                ->select();
            $orderDoneData = collection($orderDoneData)->toArray();
            //获取当前团队下所有用户的访问数量
            $visitData = $this->visitModel
                ->where('updatetime','>',$timeData[0])
                ->where('updatetime','<',$timeData[1])
                ->where('team_id',$userInfo['team_id'])
                ->select();
            $visitData = collection($visitData)->toArray();

        } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
            //组长查看自己及以下员工的数据
            $lowerUser = $this->getUserLower();
            $orderData = collection($this->orderModel->where(['team_id'=>$this->adminInfo['team_id']])->select())->toArray();
        } else {
            //业务员只能查看自己的订单数据
            $lowerUser = $this->getUserLower();
            $orderData = collection($this->orderModel->where(['admin_id'=>$this->adminInfo['id']])->select())->toArray();
        }

        $this->assign('visitData',$visitData);
        $this->assign('userData',$userData);
        return $this->view->fetch();
    }


    /**
     * 获取用户关系。往下获取，
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserLower()
    {
        if ($this->adminInfo['id'] == 1) {
            //表示当前为平台管理员
            $data = collection(\app\admin\model\Admin::field(['id','pid','nickname'])->order('id desc')->select())->toArray();
        } elseif ($this->adminInfo['pid'] == 0 && $this->adminInfo['id'] !=1) {
            //表示当前用户为老板用户，只能查看到自己团队下的所有用户
            $data = collection(\app\admin\model\Admin::field(['id','pid','nickname'])->where('team_id',$this->adminInfo['team_id'])->order('id desc')->select())->toArray();
        } else {
            //表示是团队下组长级的权限
            $data = [
                $this->adminInfo['id'],
            ];
        }
        if ($this->adminInfo['pid'] == 0 || $this->adminInfo['id'] == 1) {
            $tree = Tree::instance();
            $tree->init($data,'pid');
            $teamList = $tree->getTreeList($tree->getTreeArray(0), 'nickname');
            $adminData = [];
            foreach ($teamList as $k => $v) {
                $adminData[] = $v['id'];
            }
        } else {
            $adminData = $data;
        }

        return $adminData;
    }

}
