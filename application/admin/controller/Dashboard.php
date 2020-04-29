<?php

namespace app\admin\controller;

use app\admin\model\data\DataSummary as DataSummaryModel;
use app\common\controller\Backend;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\team\Team as TeamModel;
use app\admin\model\data\Visit as VisitModel;
use app\admin\model\production\Url as UrlModel;
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
    protected $teamModel = null;
    protected $dataSummaryModel = null;
    protected $noNeedLogin = ['test'];
    protected $noNeedRight = ['test'];

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
        $this->urlModel = new UrlModel();
        $this->visitModel = new VisitModel();
        $this->adminModel = new AdminModel();
        $this->teamModel = new TeamModel();
        $this->dataSummaryModel = new DataSummaryModel();
    }


    public function test()
    {
        dump($this->getTimeDate());
    }


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
     * @internal
     * @param {dataArr:需要分组的数据；keyStr:分组依据}
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
        foreach ($userIds as $userId) {
            if (!in_array($userId,$adminIds)) {
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
        $date = date('m-d',time());
        //获取当天所有用户的报表
        $dataSummary = collection($this->dataSummaryModel->where('date',$date)->select())->toArray();
        //先将所有数据按日期分类
        $data = [
            'visit'         => 0,
            'order_count'   => 0,
            'order_nums'    => 0,
            'pay_done'      => 0,
            'pay_done_nums' => 0
        ];
        foreach ($dataSummary as $value) {
            $data['visit']            += $value['visit_nums'];
            $data['order_count']      += $value['order_count'];
            $data['order_nums']       += $value['order_nums'];
            $data['pay_done']         += $value['pay_done'];
            $data['pay_done_nums']    += $value['pay_done_nums'];
        }
        //渲染当前实时变量
        $this->assignconfig('data',$data);

        //昨天数据汇总
        $yesterDayTime = $this->getYesterDayTime();
        $yseterDayData = collection($this->dataSummaryModel->where('createtime','>',$yesterDayTime[0])->where('createtime','<',$yesterDayTime[1])->select())->toArray();
        $newYesData = [
            'visit'         => 0,
            'order_count'   => 0,
            'order_nums'    => 0,
            'pay_done'      => 0,
            'pay_done_nums' => 0
        ];
        foreach ($yseterDayData as $value) {
            $newYesData['visit']            += $value['visit_nums'];
            $newYesData['order_count']      += $value['order_count'];
            $newYesData['order_nums']       += $value['order_nums'];
            $newYesData['pay_done']         += $value['pay_done'];
            $newYesData['pay_done_nums']    += $value['pay_done_nums'];
        }
        //渲染模板变量
        $this->assign('yesterdayData',$newYesData);
        //渲染历史数据汇总
        $historyData = collection($this->dataSummaryModel->select())->toArray();
        $newHisData = [
            'visit'         =>  0,
            'order_count'   =>  0,
            'order_nums'    =>  0,
            'pay_done'      =>  0,
            'pay_done_nums' =>  0
        ];
        foreach ($historyData as $value) {
            $newHisData['visit']        += $value['visit_nums'];
            $newHisData['order_count']  += $value['order_count'];
            $newHisData['order_nums']   += $value['order_nums'];
            $newHisData['pay_done']     += $value['pay_done'];
            $newHisData['pay_done_nums'] += $value['pay_done_nums'];
        }
        $this->assign('historyData',$newHisData);
        //历史数据
//        $this->view->assign([
//            'paylist'          => $data,
//            'visit'            => $newArr['visit_nums'],
//            'order_count'      => $newArr['order_count'],
//            'order_nums'       => $newArr['order_nums'],
//            'pay_done'         => $newArr['pay_done'],
//            'pay_done_nums'    => $newArr['pay_done_nums'],
//        ]);

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

}
