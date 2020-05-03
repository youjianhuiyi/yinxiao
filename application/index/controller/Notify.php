<?php
namespace app\index\controller;

use app\admin\model\order\Order as OrderModel;
use app\common\controller\Frontend;
use think\Cache;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\model\production\Url as UrlModel;

/**
 * 回调处理类
 * Class Notify
 * @package app\index\controller
 */
class Notify extends Frontend
{
    protected $orderModel = null;
    protected $urlModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
        $this->urlModel = new UrlModel();

    }

    /**
     * 类的测试方法
     */
    public function test()
    {

        $this->orderModel->where('sn','P2020043009492000036000065994')->update(['summary_status'=>1]);
//        $saveData  = [
//            'id'             => 239,
//            'transaction_id' => '9115874769620050454027534',/*微信支付订单号*/
//            'pay_type'       => 1,/*支付类型，0=微信，1=享钱*/
//            'pay_status'     => 1,/*支付状态，已经完成支付*/
//            'pay_id'         => 1,/*使用的支付id，支付链接在产生支付的时候进行写入*/
//            'xdd_trade_no'   => '4200000523202004212089143693',/*使用的支付id，支付链接在产生支付的时候进行写入*/
//        ];
//        $this->orderModel->isUpdate(true)->save($saveData);
    }

    /**
     * 微信原生支付回调
     */
    public function WeChatNotify()
    {
        $result = $this->xml2arr(file_get_contents('php://input'));
        //通过回调的信息反查订单相关信息
        $orderInfo = $this->orderModel->where(['sn'=>$result['out_trade_no']])->find()->toArray();
        //根据订单数据提取支付信息
        $payInfo = Cache::get($orderInfo['order_ip'].'-pay_config');
        // 创建接口实例
//        [appid]=>wx90588380da4a2bb0
//        [bank_type]=>OTHERS
//        [cash_fee]=>1
//        [fee_type]=>CNY
//        [is_subscribe]=>Y
//        [mch_id]=>1583492131
//        [nonce_str]=>nrdclxldx8r05pi555dw7o51gqa0vxgr
//        [openid]=>of5TOwBkJC0jSnth-D20xiL1W_i4
//        [out_trade_no]=>2020040600445700003000026634
//        [result_code]=>SUCCESS
//        [return_code]=>SUCCESS
//        [sign]=>A654BED136BDA766BB930D9C4ED124CF
//        [time_end]=>20200406004505
//        [total_fee]=>1
//        [trade_type]=>JSAPI
//        [transaction_id]=>4200000495202004060198644235
        // 先回调验签
        $newSign = $this->paySignParams($result,$payInfo['mch_key']);
        if ($result['sign'] === $newSign) {
            //表示验签成功
            $data  = [
                'id'             => $orderInfo['id'],
                'transaction_id' => $result['transaction_id'],/*微信支付订单号*/
                'nonce_str'      => $result['nonce_str'],
                'pay_type'       => 0,/*支付类型，0=微信，1=享钱*/
                'pay_status'     => 1,/*支付状态，已经完成支付*/
                'pay_id'         => $payInfo['id'],/*使用的支付id，支付链接在产生支付的时候进行写入*/
            ];
            //更新数据
            Db::startTrans();
            try {
                (new OrderModel())->isUpdate(true)->save($data);
                //增加订单完成数量
                $this->urlModel->where('admin_id',$orderInfo['admin_id'])->setInc('order_done');
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
            //返回成功
            $str = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            echo $str;
            return ;
        } else {
            //返回失败
            $str = '<xml><return_code><![CDATA[fail]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>';
            echo $str;
            return ;
        }

    }

    /**
     * 享钱支付回调，
     * @comment 由于业务原因。暂时没有接通付款通道到回调环节
     */
    public function xpayNotify()
    {
        $returnData = urldecode(file_get_contents('php://input'));
        $data = $this->do403Params($returnData);
        //通过回调的信息反查订单相关信息
        //通过临时订单查找真实订单号，
        $orderInfo = $this->orderModel->where('sn',$data['orderNo'])->find();
        $this->orderModel->where('sn',$data['orderNo'])->update(['notify_data'=>$returnData]);
        //根据订单数据提取支付信息
        $checkCode = $this->urlModel->where(['admin_id'=>$orderInfo['admin_id'],'team_id'=>$orderInfo['team_id'],'production_id'=>$orderInfo['production_id']])->find()['check_code'];
        $payInfo = Cache::get($orderInfo['order_ip'].'-'.$checkCode.'-xpay_config');
        // 先回调验签
        $newSign = $this->XpaySignParams($data,$payInfo['mch_key']);

        if ($data['sign'] === $newSign) {
            //表示验签成功
            $saveData  = [
                'id'             => $orderInfo['id'],
                'transaction_id' => $data['trade_no'],/*微信支付订单号*/
                'pay_type'       => 1,/*支付类型，0=微信，1=享钱*/
                'pay_status'     => 1,/*支付状态，已经完成支付*/
                'pay_id'         => $payInfo['id'],/*使用的支付id，支付链接在产生支付的时候进行写入*/
                'xdd_trade_no'   => $data['xdd_trade_no'],/*使用的支付id，支付链接在产生支付的时候进行写入*/
            ];
            //更新数据
            Db::startTrans();
            try {
                $this->orderModel->isUpdate(true)->save($saveData);
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

            //数据统计，防止重复回调造成的数据不正确的问题
            if (!Cache::has('xpay-notify-'.$checkCode.'-'.$orderInfo['sn'])) {
                //进行判断，如果订单只要有回调数据，就更新一次，
                $newOrderInfo = $this->orderModel->where('sn',$data['orderNo'])->find();
                if ($newOrderInfo['summary_status'] == 0) {
                    //增加订单完成次数
                    $this->urlModel->where('admin_id',$orderInfo['admin_id'])->setInc('order_done');
                    //数据统计
                    $this->doDataSummary($checkCode,['type'=>'pay_done','nums'=>1]);
                    $this->doDataSummary($checkCode,['type'=>'pay_nums','nums'=>$orderInfo['num']]);
                    //支付商户统计
                    $this->doPaySummary($payInfo['id'],1,['type'=>'money','nums'=>$orderInfo['price']]);
                    $this->doPaySummary($payInfo['id'],1,['type'=>'pay_nums','nums'=>1]);
                    //发送短信提醒
                    $orderInfo['content'] = '【花花运动旗舰店】亲！您订购的运动跑鞋已下单成功，明天统一发货，3-7天到货，请保持手机畅通，售后电话0771-5600499';
                    $this->sendSMS($orderInfo);
                    //因为回调最长时间一天
                    Cache::set('xpay-notify-'.$checkCode.'-'.$orderInfo['sn'],'ok',86400);
                    $this->orderModel->where('sn',$data['orderNo'])->update(['summary_status'=>1]);
                }
            }
            //返回成功
            $str = 'SUCCESS';
            echo $str;
            return ;
        } else {
            //返回失败
            $str = 'FAIL';
            echo $str;
            return ;
        }

    }

    /**
     * 享钱支付手动补单
     * @comment 防止别人扫漏洞方法，使用加密参数进行访问
     */
    public function xpayHand()
    {
        $params = $this->request->param();
        if ($params['code'] == md5('dehub.com.cn')) {
            $orderInfo = $this->orderModel->where('notify_data','neq','')->where('transaction_id','')->where('xdd_trade_no','')->select();

            if (count($orderInfo) > 0) {
                foreach ($orderInfo as $key => $value) {
                    //循环查询 数据并写入
                    $saveData  = [
                        'transaction_id' => $this->do403Params($value['notify_data'])['trade_no'],/*微信支付订单号*/
                        'pay_type'       => 1,/*支付类型，0=微信，1=享钱*/
                        'pay_status'     => 1,/*支付状态，已经完成支付*/
                        'pay_id'         => $value['pay_id'],/*使用的支付id，支付链接在产生支付的时候进行写入*/
                        'xdd_trade_no'   => $this->do403Params($value['notify_data'])['xdd_trade_no'],/*使用的支付id，支付链接在产生支付的时候进行写入*/
                    ];
                    //更新数据
                    $res = $this->orderModel->where(['id'=>$value['id']])->update($saveData);
                    //增加订单完成次数
                    $this->urlModel->where('admin_id',$value['admin_id'])->setInc('order_done');
                    //数据统计
                    if ($orderInfo['check_code']) {
                        $where = ['production_id'=>$value['production_id'],'admin_id'=>$value['admin_id']];
                        $checkCode = $this->urlModel->where($where)->find()['check_code'];
                    } else {
                        $checkCode = $orderInfo['check_code'];
                    }
                    $this->doDataSummary($checkCode,['type'=>'pay_done','nums'=>1]);
                    $this->doDataSummary($checkCode,['type'=>'pay_nums','nums'=>$value['num']]);
                    //支付商户统计
                    $this->doPaySummary($value['pay_id'],1,['type'=>'money','nums'=>$value['price']]);
                    $this->doPaySummary($value['pay_id'],1,['type'=>'pay_nums','nums'=>1]);
                }

                echo "<script>alert('手动补单成功');</script>";
            } else {
                echo "<script>alert('没有需要手动被的数据，请与支付平台联系。');</script>";
                die;

            }
        }



    }

    /**
     * 如意付支付回调，
     * @comment 由于业务原因。暂时没有接通付款通道到回调环节
     * @comment income=465&payOrderId=P01202004182232415270068&amount=500&mchId=20000010&productId=8002
     * mchOrderNo=2020041822323900019000069338&paySuccTime=1587220535000&sign=F042F1EE3AC470108DAB806A8A2FED18
     * channelOrderNo=P01202004182232415270068&backType=2&param1=&param2=&appId=8ccfec053ab045288f369aeba0aa0fd4
     * status=2
     */
    public function rypayNotify()
    {
        $returnData = file_get_contents('php://input');
        $notifyArr = $this->do403Params($returnData);
        //通过订单号查找
        $orderInfo = Cache::get($notifyArr['mchOrderNo']);
        //根据订单数据提取支付信息
        $payInfo = Cache::get($orderInfo['order_ip'].'-rypay_config');
        // 先回调验签
        $newSign = $this->RyPaySignParams($notifyArr,$payInfo['mch_key']);
        Cache::set('newSign',$newSign);
        if ($notifyArr['sign'] === $newSign) {
            //表示验签成功
            $data  = [
                'id'             => $orderInfo['id'],
                'ry_order_no'    => $notifyArr['payOrderId'],/*微信支付订单号*/
                'transaction_id' => $notifyArr['channelOrderNo'],/*微信支付订单号*/
                'sign'           => $notifyArr['sign'],
                'pay_type'       => 2,/*支付类型，0=微信，1=享钱，2=如意付*/
                'pay_status'     => 1,/*支付状态，已经完成支付*/
                'pay_id'         => $payInfo['id'],/*使用的支付id，支付链接在产生支付的时候进行写入*/
            ];
            //更新数据
            Db::startTrans();
            try {
                $this->orderModel->isUpdate(true)->save($data);
                //增加订单下单完成次数
                $this->urlModel->where('admin_id',$orderInfo['admin_id'])->setInc('order_done');
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
            //返回成功
            return 'success';
        } else {
            //返回失败
            return 'fail';
        }

    }
}