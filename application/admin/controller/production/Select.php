<?php

namespace app\admin\controller\production;

use app\common\controller\Backend;
use think\Cache;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\model\production\Production as ProductionModel;
use app\admin\model\team\Team as TeamModel;

/**
 * 产品文案选择
 *
 * @icon fa fa-file-word-o
 */
class Select extends Backend
{
    
    /**
     * Select模型对象
     * @var \app\admin\model\production\Select
     */
    protected $model = null;
    protected $goodsData = [];//商品数据
    protected $selectData = [];//商品数据
    protected $teamModel = null;
    protected $productionModel = null;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\production\Select;
        $this->teamModel = new TeamModel();
        $this->productionModel = new ProductionModel();
        $this->goodsData = $this->productionModel->where('status',0)->select();
        $this->selectData = [0=>'请选择商品模板'];
        foreach ($this->goodsData as $v) {
            $this->selectData[$v['id']] = '编号--'.$v['id'].'；-产品名：'.$v['name'].'；-原销售价：'.$v['sales_price'].'；-原产品优惠价：'.$v['discount'];
        }

        $this->assign('selectData',$this->selectData);
        $this->assign('goodsData',$this->goodsData);
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
            $params['team_name'] = $params['team_id'] == 0 ? '平台测试':$this->adminInfo['team_name'];
            $params['production_name'] = $this->productionModel->get($params['production_id'])->name;
            if ($params) {
                $params = $this->preExcludeFields($params);
                $params['sales_price'] = $params['sales_price'] == 0 ? $this->productionModel->get($params['production_id'])->sales_price : $params['sales_price'];
                $params['discount'] = $params['discount'] == 0 ? $this->productionModel->get($params['production_id'])->discount : $params['discount'];
                $params['true_price'] = $params['true_price'] == 0 ? $this->productionModel->get($params['production_id'])->true_price : $params['true_price'];
                //判断填写的数据
                if ($params['sales_price'] > $params['discount']) {
                    if ($params['sales_price']-$params['discount'] != $params['true_price']) {
                        $params['true_price'] = $params['sales_price']-$params['discount'];
                    }
                } else {
                    $this->error('价格体系填写不正确！');
                }

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = $result1 = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    //团队数据更新
                    $teamData = $this->teamModel->get($params['team_id'])->team_productions;
                    $newTeamProduction = empty($teamData) ? $params['production_id'] : $teamData.','.$params['production_id'];
                    $result1 = $this->teamModel->isUpdate(true)->save($newTeamProduction,['id'=>$params['team_id']]);
                    //将本团队的商品数据缓存起来
                    Cache::set('select?tid='.$params['team_id'].'&gid='.$params['production_id'],$params,0);
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
            $params['team_id'] = $this->adminInfo['team_id'];
            $params['team_name'] = $params['team_id'] == 0 ? '平台测试':$this->adminInfo['team_name'];
            $params['production_name'] = $this->productionModel->get($params['production_id'])->name;
            if ($params) {
                $params['sales_price'] = $params['sales_price'] == 0 ? $this->productionModel->get($params['production_id'])->sales_price : $params['sales_price'];
                $params['discount'] = $params['discount'] == 0 ? $this->productionModel->get($params['production_id'])->discount : $params['discount'];
                $params['true_price'] = $params['true_price'] == 0 ? $this->productionModel->get($params['production_id'])->true_price : $params['true_price'];
                //判断填写的数据
                if ($params['sales_price'] > $params['discount']) {
                    if ($params['sales_price']-$params['discount'] != $params['true_price']) {
                        $params['true_price'] = $params['sales_price']-$params['discount'];
                    }
                } else {
                    $this->error('价格体系填写不正确！');
                }
                $params = $this->preExcludeFields($params);
                $result =  $result1 = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    //团队数据更新
                    if ($params['team_id'] != 0) {
                        $teamData = $this->teamModel->get($params['team_id'])->team_productions;
                        $newTeamProduction = empty($teamData) ? $params['production_id'] : $teamData.','.$params['production_id'];
                        $result1 = $this->teamModel->isUpdate(true)->save($newTeamProduction,['id'=>$params['team_id']]);
                    }
                    //将本团队的商品数据缓存起来
                    Cache::set('select?tid='.$params['team_id'].'&gid='.$params['production_id'],$params,0);
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


}
