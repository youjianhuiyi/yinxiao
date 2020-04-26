<?php
namespace app\admin\controller\data;

use app\common\controller\Backend;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\team\Team as TeamModel;
use app\admin\model\data\Visit as VisitModel;
use app\admin\model\production\Url as UrlModel;
use app\admin\model\Admin as AdminModel;
use fast\Tree;

/**
 * 日数据报表
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Day extends Backend
{

    protected $orderModel = null;
    protected $visitModel = null;
    protected $urlModel = null;
    protected $adminModel = null;
    protected $teamModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
        $this->urlModel = new UrlModel();
        $this->visitModel = new VisitModel();
        $this->adminModel = new AdminModel();
        $this->teamModel = new TeamModel();
    }

    /**
     * 获取当天0点到当天23点59分59秒的时间戳
     * @internal
     */
    public function getBeginEndTime()
    {
        $ytime = strtotime(date("Y-m-d", strtotime("-1 day")));//昨天开始时间戳
        $zerotime = $ytime + 24 * 60 * 60;//昨天23点59分59秒+1秒
        $totime = $zerotime + 24 * 60 * 60 - 1;//今天结束时间戳 23点59分59秒。
        return [$zerotime, $totime];
    }

    /**
     * @description:根据数据
     * @param {dataArr:需要分组的数据；keyStr:分组依据}
     * @return array
     * @internal
     */
    protected function dataGroup($dataArr, $keyStr, $userIds)
    {
        $newArr = [];
        foreach ($dataArr as $k => $val) {    //数据根据日期分组
            $newArr[$val[$keyStr]][] = $val;
        }
        foreach ($newArr as $key => $value) {
            $newArr[$key] = count($value);
        }

        $adminIds = array_keys($newArr);
        foreach ($userIds as $userId) {
            if (!in_array($userId, $adminIds)) {
                $newArr[$userId] = 0;
            }
        }
        return $newArr;
    }

    /**
     * 查看
     */
    public function index()
    {
        //获取当前用户信息
        $userInfo = $this->adminInfo;
        $timeData = $this->getBeginEndTime();
        $teamData = $this->teamModel->column('name', 'id');
        $adminName = $this->adminModel->column('nickname', 'id');
        $userIds = $this->getUserLower();

        if ($userInfo['id'] == 1) {
            //表示是平台总管理员，可以查看所有记录
            $userInfoData = collection($this->adminModel->select())->toArray();
            //获取当天时间 0点到23点59分59秒的订单数量。
            $orderDoneData = $this->orderModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->where('pay_status', 1)
                ->select();
            $orderDoneData = collection($orderDoneData)->toArray();
            $orderDoneData = $this->dataGroup($orderDoneData, 'admin_id', $userIds);

            $orderData = $this->orderModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->select();
            $orderData = collection($orderData)->toArray();
            $orderData = $this->dataGroup($orderData, 'admin_id', $userIds);

            $visitData = $this->visitModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->select();
            $visitData = collection($visitData)->toArray();
            $visitData = $this->dataGroup($visitData, 'admin_id', $userIds);

        } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
            //老板查看团队所有人员的数据
            //获取团队下所有的用户数据
            $userInfoData = $this->adminModel->where('team_id', $userInfo['team_id'])->select();
            $userInfoData = collection($userInfoData)->toArray();
            //获取当前此团队下订单的所有数量
            $orderData = $this->orderModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->where('team_id', $userInfo['team_id'])
                ->select();
            $orderData = collection($orderData)->toArray();
            $orderData = $this->dataGroup($orderData, 'admin_id', $userIds);

            //获取当前此团队成交订单的所有数量
            $orderDoneData = $this->orderModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->where('team_id', $userInfo['team_id'])
                ->where('pay_status', 1)
                ->select();
            $orderDoneData = collection($orderDoneData)->toArray();
            $orderDoneData = $this->dataGroup($orderDoneData, 'admin_id', $userIds);

            //获取当前团队下所有用户的访问数量
            $visitData = $this->visitModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->where('team_id', $userInfo['team_id'])
                ->select();
            $visitData = collection($visitData)->toArray();
            $visitData = $this->dataGroup($visitData, 'admin_id', $userIds);

        } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
            //组长查看自己及以下员工的数据
            $userInfoData = $this->adminModel
                ->where('team_id', $userInfo['team_id'])
                ->where('id', 'in', $userIds)
                ->select();
            $userInfoData = collection($userInfoData)->toArray();
            //订单下单量
            $orderData = $this->orderModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->where('team_id', $userInfo['team_id'])
                ->where('admin_id', 'in', $userIds)
                ->select();
            $orderData = collection($orderData)->toArray();
            $orderData = $this->dataGroup($orderData, 'admin_id', $userIds);

            //获取当前此团队成交订单的所有数量
            $orderDoneData = $this->orderModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->where('team_id', $userInfo['team_id'])
                ->where('admin_id', 'in', $userIds)
                ->where('pay_status', 1)
                ->select();
            $orderDoneData = collection($orderDoneData)->toArray();
            $orderDoneData = $this->dataGroup($orderDoneData, 'admin_id', $userIds);

            //获取当前团队下所有用户的访问数量
            $visitData = $this->visitModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->where('team_id', $userInfo['team_id'])
                ->where('admin_id', 'in', $userIds)
                ->select();
            $visitData = collection($visitData)->toArray();
            $visitData = $this->dataGroup($visitData, 'admin_id', $userIds);

        } else {
            //业务员只能查看自己的订单数据
            //获取当前此团队下订单的所有数量
            $userInfoData = $this->adminModel
                ->where('team_id', $userInfo['team_id'])
                ->where('id', $userIds[0])
                ->select();
            $userInfoData = collection($userInfoData)->toArray();

            $orderData = $this->orderModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->where('id', $userInfo['id'])
                ->select();
            $orderData = collection($orderData)->toArray();
            $orderData = $this->dataGroup($orderData, 'admin_id', $userIds);

            //获取当前此团队成交订单的所有数量
            $orderDoneData = $this->orderModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->where('admin_id', $userInfo['id'])
                ->where('pay_status', 1)
                ->select();
            $orderDoneData = collection($orderDoneData)->toArray();
            $orderDoneData = $this->dataGroup($orderDoneData, 'admin_id', $userIds);

            //获取当前团队下所有用户的访问数量
            $visitData = $this->visitModel
                ->where('updatetime', '>', $timeData[0])
                ->where('updatetime', '<', $timeData[1])
                ->where('admin_id', $userInfo['id'])
                ->select();
            $visitData = collection($visitData)->toArray();
            $visitData = $this->dataGroup($visitData, 'admin_id', $userIds);
        }

        $this->assign('orderDoneData', $orderDoneData);/*成交数量*/
        $this->assign('orderData', $orderData);/*下单数量*/
        $this->assign('visitData', $visitData);/*访问数量*/
        $this->assign('userInfoData', $userInfoData);/*用户数据*/
        $this->assign('teamData', $teamData);/*团队数据*/
        $this->assign('adminName', $adminName);/*业务员ID=>名称数据*/
        $this->assign('user', $this->adminInfo);/*当前用户信息*/
        return $this->view->fetch();
    }


    /**
     * 获取用户关系。往
     * @return array
     * @internal
     */
    public function getUserLower()
    {
        if ($this->adminInfo['id'] == 1) {
            $data = $this->adminModel->field(['id', 'pid', 'nickname'])->order('id desc')->select();
            $data = collection($data)->toArray();
        } elseif ($this->adminInfo['pid'] == 0) {
            $data = $this->adminModel->field(['id', 'pid', 'nickname'])->where('team_id', $this->adminInfo['team_id'])->order('id desc')->select();
            $data = collection($data)->toArray();
        } elseif ($this->adminInfo['pid'] != 0 && $this->adminInfo['level'] != 2) {
            $data = $this->adminModel->field(['id', 'pid', 'nickname'])->where('team_id', $this->adminInfo['team_id'])->order('id desc')->select();
            $data = collection($data)->toArray();
        } else {
            $data = $this->adminModel->field(['id', 'pid', 'nickname'])->find($this->adminInfo['id']);
        }

        $tree = Tree::instance();
        $tree->init($data, 'pid');
        if ($this->adminInfo['id'] == 1) {
            $teamList = $tree->getTreeList($tree->getTreeArray(0), 'nickname');
        } elseif ($this->adminInfo['pid'] == 0) {
            $teamList = $tree->getTreeList($tree->getTreeArray(0), 'nickname');
        } else {
            $teamList = $tree->getTreeList($tree->getTreeArray($this->adminInfo['id']), 'nickname');
        }
        $adminData = [];
        foreach ($teamList as $k => $v) {
            $adminData[] = $v['id'];
        }
        //把自己添加进去
        array_push($adminData, $this->adminInfo['id']);
        return $adminData;
    }

}
