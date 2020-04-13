<?php

namespace app\admin\controller\production;

use app\common\controller\Backend;
use Endroid\QrCode\QrCode;
use think\Cache;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Response;
use app\admin\model\sysconfig\Consumables as ConsumablesModel;
use app\admin\model\sysconfig\Ground as GroundModel;
use app\admin\model\production\Production as ProductionModel;
use app\admin\model\production\Production_select as Production_selectModel;
use app\admin\model\production\Url as UrlModel;
use function fast\array_except;

/**
 * 商品链接
 *
 * @icon fa fa-link
 */
class Url extends Backend
{

    /**
     * Url模型对象
     */
    protected $model = null;
    protected $selectModel = null;
    protected $groundModel = null;
    protected $consumablesModel = null;
    protected $productionModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->productionModel = new ProductionModel();
        $this->model = new UrlModel();
        $this->selectModel = new Production_selectModel();
        $this->groundModel = new GroundModel();
        $this->consumablesModel = new ConsumablesModel();

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
                ->where(['admin_id'=>$this->adminInfo['id']])
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where(['team_id'=>$this->adminInfo['team_id']])
                ->where(['admin_id'=>$this->adminInfo['id']])
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
     * 生成产品数据
     */
    public function setProductionData()
    {
        $uid = $this->adminInfo['id'];
        //查找出当前团队所选择的产品模板数据
        $productionSelectData = $this->selectModel->where(['team_id'=>$this->adminInfo['team_id']])->select();
        $params = [];
        foreach ($productionSelectData as $value) {
            $params[] = [
                'production_id'     =>  $value['production_id'],
                'production_name'   =>  $value['production_name'],
                'team_id'           =>  $this->adminInfo['team_id'],
                'team_name'         =>  $this->adminInfo['team_name'],
                'admin_id'          =>  $uid,
                'admin_name'        =>  $this->adminInfo['nickname']
            ];
        }
        //对比当前用户已经生成了几个商品
        $existsSelectProductionData = $this->model->where(['team_id'=>$this->adminInfo['team_id'],'admin_id'=>$uid])->select();
        $newParams = [];
        $neArr = [];
        //判断
        if (count($params) > count($existsSelectProductionData)) {
            //表示商品数量大于已经选择的数量。
            foreach ($params as $key => $value) {
                foreach ($existsSelectProductionData as $val) {
                    if ($value['production_id'] == $val['production_id']) {
                        //表示已经该用户已经生成过该商品的记录，删除记录
                        $newParams[] = $value;
                        break;
                    }
                }
            }
            //求出未生成的链接
            foreach ($params as $k1 => $v1) {
                if (in_array($v1,$newParams)) {
                    unset($params[$k1]);
                }
            }
            //整理数据
            sort($params);
        } elseif (count($params) ==  count($existsSelectProductionData)) {
            //表示商品数量与链接种类数量一致，不需要操作。
            $this->error('商品数量与链接种类数量一致，不需要操作，直接获取链接即可');
        } else {
            //表示商品数量小于链接数量，这个是个bug。商品没有，还发什么链接
            $this->error('商品数量小于链接数量，这个是个bug。商品没有，还发什么链接');
        }

        //更新数据表
        if ($params) {
            $result = false;
            Db::startTrans();
            try {
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
        $this->error(__('Parameter %s can not be empty', ''));
    }

    /**
     * 获取商品推广地址
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function url($ids = null)
    {
        //获取当前选择项目的数据
        $urlData = $this->model->get($ids);
        //获取主表商品相关数据
        $productionData = $this->productionModel->get($urlData['production_id']);
        //加密算法
        $str = 'aid='.$this->adminInfo['id'].'&gid='.$urlData['production_id'].'&tid='.$this->adminInfo['team_id'].'&tp='.$productionData['module_name'];
        $checkCode = md5($str);
        //接入403逻辑，用于验证入口地址的真实性。
        Cache::set($checkCode,$str.'&check_code='.$checkCode,0);
        //获取当前可用的入口域名
        $groudDomainData = $this->groundModel->where(['is_forbidden'=>0])->column('domain_url');

        //判断域名是否已经被封
        if (1 === $urlData['is_forbidden'] || $checkCode != $urlData['check_code']) {
            //表示已经被封，需要重新生成新的入口推广链接
            //拼接随机域名前缀
            $urlPrefix = $this->getRandomStrDomainPrefix();
            $groundUrl = $urlPrefix.'.'.$groudDomainData[mt_rand(0,count($groudDomainData)-1)];
            //拼接最后的访问链接
            $url = 'http://'.$groundUrl.'/index.php/index/index/code/'.$checkCode;
            //缓存好当前入口链接
            $params = [
                'id'            =>  $ids,
                'url'           =>  $url,
                'domain_url'    =>  $groundUrl,
                'check_code'    =>  $checkCode,
                'query_string'  =>  $str
            ];

            //更新数据表
            Db::startTrans();
            try {
                $this->model->allowField(true)->isUpdate(true)->save($params,['id'=>$ids]);
                Cache::set('ground_url_'.$this->adminInfo['id'],$url);
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
        } else {
            //表示域名正常，不需要重新生成，直接返回即可
            if (empty($urlData['url'])) {
                //拼接随机域名前缀
                $urlPrefix = $this->getRandomStrDomainPrefix();
                $groundUrl = $urlPrefix.'.'.$groudDomainData[mt_rand(0,count($groudDomainData)-1)];
                //拼接最后的访问链接
                $url = 'http://'.$groundUrl.'/index.php/index/index/code/'.$checkCode;
                $params = [
                    'id'            =>  $ids,
                    'url'           =>  $url,
                    'domain_url'    =>  $groundUrl,
                    'check_code'    =>  $checkCode,
                    'query_string'  =>  $str
                ];
                //更新数据表
                Db::startTrans();
                try {
                    $this->model->allowField(true)->isUpdate(true)->save($params,['id'=>$ids]);
                    Cache::set('ground_url_'.$this->adminInfo['id'],$url);
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
            }
        }
        //入口地址域名缓存起来。入口域名+业务员id
        if (Cache::has('ground_url_'.$this->adminInfo['id'])) {
            $urlData['production_url'] = Cache::get('ground_url_'.$this->adminInfo['id']);
        } else {
            $groundUrl = $this->model->get($ids)->url;
            $urlData['production_url'] = $groundUrl;
            Cache::set('ground_url_'.$this->adminInfo['id'],$groundUrl);
        }

        $this->assign('data',$urlData);
        return $this->view->fetch();
    }

    /**
     * 生成二维码
     * @return Response
     * @throws \Endroid\QrCode\Exceptions\DataDoesntExistsException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionFailedException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionUnknownException
     * @throws \Endroid\QrCode\Exceptions\ImageTypeInvalidException
     */
    public function build()
    {
        $text = $this->request->get('text', 'hello world');
        $size = $this->request->get('size', 250);
        $padding = $this->request->get('padding', 15);
        $errorcorrection = $this->request->get('errorcorrection', 'medium');
        $foreground = $this->request->get('foreground', "#ffffff");
        $background = $this->request->get('background', "#000000");
        $logo = $this->request->get('logo');
        $logosize = $this->request->get('logosize');
        $label = $this->request->get('label');
        $labelfontsize = $this->request->get('labelfontsize');
        $labelhalign = $this->request->get('labelhalign');
        $labelvalign = $this->request->get('labelvalign');

        // 前景色
        list($r, $g, $b) = sscanf($foreground, "#%02x%02x%02x");
        $foregroundcolor = ['r' => $r, 'g' => $g, 'b' => $b];

        // 背景色
        list($r, $g, $b) = sscanf($background, "#%02x%02x%02x");
        $backgroundcolor = ['r' => $r, 'g' => $g, 'b' => $b];

        $qrCode = new QrCode();
        $qrCode
            ->setText($text)
            ->setSize($size)
            ->setPadding($padding)
            ->setErrorCorrection($errorcorrection)
            ->setForegroundColor($foregroundcolor)
            ->setBackgroundColor($backgroundcolor)
            ->setLogoSize($logosize)
            ->setLabel($label)
            ->setLabelFontSize($labelfontsize)
            ->setLabelHalign($labelhalign)
            ->setLabelValign($labelvalign)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);
        $fontPath = ROOT_PATH . 'public/assets/fonts/SourceHanSansK-Regular.ttf';
        if (file_exists($fontPath)) {
            $qrCode->setLabelFontPath($fontPath);
        }
        if ($logo) {
            $qrCode->setLogo(ROOT_PATH . 'public/assets/img/qrcode.png');
        }
        //也可以直接使用render方法输出结果
        //$qrCode->render();
        return new Response($qrCode->get(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }



    /**
     * 回收站
     * @internal
     */
    public function recyclebin()
    {
        return $this->error('不允许此操作');
    }

    /**
     * 添加
     * @internal
     */
    public function add()
    {
        return $this->error('不允许此操作');
    }

    /**
     * 编辑
     * @param null $ids
     * @internal
     */
    public function edit($ids = null)
    {
        return $this->error('不允许此操作');
    }

//    /**
//     * 删除
//     * @param null $ids
//     * @internal
//     */
//    public function del($ids = null)
//    {
//        return $this->error('不允许此操作');
//    }
}
