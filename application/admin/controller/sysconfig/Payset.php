<?php
namespace app\admin\controller\sysconfig;

use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\model\sysconfig\Pay as PayModel;
use app\admin\model\sysconfig\Xpay as XPayModel;
use app\admin\model\sysconfig\Rypay as RyPayModel;

/**
 * 支付管理
 *
 * @icon fa fa-circle-o
 */
class Payset extends Backend
{
    
    /**
     * Payset模型对象
     * @var \app\admin\model\sysconfig\Payset
     */
    protected $model = null;
    protected $payModel = null;
    protected $xpayModel = null;
    protected $rypayModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\sysconfig\Payset;
        $this->payModel = new PayModel();
        $this->xpayModel = new XPayModel();
        $this->rypayModel = new RyPayModel();

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
                ->where('team_id',$this->adminInfo['team_id'])
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where('team_id',$this->adminInfo['team_id'])
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
     * 同步支付配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sync()
    {
        $uid = $this->adminInfo['id'];
        //查找出当前团队所选择的产品模板数据
        $payData = $this->payModel->where(['team_id'=>$this->adminInfo['team_id'],'status'=>1])->select();
        $xpayata = $this->xpayModel->where(['team_id'=>$this->adminInfo['team_id'],'status'=>1])->select();
        $rypayata = $this->rypayModel->where(['team_id'=>$this->adminInfo['team_id'],'status'=>1])->select();
        $params = [];
        foreach ($payData as $value) {
            $params[] = [
                'type'              =>  0,
                'pay_id'            =>  $value['id'],
                'pay_channel'       =>  $value['pay_name'],
                'team_id'           =>  $this->adminInfo['team_id'],
                'team_name'         =>  $this->adminInfo['team_name'],
                'is_multiple'       =>  1,
                'status'            =>  1
            ];
        }

        foreach ($xpayata as $value) {
            $params[] = [
                'type'              =>  1,
                'pay_id'            =>  $value['id'],
                'pay_channel'       =>  $value['pay_name'],
                'team_id'           =>  $this->adminInfo['team_id'],
                'team_name'         =>  $this->adminInfo['team_name'],
            ];
        }

        foreach ($rypayata as $value) {
            $params[] = [
                'type'              =>  2,
                'pay_id'            =>  $value['id'],
                'pay_channel'       =>  $value['pay_name'],
                'team_id'           =>  $this->adminInfo['team_id'],
                'team_name'         =>  $this->adminInfo['team_name'],
            ];
        }
        //查找当前有的支付数据，直接全部干掉，使用全新的数据
        $existsPayData = $this->model->where(['team_id'=>$this->adminInfo['team_id']])->select();
        $newParams = [];
        foreach ($existsPayData as $value) {
            $newParams[] .= $value['id'];
        }

        //更新数据表
        if ($params) {
            $result = false;
            Db::startTrans();
            try {
                $this->model->destroy($newParams);
                $result = $this->model->allowField(true)->saveAll($params);
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

    }

}
