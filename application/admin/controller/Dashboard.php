<?php

namespace app\admin\controller;

use app\admin\model\data\DataSummary as DataSummaryModel;
use app\common\controller\Backend;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\team\Team as TeamModel;
use app\admin\model\data\Visit as VisitModel;
use app\admin\model\data\PayRecord as PayRecordModel;
use app\admin\model\production\Url as UrlModel;
use app\admin\model\Admin as AdminModel;
use app\admin\model\sysconfig\Pay as PayModel;
use app\admin\model\sysconfig\Xpay as XPayModel;
use app\admin\model\sysconfig\Rypay as RyPayModel;
use fast\Tree;
use think\Cache;

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
    protected $teamModel = null;
    protected $dataSummaryModel = null;
    protected $payRecordModel = null;
    protected $payModel = null;
    protected $xpayModel = null;
    protected $rypayModel = null;
//    protected $noNeedLogin = ['test28','test27','test26','test25'];
//    protected $noNeedRight = ['test28','test27','test26','test25'];

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
        $this->urlModel = new UrlModel();
        $this->visitModel = new VisitModel();
        $this->adminModel = new AdminModel();
        $this->teamModel = new TeamModel();
        $this->payModel = new PayModel();
        $this->xpayModel = new XPayModel();
        $this->rypayModel = new RyPayModel();
        $this->dataSummaryModel = new DataSummaryModel();
        $this->payRecordModel = new PayRecordModel();
    }

    /**
     * 生成支付状态数据
     * @param $date
     */
    protected function growPayRecord($date)
    {
        $xpayData = collection($this->xpayModel->select())->toArray();/*享钱支付*/
        $rypayData = collection($this->rypayModel->select())->toArray();/*如意支付*/
        $payData = collection($this->payModel->select())->toArray();/*微信支付*/
        $newData = [];
        foreach ($xpayData as $v) {
            $newData[] = [
                'date'          => $date,
                'team_id'       => $v['team_id'],
                'pay_id'        => $v['id'],
                'pay_type'      => 1,
                'use_count'     => 0,
                'pay_nums'      => 0,
                'money'         => 0.00,
            ];
        }
        //
        foreach ($rypayData as $v1) {
            $newData[] = [
                'date'          => $date,
                'team_id'       => $v1['team_id'],
                'pay_id'        => $v1['id'],
                'pay_type'      => 2,
                'use_count'     => 0,
                'pay_nums'      => 0,
                'money'         => 0.00,
            ];
        }

        foreach ($payData as $v2) {
            $newData[] = [
                'date'          => $date,
                'team_id'       => $v2['team_id'],
                'pay_id'        => $v2['id'],
                'pay_type'      => 0,
                'use_count'     => 0,
                'pay_nums'      => 0,
                'money'         => 0.00,
            ];
        }
        //初始化数据
        $result = $this->payRecordModel->isUpdate(false)->saveAll($newData);

    }

    /**
     * 修复26号数据，包括访问数据，订单成单数，支付成功数，支付成功商品数，支付商户号使用次数，以及商户收钱数据
     */
//    public function test26()
//    {
//        $zeroTime = $this->request->param('zero');
//        $twoTime = $this->request->param('two');
//        $date = date('m-d',$zeroTime);
//        //生成数据统计表基础数据
//        $urlQRCode = collection($this->urlModel->select())->toArray();
//
//        $newData = [];
//        foreach ($urlQRCode as $value) {
//            $newData[] = [
//                'gid'           => $value['production_id'],
//                'date'          => $date,
//                'team_id'       => $value['team_id'],
//                'pid'           => $this->adminModel->get($value['admin_id'])['pid'],
//                'admin_id'      => $value['admin_id'],
//                'check_code'    => $value['check_code'],
//                'visit_nums'    => 0,
//                'order_count'   => 0,
//                'order_nums'    => 0,
//                'pay_done'      => 0,
//                'pay_done_nums' => 0
//            ];
//        }
//        $this->dataSummaryModel->isUpdate(false)->saveAll($newData);
//
//        //生成数据统计表基础数据
//        $urlQRCode = collection($this->dataSummaryModel->where('date',$date)->select())->toArray();
//
//        $newData = [];
//        foreach ($urlQRCode as $value) {
//            $newData[] = [
//                'id'            => $value['id'],
//                'gid'           => $value['gid'],
//                'date'          => $date,
//                'team_id'       => $value['team_id'],
//                'pid'           => $value['pid'],
//                'admin_id'      => $value['admin_id'],
//                'check_code'    => $value['check_code'],
//                'visit_nums'    => $value['visit_nums'],
//                'order_count'   => 0,
//                'order_nums'    => 0,
//                'pay_done'      => 0,
//                'pay_done_nums' => 0
//            ];
//        }
//
//        $visitNums = collection($this->visitModel->where('team_id',6)->where('updatetime','>',$zeroTime)->where('updatetime','<=',$twoTime)->select())->toArray();
//        //根据订单数据更新数据报表订单数据
//        foreach ($visitNums as $visit) {
//            foreach ($newData as &$value) {
//                if ($visit['admin_id'] == $value['admin_id']) {
//                    $value['visit_nums'] += 1;
//                }
//            }
//        }
//        $orderList = collection($this->orderModel->where('team_id',6)->where('updatetime','>',$zeroTime)->where('updatetime','<=',$twoTime)->select())->toArray();
//        //根据订单数据更新数据报表订单数据
//        foreach ($orderList as $order) {
//            foreach ($newData as &$value) {
//                if ($order['admin_id'] == $value['admin_id']) {
//                    $value['order_count'] += 1;
//                    $value['order_nums']    += $order['num'];
//                }
//            }
//        }
//
//        //生成支付数据
//        $this->growPayRecord($date);
//        //查找支付数据
//        $payRecordData = collection($this->payRecordModel->where('date',$date)->select())->toArray();
//        $newRecord = [];
//        foreach ($payRecordData as $v) {
//            $newRecord[] = [
//                'id'            => $v['id'],
//                'date'          => $v['date'],
//                'team_id'       => $v['team_id'],
//                'pay_id'        => $v['pay_id'],
//                'pay_type'      => $v['pay_type'],
//                'use_count'     => 0,
//                'pay_nums'      => 0,
//                'money'         => 0.00,
//            ];
//        }
//
//        $payDones = collection($this->orderModel->where('team_id',6)->where('updatetime','>',$zeroTime)->where('updatetime','<=',$twoTime)->where('pay_status',1)->select())->toArray();
//        //根据订单数据更新数据报表订单数据
//        foreach ($payDones as $order) {
//            foreach ($newData as &$value) {
//                if ($order['admin_id'] == $value['admin_id']) {
//                    $value['pay_done'] += 1;
//                    $value['pay_done_nums'] += $order['num'];
//                }
//            }
//            //更新商户数据
//            foreach ($newRecord as &$item) {
//                if ($order['pay_id'] == $item['pay_id'] && $order['pay_type']== $item['pay_type']) {
//                    $item['use_count'] += 1;
//                    $item['pay_nums'] += 1;
//                    $item['money']  += $order['price'];
//                }
//            }
//        }
//
//        $this->payRecordModel->isUpdate(true)->saveAll($newRecord);
//        $this->dataSummaryModel->isUpdate(true)->saveAll($newData);
//    }

    /**
     * 修复27号数据，包括访问数据，订单成单数，支付成功数，支付成功商品数，支付商户号使用次数，以及商户收钱数据
     */
//    public function test27()
//    {
//        $date = date('m-d',1588003200-86400);
//        //生成数据统计表基础数据
//        $urlQRCode = collection($this->urlModel->select())->toArray();
//
//        $newData = [];
//        foreach ($urlQRCode as $value) {
//            $newData[] = [
//                'gid'           => $value['production_id'],
//                'date'          => $date,
//                'team_id'       => $value['team_id'],
//                'pid'           => $this->adminModel->get($value['admin_id'])['pid'],
//                'admin_id'      => $value['admin_id'],
//                'check_code'    => $value['check_code'],
//                'visit_nums'    => 0,
//                'order_count'   => 0,
//                'order_nums'    => 0,
//                'pay_done'      => 0,
//                'pay_done_nums' => 0
//            ];
//        }
//        $this->dataSummaryModel->isUpdate(false)->saveAll($newData);
//
//        //生成数据统计表基础数据
//        $urlQRCode = collection($this->dataSummaryModel->where('date',$date)->select())->toArray();
//
//        $newData = [];
//        foreach ($urlQRCode as $value) {
//            $newData[] = [
//                'id'            => $value['id'],
//                'gid'           => $value['gid'],
//                'date'          => $date,
//                'team_id'       => $value['team_id'],
//                'pid'           => $value['pid'],
//                'admin_id'      => $value['admin_id'],
//                'check_code'    => $value['check_code'],
//                'visit_nums'    => $value['visit_nums'],
//                'order_count'   => 0,
//                'order_nums'    => 0,
//                'pay_done'      => 0,
//                'pay_done_nums' => 0
//            ];
//        }
//
//        $visitNums = collection($this->visitModel->where('team_id',6)->where('updatetime','>',1588003200-86400)->where('updatetime','<=',1588003200)->select())->toArray();
//        //根据订单数据更新数据报表订单数据
//        foreach ($visitNums as $visit) {
//            foreach ($newData as &$value) {
//                if ($visit['admin_id'] == $value['admin_id']) {
//                    $value['visit_nums'] += 1;
//                }
//            }
//        }
//        $orderList = collection($this->orderModel->where('team_id',6)->where('updatetime','>',1588003200-86400)->where('updatetime','<=',1588003200)->select())->toArray();
//        //根据订单数据更新数据报表订单数据
//        foreach ($orderList as $order) {
//            foreach ($newData as &$value) {
//                if ($order['admin_id'] == $value['admin_id']) {
//                    $value['order_count'] += 1;
//                    $value['order_nums']    += $order['num'];
//                }
//            }
//        }
//
//        //生成支付数据
//        $this->growPayRecord($date);
//        //查找支付数据
//        $payRecordData = collection($this->payRecordModel->where('date',$date)->select())->toArray();
//        $newRecord = [];
//        foreach ($payRecordData as $v) {
//            $newRecord[] = [
//                'id'            => $v['id'],
//                'date'          => $v['date'],
//                'team_id'       => $v['team_id'],
//                'pay_id'        => $v['pay_id'],
//                'pay_type'      => $v['pay_type'],
//                'use_count'     => 0,
//                'pay_nums'      => 0,
//                'money'         => 0.00,
//            ];
//        }
//
//        $payDones = collection($this->orderModel->where('team_id',6)->where('updatetime','>',1588003200-86400)->where('updatetime','<=',1588003200)->where('pay_status',1)->select())->toArray();
//        //根据订单数据更新数据报表订单数据
//        foreach ($payDones as $order) {
//            foreach ($newData as &$value) {
//                if ($order['admin_id'] == $value['admin_id']) {
//                    $value['pay_done'] += 1;
//                    $value['pay_done_nums'] += $order['num'];
//                }
//            }
//            //更新商户数据
//            foreach ($newRecord as &$item) {
//                if ($order['pay_id'] == $item['pay_id'] && $order['pay_type']== $item['pay_type']) {
//                    $item['use_count'] += 1;
//                    $item['pay_nums'] += 1;
//                    $item['money']  += $order['price'];
//                }
//            }
//        }
//
//        $this->payRecordModel->isUpdate(true)->saveAll($newRecord);
//        $this->dataSummaryModel->isUpdate(true)->saveAll($newData);
//    }

    /**
     * 修复28号数据 ，包括订单成单数，支付成功数，支付成功商品数，支付商户号使用次数，以及商户收钱数据
     */
//    public function test28()
//    {
//        $date = date('m-d',1588003200);
//        //生成数据统计表基础数据
//        $urlQRCode = collection($this->dataSummaryModel->where('date',$date)->select())->toArray();
//
//        $newData = [];
//        foreach ($urlQRCode as $value) {
//            $newData[] = [
//                'id'            => $value['id'],
//                'gid'           => $value['gid'],
//                'date'          => $date,
//                'team_id'       => $value['team_id'],
//                'pid'           => $value['pid'],
//                'admin_id'      => $value['admin_id'],
//                'check_code'    => $value['check_code'],
//                'visit_nums'    => $value['visit_nums'],
//                'order_count'   => 0,
//                'order_nums'    => 0,
//                'pay_done'      => 0,
//                'pay_done_nums' => 0
//            ];
//        }
//
//        $orderList = collection($this->orderModel->where('team_id',6)->where('updatetime','>',1588003200)->where('updatetime','<=',1588003200+86400)->select())->toArray();
//        //根据订单数据更新数据报表订单数据
//        foreach ($orderList as $order) {
//            foreach ($newData as &$value) {
//                if ($order['admin_id'] == $value['admin_id']) {
//                    $value['order_count']   += 1;
//                    $value['order_nums']    += $order['num'];
//                }
//            }
//        }
//        //查找支付数据
//        $payRecordData = collection($this->payRecordModel->where('date',$date)->select())->toArray();
//        $newRecord = [];
//        foreach ($payRecordData as $v) {
//            $newRecord[] = [
//                'id'            => $v['id'],
//                'date'          => $v['date'],
//                'team_id'       => $v['team_id'],
//                'pay_id'        => $v['pay_id'],
//                'pay_type'      => $v['pay_type'],
//                'use_count'     => $v['use_count'],
//                'pay_nums'      => 0,
//                'money'         => 0.00,
//            ];
//        }
//
//        $payDones = collection($this->orderModel->where('team_id',6)->where('updatetime','>',1588003200)->where('updatetime','<=',1588003200+86400)->where('pay_status',1)->select())->toArray();
//        //根据订单数据更新数据报表订单数据
//        foreach ($payDones as $order) {
//            foreach ($newData as &$value) {
//                if ($order['admin_id'] == $value['admin_id']) {
//                    $value['pay_done'] += 1;
//                    $value['pay_done_nums'] += $order['num'];
//                }
//            }
//            //更新商户数据
//            foreach ($newRecord as &$item) {
//                if ($order['pay_id'] == $item['pay_id'] && $order['pay_type']== $item['pay_type']) {
//                    $item['pay_nums'] += 1;
//                    $item['money']  += $order['price'];
//                }
//            }
//        }
//
//        $this->payRecordModel->isUpdate(true)->saveAll($newRecord);
//        $this->dataSummaryModel->isUpdate(true)->saveAll($newData);
//    }


    /**
     * 获取当天0点到当天23点59分59秒的时间戳
     * @internal
     */
    protected function getBeginEndTime()
    {
        $ytime = strtotime(date("Y-m-d",strtotime("-1 day")));//昨天开始时间戳
        $zerotime = $ytime+24 * 60 * 60;//昨天23点59分59秒+1秒
        $totime = $zerotime+24 * 60 * 60-1;//今天结束时间戳 23点59分59秒。
        return [$zerotime,$totime];
    }


    /**
     * 获取昨天的时间戳
     */
    protected function getYesterDayTime()
    {
        $ytime = strtotime(date("Y-m-d",strtotime("-2 day")));//昨天开始时间戳
        $zerotime = $ytime+24 * 60 * 60;//昨天23点59分59秒
        $totime = $zerotime+24 * 60 * 60-1;//今天结束时间戳 23点59分59秒。
        return [$zerotime,$totime];
    }


    /**
     * 获取当天时间小时段
     */
    protected function getTimeDate()
    {
        $time = $this->getBeginEndTime();
        $newArr = [];
        for ($i = 1; $i <= 24; $i++) {
            $newArr[] = $time[0]+3600*$i;
        }
        return $newArr;
    }

    /**
     * @description:根据数据
     * @param $dataArr array    需要分组的数据
     * @param $keyStr   string  分组依据
     * @param $userIds  array    业务员ID
     * @return array
     * @internal
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
        foreach ($userIds as $userId) {
            if (!in_array($userId,$adminIds)) {
                $newArr[$userId] = 0;
            }
        }
        return $newArr;
    }


    /**
     * 根据时间戳划分订单数据
     * @param $data array 需要区分的数据
     * @param $visitData array 访问记录
     * @return array
     */
    protected function doDataGroupByTime($data,$visitData)
    {
        $tmp = [
            'visit'         => 0,
            'order_count'   => 0,
            'order_nums'    => 0,
            'pay_done'      => 0,
            'pay_done_nums' => 0,
            'pay_total'     => 0.00
        ];

        foreach ($data as $value) {
            //表示属于一天的数据量。
            $tmp['order_count']         += 1;
            $tmp['order_nums']          += (int)$value['num'];
            if ($value['pay_status'] == 1) {
                $tmp['pay_done']        += 1;
                $tmp['pay_done_nums']   += (int)$value['num'];
                $tmp['pay_total']       += (float)$value['price'];
            }
        }

        foreach ($visitData as $v1) {
            $tmp['visit'] += 1;
        }

        return $tmp;

    }


    /**
     * 查看
     */
    public function index()
    {
        $orderField = ['team_id','admin_id','num','price','pay_status','createtime'];
        $visitField = ['team_id','admin_id','count','createtime'];
        if ($this->adminInfo['id'] == 1) {
            //获取当天的日期
            $dateTime = $this->getBeginEndTime();
            //获取当天所有用户的报表
            $visitData = collection($this->visitModel->field($visitField)->whereTime('createtime','between',[$dateTime[0],$dateTime[1]])->select())->toArray();
            //先将所有数据按日期分类
            $orderData = collection($this->orderModel->field($orderField)->whereTime('createtime','between',[$dateTime[0],$dateTime[1]])->select())->toArray();
            //昨天数据汇总
            $yesterDayTime = $this->getYesterDayTime();
            //获取当天所有用户的报表
            $yesVisitData = collection($this->visitModel->field($visitField)->whereTime('createtime','between',[$yesterDayTime[0],$yesterDayTime[1]])->select())->toArray();
            //先将所有数据按日期分类
            $yesOrderData = collection($this->orderModel->field($orderField)->whereTime('createtime','between',[$yesterDayTime[0],$yesterDayTime[1]])->select())->toArray();
            //渲染历史数据汇总
            if (!Cache::has('all-history-visit-data')) {
                $historyVisitData = collection($this->visitModel->field($visitField)->select())->toArray();
                Cache::set('all-history-visit-data',$historyVisitData);
            } else {
                $historyVisitData = Cache::get('all-history-visit-data');
            }

            if (!Cache::has('all-history-order-data')) {
                $historyOrderData = collection($this->orderModel->field($orderField)->select())->toArray();
                Cache::set('all-history-order-data',$historyOrderData);
            } else {
                $historyOrderData = Cache::get('all-history-order-data');
            }

        } else {
            if ($this->adminInfo['pid'] == 0) {
                //获取当天的日期
                $dateTime = $this->getBeginEndTime();
                $where = ['team_id',$this->adminInfo['team_id']];
                //获取当天所有用户的报表
                $visitData = collection($this->visitModel->field($visitField)->whereTime('createtime','between',[$dateTime[0],$dateTime[1]])->where($where)->select())->toArray();
                //先将所有数据按日期分类
                $orderData = collection($this->orderModel->field($orderField)->whereTime('createtime','between',[$dateTime[0],$dateTime[1]])->where($where)->select())->toArray();
                //昨天数据汇总
                $yesterDayTime = $this->getYesterDayTime();
                //获取当天所有用户的报表
                $yesVisitData = collection($this->visitModel->field($visitField)->whereTime('createtime','between',[$yesterDayTime[0],$yesterDayTime[1]])->where($where)->select())->toArray();
                //先将所有数据按日期分类
                $yesOrderData = collection($this->orderModel->field($orderField)->whereTime('createtime','between',[$yesterDayTime[0],$yesterDayTime[1]])->where($where)->select())->toArray();
                //渲染历史数据汇总
                if (!Cache::has('all-history-visit-data-team-'.$this->adminInfo['team_id'])) {
                    $historyVisitData = collection($this->visitModel->field($visitField)->where('team_id',$this->adminInfo['team_id'])->select())->toArray();
                    Cache::set('all-history-visit-data-team-'.$this->adminInfo['team_id'],$historyVisitData);
                } else {
                    $historyVisitData = Cache::get('all-history-visit-data-team-'.$this->adminInfo['team_id']);
                }
                if (!Cache::has('all-history-order-data-team-'.$this->adminInfo['team_id'])) {
                    $historyOrderData = collection($this->orderModel->field($orderField)->where('team_id',$this->adminInfo['team_id'])->select())->toArray();
                    Cache::set('all-history-order-data-team-'.$this->adminInfo['team_id'],$historyOrderData);
                } else {
                    $historyOrderData = Cache::get('all-history-order-data-team-'.$this->adminInfo['team_id']);
                }
            } else {
                //获取当天的日期
                $dateTime = $this->getBeginEndTime();
                $where = ['admin_id',$this->adminInfo['id']];
                //获取当天所有用户的报表
                $visitData = collection($this->visitModel->field($visitField)->whereTime('createtime','between',[$dateTime[0],$dateTime[1]])->where($where)->select())->toArray();
                //先将所有数据按日期分类
                $orderData = collection($this->orderModel->field($orderField)->whereTime('createtime','between',[$dateTime[0],$dateTime[1]])->where($where)->select())->toArray();
                //昨天数据汇总
                $yesterDayTime = $this->getYesterDayTime();
                //获取当天所有用户的报表
                $yesVisitData = collection($this->visitModel->field($visitField)->whereTime('createtime','between',[$yesterDayTime[0],$yesterDayTime[1]])->where($where)->select())->toArray();
                //先将所有数据按日期分类
                $yesOrderData = collection($this->orderModel->field($orderField)->whereTime('createtime','between',[$yesterDayTime[0],$yesterDayTime[1]])->where($where)->select())->toArray();
                //渲染历史数据汇总
                if (!Cache::has('all-history-visit-data-admin-'.$this->adminInfo['id'])) {
                    $historyVisitData = collection($this->visitModel->field($visitField)->where('admin_id',$this->adminInfo['id'])->select())->toArray();
                    Cache::set('all-history-visit-data-admin-'.$this->adminInfo['id'],$historyVisitData);
                } else {
                    $historyVisitData = Cache::get('all-history-visit-data-admin-'.$this->adminInfo['id']);
                }
                if (!Cache::has('all-history-order-data-admin-'.$this->adminInfo['id'])) {
                    $historyOrderData = collection($this->orderModel->field($orderField)->where('admin_id',$this->adminInfo['id'])->select())->toArray();
                    Cache::set('all-history-order-data-admin-'.$this->adminInfo['id'],$historyOrderData);
                } else {
                    $historyOrderData = Cache::get('all-history-order-data-admin-'.$this->adminInfo['id']);
                }
            }

        }
        $newData = $this->doDataGroupByTime($orderData,$visitData);
        $newYesData = $this->doDataGroupByTime($yesOrderData,$yesVisitData);
        $newHisData = $this->doDataGroupByTime($historyOrderData,$historyVisitData);
        //渲染当前实时变量
        $this->assign('data',$newData);
        //渲染模板变量
        $this->assign('yesterdayData',$newYesData);
        $this->assign('historyData',$newHisData);
        //历史数据
        return $this->view->fetch();
    }


    /**
     * 查看
     */
    public function index0()
    {
        //获取当天的日期
        $date = date('m-d',time());
        //获取当天所有用户的报表
        $dataSummary = collection($this->dataSummaryModel->where('date',$date)->select())->toArray();
        //先将所有数据按日期分类
        $todayPayData = collection($this->payRecordModel->where('date',$date)->select())->toArray();
        $todayPayTotal = 0.00;
        foreach ($todayPayData as $todayPay) {
            $todayPayTotal += (float)$todayPay['money'];
        }
        $data = [
            'visit'         => 0,
            'order_count'   => 0,
            'order_nums'    => 0,
            'pay_done'      => 0,
            'pay_done_nums' => 0
        ];
        foreach ($dataSummary as $v1) {
            $data['visit']            += $v1['visit_nums'];
            $data['order_count']      += $v1['order_count'];
            $data['order_nums']       += $v1['order_nums'];
            $data['pay_done']         += $v1['pay_done'];
            $data['pay_done_nums']    += $v1['pay_done_nums'];
        }
        $data['pay_total'] = $todayPayTotal;


        //昨天数据汇总
        $yesterDayTime = $this->getYesterDayTime();
        $yesterDate = date('m-d',$yesterDayTime[1]);
        $yesterDayPayData = collection($this->payRecordModel->where('date',$yesterDate)->select())->toArray();
        $yesterPayTotal = 0.00;
        foreach ($yesterDayPayData as $pay) {
            $yesterPayTotal += (float)$pay['money'];
        }

        $yseterDayData = collection($this->dataSummaryModel->where('date',$yesterDate)->select())->toArray();
        $newYesData = [
            'visit'         => 0,
            'order_count'   => 0,
            'order_nums'    => 0,
            'pay_done'      => 0,
            'pay_done_nums' => 0
        ];
        foreach ($yseterDayData as $v2) {
            $newYesData['visit']            += $v2['visit_nums'];
            $newYesData['order_count']      += $v2['order_count'];
            $newYesData['order_nums']       += $v2['order_nums'];
            $newYesData['pay_done']         += $v2['pay_done'];
            $newYesData['pay_done_nums']    += $v2['pay_done_nums'];
        }
        //增加入账总金额
        $newYesData['pay_total'] = $yesterPayTotal;
        //渲染历史数据汇总
        $historyData = collection($this->dataSummaryModel->select())->toArray();
        $historyPayData = collection($this->payRecordModel->select())->toArray();
        $hisPayTotal = 0.00;
        foreach ($historyPayData as $historyDatum) {
            $hisPayTotal += $historyDatum['money'];
        }

        $newHisData = [
            'visit'         =>  0,
            'order_count'   =>  0,
            'order_nums'    =>  0,
            'pay_done'      =>  0,
            'pay_done_nums' =>  0
        ];
        foreach ($historyData as $v3) {
            $newHisData['visit']        += $v3['visit_nums'];
            $newHisData['order_count']  += $v3['order_count'];
            $newHisData['order_nums']   += $v3['order_nums'];
            $newHisData['pay_done']     += $v3['pay_done'];
            $newHisData['pay_done_nums'] += $v3['pay_done_nums'];
        }
        //将入账金额写入历史变量
        $newHisData['pay_total'] = $hisPayTotal;
        //渲染当前实时变量
        $this->assign('data',$data);
        //渲染模板变量
        $this->assign('yesterdayData',$newYesData);
        $this->assign('historyData',$newHisData);
        //历史数据
        return $this->view->fetch();
    }



    /**
     * 获取用户关系。往
     * @return array
     * @internal
     */
    protected function getUserLower()
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

}
