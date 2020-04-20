<?php
namespace app\index\controller;

use app\admin\model\order\Order as OrderModel;
use app\common\controller\Frontend;
use think\Cache;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 回调处理类
 * Class Notify
 * @package app\index\controller
 */
class Notify extends Frontend
{
    protected $orderModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();

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
        $payInfo = Cache::get($orderInfo['order_ip'].'-xpay_config');
        // 先回调验签
        $newSign = $this->XpaySignParams($data,$payInfo['mch_key']);

        Cache::set('old-xsign',$data['sign']);
        Cache::set('new-xsign',$newSign);

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