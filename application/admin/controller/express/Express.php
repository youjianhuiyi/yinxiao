<?php

namespace app\admin\controller\express;

use app\admin\library\Auth;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\express\Sms as SmsModel;
use app\admin\model\sysconfig\Smsconfig as SmsConfigModel;
use app\common\controller\Backend;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use think\Cache;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 快递信息管理
 *
 * @icon fa fa-space-shuttle
 */
class Express extends Backend
{
    
    /**
     * Express模型对象
     * @var \app\admin\model\express\Express
     */
    protected $model = null;
    protected $smsModel = null;
    protected $orderModel = null;
    protected $smsConfigModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\express\Express;
        $this->smsModel = new SmsModel();
        $this->orderModel = new OrderModel();
        $this->smsConfigModel = new SmsConfigModel();
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
            $total = $this->model
                ->where($where)
                ->where(['team_id'=>$this->adminInfo['team_id']])
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where(['team_id'=>$this->adminInfo['team_id']])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 回收站
     */
    public function recyclebin()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->onlyTrashed()
                ->where($where)
                ->where(['team_id'=>$this->adminInfo['team_id']])
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->onlyTrashed()
                ->where($where)
                ->where(['team_id'=>$this->adminInfo['team_id']])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['team_id'] = $this->adminInfo['team_id'];
            $params['team_name'] = $this->adminInfo['team_name'];
            $params['order_id'] = $this->orderModel->where(['sn'=>$params['order_sn']])->find()->id;
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['team_id'] = $this->adminInfo['team_id'];
            $params['team_name'] = $this->adminInfo['team_name'];
            $params['order_id'] = $this->orderModel->where(['sn'=>$params['order_sn']])->find()->id;
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    /**
     * 导入
     */
    public function import()
    {
        $file = $this->request->request('file');
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
        //实例化reader
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $this->error(__('Unknown data format'));
        }
        if ($ext === 'csv') {
            $file = fopen($filePath, 'r');
            $filePath = tempnam(sys_get_temp_dir(), 'import_csv');
            $fp = fopen($filePath, "w");
            $n = 0;
            while ($line = fgets($file)) {
                $line = rtrim($line, "\n\r\0");
                $encoding = mb_detect_encoding($line, ['utf-8', 'gbk', 'latin1', 'big5']);
                if ($encoding != 'utf-8') {
                    $line = mb_convert_encoding($line, 'utf-8', $encoding);
                }
                if ($n == 0 || preg_match('/^".*"$/', $line)) {
                    fwrite($fp, $line . "\n");
                } else {
                    fwrite($fp, '"' . str_replace(['"', ','], ['""', '","'], $line) . "\"\n");
                }
                $n++;
            }
            fclose($file) || fclose($fp);

            $reader = new Csv();
        } elseif ($ext === 'xls') {
            $reader = new Xls();
        } else {
            $reader = new Xlsx();
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
        foreach ($list as $k => $v) {
            if ($importHeadType == 'comment') {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            }else {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        //加载文件
        $insert = [];
        try {
            if (!$PHPExcel = $reader->load($filePath)) {
                $this->error(__('Unknown data format'));
            }
            $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
            $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
            $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
            $maxColumnNumber = Coordinate::columnIndexFromString($allColumn);
            $fields = [];
            for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $fields[] = $val;
                }
            }

            for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                $values = [];
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $values[] = is_null($val) ? '' : $val;
                }
                $row = [];
                $temp = array_combine($fields, $values);
                foreach ($temp as $k => $v) {
                    if (isset($fieldArr[$k]) && $k !== '') {
                        $row[$fieldArr[$k]] = $v;
                    }
                }
                if ($row) {
                    $insert[] = $row;
                }
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
        if (!$insert) {
            $this->error(__('No rows were updated'));
        }

        try {
            //是否包含admin_id字段
            $has_admin_id = false;
            foreach ($fieldArr as $name => $key) {
                if ($key == 'admin_id') {
                    $has_admin_id = true;
                    break;
                }
            }
            if ($has_admin_id) {
                $auth = Auth::instance();
                foreach ($insert as &$val) {
                    if (!isset($val['admin_id']) || empty($val['admin_id'])) {
                        $val['admin_id'] = $auth->isLogin() ? $auth->id : 0;
                    }
                }
            }

            foreach ($insert as $key => &$value) {
                if ($value['order_sn'] == '') {
                    unset($insert[$key]);
                } else {
                    $value['team_id'] = $this->adminInfo['team_id'];
                    $value['team_name'] = $this->adminInfo['team_name'];
                    $value['express_no'] = (string)$value['express_no'];
                    $value['order_id'] = $this->orderModel->where(['sn'=>$value['order_sn']])->find()['id'] ? : 0;
                }

            }
//            dump($insert);die;
            $this->setExpressToOrder($insert);
            $this->model->saveAll($insert);
        } catch (PDOException $exception) {
            $msg = $exception->getMessage();
            if (preg_match("/.+Integrity constraint violation: 1062 Duplicate entry '(.+)' for key '(.+)'/is", $msg, $matches)) {
                $msg = "导入失败，包含【{$matches[1]}】的记录已存在";
            };
            $this->error($msg);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success();
    }


    /**
     * 联动写入订单信息到订单表
     * @param $data
     */
    private function setExpressToOrder($data)
    {
        $newArr = [];
        foreach ($data as $value) {
            $newArr[]= [
                'id'            =>  $value['order_id'],
                'express_no'    =>  $value['express_no'],
                'express_com'   =>  $value['express_com'],
                'order_status'  =>  1
            ];
//            $orderInfo = $this->orderModel->get($value['order_id'])->toArray();
//            $orderInfo['content'] = '【花花运动旗舰店】尊敬的客户'.$orderInfo['name'].'，您购买的商品已发货，快递单号：'.$value['express_no'].',快递公司：'.$value['express_com'];
//            $this->sendSms($orderInfo);
        }

        $this->orderModel->isUpdate(true)->saveAll($newArr);
    }


    /**
     * 发送短信
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendSMS()
    {
        $smsData = $this->smsConfigModel->where(['team_id'=>$this->adminInfo['team_id'],'status'=>0])->find();
        //查找当前导入的数据里面有没有发送短信成功
        $expressData = collection($this->model->where('is_send',0)->select())->toArray();
        //进行发送短信
        $newData = $newExpressData =  [];
        foreach ($expressData as $value) {
            $orderInfo = $this->orderModel->get($value['order_id']);
            //拼接短信内容模板
            $content = '';
            $template = explode('${code}',$smsData['template_2']);
            if (count($template) == 4) {
                //表示有3个参数需要填写
                $content = $template[0].$orderInfo['name'].$template[1].$value['express_no'].$template[2].$value['express_com'];
            } elseif (count($template)== 3) {
                //表示有2个参数需要填写
                $content = $template[0].$template[1].$orderInfo['express_no'].$template[2].$value['express_com'];
            }
            // 构建发送短信内容
            $data ='account='.$smsData['username'].'&password='.$smsData['password'].'&mobiles='.$value['phone'].'&content='.urlencode($content);
            //发送请求
            $result = $this->curlPostForm($data,$smsData['send_url']);
            Cache::set('send-sms',$result,1800);
            $res = json_decode($result,true);
            if ($res['resCode'] == '0000') {
                //表示发送成功
                $newData[] = [
                    'order_id'  => $value['order_id'],
                    'team_id'   => $value['team_id'],
                    'admin_id'  => $orderInfo['admin_id'],
                    'phone'     => $value['phone'],
                    'status'    => 1,
                    'msg'       => $content,
                    'return_data'=>$result
                ];
                //构建发货通通知
                $newExpressData[] = [
                    'id'    =>  $value['id'],
                    'is_send'   => 1
                ];
            } else {
                //表示发送失败
                $newData[] = [
                    'order_id'  => $value['order_id'],
                    'team_id'   => $value['team_id'],
                    'admin_id'  => $orderInfo['admin_id'],
                    'phone'     => $value['phone'],
                    'status'    => 0,
                    'msg'       => $content,
                    'return_data'=>$result
                ];
            }
        }
        if ($newExpressData) {
            $this->model->isUpdate(true)->saveAll($newExpressData);
        }
        $this->smsModel->isUpdate(false)->saveAll($newData);
        $this->success('已经全部发送成功！');
    }
    

    /**
     * 下载导入模板
     */
    public function template()
    {
        $filename = 'express_import.zip'; //获取文件名称
        $down_host = $this->request->domain().DS; //当前域名
        //判断如果文件存在,则跳转到下载路径
        if(file_exists(ROOT_PATH.'public/'.$filename)){
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Accept-Length:".filesize(ROOT_PATH.'public/'.$filename));
            header("Content-Disposition: attachment; filename=".$filename);
            readfile($down_host.$filename);
        }else{
            header('HTTP/1.1 404 Not Found');
        }
    }
}
