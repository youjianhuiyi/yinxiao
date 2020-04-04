<?php
namespace app\index\Controller;

use app\admin\model\Admin as AdminModel;
use app\admin\model\order\Order as OrderModel;
use app\admin\model\sysconfig\Pay as PayModel;
use app\admin\model\team\Team as TeamModel;
use app\common\controller\Frontend;
use think\Cache;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;


class Order extends Frontend
{
    protected $orderModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->orderModel = new OrderModel();
    }

    /**
     * 生成订单sn
     * @param array $data 生成订单的参数
     * @return string
     */
    protected function orderSn($data)
    {
        $adminId = $data['admin_id'];
        $teamId = $data['team_id'];
        return date('YmdHis',time())
            .str_pad($adminId,5,'0',STR_PAD_LEFT)
            .str_pad($teamId,5,'0',STR_PAD_LEFT)
            .str_pad(mt_rand(0,9999),4,'0',STR_PAD_LEFT);
    }

    /**
     * 提交订单
     */
    public function submitOrder()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->param();
            if (empty($params) || empty($params['token'])) {
                //表示假提交或者是伪造提交数据,后期再做token验证
                return ['status'=>1,'code'=>'提交错误'];
            }
            $sn = $this->orderSn($params);
            //构建订单数据
            $data = [
                'admin_id'  => $params['admin_id'],
                'admin_name'=> AdminModel::get($params['admin_id'])->nickname,
                'pid'       => AdminModel::get($params['admin_id'])->pid,
                'num'       => $params['number'],
                'name'      => $params['name'],
                'phone'     => $params['mobile'],
                'address'   => $params['province'].'-'.$params['city'].'-'.$params['district'].'-'.$params['detailaddress'],
                'team_id'   => AdminModel::get($params['admin_id'])->team_id,
                'team_name' => TeamModel::get(AdminModel::get($params['admin_id'])->team_id)->name,
                'production_id'     => $params['pid'],
                'production_name'   => $params['production_name'],
                'goods_info'=> 'pattern='.$params['pattern'].';sex='.$params['sex'].';attr='.$params['attr'],
                'pay_type'  => $params['pay_type'],
                'price'     => $params['price'],
                'pay_id'    => PayModel::get(AdminModel::get($params['admin_id'])->team_id)->id,
                'sn'        => $sn
            ];

            $result = false;

            Db::startTrans();
            try {
                $result = $this->orderModel->save($data);
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
                array_push($data,['id'=>$orderId]);
                Cache::set($sn,$data,3600);
                return ['status'=>0,'msg'=>'提交订单成功','order_id'=>$orderId,'sn'=>$sn,'code'=>$params['code']];
            } else {
                return ['status'=>1,'msg'=>'提交订单失败，请稍候再试~'];
            }
        }
        die;
    }
}
