<?php
namespace app\admin\controller\data;

use app\admin\model\data\DataSummary as DataSummaryModel;
use app\common\controller\Backend;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\team\Team as TeamModel;
use app\admin\model\data\Visit as VisitModel;
use app\admin\model\production\Url as UrlModel;
use app\admin\model\Admin as AdminModel;
use fast\Date;
use fast\Tree;
use think\Config;

/**
 * 总数据报表
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Summary extends Backend
{

    protected $noNeedRight = ['daySummary'];
    protected $noNeedLogin = ['daySummary'];

    protected $orderModel = null;
    protected $visitModel = null;
    protected $urlModel = null;
    protected $adminModel = null;
    protected $teamModel = null;
    protected $model  = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new DataSummaryModel();
        $this->orderModel = new OrderModel();
        $this->urlModel = new UrlModel();
        $this->visitModel = new VisitModel();
        $this->adminModel = new AdminModel();
        $this->teamModel = new TeamModel();
    }

    /**
     * 统计订单数量及订单商品数量
     * @param $data array 用于统计的数组
     * @return array
     */
    public function countNums($data)
    {
        $tmp = [];
        $tmp['order_nums'] = 0;
        $tmp['pay_done'] = 0;
        $tmp['pay_done_nums'] = 0;
        foreach ($data as $key => $value) {
            $tmp['order_nums'] += $value['num'];
            if ($value['pay_status'] == 1) {
                $tmp['pay_done'] += 1;
                $tmp['pay_done_nums'] += $value['num'];
            }
        }
        return $tmp;
    }

    /**
     * 获取当天0点到当天23点59分59秒的时间戳
     * @internal
     */
    public function getBeginEndTime()
    {
        $ytime = strtotime(date("Y-m-d",strtotime("-1 day")));//昨天开始时间戳
        $zerotime = $ytime+24 * 60 * 60;//昨天23点59分59秒+1秒
        $totime = $zerotime+24 * 60 * 60-1;//今天结束时间戳 23点59分59秒。
        return [$zerotime,$totime];
    }

    /**
     * @description:根据数据
     * @internal
     * @param $dataArr  array       需要分组的数组
     * @param $keyStr   string      分组的依据
     * @param $userIds  integer    用户ID集合
     * @return array
     */
    protected function dataGroup($dataArr, $keyStr,$userIds)
    {
        $newArr=[];
        foreach ($dataArr as $k => $val) {    //数据根据日期分组
            $newArr[$val[$keyStr]][] = $val;
        }
        foreach ($newArr as $key => $value) {
            $newArr[$key] = count($value);
        }

        $adminIds = array_keys($newArr);
        //根据用户ID进行判断。
        foreach ($userIds as $userId) {
            if (!in_array($userId,$adminIds)) {
                $newArr[$userId] = 0;
            }
        }
        return $newArr;
    }

    /**
     * 查看
     * @comment 读取统计报表得到的数据，暂时有问题。先停止使用
     */
    public function index1()
    {
        if ($this->request->isPost()) {

            dump($this->request->param());die;
        }
        //获取当前用户信息
        $userInfo = $this->adminInfo;
        $timeData = $this->getBeginEndTime();
        $teamData = $this->teamModel->column('name','id');
        $adminName = $this->adminModel->column('nickname','id');
        $userIds = $this->getUserLower();

        if ($this->request->isAjax()) {
            if ($userInfo['id'] == 1) {
                //表示是平台总管理员，可以查看所有记录
                $userInfoData = collection($this->adminModel->select())->toArray();
                //获取当天时间 0点到23点59分59秒的订单数量。
                $orderDoneData = $this->orderModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('pay_status',1)
                    ->select();
                $orderDoneData = collection($orderDoneData)->toArray();
                $orderDoneData = $this->dataGroup($orderDoneData,'admin_id',$userIds);

                $orderData = $this->orderModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->select();
                $orderData = collection($orderData)->toArray();
                $orderData = $this->dataGroup($orderData,'admin_id',$userIds);

                $visitData = $this->visitModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('type',0)
                    ->select();
                $visitData = collection($visitData)->toArray();
                $visitData = $this->dataGroup($visitData,'admin_id',$userIds);

            } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
                //老板查看团队所有人员的数据
                //获取团队下所有的用户数据
                $userInfoData = $this->adminModel->where('team_id',$userInfo['team_id'])->select();
                $userInfoData  = collection($userInfoData)->toArray();
                //获取当前此团队下订单的所有数量
                $orderData = $this->orderModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('team_id',$userInfo['team_id'])
                    ->select();
                $orderData = collection($orderData)->toArray();
                $orderData = $this->dataGroup($orderData,'admin_id',$userIds);

                //获取当前此团队成交订单的所有数量
                $orderDoneData = $this->orderModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('team_id',$userInfo['team_id'])
                    ->where('pay_status',1)
                    ->select();
                $orderDoneData = collection($orderDoneData)->toArray();
                $orderDoneData = $this->dataGroup($orderDoneData,'admin_id',$userIds);

                //获取当前团队下所有用户的访问数量
                $visitData = $this->visitModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('team_id',$userInfo['team_id'])
                    ->where('type',0)
                    ->select();
                $visitData = collection($visitData)->toArray();
                $visitData = $this->dataGroup($visitData,'admin_id',$userIds);

            } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
                //组长查看自己及以下员工的数据
                $userInfoData = $this->adminModel
                    ->where('team_id',$userInfo['team_id'])
                    ->where('id','in',$userIds)
                    ->select();
                $userInfoData  = collection($userInfoData)->toArray();
                //订单下单量
                $orderData = $this->orderModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('team_id',$userInfo['team_id'])
                    ->where('admin_id','in',$userIds)
                    ->select();
                $orderData = collection($orderData)->toArray();
                $orderData = $this->dataGroup($orderData,'admin_id',$userIds);

                //获取当前此团队成交订单的所有数量
                $orderDoneData = $this->orderModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('team_id',$userInfo['team_id'])
                    ->where('admin_id','in',$userIds)
                    ->where('pay_status',1)
                    ->select();
                $orderDoneData = collection($orderDoneData)->toArray();
                $orderDoneData = $this->dataGroup($orderDoneData,'admin_id',$userIds);

                //获取当前团队下所有用户的访问数量
                $visitData = $this->visitModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('team_id',$userInfo['team_id'])
                    ->where('admin_id','in',$userIds)
                    ->where('type',0)
                    ->select();
                $visitData = collection($visitData)->toArray();
                $visitData = $this->dataGroup($visitData,'admin_id',$userIds);

            } else {
                //业务员只能查看自己的订单数据
                //获取当前此团队下订单的所有数量
                $userInfoData = $this->adminModel
                    ->where('team_id',$userInfo['team_id'])
                    ->where('id',$userIds[0])
                    ->select();
                $userInfoData  = collection($userInfoData)->toArray();

                $orderData = $this->orderModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('id',$userInfo['id'])
                    ->select();
                $orderData = collection($orderData)->toArray();
                $orderData = $this->dataGroup($orderData,'admin_id',$userIds);

                //获取当前此团队成交订单的所有数量
                $orderDoneData = $this->orderModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('admin_id',$userInfo['id'])
                    ->where('pay_status',1)
                    ->select();
                $orderDoneData = collection($orderDoneData)->toArray();
                $orderDoneData = $this->dataGroup($orderDoneData,'admin_id',$userIds);

                //获取当前团队下所有用户的访问数量
                $visitData = $this->visitModel
                    ->where('updatetime','>',$timeData[0])
                    ->where('updatetime','<',$timeData[1])
                    ->where('admin_id',$userInfo['id'])
                    ->where('type',0)
                    ->select();
                $visitData = collection($visitData)->toArray();
                $visitData = $this->dataGroup($visitData,'admin_id',$userIds);
            }
            $this->assign('orderDoneData',$orderDoneData);/*成交数量*/
            $this->assign('orderData',$orderData);/*下单数量*/
            $this->assign('visitData',$visitData);/*访问数量*/
            $this->assign('userInfoData',$userInfoData);/*用户数据*/
            $this->assign('teamData',$teamData);/*团队数据*/
            $this->assign('adminName',$adminName);/*业务员ID=>名称数据*/
            $this->assign('user',$this->adminInfo);/*当前用户信息*/


        }

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
            $data = $this->adminModel->field(['id','pid','nickname'])->order('id desc')->select();
            $data = collection($data)->toArray();
        } elseif ($this->adminInfo['pid'] == 0) {
            $data = $this->adminModel->field(['id','pid','nickname'])->where('team_id',$this->adminInfo['team_id'])->order('id desc')->select();
            $data = collection($data)->toArray();
        } elseif ($this->adminInfo['pid'] != 0 && $this->adminInfo['level'] != 2) {
            $data = $this->adminModel->field(['id','pid','nickname'])->where('team_id',$this->adminInfo['team_id'])->order('id desc')->select();
            $data = collection($data)->toArray();
        } else {
            $data = $this->adminModel->field(['id','pid','nickname'])->find($this->adminInfo['id']);
        }

        $tree = Tree::instance();
        $tree->init($data,'pid');
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
        array_push($adminData,$this->adminInfo['id']);
        return $adminData;
    }

    /**
     * @return array
     */
    public function shellGetAllUser()
    {
        $data = $this->adminModel->field(['id','pid','nickname'])->order('id desc')->select();
        $data = collection($data)->toArray();

        $tree = Tree::instance();
        $tree->init($data,'pid');
        $teamList = $tree->getTreeList($tree->getTreeArray(0), 'nickname');
        $adminData = [];
        foreach ($teamList as $k => $v) {
            $adminData[] = $v['id'];
        }
        //把自己添加进去
        return $adminData;
    }

    /**
     * 日汇总记录
     */
    public function daySummary()
    {
        $dateTime = $this->getBeginEndTime();
        $userIds = $this->shellGetAllUser();
        //访问记录
        $visitSummary = $this->visitModel
            ->where('createtime','>',$dateTime[0])
            ->where('createtime','<',$dateTime[1])
            ->where('type',0)
            ->select();
        $visitSummary = collection($visitSummary)->toArray();
        //整理数据访问记录数据
        $newVisitSummary = [];
        foreach ($visitSummary as $value) {
            $newVisitSummary[$value['admin_id']][] = $value;
        }

        foreach ($newVisitSummary as $key => $value) {
            $newVisitSummary[$key] = count($value);
        }

        //获取订单数据
        $orderData = $this->orderModel
            ->where('createtime','>',$dateTime[0])
            ->where('createtime','<',$dateTime[1])
            ->select();
        $orderData = collection($orderData)->toArray();
        //整理订单数据
        $newOrderData = [];
        foreach ($orderData as $value) {
            $newOrderData[$value['admin_id']][] = $value;
        }

        $adminIds = array_keys($newOrderData);
        //根据用户ID进行判断。
        foreach ($userIds as $userId) {
            if (!in_array($userId,$adminIds)) {
                $newOrderData[$userId] = '';
            }
        }

        $newResOrderData = [];

        foreach ($newOrderData as $key => $item) {
            if (!empty($item)) {
                $newResOrderData[]  = [
                    'team_id'       => $this->adminModel->get($key)['team_id'],
                    'pid'           => $this->adminModel->get($key)['pid'],
                    'admin_id'      => $key,
                    'visit_nums'    => isset($newVisitSummary[$key]) ? $newVisitSummary[$key] : 0,
                    'order_count'   => count($item),
                    'order_nums'    => $this->countNums($item)['order_nums'],
                    'pay_done'      => $this->countNums($item)['pay_done'],
                    'pay_done_nums' => $this->countNums($item)['pay_done_nums']
                ];
            } else {
                $newResOrderData[] = [
                    'team_id'       => $this->adminModel->get($key)['team_id'],
                    'pid'           => $this->adminModel->get($key)['pid'],
                    'admin_id'      => $key,
                    'visit_nums'    => 0,
                    'order_count'   => 0,
                    'order_nums'    => 0,
                    'pay_done'      => 0,
                    'pay_done_nums' => 0
                ];
            }
        }

//        $result = $this->model->isUpdate(false)->saveAll($newResOrderData);
//        if ($result) {
//            echo 'OK';
//            die();
//        } else {
//            echo 'failure';
//            die();
//        }
    }

    /**
     * 查看
     */
    public function index()
    {
        die('此功能暂停使用');
        $date = date('m-d',time());
        $dataSummary = collection($this->model->select())->toArray();
        //先将所有数据按日期分类
        $data = [];
        $dateData = [];
        foreach ($dataSummary as $item) {
            $data[$item['date']][] = $item;
            $dateData[$item['date']]['visit_nums'] = 0;
            $dateData[$item['date']]['order_count'] = 0;
            $dateData[$item['date']]['order_nums'] = 0;
            $dateData[$item['date']]['pay_done'] = 0;
            $dateData[$item['date']]['pay_done_nums'] = 0;
        }
        //再将分类好的日期数据归总数值
        foreach ($data as $key => $value) {
            foreach ($value as $v) {
                $dateData[$key]['visit_nums'] += $v['visit_nums'];
                $dateData[$key]['order_count'] += $v['order_count'];
                $dateData[$key]['order_nums'] += $v['order_nums'];
                $dateData[$key]['pay_done'] += $v['pay_done'];
                $dateData[$key]['pay_done_nums'] += $v['pay_done_nums'];
            }
        }

        //构建图标需要的数值
        $newArr = [];
        foreach ($dateData as $key => $value) {
            $newArr['visit_nums'][$key] =  $value['visit_nums'];
            $newArr['order_count'][$key] =  $value['order_count'];
            $newArr['order_nums'][$key] =  $value['order_nums'];
            $newArr['pay_done'][$key] =  $value['pay_done'];
            $newArr['pay_done_nums'][$key] =  $value['pay_done_nums'];
        }

        $this->view->assign([
            'paylist'          => $dateData,
            'createlist'       => $dateData,
            'visit'            => isset($newArr['visit_nums']) ? $newArr['visit_nums'] : [0],
            'order_count'      => isset($newArr['order_count']) ? $newArr['order_count'] : [0],
            'order_nums'       => isset($newArr['order_nums']) ? $newArr['order_nums'] : [0],
            'pay_done'         => isset($newArr['pay_done']) ? $newArr['pay_done'] : [0],
            'pay_done_nums'    => isset($newArr['pay_done_nums']) ? $newArr['pay_done_nums'] : [0],
        ]);

        return $this->view->fetch();
    }

}
