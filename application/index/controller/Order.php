<?php
namespace app\index\Controller;

use app\admin\model\order\Order as OrderModel;
use app\admin\model\team\Team as TeamModel;
use app\common\controller\Frontend;
use think\Cache;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;


class Order extends Frontend
{
    protected $orderModel = null;
    protected $teamModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
        $this->teamModel = new TeamModel();
    }


    /**
     * 生成订单sn
     * @param array $data 生成订单的参数
     * @return string
     */
    protected function orderSn($data)
    {
        $adminId = $data['aid'];
        $teamId = $data['tid'];
        return date('YmdHis',time())
            .str_pad($adminId,5,'0',STR_PAD_LEFT)
            .str_pad($teamId,5,'0',STR_PAD_LEFT)
            .str_pad(mt_rand(0,9999),4,'0',STR_PAD_LEFT);
    }


    /**
     * 享钱平台获取微信openid
     * @param $payInfo array 传入支付参数
     * @param $sn   string 订单编号
     */
    public function xpayGrant($payInfo,$sn)
    {
        $url = 'http://open.xiangqianpos.com/wxPayOauth/openid';
        $data = [
            'mch_code' => $payInfo['mch_code'],
            'charset'   => 'UTF-8',
            'nonce_str' => md5(time()),
            'redirect'  => urlencode($this->request->domain().'index.php/index/payorder/orderpayment?sn='.$sn),
            'sign'  => ''
        ];
        $data['sign'] = $this->XpaySignParams($data,$payInfo['mch_key']);
        //跳转享钱平台获取openid
        header('Location:'.$url.'?charset='.$data['charset'].'&mch_code='.$data['mch_code'].'&nonce_str='.$data['nonce_str'].'&redirect='.$data['redirect'].'&sign='.$data['sign']);
    }

    /**
     * 提交订单
     */
    public function submitOrder()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->param();
            //TODO::测试流程先不判断订单是否有效，后面再做这块的检验
            $sn = $this->orderSn($params);
            //构建订单数据
            $data = [
                'admin_id'  => $params['aid'],
                'admin_name'=> $this->adminModel->get($params['aid'])->nickname,
                'pid'       => $this->adminModel->get($params['aid'])->pid,
                'num'       => $params['number'],
                'name'      => $params['name'],
                'phone'     => $params['mobile'],
                'address'   => $params['province'].$params['city'].$params['district'].$params['detailaddress'],
                'team_id'   => $this->adminModel->get($params['aid'])->team_id,
                'team_name' => $this->teamModel->get($this->adminModel->get($params['aid'])->team_id)->name,
                'production_id'     => $params['gid'],
                'production_name'   => $params['production_name'],
                'goods_info'=> '款式='.$params['pattern'].';性别='.$params['sex'].';属性='.$params['attr'],
                'price'     => $params['price'],
                'pay_id'    => $params['pay_id'],
                'pay_type'  => $params['pay_type'],
                'sn'        => $sn,
                'order_ip'  => $this->request->ip(),
            ];

            $result = false;
            Db::startTrans();
            try {
                $result = $this->orderModel->isUpdate(false)->save($data);
                $orderId = $this->orderModel->id;
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

            //求出享钱url获取 openid链接 ，提交URL地址
            if ($params['pay_type'] == 1) {
                //支付类型为1，表示是享钱支付
                $xpayReq = $this->xpayGrant(Cache::get($this->request->ip().'-xpay_config'));

            }
            if ($result !== false) {
                $data = array_merge($data,['id'=>$orderId]);
                Cache::set($sn,$data);
                return ['status'=>0,'msg'=>'提交订单成功','order_id'=>$orderId,'sn'=>$sn];
            } else {
                return ['status'=>1,'msg'=>'提交订单失败，请稍候再试~'];
            }
        }
        die;
    }


    /**
     * 订单查询页
     */
    public function orderQuery()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->param();
            $orderInfo = OrderModel::where(['phone'=>$params['mobile']])->select();
            if (!empty($orderInfo)) {
                $data = [
                    'status'    => 0,
                    'order'     => $orderInfo,
                    'msg'       => '获取成功'
                ];
            } else {
                $data = [
                    'status'    => -1,
                    'order'     => [],
                    'msg'       => '获取失败'
                ];
            }

            return $data;
        }
        return $this->view->fetch('orderquery');
    }
}
