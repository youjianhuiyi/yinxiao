<?php

namespace app\admin\controller\sysconfig;

use app\admin\model\data\PayRecord as PayRecordModel;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\model\team\Team as TeamModel;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\sysconfig\Xpay as XpayModel;
use app\admin\model\Admin as AdminModel;
use app\admin\model\sysconfig\Payset as PaySetModel;


/**
 * 享钱支付
 *
 * @icon fa fa-circle-o
 */
class Xpay extends Backend
{
    public $noNeedLogin = ['testPay','xpayGrant','url'];
    public $noNeedRight = ['testPay','xpayGrant','url'];

    /**
     * Xpay模型对象
     */
    protected $model = null;
    protected $teamModel = null;
    protected $adminModel = null;
    protected $orderModel = null;
    protected $payRecordMode = null;
    protected $paysetModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new XpayModel();
        $this->teamModel = new TeamModel();
        $this->adminModel = new AdminModel();
        $this->orderModel = new OrderModel();
        $this->payRecordMode = new PayRecordModel();
        $this->paysetModel = new PaySetModel();

        //团队数据
        if ($this->request->action() == 'add' || $this->request->action() == 'edit' || $this->request->action() == 'index' ) {
            $teamData = collection($this->teamModel->column('name','id'))->toArray();
            if ($this->adminInfo['id'] == 1 ) {
                $teamData[0] = '自动新增团队请选择我(新加老板账号),否则下拉选择(新加非老板账号)';
                ksort($teamData);
                $newTeamData = $teamData;
            } else {
                $newTeamData[$this->adminInfo['team_id']] = $teamData[$this->adminInfo['team_id']];
            }
            $this->view->assign('teamData', $newTeamData);
        }

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
            if ($this->adminInfo['id'] == 1) {
                $total = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            } else {
                $total = $this->model
                    ->where($where)
                    ->where('team_id',$this->adminInfo['team_id'])
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->where('team_id',$this->adminInfo['team_id'])
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
     * 同步更新支付管理表
     * @param $data array   更新数据
     * @param $payId    integer     支付ID
     * @return bool
     */
    protected function addPayManagement($data,$payId)
    {
        $newArr = [
            'type'              =>  1,/*表示享钱支付类型*/
            'pay_id'            =>  $payId,
            'pay_channel'       =>  $data['pay_name'],
            'team_id'           =>  $data['team_id'],
            'team_name'         =>  $this->teamModel->get($data['team_id'])['name'],
            'is_multiple'       =>  1,
            'status'            =>  1,
        ];
        $result = $this->paysetModel->isUpdate(false)->save($newArr);
        return $result ? true : false;
    }

    /**
     * 同步更新支付管理表
     * @param $data array   更新数据
     * @param $payId    integer     支付ID
     * @return bool
     */
    protected function editPayManagement($data,$payId)
    {
        if ($data['status'] == 0) {
            //表示当前是禁用操作。
            $result = $this->paysetModel->destroy(['pay_id'=>$payId,'type'=>1]);
        } else {
            $result = $this->addPayManagement($data,$payId);
        }
        return $result;
    }

    /**
     * 添加
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $teamName = $this->teamModel->where('id',$params['team_id'])->find()['name'];
                $params['team_name'] = $teamName ? $teamName :'未知团队';
                $params = $this->preExcludeFields($params);
                $params['status'] = 1;

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = $result1 = $result2 = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    //同步将商户添加到商户收款表里面
                    $newData = [
                        'date'          => date('m-d',time()),
                        'team_id'       => $params['team_id'],
                        'pay_id'        => $this->model->id,
                        'pay_type'      => 1,
                        'use_count'     => 0,
                        'pay_nums'      => 0,
                        'money'         => 0.00,
                    ];
                    $result1 = $this->payRecordMode->isUpdate(false)->save($newData);
                    //如果是新加商户，直接同步到支付管理表。并开启
                    $result2 = $this->addPayManagement($params,$this->model->id);
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
                if ($result !== false && $result1 !== false && $result2 !== false) {
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
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\DbException
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
            if ($params) {
                //如果有提交team_id，表示是编辑 操作，否则是x-editable操作。
                if (isset($params['team_id'])) {
                    $teamName = $this->teamModel->where('id',$params['team_id'])->find()['name'];
                    $params['team_name'] = $teamName ? $teamName :'未知团队';
                }
                $params = $this->preExcludeFields($params);
                $result = $result1 = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    //修改支付数据，需要同步到支付管理里面，如果是禁用修改的话，直接将支付管理里面关闭或者删除
                    $params['team_id'] = $row['team_id'];
                    $params['pay_name'] = $row['pay_name'];
                    $result1 = $this->editPayManagement($params,$ids);
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
                if ($result !== false && $result1 !== false) {
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
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();

            $count = 0;
            Db::startTrans();
            try {
                foreach ($list as $k => $v) {
                    $count += $v->delete();
                }
                //删除支付管理里面的商户号
                $this->editPayManagement(['status'=>0],$ids);
                Db::commit();
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 支付测试
     * @return bool|string
     * @throws \think\Exception
     */
    public function testPay()
    {

        $params = $this->request->param();
        $payInfo = $this->model->get($params['pay_id']);
        $teamData = $this->teamModel->get($params['tid']);
        $goodsName = '测试支付通道商品'.$payInfo['pay_name'];

        //构建订单数据
        $data = [
            'admin_id'  => $params['aid'],
            'admin_name'=> $this->adminModel->get($params['aid'])->nickname,
            'pid'       => $this->adminModel->get($params['aid'])->pid,
            'num'       => 1,
            'name'      => $goodsName,
            'phone'     => '18888888888',
            'address'   => '测试地址',
            'team_id'   => $teamData['id'],
            'team_name' => '测试订单数据',
            'production_id'     => 4,
            'production_name'   => '测试商品',
            'goods_info'=> '款式=;性别=;属性=',
            'price'     => 0.01,
            'pay_id'    => $params['pay_id'],
            'pay_type'  => 1,
            'sn'        => $params['sn'],
            'order_ip'  => $this->request->ip(),
        ];

        $this->orderModel->isUpdate(false)->save($data);

        $data = [
            'ticket' => time(),/*用来匹配请求*/
            'service' => 'pay.xiangqian.wxjspay',
            'version' => '2.0',/*版本号 默认是2.0*/
            'sign_type' => 'MD5',/*签名方式，默认是md5*/
            'mch_code' => $payInfo['mch_code'],/*商户号 享多多系统的门店编码*/
            'timestamp' => date('YmdHis', time()),/*时间戳 发送请求的时间，格式"yyyyMMddHHmmss"*/
            'sign' => '',/*签名*/
            'body' => [
                'orderNo' => $params['sn'],/*商户订单号 商户系统内部的订单号 ,32个字符内、 可包含字母,确保在商户系统唯一*/
                'order_info' => 'test',/*商品描述*/
                'total_amount' => 1,/*总金额，以分为单位，不允许包含任何字、符号*/
                'mch_create_ip' => $this->request->ip(),/*订单生成的机器 IP*/
                'notify_url' => 'http://back.dehub.com.cn/index.php/index/notify/xpayNotify',
                'sub_appid' => 'wx092575bf6bc1636d',/*wx092575bf6bc1636d*/
                'sub_openid' => $params['openid'],
            ],
        ];
        //缓存当前申请支付的临时订单与本订单之前的关系
        $newParams = $this->XpaySignParams($data, $payInfo['mch_key']);
        $data['sign'] = $newParams;
        //构建请求支付接口参数
        $urlParams = str_replace('\\', '', json_encode($data, JSON_UNESCAPED_UNICODE));
        //发起POST请求，获取订单信息
        $result = $this->curlPostJson($urlParams, 'http://openapi.xiangqianpos.com/gateway');
        //接收请求下单接口回来的数据
        $newData = json_decode($result,true);
        //计算下单接口返回过来数据的签名
        $newParams1 = $this->XpaySignParams($newData,$payInfo['mch_key']);
        //构建跳转收银台所需要的参数
        $jsonData = [
            'casher_id' => $newData['body']['casher_id'],
            'mch_code'  => $payInfo['mch_code'],
            'third_no'  => $params['sn'],
            'sign'      => ''
        ];
        //
        $cashSign = $this->XpaySignParams($jsonData,$payInfo['mch_key']);
        //构建跳转的参数
        $queryString = 'mch_code='.$payInfo['mch_code'].'&sign='.$cashSign.'&casher_id='.$newData['body']['casher_id'].'&third_no='.$params['sn'];

        // 验证下单接口的签名，如果签名没问题，返回JSON数据跳转收银台，如果有问题则不跳转
        if ($newParams1 == $newData['sign']) {
            //构建json数据
            $url = 'https://open.xiangqianpos.com/wxJsPayV3/casher'.'?'.$queryString;
            header('Location:'.$url);
        } else {
            return '';
        }
    }

    /**
     * 享钱平台获取微信openid
     * @param null $ids
     */
    public function xpayGrant($ids = null)
    {
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $payInfo = $this->model->get($ids);
        $params  = $this->request->param();
        $orderNo = mt_rand(11111,99999).time();

        $url = 'http://open.xiangqianpos.com/wxPayOauth/openid';
        $data = [
            'mch_code'  => $payInfo['mch_code'],
            'charset'   => 'UTF-8',
            'nonce_str' => md5(time()),
            'redirect'  => urlencode($this->request->domain().$this->request->baseFile().'/sysconfig/Xpay/testPay?sn='.$orderNo.'&pay_id='.$ids.'&tid='.$params['tid'].'&aid='.$params['aid']),
            'sign'      => '',
        ];
        $data['sign'] = $this->XpaySignParams($data,$payInfo['mch_key']);
        //跳转享钱平台获取openid
        $queryString = 'charset='.$data['charset'].'&mch_code='.$data['mch_code'].'&nonce_str='.$data['nonce_str'].'&redirect='.$data['redirect'].'&sign='.$data['sign'];
        header('Location:'.$url.'?'.$queryString);
    }

    /**
     * 生成二维码的入口链接
     * @param integer $ids
     * @return string
     * @throws \think\Exception
     */
    public function url($ids = null)
    {
        $url = $this->request->domain().$this->request->baseFile().'/sysconfig/Xpay/xpayGrant/ids/'.$ids.'?tid='.$this->adminInfo['team_id'].'&aid='.$this->adminInfo['id'];
        $this->assign('url',urlencode($url));
        return $this->view->fetch('url');
    }

}
