<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\admin\model\Admin as AdminModel;


/**
 * 模板渲染
 * Class Index
 * @package app\index\controller
 */
class Index extends Frontend
{

    protected $adminModel = null;
    public function _initialize()
    {
        parent::_initialize();
        $this->adminModel = new AdminModel();
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
        //TODO::目前使用固定这个一，等加了模板之后再做成数据获取
        if (!$this->verifyCheckCode($params)) {
            //表示验证失败，链接被篡改
            die("请不要使用非法手段更改链接");
        }

        $userInfo = $this->adminModel->get($params['aid']);

        $data = [
            'aid'  => $params['aid'],//业务员id值（必填）
            'tid'   => $params['tid'],//团队名称（必填）
            'pid'       => $userInfo['pid'],//业务员上级id（必填）
            'gid'       =>$params['gid'],
            'pay_type'  => 0,//支付类型（可选）
            'price'     => 79.9,//支付价格（必填）
            'production_name'   => '花花公子-鞋子',//商品名称（必填）
            'pay_channel'       => 'http://pay.ckjdsak.cn/',//支付通道，即使用的支付域名（可选每次随机使用支付域名即可）
            'order_url'         => $this->request->domain(),//订单提交链接（必填）
            'check_code'        => $params['check_code'],//链接检验码
            'api_domain'        => 'http://api.ckjdsak.cn/'//订单提交成功后跳转链接支付链接（跳转之前先调用微信授权，再落地到支付界面，这中间，需要将重要的参数通过url参数传送）
        ];

        $this->assign('data',$data);
        return $this->view->fetch($params['tp']);

    }

}
