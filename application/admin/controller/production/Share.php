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
 * 分享有礼
 *
 * @icon fa fa-share
 */
class Share extends Backend
{

    protected $noNeedRight = ['openUrl','forbidden'];
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
        $this->setProductionData();
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
     * 生成域名与推广链接
     * @param $shareCode string shareCode = checkcode + base64encode('shop')
     * @return array
     */
    private function setDomainUrl($shareCode)
    {
        //获取当前可用的入口域名
        $groudDomainData = $this->groundModel->where(['is_forbidden'=>0])->column('domain_url');
        //判断域名是否已经被封，表示已经被封，需要重新生成新的入口推广链接
        //拼接随机域名前缀
        $urlPrefix = $this->getRandomStrDomainPrefix();
        $groundUrl = $urlPrefix.'.'.$groudDomainData[mt_rand(0,count($groudDomainData)-1)];
        //拼接最后的访问链接
        $url = $groundUrl.'laotie888'.rtrim($shareCode,'==');
        $url1 = 'http://'.$groundUrl.'/index.php/index/index/code/'.rtrim($shareCode,'==');
        //配合快站参数生成链接参数码
        return ['url'=> $url,'domain_url'=> $groundUrl,'url1'=>$url1];
    }

    /**
     * 生成产品数据
     */
    protected function setProductionData()
    {
        $uid = $this->adminInfo['id'];
        //查找出当前团队所选择的产品模板数据
        $existsData = collection($this->model->where(['admin_id'=>$uid,'team_id'=>$this->adminInfo['team_id'],'is_forbidden'=>0])->select())->toArray();
        //获取快站链接，是否指定为固定
        $kzDomain = collection($this->kzModel->where('status',1)->where('is_forbidden',0)->select())->toArray();
        if (count($kzDomain) > 1) {
            $kzurl = $kzDomain[mt_rand(0,count($kzDomain)-1)];
        } elseif (count($kzDomain) == 1) {
            $kzurl = $kzDomain[0];
        } else {
            //表示快站域名已经没了，
            $kzurl = false;
        }
        //因为分享码是直接依赖于推广码生成的。如果当前推广码存在，则应该生成分享码。前提是不能禁用
        //这是即可查询当前用户有哪些推广码，没有生成分享码的。直接生成即可。
        //
        $params = [];
        foreach ($existsData as $value) {
            if ($value['share_code'] == '' && $value['share_url'] == '') {
                $domainData = $this->setDomainUrl($value['check_code'].base64_encode('shop'));
                $params[] = [
                    'id'                =>  $value['id'],
                    'share_code'        =>  $value['check_code'].base64_encode('shop'),
                    'share_url'         =>  $kzurl ? $kzurl['domain_url'].$domainData['url'] : $domainData['url1']
                ];
            }

        }

        //更新数据表
        if ($params) {
            Db::startTrans();
            try {
                $result = $this->model->isUpdate(true)->saveAll($params);
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
        return json_encode(['code'=>1]);
    }

    /**
     * 重新获取分享链接
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function url($ids = null)
    {
        //获取当前选择项目的数据
        $urlData = $this->model->get($ids);
        //获取快站链接，是否指定为固定
        $kzDomain = collection($this->kzModel->where(['status'=>1,'is_forbidden'=>0])->select())->toArray();
        if (count($kzDomain) > 1) {
            $kzurl = $kzDomain[mt_rand(0,count($kzDomain)-1)];
        } elseif (count($kzDomain) == 1) {
            $kzurl = $kzDomain[0];
        } else {
            //表示快站域名已经没了，
            $kzurl = false;
        }
        //TODO::需要根据真实情况，目前此条件没有实际意义
//        if (1 === $urlData['share_code_status']) {
            //缓存好当前入口链接
            $domainData = $this->setDomainUrl($urlData['share_code']);
            $params = [
                'id'            =>  $ids,
                'share_url'     =>  $kzurl ? $kzurl['domain_url'].$domainData['url'] : $domainData['url1'],
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
//        } else {
//            //表示域名正常，不需要重新生成，直接返回即可
//            if (empty($urlData['share_url'])) {
//                $domainData = $this->setDomainUrl($urlData['share_code']);
//                $params = [
//                    'id'            =>  $ids,
//                    'share_url'     =>  $kzurl ? $kzurl['domain_url'].$domainData['url'] : $domainData['url1'],
//                ];
//                //更新数据表
//                Db::startTrans();
//                try {
//                    $this->model->isUpdate(true)->save($params);
//                    Db::commit();
//                } catch (ValidateException $e) {
//                    Db::rollback();
//                    $this->error($e->getMessage());
//                } catch (PDOException $e) {
//                    Db::rollback();
//                    $this->error($e->getMessage());
//                } catch (\Exception $e) {
//                    Db::rollback();
//                    $this->error($e->getMessage());
//                }
//            }
//        }
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
     * 关闭二维码
     * @param null $ids
     * @return bool
     */
    public function forbidden($ids = null)
    {
        $data = [
            'id'    => $ids,
            'share_code_status'  => 1
        ];
        $result = $this->model->isUpdate(true)->save($data);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * 开启二维码
     * @param null $ids
     * @return bool
     */
    public function openUrl($ids = null)
    {
        $data = [
            'id'    => $ids,
            'share_code_status'  => 0
        ];
        $result = $this->model->isUpdate(true)->save($data);
        if ($result) {
            return true;
        }
        return false;
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
