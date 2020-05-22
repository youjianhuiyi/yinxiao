<?php

namespace app\index\controller;

use app\admin\model\production\Production as ProductionModel;
use app\common\controller\Frontend;
use think\Cache;
use app\admin\model\production\Production_select as SelectModel;
use app\admin\model\production\Url as UrlModel;
use app\admin\model\sysconfig\Consumables as ConsumablesModel;
use app\admin\model\sysconfig\Ground as GroundModel;
use app\admin\model\sysconfig\Payset as PaySetModel;
use app\admin\model\data\Visit as VisitModel;
use app\admin\model\data\Analysis as AnalysisModel;

/**
 * 模板渲染
 * Class Index
 * @package app\index\controller
 */
class Index extends Frontend
{

    protected $selectModel = null;
    protected $urlModel = null;
    protected $groundModel = null;
    protected $consumablesModel = null;
    protected $paysetModel = null;
    protected $visitModel = null;
    protected $analysisModel = null;
    protected $productionModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->selectModel = new SelectModel();
        $this->urlModel = new UrlModel();
        $this->groundModel = new GroundModel();
        $this->consumablesModel = new ConsumablesModel();
        $this->paysetModel = new PaySetModel();
        $this->visitModel = new VisitModel();
        $this->analysisModel = new AnalysisModel();
        $this->productionModel = new ProductionModel();
    }

    /**
     * 文案预览
     * @param null $ids
     * @return string
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function reviewHtml($ids = null)
    {
        $moduleData = $this->productionModel->get($ids)->toArray();
        $addArr = [
            'price' =>  $moduleData['true_price'],
            'aid'   =>  0,
            'tid'   =>  0,
            'pid'   =>  0,
            'gid'   =>  0,
            'pay_type'   =>  0,
            'pay_id'   =>  0,
            'production_name'   =>  $moduleData['name'],
            'api_domain'   =>  $this->request->url(),
            'tp'   =>  $moduleData['module_name'],
            'order_url'   =>  $this->request->url(),
            'check_code' => '',
        ];
        $moduleData = array_merge($addArr,$moduleData);
        $this->assign('data',$moduleData);
        return $this->view->fetch($moduleData['module_name']);
    }

    /**
     * 类单元测试方法
     */
    public function test()
    {

    }

    /**
     * 落地检测是否是微信打开
     * @return bool
     */
    protected function isWx()
    {
        if (strpos($this->request->header('user-agent'), 'WindowsWechat') !== false) {
            return false;
        }elseif (strpos($this->request->header('user-agent'), 'MicroMessenger') !== false) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * 空方法
     * @throws \think\Exception
     */
    public function index()
    {
        return $this->return500Error('此网站已经关闭～～');
    }

    /**
     * 微信支付落地页面
     * @return string
     * @throws \think\Exception
     */
    public function index0()
    {
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        if (empty($params)) {
            return $this->return500Error('请使用正确的链接进行访问！！');
        }

        if (!$this->verifyCheckCode($params)) {
            //表示验证失败，链接被篡改
            return $this->return500Error('请不要使用非法手段更改链接');
        }
        //判断访问链接是否属于正常状态
        $where = ['admin_id'=>$params['aid'],'team_id'=>$params['tid'],'production_id'=>$params['gid'],'check_code'=>$params['check_code']];
        $urlQrStatus = $this->urlModel->where($where)->find();
        if ($urlQrStatus['is_forbidden'] == 1) {
            return $this->return500Error('活动已经结束!');
        }

        $userInfo = $this->adminModel->get($params['aid']);
        //获取团队推广商品数据
        $goodsData = $this->getSelectGoodsInfo($params['tid'],$params['gid']);
        //获取访问者IP
        $userIp  = $this->request->ip();
        $payInfo = Cache::get($userIp.'-'.$params['check_code'].'-pay_config');
        //获取当前支付状态
        $payStatus = $this->getCurrentPayStatus($payInfo['mch_id'],1);
        if (!$payInfo || $payStatus == 0) {
            //表示支付全挂了。
            return $this->return500Error('请检查支付通道是否正常~~');
        }
        //将本团队的商品数据缓存起来
        $data = [
            'aid'       => $params['aid'],//业务员id值（必填）
            'tp'        => $params['tp'],//模板名称，加密使用
            'tid'       => $params['tid'],//团队名称（必填）
            'pid'       => $userInfo['pid'],//业务员上级id（必填）
            'gid'       => $params['gid'],
            'phone1'    => $goodsData['phone1'],
            'phone2'    => $goodsData['phone2'],
            'tongji'    => $goodsData['tongji'],
            'pay_type'  => 0,//支付类型（可选）
            'pay_id'    => $payInfo['id'],//支付类型（可选）
            'price'     => $goodsData['true_price'],//支付价格（必填）
            'sales_price'     => $goodsData['sales_price'],//支付价格（必填）
            'discount'     => $goodsData['discount'],//支付价格（必填）
            'production_name'   => $goodsData['own_name'] == '' ? $goodsData['production_name'] : $goodsData['own_name'],//商品名称（必填）
            'pay_channel'       => $payInfo['pay_domain'.mt_rand(1,5)],//支付通道，即使用的支付域名（可选每次随机使用支付域名即可）
            'order_url'         => $this->request->domain(),//订单提交链接（必填）
            'check_code'        => $params['check_code'],//链接检验码
            'api_domain'        =>  rtrim($payInfo['grant_domain_'.mt_rand(1,3)],'/')//订单提交成功后跳转链接支付链接（跳转之前先调用微信授权，再落地到支付界面，这中间，需要将重要的参数通过url参数传送）
        ];

        //缓存组装好的数据，进行跳转403,组装好中间域名。
        $this->assign('data',$data);
        //更新链接访问次数
        $url = $this->request->url(true);
        $visitIp = $userIp.'-'.$url;
        //设置当前链接缓存
        $urlData = [
            'admin_id'  => $params['aid'],
            'date'      => date('m-d',time()),
            'url'       => $visitIp,
            'team_id'   => $params['tid'],
            'production_id'     => $params['gid'],
            'check_code'        => $params['check_code'],
            'count'             => 1
        ];
        //如果今天已经存在访问链接 ，就不在记录
        if (!Cache::has($visitIp)) {
            Cache::set($visitIp,$visitIp,$this->getDiscountTime());
            $this->urlModel->where(['admin_id'=>$params['aid'],'check_code'=>$params['check_code']])->setInc('count');
            $this->visitModel->save($urlData);
            //进行数据统计
            $this->doDataSummary($params['check_code'],['type'=>'visit','nums'=>1]);
        }else {
            $this->visitModel->where('url',$visitIp)->setInc('count');
        }
        return $this->view->fetch($params['tp']);
    }

    /**
     * xpay落地页面
     * @return string
     * @throws \think\Exception
     */
    public function index1()
    {
        $isWx = $this->isWx();
        if (!$isWx) {
            return $this->return500Error('请用微信打开页面~~');
        }
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        if (empty($params)) {
            return $this->return500Error('请使用正确的链接进行访问！！');
        }

        if (!$this->verifyCheckCode($params)) {
            //表示验证失败，链接被篡改
            return $this->return500Error('请不要使用非法手段更改链接');
        }

        //判断访问链接是否属于正常状态
        $where = ['admin_id'=>$params['aid'],'team_id'=>$params['tid'],'production_id'=>$params['gid'],'check_code'=>$params['check_code']];
        $urlQrStatus = $this->urlModel->where($where)->find();
        if ($urlQrStatus['is_forbidden'] == 1) {
            return $this->return500Error('活动已经结束!');
        }
        $userInfo = $this->adminModel->get($params['aid']);
        //获取团队推广商品数据
        $goodsData = $this->getSelectGoodsInfo($params['tid'],$params['gid']);
        //获取访问者IP
        $userIp  = $this->request->ip();
        //直接根据访问此方法的话，就确定支付方式为xpay
        $payInfo = Cache::get($userIp.'-'.$params['check_code'].'-xpay_config');
        //获取当前支付状态
        $payStatus = $this->getCurrentPayStatus($payInfo['mch_id'],1);
        if (!$payInfo || $payStatus == 0) {
            //表示支付全挂了。
            return $this->return500Error('请检查支付通道是否正常~~~');
        }
        //将本团队的商品数据缓存起来
        $data = [
            'aid'       => $params['aid'],//业务员id值（必填）
            'tp'        => $params['tp'],//模板名称，加密使用
            'tid'       => $params['tid'],//团队名称（必填）
            'pid'       => $userInfo['pid'],//业务员上级id（必填）
            'gid'       => $params['gid'],
            'phone1'    => $goodsData['phone1'],
            'phone2'    => $goodsData['phone2'],
            'tongji'    => $goodsData['tongji'],
            'pay_type'  => 1,//支付类型（可选）
            'pay_id'    => $payInfo['id'],//支付类型（可选）
            'price'     => $goodsData['true_price'],//支付价格（必填）
            'sales_price'     => $goodsData['sales_price'],//支付价格（必填）
            'discount'     => $goodsData['discount'],//支付价格（必填）
            'production_name'   => $goodsData['own_name'] == '' ? $goodsData['production_name'] : $goodsData['own_name'],//商品名称（必填）
            'pay_channel'       => $payInfo['api_url'],//支付通道，即使用的支付域名（可选每次随机使用支付域名即可）
            'order_url'         => $this->request->domain(),//订单提交链接（必填）
            'check_code'        => $params['check_code'],//链接检验码
            'api_domain'        => $this->request->domain(),/*原微信请求授权地址*/
        ];

        //缓存组装好的数据，进行跳转403,组装好中间域名。
        Cache::set('index_'.$params['check_code'],$data,300);/*缓存好数据。用于后面调用数据*/
        $this->assign('data',$data);
        //更新链接访问次数
        $url = $this->request->url(true);
        $visitIp = $userIp.'-'.$url;
        //设置当前链接缓存
        $urlData = [
            'admin_id'  => $params['aid'],
            'date'      => date('m-d',time()),
            'url'       => $visitIp,
            'team_id'   => $params['tid'],
            'production_id'     => $params['gid'],
            'check_code'        => $params['check_code'],
            'count'             => 1
        ];
        //如果今天已经存在访问链接 ，就不在记录
        if (!Cache::has($visitIp)) {
            Cache::set($visitIp,$visitIp,$this->getDiscountTime());
            $this->urlModel->where(['admin_id'=>$params['aid'],'check_code'=>$params['check_code']])->setInc('count');
            $this->visitModel->isUpdate(false)->save($urlData);
            //进行数据统计
            $this->doDataSummary($params['check_code'],['type'=>'visit','nums'=>1]);
        } else {
            $this->visitModel->where('url',$visitIp)->setInc('count');
        }

        return $this->view->fetch($params['tp']);
    }

    /**
     * rypay落地页面
     * @return string
     * @throws \think\Exception
     */
    public function index2()
    {
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        if (empty($params)) {
            return $this->return500Error('请使用正确的链接进行访问！！');
        }

        if (!$this->verifyCheckCode($params)) {
            //表示验证失败，链接被篡改
            return $this->return500Error('请不要使用非法手段更改链接');
        }

        $userInfo = $this->adminModel->get($params['aid']);
        //获取团队推广商品数据
        $goodsData = $this->getSelectGoodsInfo($params['tid'],$params['gid']);
        //获取访问者IP
        $userIp  = $this->request->ip();
        //直接根据访问此方法的话，就确定支付方式为rypay
        $payInfo = Cache::get($userIp.'-'.$params['check_code'].'-rypay_config');
        if (!$payInfo) {
            //表示支付全挂了。
            die('请检查支付通道是否正常~~~');
        }
        //将本团队的商品数据缓存起来
        $data = [
            'aid'       => $params['aid'],//业务员id值（必填）
            'tp'        => $params['tp'],//模板名称，加密使用
            'tid'       => $params['tid'],//团队名称（必填）
            'pid'       => $userInfo['pid'],//业务员上级id（必填）
            'gid'       => $params['gid'],
            'phone1'    => $goodsData['phone1'],
            'phone2'    => $goodsData['phone2'],
            'tongji'    => $goodsData['tongji'],
            'pay_type'  => 2,//支付类型（可选）
            'pay_id'    => $payInfo['id'],//支付类型（可选）
            'price'     => $goodsData['true_price'],//支付价格（必填）
            'sales_price'     => $goodsData['sales_price'],//支付价格（必填）
            'discount'     => $goodsData['discount'],//支付价格（必填）
            'production_name'   => $goodsData['own_name'] == '' ? $goodsData['production_name'] : $goodsData['own_name'],//商品名称（必填）
            'pay_channel'       => $payInfo['api_url'],//支付通道，即使用的支付域名（可选每次随机使用支付域名即可）
            'order_url'         => $this->request->domain(),//订单提交链接（必填）
            'check_code'        => $params['check_code'],//链接检验码
            'api_domain'        => $this->request->domain(),/*原微信请求授权地址*/
        ];

        //缓存组装好的数据，进行跳转403,组装好中间域名。
        Cache::set('index_'.$params['check_code'],$data,300);/*缓存好数据。用于后面调用数据*/
        $this->assign('data',$data);
        //更新链接访问次数
        $url = $this->request->url(true);
        $visitIp = $userIp.'-'.$url;
        //设置当前链接缓存
        $urlData = [
            'admin_id'  => $params['aid'],
            'date'      => date('m-d',time()),
            'url'       => $visitIp,
            'team_id'   => $params['tid'],
            'production_id'     => $params['gid'],
            'check_code'        => $params['check_code'],
            'count'             => 1
        ];
        //如果今天已经存在访问链接 ，就不在记录
        if (!Cache::has($visitIp)) {
            Cache::set($visitIp,$visitIp,$this->getDiscountTime());
            $this->urlModel->where(['admin_id'=>$params['aid'],'check_code'=>$params['check_code']])->setInc('count');
            $this->visitModel->save($urlData);
        } else {
            $this->visitModel->where('url',$visitIp)->setInc('count');
        }
        return $this->view->fetch($params['tp']);
    }

    /**
     * 获取落地链接
     * @return string
     */
    private function getLuckDomain()
    {
        //获取落地域名，一个个的消耗。
        if (!Cache::has('luck_domain')) {
            //表示验证成功，获取炮灰域名准备落地
            //TODO::可以考虑使用缓存，后台增加或者修改删除等操作的时候对炮灰域名进行缓存更改。不然每次落地都要全部查询一次数据
            $consumables = $this->consumablesModel->order('id','desc')->where(['is_forbidden'=>0])->column('domain_url');
            //表示不存在
            if (count($consumables) >= 1) {
                $luckDomain = array_pop($consumables);
                $this->consumablesModel->where('domain_url',$luckDomain)->update(['is_inuse'=>1]);
                Cache::set('luck_domain',$luckDomain,1800);
                //TODO::更改域名为正在使用状态
            } else {
                //表示没有炮灰域名了
                $luckDomain = 'http://www.qq.com';
            }
        } else {
            //表示存在
            $luckDomain = Cache::get('luck_domain');
        }

        return $luckDomain;
    }

    /**
     * 最终落地页面,403请求接口
     * @throws \think\Exception
     */
    public function loadGround()
    {
        //获取请求接口允许的域名列表
        header('Content-Type: text/html;charset=utf-8');
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
        header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with,X_Requested_With,Origin,application/json'); // 设置允许自定义请求头的字段
        //接收403页面来的参数请求
        $params = $this->request->param();
        if (strlen($params['code']) == 32) {
            //表示验签参数可能有效，接下来进行验证,先查缓存，缓存不存在则查数据库
            if (Cache::has($params['code'])) {
                $queryStr = Cache::get($params['code']);
            } else {
                $queryStr = $this->urlModel->where(['check_code'=>$params['code']])->find()['query_string'];
            }
            $str = md5(explode('&check_code',$queryStr)[0]);
            //处理字符串为键值对的数组
            $condition = $this->do403Params($queryStr);
            //查询当前团队使用的是哪种支付方式,通过渲染方式不同，落地方法不一样，流程不一样。
            //根据推广链接403入口，来决定是走哪种支付方式，不同的支付方式，需要不同地流程与渲染，落地，成交，支付
            //获取开启的域名池，默认开启轮询操作
            $payPool = collection($this->paysetModel->where(['team_id'=>$condition['tid'],'status'=>1])->select())->toArray();
            Cache::set('403-paypool',$payPool,120);
            //根据查询出来的数据，生成支付通道。获取指定的支付域名。
            $payInfo = $this->getPayChannel($payPool,$params['code']);
            Cache::set('403-payinfo',$payInfo,600);
            if (false === $payInfo || $payInfo['status'] == 0) {
                //表示没有支付
                return $this->return500Error("支付通道无效，请联系老板！！！");
            }

            //对参数进行验证
            if ($str === $params['code']) {
                //获取落地域名，一个个的消耗。
                $luckDomain = $this->getLuckDomain();
                //更新支付使用情况，只要生成一次落地就使用一次支付
                $this->paysetModel->where(['team_id'=>$condition['tid'],'pay_id'=>$payInfo['id']])->setInc("count");
                //根据不同的支付类型，跳转不同的支付方法与落地页面
                $wholeDomain = 'http://'.time().'.'.$luckDomain.'/index.php/index/index/index'.$payInfo['type'].'?'.$queryStr;
                echo "handler('successcode','{$wholeDomain}')";
                die;
            } else {
                //表示验证失败，目前暂时没做。后台响应
                echo "handler('failure','https://news.qq.com')";
                die;
            }
        } else {
            //表示该访问方法为分享二维码，落地
            $params['code'] = $params['code'].'==';/*为防止传输过程中丢失特殊符号，落地拼接*/
            $checkCode = explode(base64_encode('shop'),$params['code'])[0];
            //表示验签参数可能有效，接下来进行验证,先查缓存，缓存不存在则查数据库
            if (Cache::has($checkCode)) {
                $queryStr = Cache::get($checkCode);
            } else {
                $queryStr = $this->urlModel->where(['check_code'=>$checkCode])->find()['query_string'];
            }
            $str = md5(explode('&check_code',$queryStr)[0]).base64_encode('shop');
            //对share_code 进行验证
            if ($str === $params['code']) {
                $luckDomain = $this->getLuckDomain();
                //根据不同的支付类型，跳转不同的支付方法与落地页面
                $wholeDomain = 'http://'.time().'.'.$luckDomain.'/index.php/index/index/share?'.$queryStr.base64_encode('shop');
                echo "handler('successcode','{$wholeDomain}')";
                die;
            } else {
                //表示验证失败，目前暂时没做。后台响应
                echo "handler('failure','http://www.qq.com')";
                die;
            }
        }
    }

    /**
     * 分享码落地展示页面
     * @throws \think\Exception
     */
    public function share()
    {
        if (!$this->isWx()) {
            return $this->return500Error('请用微信打开页面~~');
        }
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        if (empty($params)) {
            return $this->return500Error('请使用正确的链接进行访问！！');
        }
        //aid=12&gid=5&tid=6&tp=shoes4&check_code=247740f159df08abc570a30c89e4e412c2hvcA==
        if (!$this->verifyShareCode($params)) {
            //表示验证失败，链接被篡改
            return $this->return500Error('请不要使用非法手段更改链接');
        }
        //判断访问链接是否属于正常状态
        $where = ['admin_id'=>$params['aid'],'team_id'=>$params['tid'],'production_id'=>$params['gid'],'check_code'=>$params['check_code']];
        $urlQrStatus = $this->urlModel->where($where)->find();
        if ($urlQrStatus['share_code_status'] == 1) {
            return $this->return500Error('活动已经结束');
        }
        $userInfo = $this->adminModel->get($params['aid']);
        //获取团队推广商品数据
        $goodsData = $this->getSelectGoodsInfo($params['tid'],$params['gid']);
        //获取访问者IP
        $userIp  = $this->request->ip();
        //更新链接访问次数
        $url = $this->request->url(true);
        $visitIp = $userIp.'-share-'.$url;
        //设置当前链接缓存
        $urlData = [
            'admin_id'  => $params['aid'],
            'date'      => date('m-d',time()),
            'url'       => $visitIp,
            'team_id'   => $params['tid'],
            'production_id'     => $params['gid'],
            'check_code'        => $params['check_code'],
            'type'              => 1,
            'count'             => 1
        ];
        //如果今天已经存在访问链接 ，就不在记录
        //TODO:: 访问记录这个数据暂时只是考虑落地一次记录访问次数，刷新或者重新打开不计算
        if (!Cache::has($visitIp)) {
            Cache::set($visitIp,$visitIp,$this->getDiscountTime());
            $this->urlModel->where(['admin_id'=>$params['aid'],'share_code'=>$params['check_code']])->setInc('count');
            $this->visitModel->isUpdate(false)->save($urlData);
            //进行数据统计,分享有礼暂时不进行报表数据写入
        } else {
            $this->visitModel->where('url',$visitIp)->setInc('count');
        }
        //将本团队的商品数据缓存起来
        $data = [
            'aid'       => $params['aid'],//业务员id值（必填）
            'tid'       => $params['tid'],//团队名称（必填）
            'pid'       => $userInfo['pid'],//业务员上级id（必填）
            'gid'       => $params['gid'],
            'tongji'    => $goodsData['tongji'],
            'production_name'   => $goodsData['own_name'] == '' ? $goodsData['production_name'] : $goodsData['own_name'],//商品名称（必填）
            'share_code'        => $params['check_code'],//链接检验码
            'shop_url'          => $this->urlModel->where('share_code',$params['check_code'])->find()['url'],
        ];

        $this->assign('data',$data);
        return $this->view->fetch('lndex/shareover');
    }
    
    /**
     * 根据支付池里面的数据，生成支付参数
     * @param $data
     * @param $checkCode
     * @return bool|array
     */
    public function getPayChannel($data,$checkCode)
    {
        if (!empty($data)) {
            if (count($data) == 1) {
                //表示只有一个支付通道
                return $this->getPayInfo($data[0]['type'],$data[0]['pay_id'],$checkCode);
            } else {
                //表示有多个支付通道，进行随机抽取。
                $res =  $data[mt_rand(0,count($data)-1)];
                Cache::set('pay_mt_rand',$res,120);
                return $this->getPayInfo($res['type'],$res['pay_id'],$checkCode);
            }
        } else {
            //表示已经没有支付通道使用了
            return false;
        }
    }

}
