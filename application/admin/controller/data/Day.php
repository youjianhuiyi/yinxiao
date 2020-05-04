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
     * @return \think\response\Json|void
     * @throws \think\Exception
     */
    public function index()
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
