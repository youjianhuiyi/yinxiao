<?php
namespace app\index\Controller;

use app\admin\model\order\Order as OrderModel;
use app\admin\model\order\Sharedata as ShareDataModel;
use app\admin\model\team\Team as TeamModel;
use app\admin\model\data\Analysis as AnalysisModel;
use app\common\controller\Frontend;
use think\Cache;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 订单类
 * Class Order
 * @package app\index\Controller
 */
class Order extends Frontend
{
    protected $orderModel = null;
    protected $teamModel = null;
    protected $analysisModel = null;
    protected $shareDataModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
        $this->teamModel = new TeamModel();
        $this->analysisModel = new AnalysisModel();
        $this->shareDataModel = new SharedataModel();
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
        return 'P'.date('YmdHis',time())
            .str_pad($adminId,5,'0',STR_PAD_LEFT)
            .str_pad($teamId,5,'0',STR_PAD_LEFT)
            .str_pad(mt_rand(0,9999),4,'0',STR_PAD_LEFT);
    }

    /**
     * 提交鞋子模板订单
     * @return array
     */
    public function submitOrder()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->param();
            //TODO::测试流程先不判断订单是否有效，后面再做这块的检验
            $sn = $this->orderSn($params);
            $pattern = isset($params['pattern']) ? $params['pattern'] : '';
            $sex = isset($params['sex']) ? $params['sex'] : '';
            $attr = isset($params['attr']) ? $params['attr'] : '';
            $goodsInfo = '';
            if ($pattern) {
                $goodsInfo .= '款式='.$pattern.';';
            }
            if ($sex) {
                $goodsInfo .= '性别='.$sex.';';
            }

            if ($attr) {
                $goodsInfo .= '属性='.$attr.';';
            }

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
                'goods_info'=> $goodsInfo,
                'price'     => $params['price']*$params['number'],
                'pay_id'    => $params['pay_id'],
                'pay_type'  => $params['pay_type'],
                'sn'        => $sn,
                'order_ip'  => $this->request->ip(),
                'check_code'=> $params['check_code'],
                'comment'   => isset($params['remarks']) ? $params['remarks'] : ''
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

            if ($result !== false) {
                $data = array_merge($data,['id'=>$orderId]);
                if (!Cache::has('xpay-order-'.$params['check_code'].$sn) && $data['pay_type'] == 1) {
                    //进行数据统计
                    $this->doDataSummary($params['check_code'],['type'=>'order_count','nums'=>1]);
                    $this->doDataSummary($params['check_code'],['type'=>'order_nums','nums'=>$data['num']]);
                    Cache::set('xpay-order-'.$params['check_code'].$sn,$sn,600);
                    //报表详情
                } elseif ($data['pay_type'] == 0) {
                    //表示原生支付
                    //进行数据统计
                    $this->doDataSummary($params['check_code'],['type'=>'order_count','nums'=>1]);
                    $this->doDataSummary($params['check_code'],['type'=>'order_nums','nums'=>$data['num']]);
                    Cache::set('pay-order-'.$params['check_code'].$sn,$sn,600);
                }
                Cache::set($sn,$data);
                return ['status'=>0,'msg'=>'提交订单成功','order_id'=>$orderId,'sn'=>$sn];
            } else {
                return ['status'=>1,'msg'=>'提交订单失败，请稍候再试~'];
            }
        }
        die;
    }

    /**
     * 提交鞋子模板订单新
     * @return array
     */
    public function submitOrderNew()
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
                'price'     => 0,
                'pay_id'    => $params['pay_id'],
                'pay_type'  => $params['pay_type'],
                'sn'        => $sn,
                'order_ip'  => $this->request->ip(),
                'check_code'=> $params['check_code'],
                'comment'   => isset($params['remarks']) ? $params['remarks'] : ''
            ];
            //更改订单金额
            if ($params['number'] == 1) {
                $data['price'] = 79.9;
            } else {
                $data['price'] = round(79.9*$params['number']*0.75,2);
            }

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

            if ($result !== false) {
                $data = array_merge($data,['id'=>$orderId]);
                if (!Cache::has('xpay-order-'.$params['check_code'].$sn)) {
                    //进行数据统计
                    $this->doDataSummary($params['check_code'],['type'=>'order_count','nums'=>1]);
                    $this->doDataSummary($params['check_code'],['type'=>'order_nums','nums'=>$data['num']]);
                    Cache::set('xpay-order-'.$params['check_code'].$sn,$sn,600);
                }
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
            $orderInfo = $this->orderModel->where(['phone'=>$params['mobile']])->select();
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

    /**
     * 分类有礼数据提交
     */
    public function shareSubmit()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->param();
            //TODO::测试流程先不判断订单是否有效，后面再做这块的检验
            $sn = 'HD'.round(microtime(true) * 1000);
            //构建订单数据
            $data = [
                'admin_id'  => $params['aid'],
                'pid'       => $params['pid'],
                'name'      => $params['name'],
                'phone'     => $params['mobile'],
                'address'   => $params['province'].$params['city'].$params['district'].$params['detailaddress'],
                'team_id'   => $params['tid'],
                'production_id'     => $params['gid'],
                'production_name'   => $params['production_name'],
                'goods_info'=> $params['tips'],
                'sn'        => $sn,
                'order_ip'  => $this->request->ip(),
                'share_code'=> $params['share_code'],
            ];

            $result = false;
            Db::startTrans();
            try {
                $result = $this->shareDataModel->isUpdate(false)->save($data);
                $orderId = $this->shareDataModel->id;
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
                $data = array_merge($data,['id'=>$orderId]);
                $res = $this->sendShareSMS($data);
                if ($res) {
                    $this->shareDataModel->isUpdate(true)->save(['id'=>$orderId,'send_status'=>1]);
                }
                return ['status'=>0,'msg'=>'提交信息成功','order_id'=>$orderId,'sn'=>$sn];
            } else {
                return ['status'=>1,'msg'=>'提交信息失败，请稍候再试~'];
            }
        }
        die;
    }
}
