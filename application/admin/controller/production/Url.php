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
use app\admin\model\sysconfig\Kzdomain as KzDomainModel;

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
    protected $kzModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->productionModel = new ProductionModel();
        $this->model = new UrlModel();
        $this->selectModel = new Production_selectModel();
        $this->groundModel = new GroundModel();
        $this->consumablesModel = new ConsumablesModel();
        $this->kzModel = new KzDomainModel();

    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
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
        $this->assign('result',$result);
        return $this->view->fetch();
    }


    /**
     * 生成加密推广值
     * @param $gid
     * @param $tid
     * @param $tp
     * @return array
     */
    private function setCheckCode($gid,$tid,$tp)
    {
        //加密算法
        $str = 'aid='.$this->adminInfo['id'].'&gid='.$gid.'&tid='.$tid.'&tp='.$tp;
        $checkCode = md5($str);
        //接入403逻辑，用于验证入口地址的真实性。
        Cache::set($checkCode,$str.'&check_code='.$checkCode,0);
        return ['str'=> $str.'&check_code='.$checkCode,'check_code' => $checkCode];
    }

    /**
     * 生成域名与推广链接
     * @param $checkCode
     * @return array
     */
    private function setDomainUrl($checkCode)
    {
        //获取当前可用的入口域名
        $groudDomainData = $this->groundModel->where(['is_forbidden'=>0])->column('domain_url');
        //判断域名是否已经被封，表示已经被封，需要重新生成新的入口推广链接
        //拼接随机域名前缀
        $urlPrefix = $this->getRandomStrDomainPrefix();
        $groundUrl = $urlPrefix.'.'.$groudDomainData[mt_rand(0,count($groudDomainData)-1)];
        //拼接最后的访问链接
        $url = $groundUrl.'laotie888'.$checkCode;
        $url1 = 'http://'.$groundUrl.'/index.php/index/index/code/'.$checkCode;
        //配合快站参数生成链接参数码
        return ['url'=> $url,'domain_url'=> $groundUrl,'url1'=>$url1];
    }

    /**
     * 生成产品数据
     */
    public function setProductionData()
    {
        $uid = $this->adminInfo['id'];
        //查找出当前团队所选择的产品模板数据
        $productionSelectData = collection($this->selectModel->where(['team_id'=>$this->adminInfo['team_id'],'is_use'=>1])->select())->toArray();
        $field = ['production_id','production_name','team_id','team_name','admin_id','admin_name','check_code','query_string','domain_url','url'];
        $existsData = collection($this->model->field($field)->where(['admin_id'=>$uid,'team_id'=>$this->adminInfo['team_id']])->select())->toArray();
        //获取快站链接，是否指定为固定
        $kzDomain = collection($this->kzModel->where('status','neq',2)->select())->toArray();
        if (count($kzDomain) > 1) {
            $kzurl = $kzDomain[mt_rand(0,count($kzDomain)-1)];
        } elseif (count($kzDomain) == 1) {
            $kzurl = $kzDomain[0];
        } else {
            //表示快站域名已经没了，
            $kzurl = false;
        }
        //老数据里面如果存在已经选择的文案，则不进行更新操作。因为里面有对应的访问数据与下单数据
        $params = [];
        foreach ($productionSelectData as $value) {
            //获取模板名称
            $moduleName = $this->productionModel->get($value['production_id'])['module_name'];
            //获取推广码字串
            $checkData = $this->setCheckCode($value['production_id'],$this->adminInfo['team_id'],$moduleName);
            //获取推广链接
            $domainData = $this->setDomainUrl($checkData['check_code']);
            $params[] = [
                'production_id'     =>  $value['production_id'],
                'production_name'   =>  $value['own_name'],
                'team_id'           =>  $this->adminInfo['team_id'],
                'team_name'         =>  $this->adminInfo['team_name'],
                'admin_id'          =>  $uid,
                'admin_name'        =>  $this->adminInfo['nickname'],
                'check_code'        =>  $checkData['check_code'],
                'query_string'      =>  $checkData['str'],
                'domain_url'        =>  $domainData['domain_url'],
                'url'               =>  $kzurl ? $kzurl['domain_url'].$domainData['url'] : $domainData['url1']
            ];
        }

        //对比新老数据
        foreach ($params as $key => $value) {
            if (in_array($value,$existsData)) {
                //表示新文案在原有数据里面已存在
                unset($params[$key]);
            }
        }
        //更新数据表
        //不能删除现有数据，因为里面有访问数据以及成单数据
        //TODO::这里没做限制，如果用户不断点击，会重复写入数据库,可以选择真实删除，目前使用的是软删除
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
        $this->success('没有启用的文案可以生成链接！！');
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
        //获取方案选择表，获取 状态，如果有状态。则可以获取成功，如果是禁用状态。则不能生成链接
        $selectData = $this->selectModel->where(['production_id'=>$urlData['production_id'],'team_id'=>$this->adminInfo['team_id']])->find();
        if ($selectData['is_use'] == 0) {
            //表示没有启动模板，不能获取推广链接
            $urlData['app-debug'] = false;
            $urlData['is_use'] = false;
            $urlData['production_url'] = '';
            $this->assign('data',$urlData);
            return $this->view->fetch();
        }
        //加密算法
        $str = 'aid='.$this->adminInfo['id'].'&gid='.$urlData['production_id'].'&tid='.$this->adminInfo['team_id'].'&tp='.$productionData['module_name'];
        $checkCode = md5($str);
        //接入403逻辑，用于验证入口地址的真实性。
        Cache::set($checkCode,$str.'&check_code='.$checkCode,0);
        //获取当前可用的入口域名
        $groudDomainData = $this->groundModel->where(['is_forbidden'=>0])->column('domain_url');

        //获取快站链接，是否指定为固定
        $kzDomain = collection($this->kzModel->where(['status'=>1])->select())->toArray();
        if (count($kzDomain) > 1) {
            $kzurl = $kzDomain[mt_rand(0,count($kzDomain)-1)];
        } elseif (count($kzDomain) == 1) {
            $kzurl = $kzDomain[0];
        } else {
            //表示快站域名已经没了，
            $kzurl = false;
        }
        //判断域名是否已经被封
        if ($checkCode != $urlData['check_code'] || $str.'&check_code='.$checkCode == $urlData['query_string'] || 1 === $urlData['is_forbidden']) {
            //表示已经被封，需要重新生成新的入口推广链接
            //获取模板名称
            $moduleName = $this->productionModel->get($urlData['production_id'])['module_name'];
            //获取推广码字串
            $checkData = $this->setCheckCode($urlData['production_id'],$this->adminInfo['team_id'],$moduleName);
            //获取推广链接
            $domainData = $this->setDomainUrl($checkData['check_code']);

            //缓存好当前入口链接
            $params = [
                'id'            =>  $ids,
                'url'           =>  $kzurl ? $kzurl['domain_url'].$domainData['url'] : $domainData['url1'],
                'domain_url'    =>  $domainData['domain_url'],
                'check_code'    =>  $checkCode,
                'query_string'  =>  $str.'&check_code='.$checkCode
            ];

            //更新数据表
            Db::startTrans();
            try {
                $this->model->allowField(true)->isUpdate(true)->save($params,['id'=>$ids]);
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
                //获取模板名称
                $moduleName = $this->productionModel->get($urlData['production_id'])['module_name'];
                //获取推广码字串
                $checkData = $this->setCheckCode($urlData['production_id'],$this->adminInfo['team_id'],$moduleName);
                //获取推广链接
                $domainData = $this->setDomainUrl($checkData['check_code']);
                $params = [
                    'id'            =>  $ids,
                    'url'           =>  $kzurl ? $kzurl['domain_url'].$domainData['url'] : $domainData['url1'],
                    'domain_url'    =>  $domainData['domain_url'],
                    'check_code'    =>  $checkCode,
                    'query_string'  =>  $str.'&check_code='.$checkCode
                ];
                //更新数据表
                Db::startTrans();
                try {
                    $this->model->allowField(true)->isUpdate(true)->save($params,['id'=>$ids]);
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
        $urlData['app-debug'] = false;
        $urlData['is_use'] = true;
        return $urlData;
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

}
