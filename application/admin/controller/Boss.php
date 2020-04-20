<?php

namespace app\admin\controller;

use app\admin\model\AdminLog;
use app\common\controller\Backend;
use think\Cache;
use think\Config;
use think\Cookie;
use think\Env;
use think\Hook;
use think\Session;
use think\Validate;

/**
 * 后台首页
 * @internal
 */
class Boss extends Backend
{

    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }

    /**
     * 后台首页
     */
    public function index()
    {
        //左侧菜单
        list($menulist, $navlist, $fixedmenu, $referermenu) = $this->auth->getSidebar([
            'dashboard' => 'hot',
            'addon'     => ['new', 'red', 'badge'],
            'auth/rule' => __('Menu'),
            'general'   => ['new', 'purple'],
        ], $this->view->site['fixedpage']);
        $action = $this->request->request('action');
        if ($this->request->isPost()) {
            if ($action == 'refreshmenu') {
                $this->success('', null, ['menulist' => $menulist, 'navlist' => $navlist]);
            }
        }
        $this->view->assign('menulist', $menulist);
        $this->view->assign('navlist', $navlist);
        $this->view->assign('fixedmenu', $fixedmenu);
        $this->view->assign('referermenu', $referermenu);
        $this->view->assign('title', __('Home'));
        return $this->view->fetch();
    }

    /**
     * 管理员登录
     */
    public function login()
    {
        //登录前置方法
        $paramSn = $this->request->param();
        $url = $this->request->get('url', 'index/index');
        if ($this->auth->isLogin()) {
            $this->success(__("You've logged in, do not login again"), $url);
        }
        if ($this->request->isPost()) {

            //记录当前访问参数与用户代理 与IP绑定
            $agent = $this->request->header('user-agent');
            $userIp = $this->request->ip();
            $urlLogin = $this->request->url(true);

            //缓存登录链接数据
            Cache::set($userIp.'-'.$agent,$urlLogin,86400);
            $this->loginUrl = $urlLogin;
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $keeplogin = $this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                '__token__' => 'require|token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                '__token__' => $token,
            ];

            //加密登录入口判断
            $snStr = null;
            $newSnData = [];
            if (isset($paramSn['sn'])) {
                $snStr = base64_decode(urldecode($paramSn['sn']));
                $snData = explode('&',$snStr);
                foreach ($snData as $value) {
                    $newSnData[explode('=',$value)[0]] = explode('=',$value)[1];
                }
            }
            //除平台管理员外，所有用户必须带有参数进入
            if (isset($newSnData['pid']) && $newSnData['pid'] == 0 && $newSnData['uname'] == strtolower($username) || strtolower($username) == Env::get('app.admin','linux')) {

                if (Config::get('fastadmin.login_captcha')) {
                    $rule['captcha'] = 'require|captcha';
                    $data['captcha'] = $this->request->post('captcha');
                }
                $validate = new Validate($rule, [], ['username' => __('Username'), 'password' => __('Password'), 'captcha' => __('Captcha')]);
                $result = $validate->check($data);
                if (!$result) {
                    $this->error($validate->getError(), $urlLogin, ['token' => $this->request->token()]);
                }
                AdminLog::setTitle(__('Login'));
                $result = $this->auth->login($username, $password, $keeplogin ? 86400 : 0);

                if ($result === true) {
                    Hook::listen("admin_login_after", $this->request);
                    $this->success(__('Login successful'), 'index/index', ['url' => 'index/index', 'id' => $this->auth->id, 'username' => $username, 'avatar' => $this->auth->avatar]);
                } else {
                    $msg = $this->auth->getError();
                    $msg = $msg ? $msg : __('Username or password is incorrect');
                    $this->error($msg, $urlLogin, ['token' => $this->request->token()]);
                }
            } else {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg :'请使用正确的登录链接进行登录';
                $this->error($msg, $urlLogin, ['token' => $this->request->token()]);
            }


        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin()) {
            $this->redirect($url);
        }
        $background = Config::get('fastadmin.login_background');
        $background = stripos($background, 'http') === 0 ? $background : config('site.cdnurl') . $background;
        $this->view->assign('background', $background);
        $this->view->assign('title', __('Login'));
        Hook::listen("admin_login_init", $this->request);
        return $this->view->fetch();
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $loginUrl = $this->adminInfo['login_url'];
        $this->auth->logout();
        Session::delete("admin");
        Cookie::delete("keeplogin");
        Hook::listen("admin_logout_after", $this->request);
        $this->success(__('Logout successful'), $loginUrl);
    }

}
