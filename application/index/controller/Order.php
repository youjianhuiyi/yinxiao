<?php
namespace app\index\Controller;

use app\admin\model\Admin;
use app\admin\model\sysconfig\Pay;
use app\admin\model\team\Team;
use app\common\controller\Frontend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use function GuzzleHttp\Psr7\str;

class Order extends Frontend
{

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
            $params = $this->request->post();
            if (empty($params) || empty($params['token'])) {
                //表示假提交或者是伪造提交数据,后期再做token验证
                return ['status'=>1,'code'=>'提交错误'];
            }
            $sn = $this->orderSn($params);
            //构建订单数据
            $data = [
                'admin_id'  => $params['admin_id'],
                'admin_name'=> Admin::get($params['admin_id'])->nickname,
                'pid'       => Admin::get($params['admin_id'])->pid,
                'num'       => $params['number'],
                'name'      => $params['name'],
                'phone'     => $params['mobile'],
                'address'   => $params['province'].'-'.$params['city'].'-'.$params['district'].'-'.$params['detailaddress'],
                'team_id'   => $params['team_id'],
                'team_name' => Team::get($params['team_id'])->name,
                'production_id'     => $params['pid'],
                'production_name'   => $params['production_name'],
                'goods_info'=> 'pattern='.$params['pattern'].';sex='.$params['sex'].';attr='.$params['attr'],
                'pay_type'  => $params['pay_type'],
                'price'     => $params['price'],
                'pay_id'    => Pay::get($params['team_id'])->id,
                'sn'        => $sn
            ];
//            2020040122224520000200002368
//            2020 0401 22 27 59 00003 00002 8733
//            2020 0401 22 25 04 30000200007426
            $result = false;

            Db::startTrans();
            try {
                $result = \app\admin\model\order\Order::create($data);
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
                return ['status'=>0,'提交订单成功',''];
            } else {
                $this->error(__('No rows were updated'));
            }
        }
        die;
    }
}
