<?php
namespace app\admin\controller\data;

use app\admin\model\data\DataSummary as DataSummaryModel;
use app\common\controller\Backend;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\team\Team as TeamModel;
use app\admin\model\data\Visit as VisitModel;
use app\admin\model\production\Url as UrlModel;
use app\admin\model\Admin as AdminModel;
use fast\Tree;
use think\Cache;

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
    protected $dataSummaryModel = null;

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

    /**
     * 获取当天0点到当天23点59分59秒的时间戳
     * @internal
     */
    protected function getBeginEndTime()
    {
        $ytime = strtotime(date("Y-m-d", strtotime("-1 day")));//昨天开始时间戳
        $zerotime = $ytime + 24 * 60 * 60;//昨天23点59分59秒+1秒
        $totime = $zerotime + 24 * 60 * 60-1;//今天结束时间戳 23点59分59秒。
        return [$zerotime, $totime];
    }

    /**
     * 通过字符串获取时间戳
     * @param $date
     * @return float[]|int[]
     */
    protected function strToTimestamp($date)
    {
        $ytime = strtotime($date);//昨天开始时间戳
        $zerotime = $ytime;//昨天23点59分59秒+1秒
        $totime = $zerotime + 24 * 60 * 60-1;//今天结束时间戳 23点59分59秒。
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
     * 统计订单数量及订单商品数量
     * @param $data array 用于统计的数组
     * @return array
     */
    protected function countNums($data = [])
    {
        $tmp = [
            'order_count'   => 0,
            'order_nums'    => 0,
            'pay_done'      => 0,
            'pay_done_nums' => 0,
            'pay_total'     => 0.00
        ];
        foreach ($data as $value) {
            $tmp['order_count']         += 1;
            $tmp['order_nums']          += (int)$value['num'];
            if ($value['pay_status'] == 1) {
                $tmp['pay_done']        += 1;
                $tmp['pay_done_nums']   += (int)$value['num'];
                $tmp['pay_total']       += (float)$value['price'];
            }
        }
        return $tmp;
    }
    /**
     * @return array
     */
    protected function shellGetAllUser()
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
     * 根据时间查询订单与访问量的原始数据。
     * @param $dateTime array 当天的起始和结束时间戳
     * @param $level
     * @param int $teamId
     * @param array $adminIds
     * @param int $adminId
     * @return array
     */
    protected function doSummary($dateTime,$level,$teamId = 0,$adminIds=[],$adminId = 0)
    {
        if ($level == 0) {
            //访问记录
            $visitSummary = $this->visitModel
                ->where('createtime','>=',$dateTime[0])
                ->where('createtime','<=',$dateTime[1])
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
                ->where('createtime','>=',$dateTime[0])
                ->where('createtime','<=',$dateTime[1])
                ->select();
            $orderData = collection($orderData)->toArray();
        } elseif ($level == 1) {
            //访问记录
            $visitSummary = $this->visitModel
                ->where('createtime','>=',$dateTime[0])
                ->where('createtime','<=',$dateTime[1])
                ->where('team_id',$teamId)
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
                ->where('createtime','>=',$dateTime[0])
                ->where('createtime','<=',$dateTime[1])
                ->where('team_id',$teamId)
                ->select();
            $orderData = collection($orderData)->toArray();
        } elseif ($level == 2) {
            //访问记录
            $visitSummary = $this->visitModel
                ->where('createtime','>=',$dateTime[0])
                ->where('createtime','<=',$dateTime[1])
                ->where('admin_id','in',$adminIds)
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
                ->where('createtime','>=',$dateTime[0])
                ->where('createtime','<=',$dateTime[1])
                ->where('admin_id','in',$adminIds)
                ->select();
            $orderData = collection($orderData)->toArray();
        } else {
            //访问记录
            $visitSummary = $this->visitModel
                ->where('createtime','>=',$dateTime[0])
                ->where('createtime','<=',$dateTime[1])
                ->where('admin_id',$adminId)
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
                ->where('createtime','>=',$dateTime[0])
                ->where('createtime','<=',$dateTime[1])
                ->where('admin_id',$adminId)
                ->select();
            $orderData = collection($orderData)->toArray();
        }
        return [$orderData,$newVisitSummary];
    }

    /**
     * 首页
     * @remark 新报表数据，数据取自访问表与订单表
     * @return string|\think\response\Json
     * @throws \think\Exception
     */
    public function index()
    {
        $userIds = $this->shellGetAllUser();
        $userInfo = $this->adminInfo;
        $teamData = $this->teamModel->column('name','id');
        $adminName = $this->adminModel->column('nickname','id');
        $selectData = array_unique(collection($this->dataSummaryModel->field('date')->order('date','desc')->column('date'))->toArray());
        $newSelectData = [];
        foreach ($selectData as $item) {
            $newSelectData[$item] = $item;
        }

        if ($this->request->isPost()) {
            $params = $this->request->param();
            //获取当前用户信息
            $date = $params['row']['select'];
            //将05-05字符串转换为当前的时间戳
            $dateTime = $this->strToTimestamp('2020-'.$date);
            if ($userInfo['id'] == 1) {
                $data = $this->doSummary($dateTime,0);
            } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
                //老板查看团队所有人员的数据
                $data = $this->doSummary($dateTime,1,$this->adminInfo['team_id']);
            } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
                //组长查看自己及以下员工的数据
                $userIds = $this->getUserLower();
                $data = $this->doSummary($dateTime,2,$userIds);
            } else {
                //业务员只能查看自己的订单数据
                $data = $this->doSummary($dateTime,3,$this->adminInfo['id']);
            }
            $orderData=$data[0];
            $newVisitSummary = $data[1];
        } else {
            $date = date('m-d',time());
            $dateTime = $this->getBeginEndTime();
            if ($userInfo['id'] == 1) {
                //表示是平台总管理员，可以查看所有记录
                $data = $this->doSummary($dateTime,0);
            } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
                //老板查看团队所有人员的数据
                $data = $this->doSummary($dateTime,1,$this->adminInfo['team_id']);
            } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
                //组长查看自己及以下员工的数据
                $userIds = $this->getUserLower();
                $data = $this->doSummary($dateTime,2,$userIds);
            } else {
                //业务员只能查看自己的订单数据
                $data = $this->doSummary($dateTime,3,$this->adminInfo['id']);
            }
            $orderData=$data[0];
            $newVisitSummary = $data[1];
        }

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
            if ($item) {
                //表示item不为空
                $countData = $this->countNums($item);
            } else {
                //表示对应用户没有订单数据
                $countData = [
                    'order_count'   => 0,
                    'order_nums'    => 0,
                    'pay_done'      => 0,
                    'pay_done_nums' => 0,
                    'pay_total'     => 0.00
                ];
            }

            $pid = $this->adminModel->get($key)['pid'];
            $teamId =  $this->adminModel->get($key)['team_id'];
            $newResOrderData[]  = [
                'team_id'       => $teamId,
                'pid'           => $pid,
                'pid_name'      => $this->adminModel->get($pid)['nickname'],
                'admin_id'      => $key,
                'nickname'      => $this->adminModel->get($key)['nickname'],
                'visit_nums'    => isset($newVisitSummary[$key]) ? $newVisitSummary[$key] : 0,
                'order_count'   => $countData['order_count'],
                'order_nums'    => $countData['order_nums'],
                'pay_done'      => $countData['pay_done'],
                'pay_done_nums' => $countData['pay_done_nums'],
                'pay_total'     => $countData['pay_total']
            ];
        }
        //生成当天汇总数据
        $todayTotal = [
            'visit_nums'    => 0,
            'order_count'   => 0,
            'order_nums'    => 0,
            'pay_done'      => 0,
            'pay_done_nums' => 0,
            'pay_total'     => 0.00
        ];

        foreach ($newResOrderData as $item) {
            $todayTotal['visit_nums'] += $item['visit_nums'];
            $todayTotal['order_count'] += $item['order_count'];
            $todayTotal['order_nums'] += $item['order_nums'];
            $todayTotal['pay_done'] += $item['pay_done'];
            $todayTotal['pay_done_nums'] += $item['pay_done_nums'];
            $todayTotal['pay_total'] += $item['pay_total'];
        }

        $this->assign('user',$this->adminInfo);/*当前用户信息*/
        $this->assign('teamData',$teamData);/*团队数据*/
        $this->assign('adminName',$adminName);/*业务员ID=>名称数据*/
        $this->assign('today_total',$todayTotal);/*当天数据汇总*/
        $this->assign('data',$newResOrderData);
        $this->assign('date',$date);
        $this->assign('select_data',$newSelectData);/*查询数据*/
        return $this->view->fetch();
    }

    /**
     * 根据时间戳划分订单数据
     * @param $date string  日期格式 05-05
     * @param $adminId  int  业务员ID
     * @param $pid  int 上级id
     * @param $teamId   int 团队ID
     * @param $data array 需要区分的数据
     * @param $visitData array 访问记录
     * @param $begin    int 开始的时间戳
     * @param $end  int 结束的时间戳
     * @return array
     */
    protected function doDataGroupByTime($date,$adminId,$pid,$teamId,$data,$visitData,$begin,$end)
    {
        $tmp = [
            'date'          => $date,
            'pid'           => $pid,
            'admin_id'      => $adminId,
            'team_id'       => $teamId,
            'visit_nums'    => 0,
            'order_count'   => 0,
            'order_nums'    => 0,
            'pay_done'      => 0,
            'pay_done_nums' => 0,
            'pay_total'     => 0.00
        ];

        foreach ($data as $value) {
            if ($value['createtime'] >= $begin && $value['createtime'] <= $end) {
                //表示属于一天的数据量。
                $tmp['order_count']         += 1;
                $tmp['order_nums']          += (int)$value['num'];
                if ($value['pay_status'] == 1) {
                    $tmp['pay_done']        += 1;
                    $tmp['pay_done_nums']   += (int)$value['num'];
                    $tmp['pay_total']       += (float)$value['price'];
                }
            }
        }

        foreach ($visitData as $v1) {
            if ($v1['createtime'] >= $begin && $v1['createtime'] <= $end) {
                $tmp['visit_nums'] += 1;
            }
        }

        return $tmp;

    }

    /**
     * 查看
     * @return \think\response\Json|void
     * @throws \think\Exception
     */
    public function index0()
    {
        $userInfo = $this->adminInfo;
        $teamData = $this->teamModel->column('name','id');
        $adminName = $this->adminModel->column('nickname','id');
        $selectData = array_unique(collection($this->dataSummaryModel->field('date')->order('date','desc')->column('date'))->toArray());
        $newSelectData = [];
        foreach ($selectData as $item) {
            $newSelectData[$item] = $item;
        }

        if ($this->request->isPost()) {
            $params = $this->request->param();
            //获取当前用户信息
            $date = $params['row']['select'];
            //先将所有数据按日期分类
            $data = [];

            if ($userInfo['id'] == 1) {
                //表示是平台总管理员，可以查看所有记录
                //获取当天时间 0点到23点59分59秒的订单数量。
                //获取当天所有用户的报表
                $dataSummary = collection($this->dataSummaryModel->where('date',$date)->select())->toArray();
                foreach ($dataSummary as &$item) {
                    $name = $this->adminModel->get($item['admin_id'])['nickname'];
                    $item['name'] = $name;
                    $data[] = $item;
                }

            } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
                //老板查看团队所有人员的数据
                //获取团队下所有的用户数据
                //获取当天所有用户的报表
                $dataSummary = $this->dataSummaryModel
                    ->where('date',$date)
                    ->where('team_id',$this->adminInfo['team_id'])
                    ->select();
                $dataSummary = collection($dataSummary)->toArray();
                foreach ($dataSummary as &$item) {
                    $name = $this->adminModel->get($item['admin_id'])['nickname'];
                    $item['name'] = $name;
                    $data[] = $item;
                }

            } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
                //组长查看自己及以下员工的数据
                $userIds = $this->getUserLower();
                $dataSummary = $this->dataSummaryModel
                    ->where('date',$date)
                    ->where('admin_id','in',$userIds)
                    ->select();
                $dataSummary = collection($dataSummary)->toArray();
                foreach ($dataSummary as &$item) {
                    $name = $this->adminModel->get($item['admin_id'])['nickname'];
                    $item['name'] = $name;
                    $data[] = $item;
                }

            } else {
                //业务员只能查看自己的订单数据
                $dataSummary = $this->dataSummaryModel
                    ->where('date',$date)
                    ->where('admin_id',$this->adminInfo['id'])
                    ->select();
                $dataSummary = collection($dataSummary)->toArray();
                foreach ($dataSummary as &$item) {
                    $name = $this->adminModel->get($item['admin_id'])['nickname'];
                    $item['name'] = $name;
                    $data[] = $item;
                }

            }
            $this->assign('user',$this->adminInfo);/*当前用户信息*/
            $this->assign('teamData',$teamData);/*团队数据*/
            $this->assign('adminName',$adminName);/*业务员ID=>名称数据*/
            $this->assign('data',$data);
            $this->assign('date',$params['row']['select']);
            $this->assign('select_data',$newSelectData);/*查询数据*/
            return $this->view->fetch();
        } else {
            //获取当前用户信息
            $userInfo = $this->adminInfo;
            $date = date('m-d',time());
            $teamData = $this->teamModel->column('name','id');
            $adminName = $this->adminModel->column('nickname','id');
            $selectData = array_unique(collection($this->dataSummaryModel->field('date')->order('date','desc')->column('date'))->toArray());
            $newSelectData = [];
            foreach ($selectData as $item) {
                $newSelectData[$item] = $item;
            }
            //先将所有数据按日期分类
            $data = [];

            if ($userInfo['id'] == 1) {
                //表示是平台总管理员，可以查看所有记录
                //获取当天时间 0点到23点59分59秒的订单数量。
                //获取当天所有用户的报表
                $dataSummary = collection($this->dataSummaryModel->where('date',$date)->select())->toArray();
                foreach ($dataSummary as &$item) {
                    $name = $this->adminModel->get($item['admin_id'])['nickname'];
                    $item['name'] = $name;
                    $data[] = $item;
                }

            } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
                //老板查看团队所有人员的数据
                //获取团队下所有的用户数据
                //获取当天所有用户的报表
                $dataSummary = $this->dataSummaryModel
                    ->where('date',$date)
                    ->where('team_id',$this->adminInfo['team_id'])
                    ->select();
                $dataSummary = collection($dataSummary)->toArray();
                foreach ($dataSummary as &$item) {
                    $name = $this->adminModel->get($item['admin_id'])['nickname'];
                    $item['name'] = $name;
                    $data[] = $item;
                }

            } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
                //组长查看自己及以下员工的数据
                $userIds = $this->getUserLower();
                $dataSummary = $this->dataSummaryModel
                    ->where('date',$date)
                    ->where('admin_id','in',$userIds)
                    ->select();
                $dataSummary = collection($dataSummary)->toArray();
                foreach ($dataSummary as &$item) {
                    $name = $this->adminModel->get($item['admin_id'])['nickname'];
                    $item['name'] = $name;
                    $data[] = $item;
                }

            } else {
                //业务员只能查看自己的订单数据
                $dataSummary = $this->dataSummaryModel
                    ->where('date',$date)
                    ->where('admin_id',$this->adminInfo['id'])
                    ->select();
                $dataSummary = collection($dataSummary)->toArray();
                foreach ($dataSummary as &$item) {
                    $name = $this->adminModel->get($item['admin_id'])['nickname'];
                    $item['name'] = $name;
                    $data[] = $item;
                }

            }
            $this->assign('user',$this->adminInfo);/*当前用户信息*/
            $this->assign('teamData',$teamData);/*团队数据*/
            $this->assign('adminName',$adminName);/*业务员ID=>名称数据*/
            $this->assign('data',$data);
            $this->assign('date',$date);
            $this->assign('select_data',$newSelectData);/*查询数据*/
            return $this->view->fetch();
        }

    }

    /**
     * ajax查询个人历史数据
     */
    public function searchPersonHistory()
    {
        $params = $this->request->param();
        $userInfo = $this->adminModel->get($params['ids']);
        $teamData = $this->teamModel->column('name','id');
        $adminName = $this->adminModel->column('nickname','id');
        //查询数据
        $selectData = array_unique(collection($this->dataSummaryModel->field('date')->order('date','desc')->column('date'))->toArray());
        $newSelectData = [];
        foreach ($selectData as $item) {
            $newSelectData[$item] = $this->strToTimestamp('2020-'.$item);
        }
        if (!Cache::has('history-data-for-team-'.$params['ids'])) {
            $orderData = collection($this->orderModel->where('admin_id',$params['ids'])->select())->toArray();
            $visitData = collection($this->visitModel->where('admin_id',$params['ids'])->select())->toArray();
            //获取订单每日的查询时间戳
            $newData = [];
            foreach ($newSelectData as $key => $value) {
                //获取每日数据的集合，再进行数据处理
                $newData[] = $this->doDataGroupByTime($key,$params['ids'],$userInfo['pid'],$userInfo['team_id'],$orderData,$visitData,$value[0],$value[1]);
            }
            Cache::set('history-data-for-team-'.$params['ids'],$newData);
        } else {
            $newData = Cache::get('history-data-for-team-'.$params['ids']);
        }


        $string = "<table class='table text-center'><tr>";
        if ($userInfo['id'] == 1) {
            $string .= "<th>团队名称</th>";
        }
        $string .= "<th>日期</th><th>组长</th><th>业务员</th><th>访问量</th><th>订单量</th><th>订单商品量</th><th>支付订单量</th><th>支付商品量</th></tr>";
        foreach ($newData as $item) {
            $string .= "<tr>";
            if ($userInfo['id'] == 1 && $userInfo['team_id'] != 0) {
                $string .= "<td>".isset($teamData[$item['team_id']]) ? $teamData[$item['team_id']] : ''."</td>";
            }
            $string .= "<td>{$item['date']}</td>";
            $string .= "<td>".(isset($adminName[$item['pid']]) ? $adminName[$item['pid']] : '')."</td>";
            $string .= "<td>".(isset($adminName[$item['admin_id']]) ? $adminName[$item['admin_id']] : '')."</td>";
            $string .= "<td>{$item['visit_nums']}</td>";
            $string .= "<td>{$item['order_count']}</td>";
            $string .= "<td>{$item['order_nums']}</td>";
            $string .= "<td>{$item['pay_done']}</td>";
            $string .= "<td>{$item['pay_done_nums']}</td>";
            $string .= "</tr>";
        }
        $string .= "</table>";
        return $string;
    }

    /**
     * ajax查询个人历史数据
     */
    public function searchPersonHistory0()
    {
        $params = $this->request->param();
        $userInfo = $this->adminModel->get($params['ids']);
        $teamData = $this->teamModel->column('name','id');
        $adminName = $this->adminModel->column('nickname','id');
        //查询数据
        $data = collection($this->dataSummaryModel->where('admin_id',$params['ids'])->order('date','desc')->select())->toArray();
        $string = "<table class='table text-center'><tr>";
        if ($userInfo['id'] == 1) {
            $string .= "<th>团队名称</th>";
        }
        $string .= "<th>日期</th><th>组长</th><th>业务员</th><th>访问量</th><th>订单量</th><th>订单商品量</th><th>支付订单量</th><th>支付商品量</th></tr>";
        foreach ($data as $item) {
            $string .= "<tr>";
            if ($userInfo['id'] == 1) {
                $string .= "<td>".isset($teamData[$item['team_id']]) ? $teamData[$item['team_id']] : ''."</td>";
            }
            $string .= "<td>{$item['date']}</td>";
            $string .= "<td>".(isset($adminName[$item['pid']]) ? $adminName[$item['pid']] : '')."</td>";
            $string .= "<td>".(isset($adminName[$item['admin_id']]) ? $adminName[$item['admin_id']] : '')."</td>";
            $string .= "<td>{$item['visit_nums']}</td>";
            $string .= "<td>{$item['order_count']}</td>";
            $string .= "<td>{$item['order_nums']}</td>";
            $string .= "<td>{$item['pay_done']}</td>";
            $string .= "<td>{$item['pay_done_nums']}</td>";
            $string .= "</tr>";
        }
        $string .= "</table>";
        return $string;
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
