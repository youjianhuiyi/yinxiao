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
     * 获取7天前的时间戳
     * @return float[]|int[]
     */
    protected function getSevenTime()
    {
        $ytime = strtotime(date("Y-m-d", strtotime("-7 day")));//昨天开始时间戳
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
        if ($data) {
            foreach ($data as $value) {
                $tmp['order_count']         += 1;
                $tmp['order_nums']          += (int)$value['num'];
                if ($value['pay_status'] == 1) {
                    $tmp['pay_done']        += 1;
                    $tmp['pay_done_nums']   += (int)$value['num'];
                    $tmp['pay_total']       += (float)$value['price'];
                }
            }
        }

        return $tmp;
    }

    /**
     * 获取当前平台所有用户ID
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
            if ($teamId != 0) {
                //表示查询指定团队
                $where = ['team_id' => $teamId];
            } else {
                //表示查询所有团队
                $where = 1;
            }
        } elseif ($level == 1) {
            $where = ['team_id' => $teamId];
        } elseif ($level == 2) {
            $where = ['admin_id'=> ['in',$adminIds]];
        } else {
            $where = ['admin_id'=> $adminId];
        }
        //访问记录
        $visitSummary = $this->visitModel->whereTime('createtime','between',[$dateTime[0],$dateTime[1]])->where($where)->select();
        //获取订单数据
        $orderData = $this->orderModel->whereTime('createtime','between',[$dateTime[0],$dateTime[1]])->where($where)->select();
        $visitSummary = collection($visitSummary)->toArray();
        $orderData = collection($orderData)->toArray();
        //获取当前平台所有用户ID
        $platUserIds = $this->shellGetAllUser();
        //整理数据访问记录数据
        $newVisitSummary = [];
        //表示是平台管理员查看所有人员
        foreach ($visitSummary as $value) {
            $newVisitSummary[$value['admin_id']][] = $value;
        }
        //统计访问数据
        $visitSummaryKeys = array_keys($newVisitSummary);
        //处理订单数据
        $newOrderData = [];
        foreach ($orderData as $orderDatum) {
            $newOrderData[$orderDatum['admin_id']][] = $orderDatum;
        }
        $orderDataKeys = array_keys($newOrderData);

        foreach ($platUserIds as $platUserId) {
            //判断当前有数据的浏览记录是否存在
            if (!in_array($platUserId,$visitSummaryKeys)) {
                $newVisitSummary[$platUserId] = 0;
            } else {
                $newVisitSummary[$platUserId] = count($newVisitSummary[$platUserId]);
            }
            //订单数据补全
            if (!in_array($platUserId,$orderDataKeys)) {
                $newOrderData[$platUserId] = '';
            }
        }
        //订单数据统计，订单数量，订单支付，订单支付成功量
        $newResOrderData = [];
        foreach ($newOrderData as $key => $item) {
            if ($item) {
                //表示item不为空
                $countData = $this->countNums($item);
            } else {
                //表示对应用户没有订单数据
                $countData = [
                    'visit_nums'    => 0,
                    'order_count'   => 0,
                    'order_nums'    => 0,
                    'pay_done'      => 0,
                    'pay_done_nums' => 0,
                    'pay_total'     => 0.00
                ];
            }

            $pid = $this->adminModel->get($key)['pid'];
            $tid =  $this->adminModel->get($key)['team_id'];
            $newResOrderData[]  = [
                'team_id'       => $tid,
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
        return $newResOrderData;
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
        $teamData[0] = '未知团队';/*兼容*/
        $adminName = $this->adminModel->column('nickname','id');
        $row = [];
        //构建下拉列表的日期数据
        $selectData = array_unique(collection($this->dataSummaryModel->field('date')->order('date','desc')->column('date'))->toArray());
        $newSelectData = [];
        foreach ($selectData as $item) {
            $newSelectData[$item] = $item;
        }
        if ($this->adminInfo['id'] == 1) {
            //构建组长列表数据。
            $zzData = $this->adminModel->where(['level' => 1])->column('nickname','id');
            //构建员工数据
            $ygData = $this->adminModel->column('nickname','id');
            //构建团队数据
            $tdData = $this->teamModel->column('name','id');
            $tdData[0] = '请选择';
        } else {
            //构建组长列表数据。
            $zzData = $this->adminModel->where(['team_id'=>$this->adminInfo['team_id'],'level' => 1])->column('nickname','id');
            //构建员工数据
            $ygData = $this->adminModel->where(['team_id'=>$this->adminInfo['team_id']])->column('nickname','id');
            $tdData[0] = $this->adminInfo['team_id'];
        }
        $zzData[0] = '请选择';
        $ygData[0] = '请选择';
        $newArr = [];
        if ($this->request->isPost()) {
            $params = $this->request->param();
            //获取当前用户信息
            $date = isset($params['row']['select']) ? $params['row']['select'] : date('m-d',time());
            $zz = isset($params['row']['zz']) ? $params['row']['zz'] : 0;
            $yg = isset($params['row']['yg']) ? $params['row']['yg'] : 0;
            $td = isset($params['row']['td']) ? $params['row']['td'] : 0;
            $zzIds = $this->getLowerUser($zz);
            //将05-05字符串转换为当前的时间戳
            $dateTime = $this->strToTimestamp('2020-'.$date);
            $row['select'] = $date;/*回显数据使用*/
            if ($zz == 0 && $yg == 0) {
                //表示当前只针对日期进行查询
                if ($userInfo['id'] == 1) {
                    $data = $this->doSummary($dateTime,0,$td);
                    $newArr = $data;
                } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
                    //老板查看团队所有人员的数据
                    $data = $this->doSummary($dateTime,1,$this->adminInfo['team_id']);
                    //老板查看团队所有人员的数据
                    foreach ($data as $datum) {
                        if ($datum['team_id'] == $userInfo['team_id']) {
                            $newArr[] = $datum;
                        }
                    }
                } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
                    //组长查看自己及以下员工的数据
                    $lowerUserIds = $this->getLowerUser($userInfo['id']);
                    $data = $this->doSummary($dateTime,2,0,$lowerUserIds);
                    foreach ($data as $datum) {
                        if (in_array($datum['admin_id'],$lowerUserIds)) {
                            $newArr[] = $datum;
                        }
                    }
                } else {
                    //业务员只能查看自己的订单数据
                    $data = $this->doSummary($dateTime,3,0,[],$this->adminInfo['id']);
                    foreach ($data as $datum) {
                        if ($userInfo['id'] == $datum['admin_id']) {
                            $newArr[] = $datum;
                        }
                    }
                }
            } elseif ($zz != 0 && $yg == 0) {
                //表示当前查询条件为日期和组
                $data = $this->doSummary($dateTime,2,0,$zzIds);
                foreach ($data as $datum) {
                    if (in_array($datum['admin_id'],$zzIds)) {
                        $newArr[] = $datum;
                    }
                }
            } elseif ($zz == 0 && $yg != 0) {
                //表示只查询某个业务员的报表
                $data = $this->doSummary($dateTime,3,0,[],$yg);
                foreach ($data as $item) {
                    if (isset($item['admin_id']) && $item['admin_id'] == $yg) {
                        $newArr[] = $item;
                    }
                }
            } else {
                //表示即查某个业务员又查对应组的报表
                $data1 = $this->doSummary($dateTime,2,0,$zzIds);
                $data2 = $this->doSummary($dateTime,3,0,[],$yg);
                foreach ($data2 as $item) {
                    if (isset($item['admin_id']) && $item['admin_id'] == $yg) {
                        $data2 = $item;
                    }
                }
                //业务员只能查看自己的订单数据
                foreach ($data1 as $datum) {
                    if (in_array($datum['admin_id'],$zzIds)) {
                        $newArr[] = $datum;
                    }
                }
                array_push($newArr,$data2);
            }
            //构建回显数据
            $row['zz'] = $zz;
            $row['yg'] = $yg;

        } else {
            $date = date('m-d',time());
            $dateTime = $this->getBeginEndTime();
            if ($userInfo['id'] == 1) {
                //表示是平台总管理员，可以查看所有记录
                $data = $this->doSummary($dateTime,0);
                $newArr = $data;
            } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
                //老板查看团队所有人员的数据
                $data = $this->doSummary($dateTime,1,$userInfo['team_id']);
                //老板查看团队所有人员的数据
                foreach ($data as $datum) {
                    if ($datum['team_id'] == $userInfo['team_id']) {
                        $newArr[] = $datum;
                    }
                }
            } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
                //组长查看自己及以下员工的数据
                $lowerUserIds = $this->getLowerUser($userInfo['id']);
                $data = $this->doSummary($dateTime,2,0,$lowerUserIds);
                foreach ($data as $datum) {
                    if (in_array($datum['admin_id'],$lowerUserIds)) {
                        $newArr[] = $datum;
                    }
                }
            } else {
                //业务员只能查看自己的订单数据
                $data = $this->doSummary($dateTime,3,0,[],$userInfo['id']);
                foreach ($data as $datum) {
                    if ($userInfo['id'] == $datum['admin_id']) {
                        $newArr[] = $datum;
                    }
                }
            }
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
        //当前汇总数据
        foreach ($newArr as $item) {
            $todayTotal['visit_nums'] += isset($item['visit_nums']) ? $item['visit_nums'] : 0;
            $todayTotal['order_count'] += isset($item['order_count']) ? $item['order_count'] : 0;
            $todayTotal['order_nums'] += isset($item['order_nums']) ? $item['order_nums'] : 0;
            $todayTotal['pay_done'] += isset($item['pay_done']) ? $item['pay_done'] : 0;
            $todayTotal['pay_done_nums'] += isset($item['pay_done_nums']) ? $item['pay_done_nums'] : 0;
            $todayTotal['pay_total'] += isset($item['pay_total']) ? $item['pay_total'] : 0;
        }

        $this->assign('user',$this->adminInfo);/*当前用户信息*/
        $this->assign('teamData',$teamData);/*团队数据*/
        $this->assign('adminName',$adminName);/*业务员ID=>名称数据*/
        $this->assign('today_total',$todayTotal);/*当天数据汇总*/
        $this->assign('data',$newArr);
        $this->assign('date',$date);
        $this->assign('select_data',$newSelectData);/*查询数据*/
        $this->assign('zz_data',$zzData);/*组长数据*/
        $this->assign('yg_data',$ygData);/*员工数据*/
        $this->assign('td_data',$tdData);/*团队数据*/
        $this->assign('row',$row);
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
        //先将所有数据按日期分类
        $data = [];

        if ($this->request->isPost()) {
            $params = $this->request->param();
            //获取当前用户信息
            $date = $params['row']['select'];
            if ($userInfo['id'] == 1) {
                //表示是平台总管理员，可以查看所有记录
                //获取当天时间 0点到23点59分59秒的订单数量。
                //获取当天所有用户的报表
                $where = 1;
            } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
                //老板查看团队所有人员的数据
                //获取团队下所有的用户数据
                //获取当天所有用户的报表
                $where = ['team_id',$this->adminInfo['team_id']];
            } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
                //组长查看自己及以下员工的数据
                $userIds = $this->getUserLower();
                $where = ['admin_id',['in',$userIds]];
            } else {
                //业务员只能查看自己的订单数据
                $where = ['admin_id',$this->adminInfo['id']];
            }

            $dataSummary = $this->dataSummaryModel
                ->where('date',$date)
                ->where($where)
                ->select();
            $dataSummary = collection($dataSummary)->toArray();
            foreach ($dataSummary as &$item) {
                $name = $this->adminModel->get($item['admin_id'])['nickname'];
                $item['name'] = $name;
                $data[] = $item;
            }
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
            if ($userInfo['id'] == 1) {
                //表示是平台总管理员，可以查看所有记录
                //获取当天时间 0点到23点59分59秒的订单数量。
                //获取当天所有用户的报表
                $where = 1;
            } elseif ($userInfo['pid'] == 0 && $userInfo['id'] != 1) {
                //老板查看团队所有人员的数据
                //获取团队下所有的用户数据
                //获取当天所有用户的报表
                $where = ['team_id',$this->adminInfo['team_id']];
            } elseif ($userInfo['pid'] != 0 && $userInfo['level'] != 2) {
                //组长查看自己及以下员工的数据
                $userIds = $this->getUserLower();
                $where = ['admin_id',['in',$userIds]];
            } else {
                //业务员只能查看自己的订单数据
                $where = ['admin_id',$this->adminInfo['id']];
            }

            $dataSummary = $this->dataSummaryModel
                ->where('date',$date)
                ->where($where)
                ->select();
            $dataSummary = collection($dataSummary)->toArray();
            foreach ($dataSummary as &$item) {
                $name = $this->adminModel->get($item['admin_id'])['nickname'];
                $item['name'] = $name;
                $data[] = $item;
            }

        }

        //生成当天汇总数据
        $todayTotal = [
            'visit_nums'    => 0,
            'order_count'   => 0,
            'order_nums'    => 0,
            'pay_done'      => 0,
            'pay_done_nums' => 0
        ];

        foreach ($data as $item) {
            $todayTotal['visit_nums'] += $item['visit_nums'];
            $todayTotal['order_count'] += $item['order_count'];
            $todayTotal['order_nums'] += $item['order_nums'];
            $todayTotal['pay_done'] += $item['pay_done'];
            $todayTotal['pay_done_nums'] += $item['pay_done_nums'];
        }

        $this->assign('user',$this->adminInfo);/*当前用户信息*/
        $this->assign('teamData',$teamData);/*团队数据*/
        $this->assign('adminName',$adminName);/*业务员ID=>名称数据*/
        $this->assign('today_total',$todayTotal);/*当天数据汇总*/
        $this->assign('data',$data);
        $this->assign('date',$date);
        $this->assign('select_data',$newSelectData);/*查询数据*/
        return $this->view->fetch();

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
        //只查询当前时间前7天的所有数据
        $sevenTime = $this->getSevenTime();
        //查询数据
        $selectData = array_unique(collection($this->dataSummaryModel->field('date')->order('date','desc')->where('createtime','>=',$sevenTime[0])->column('date'))->toArray());
        $newSelectData = [];
        foreach ($selectData as $item) {
            $newSelectData[$item] = $this->strToTimestamp('2020-'.$item);
        }
        if (!Cache::has('history-data-for-team-'.$params['ids'])) {
            $orderData = collection($this->orderModel->where('admin_id',$params['ids'])->where('createtime','>=',$sevenTime[0])->select())->toArray();
            $visitData = collection($this->visitModel->where('admin_id',$params['ids'])->where('createtime','>=',$sevenTime[0])->select())->toArray();
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
     * 获取用户关系
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


    /**
     * 获取用户关系。往
     * @param $id
     * @return array
     * @internal
     */
    public function getLowerUser($id)
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
        $teamList = $tree->getTreeList($tree->getTreeArray($id), 'nickname');
        $adminData = [];
        foreach ($teamList as $k => $v) {
            $adminData[] = $v['id'];
        }
        //把自己添加进去
        array_push($adminData, (int)$id);
        return $adminData;
    }

}
