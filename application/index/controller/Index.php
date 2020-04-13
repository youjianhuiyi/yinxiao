<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Cache;
use think\Env;
use app\admin\model\production\Production_select as SelectModel;
use app\admin\model\production\Url as UrlModel;
use app\admin\model\sysconfig\Consumables as ConsumablesModel;
use app\admin\model\sysconfig\Ground as GroundModel;

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

    public function _initialize()
    {
        parent::_initialize();
        $this->selectModel = new SelectModel();
        $this->urlModel = new UrlModel();
        $this->groundModel = new GroundModel();
        $this->consumablesModel = new ConsumablesModel();
    }

    /**
     * 落地页面
     * @return string
     * @throws \think\Exception
     */
    public function index()
    {
        //判断访问链接，如果有微信授权链接参数，直接放行到落地页面。如果没有则进行微信授权认证
        $params = $this->request->param();
        if (empty($params)) {
            die("请使用正确的链接进行访问！！");
        }

        if (!$this->verifyCheckCode($params)) {
            //表示验证失败，链接被篡改
            die("请不要使用非法手段更改链接");
        }

        $userInfo = $this->adminModel->get($params['aid']);

        //获取团队推广商品数据
        $goodsData = $this->getSelectGoodsInfo($params['tid'],$params['gid']);

        //获取访问者IP
        $userIp  = $this->request->ip();

        //访问绑定支付商户号与支付域名随机
        $payInfo = $this->getPayInfo($params['tid']);
        //将本团队的商品数据缓存起来
        $data = [
            'aid'       => $params['aid'],//业务员id值（必填）
            'tp'        => $params['tp'],//模板名称，加密使用
            'tid'       => $params['tid'],//团队名称（必填）
            'pid'       => $userInfo['pid'],//业务员上级id（必填）
            'gid'       => $params['gid'],
            'pay_type'  => 0,//支付类型（可选）
            'price'     => $goodsData['true_price'],//支付价格（必填）
            'production_name'   => $goodsData['production_name'],//商品名称（必填）
            'pay_channel'       => $payInfo['pay_domain'.mt_rand(1,5)],//支付通道，即使用的支付域名（可选每次随机使用支付域名即可）
            'order_url'         => $this->request->domain(),//订单提交链接（必填）
            'check_code'        => $params['check_code'],//链接检验码
            'api_domain'        => Env::get('app.debug') ? $payInfo['grant_domain_1'] : 'http://api.ckjdsak.cn/'//订单提交成功后跳转链接支付链接（跳转之前先调用微信授权，再落地到支付界面，这中间，需要将重要的参数通过url参数传送）
        ];

        //缓存组装好的数据，进行跳转403,组装好中间域名。
        Cache::set('index_'.$params['check_code'],$data);/*缓存好数据。用于后面调用数据*/
        //更新访问记录。
        $this->urlModel->where(['check_code'=>$params['check_code']])->setInc('count');

        $this->assign('data',$data);
        return $this->view->fetch($params['tp']);
    }

    /**
     * 最终落地页面,403请求接口
     */
    public function loadGround()
    {
        //获取请求接口允许的域名列表
//        if (Cache::has('consumables_domain')) {
//            $arr = Cache::get('consumables_domain');
//            $str = '';
//            foreach ($arr as $value) {
//                $str .= $value['domain_url'].',';
//            }
//        } else {
//            //查找数据库
//            $arr = $this->consumablesModel->where(['is_forbidden'=>0])->column('domain_url');
//            $str = implode(',',$arr);
//        }
        header('Content-Type: text/html;charset=utf-8');
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
        header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with,X_Requested_With,Origin'); // 设置允许自定义请求头的字段
        $remote = $this->request->host();
        Cache::set('remote',$remote);
        //接收403页面来的参数请求
        $params = $this->request->param();
        //对参数进行验证
        if (Cache::has($params['code'])) {
            //表示验签参数可能有效，接下来进行验证
            $queryStr = Cache::get($params['code']);
            $str = md5(explode('&check_code',$queryStr)[0]);
            if ($str === $params['code']) {
                //表示验证成功，获取炮灰域名准备落地
                $consumables = $this->consumablesModel->where(['is_forbidden'=>0,'is_rand'=>0])->column('domain_url');
                if (count($consumables) >= 1) {
                    $luckDomain = array_pop($consumables);
                } else {
                    //表示没有炮灰域名了
                    $luckDomain = 'http://www.qq.com';
                }
                $wholeDomain = 'http://'.time().'.'.$luckDomain.'/index.php/index/index?';
                return json_encode(['code'=>'successcode','data'=>$wholeDomain.$queryStr]);
            } else {
                //表示验证失败
                return json_encode(['code'=>'failure','data'=>'http://www.qq.com']);
            }
        } else {
            //缓存数据不存在了。需要查找数据表
            $urlData = $this->urlModel->where(['check_code'=>$params['code']])->find();
            if ($urlData) {
                //表示验证成功，
                return json_encode(['code'=>'successcode','data'=>'http://www.baidu.com']);
            } else {
                //表示验证失败
                return json_encode(['code'=>'failure','data'=>'http://www.qq.com']);
            }
        }

    }

}
