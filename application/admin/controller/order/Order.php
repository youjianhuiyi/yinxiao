<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use app\admin\model\Admin as AdminModel;
use OSS\Core\OssException;
use OSS\OssClient;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Db;
use app\admin\model\sysconfig\Ossconfig as OssConfigModel;

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
    protected $adminModel = null;
    protected $ossModel = null;
    protected $noNeedLogin = ['excelExport'];
    protected $noNeedRight = ['excelExport'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\order\Order;
        $this->adminModel = new AdminModel();
        $this->ossModel = new OssConfigModel();
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


    /**
     * excel表格导出
     * @param array $headArr 表头名称
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author mrLv
     */
    public function excelExport($headArr = [])
    {
        $fileName = date("Ymdhis", time());
        $arr =  Db::query("show COLUMNS FROM yin_order");
        foreach ($arr as $item) {
            $headArr[] = $item['Field'];
        }
        //导出的数据是以当前开始的七天前所有的数据，然后再将7天前的所有数据清空
        $dateTime = $this->getSevenTime();
        if ($this->adminInfo['id'] == 1) {
            //平台级备份
            $data = Db::name('order')->where('createtime','<',$dateTime[0])->select();
        } else {
            //老板级备份
            $data = Db::name('order')->where('team_id',$this->adminInfo['team_id'])->where('createtime','<',$dateTime[0])->select();
        }
        //如果没有数据表示数据已经被清除，并且备份已经上传
        if (count($data) == 0) {
            //表示没有7 天前的数据了，或者已经上传完成了。
            return json_encode(['msg'=>'你的数据已经上传成功']);
        }
        $spreadsheet    = new Spreadsheet();
        $objPHPExcel    = $spreadsheet->getActiveSheet();
        $key            = ord("A"); // 设置表头
        $key2           = ord("@");
        foreach ($headArr as $v) {
            if($key > ord("Z")){
                $key2  += 1;
                $key    = ord("A");
                $colum = chr($key2).chr($key);
            }else{
                if($key2>=ord("A")){
                    $colum = chr($key2).chr($key);
                }else{
                    $colum = chr($key);
                }
            }
            $objPHPExcel->setCellValue($colum . '1', $v);
            $key += 1;
        }
        $column = 2;
        foreach ($data as $key => $rows) { // 行写入
            $span   = ord("A");
            $span2  = ord("@");
            foreach ($rows as $keyName => $value) { // 列写入
                if($span > ord("Z")){
                    $span2 += 1;
                    $span   = ord("A");
                    $j      = chr($span2).chr($span);
                }else{
                    if($span2>=ord("A")){
                        $j = chr($span2).chr($span);
                    }else{
                        $j = chr($span);
                    }
                }
                $objPHPExcel->setCellValue($j . $column, $value);
                $span++;
            }
            $column++;
        }
//        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
//        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
//        $writer->save('php://output');
        if (!is_dir(ROOT_PATH.'public/uploads/backup')) {
            mkdir(ROOT_PATH.'public/uploads/backup');
        }
        $writer->save(ROOT_PATH.'public/uploads/backup/'.$fileName . '.xlsx');
        //写入数据操作准备调用上传
        $this->upToOss(ROOT_PATH.'public/uploads/backup/'.$fileName . '.xlsx');
        $data = [

        ];
        //删除清空：
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
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
     * 上传到oss空间
     * @param $fileName string 绝对路径带文件名
     */
    protected function upToOss($fileName)
    {
        //查询当前团队有没有专用OSS。
        $ossData = $this->ossModel->where('team_id',$this->adminInfo['team_id'])->find();
        if (!$ossData) {
            //表示不存在团队专用OSS
            $ossData = $this->ossModel->where('access_key_id','LTAI4G8Z9Ng91NPnu4sNMnmw')->find();
        }
        // 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录RAM控制台创建RAM账号。
        $accessKeyId = $ossData['access_key_id'];
        $accessKeySecret = $ossData['access_key_secret'];
        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = $ossData['endpoint'];
        // 设置存储空间名称。
        $bucket = $ossData['bucket'];
        // 设置文件名称。
        $tid = $this->adminInfo['team_id'] ? $this->adminInfo['team_id'] : 0;
        $object = 'tid-'.$tid.'-'.date('YmdHis',time()).'.xlsx';
        // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
        $filePath = $fileName;

        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->uploadFile($bucket, $object, $filePath);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        //表示上传成功

        print(__FUNCTION__ . ": OK" . "\n");
    }
}
