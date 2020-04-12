<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Cache;
use think\Env;
use app\admin\model\production\Production_select as SelectModel;

/**
 * 模板渲染
 * Class Index
 * @package app\index\controller
 */
class Index extends Frontend
{

    protected $selectModel = null;
    public function _initialize()
    {
        parent::_initialize();
        $this->selectModel = new SelectModel();
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

        //获取支付配置相关信息
        //$payConfig = $this->getPayInfo($params['tid']);

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
        Cache::set($params['check_code'],$data);
        //组建跳转的落地域名

        $this->assign('data',$data);
        return $this->view->fetch($params['tp']);
    }

    /**
     * 最终落地页面,403请求接口
     */
    public function loadGround()
    {
        if ($this->request->isAjax()) {
            //表示403页面发来的请求
            Cache::set('403params',$this->request->param());
            return json_encode(['code'=>'successcode','data'=>'http://www.baidu.com']);
        } else {
            return json_encode(['code'=>'failure','data'=>'http://www.qq.com']);
        }
    }

}
