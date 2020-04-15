<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
        $this->redis = new \Redis();//实例化redis
        $this->redis->connect('127.0.0.1', 6379); //链接reids
        $this->redis->auth(C('REDIS')); //链接密码
    }

    public function show_article()
    {
        $type = I("get.type");
        if ($type == '') {
            echo "额,不好意思,你的链接没有参数啊.";
            // echo "handler(123456789,'Https://www.qq.com')";
            die;
        }
        //替换展示页的干扰码
        $article = "Application/Home/View/Index/" . $type . ".html";
        $body = file_get_contents($article);
        // dump($body);
        // dump($aaa);die;
        $replace_body["ganrao_type"] = 'body';
        $sjstr = M("Ganrao")->where($replace_body)->order("rand()")->limit(1)->find();
        $muns = rand(1, 100);
        if (1 <= $muns && $muns < 30) {
            $replace = "<span style='display:none'>" . $sjstr["no1"] . "</span>";
        } elseif (31 <= $muns && $muns < 60) {
            $replace = "<div style='display:none'>" . $sjstr["no1"] . "</div>";
        } elseif (61 <= $muns && $muns < 90) {
            $replace = "<p style='display:none'>" . $sjstr["no1"] . "</p>";
        } elseif (91 <= $muns && $muns <= 100) {
            $replace = "<H1 style='display:none'>" . $sjstr["no1"] . "</H1>";
        }
        //替换head的干扰码
        $replace_index_head["ganrao_type"] = 'head';
        $sjstr_head_str = M("Ganrao")->where($replace_index_head)->order("rand()")->limit(1)->find();
        $muns = rand(1, 100);
        if (1 <= $muns && $muns < 40) {
            $sjstr_head_replace = "<!--  " . $sjstr_head_str["no1"] . " -->";
        } elseif (41 <= $muns && $muns < 80) {
            $sjstr_head_replace = $sjstr_head_str["no1"];
        } elseif (81 <= $muns && $muns < 90) {
            $sjstr_head_replace = "<script>//" . $sjstr_head_str["no1"] . "</script>";
        } elseif (91 <= $muns && $muns <= 100) {
            $sjstr_head_replace = '';
        }
        $body = str_replace("ht4RjzIC2lFcG6oM7vQfBYwNTsZJnmKDbXtrkUPgdWhxu5OLqSa3", $sjstr_head_replace, $body);
        $body = str_replace("RSWtdIfAbwVcpJFBZ93Uigor8u4PzDGO6jQlY7xLqMyekmKhas", $replace, $body);
        //替换所有cdn链接,改成鉴权方式
        // dump($body);die;
        $preg = '/https:\/\/article.artgeek.com.cn[^\s*]*["|\']/i';
        preg_match_all($preg, $body, $matche, PREG_PATTERN_ORDER);
        // dump($matche);die;
        foreach ($matche[0] as $value) {
            $url = $value;
            //去掉最后一个字符"
            $url = substr($url, 0, -1);
            //失效时间,有效期固定为1800S,所以需要后调,控制有效期.
            $date = date("YmdHi", strtotime("-1500 seconds"));
            // 设定的秘钥,必须和阿里cdn保持一致
            $PrivateKey = "Mk7IbKB8tDvLsUVf3jQrd4FZ9O";
            //获取链接文件名,通过域名隔断
            $arr = explode('article.artgeek.com.cn', $url);
            $FileName = $arr[1];
            //构建加密链接
            $MD5code = $PrivateKey . $date . $FileName;
            $randstr = MD5($MD5code);
            $newurl = "https://article.artgeek.com.cn/" . $date . "/" . $randstr . $FileName;
            //替换页面中所有已有链接,变成鉴权URL
            $body = str_replace($url, $newurl, $body);
        }
        $aaa = base64_encode($body);
        $muns_index = rand(20, 50);
        $sui_code = $this->sjCode($muns_index);
        $aaa = $sui_code . $aaa;
        setcookie("pagecode", $this->sjCode(20));
        //替换框架的body干扰码
        $index_article = "Application/Home/View/Index/index.html";
        $content = file_get_contents($index_article);
        $replace_index_body["ganrao_type"] = 'body';
        $index_str = M("Ganrao")->where($replace_index_body)->order("rand()")->limit(1)->find();
        $muns = rand(1, 100);
        if (1 <= $muns && $muns < 30) {
            $html_replace = "<span style='display:none'>" . $index_str["no1"] . "</span>";
        } elseif (31 <= $muns && $muns < 60) {
            $html_replace = "<div style='display:none'>" . $index_str["no1"] . "</div>";
        } elseif (61 <= $muns && $muns < 90) {
            $html_replace = "<p style='display:none'>" . $index_str["no1"] . "</p>";
        } elseif (91 <= $muns && $muns <= 100) {
            $html_replace = "<H1 style='display:none'>" . $index_str["no1"] . "</H1>";
        }
        $content = str_replace("nooOTNtdumlKUVysBkFbqJIaeLGfxpr2SwnQ6RzjvigDXYZ5MhEP", $html_replace, $content);

        //替换head的干扰码
        $replace_index_head["ganrao_type"] = 'head';
        $index_head_str = M("Ganrao")->where($replace_index_head)->order("rand()")->limit(1)->find();
        $muns = rand(1, 100);
        if (1 <= $muns && $muns < 40) {
            $head_replace = "<!--  " . $index_head_str["no1"] . " -->";
        } elseif (41 <= $muns && $muns < 80) {
            $head_replace = $index_head_str["no1"];
        } elseif (81 <= $muns && $muns < 90) {
            $html_replace = "<script>//" . $index_head_str["no1"] . "</script>";
        } elseif (91 <= $muns && $muns <= 100) {
            $head_replace = '';
        }
        $content = str_replace("htoOTNtdumlKUVysBkFbqJIaeLGfxpr2SwnQ6RzjvigDXYZ5MhEP", $head_replace, $content);

        //替换js的干扰码
        $replace_index_js["ganrao_type"] = 'js';
        $index_js_str = M("Ganrao")->where($replace_index_js)->order("rand()")->limit(1)->find();

        $js_replace = "//" . $index_js_str["no1"];
        $content = str_replace("jsOTNtdumlKUVysBkFbqJIaeLGfxpr2SwnQ6RzjvigDXYZ5MhEP", $js_replace, $content);
        //替换index页面的cdn链接,修改成为借权
        $preg = '/https:\/\/article.artgeek.com.cn[^\s*]*["|\']/i';
        preg_match_all($preg, $content, $matche, PREG_PATTERN_ORDER);
        // dump($matche);die;
        foreach ($matche[0] as $value) {
            $url = $value;
            //去掉最后一个字符"
            $url = substr($url, 0, -1);
            //失效时间,有效期固定为1800S,所以需要后调,控制有效期.
            $date = date("YmdHi", strtotime("-1500 seconds"));
            // 设定的秘钥,必须和阿里cdn保持一致
            $PrivateKey = "Mk7IbKB8tDvLsUVf3jQrd4FZ9O";
            //获取链接文件名,通过域名隔断
            $arr = explode('article.artgeek.com.cn', $url);
            $FileName = $arr[1];
            //构建加密链接
            $MD5code = $PrivateKey . $date . $FileName;
            $randstr = MD5($MD5code);
            $newurl = "https://article.artgeek.com.cn/" . $date . "/" . $randstr . $FileName;
            //替换页面中所有已有链接,变成鉴权URL
            $content = str_replace($url, $newurl, $content);
        }
        // $this->assign('adv_type',0);
        $this->assign('article', $aaa);
        $this->assign('is_weixin_type', $is_weixin_type);
        $this->assign('muns', $muns_index);
        $this->show($content);
    }

    public function gate()
    {
        $canshu = I("get.code");
        $userinfo = I("get.userinfo");
        if ($canshu == '') {
            echo "额,不好意思,你的链接没有参数啊.";
            die;
        }
        //查询信息
        $re = $this->redis->get($canshu);
        if (empty($re)) {   //没有信息
            $a["sui_code"] = $canshu;
            $re = M("Link")->where($a)->find();
            $rediscode = $re["sui_code"];  //构建redis唯一key值
            $re = serialize($re);          // 序列化
            $this->redis->set($rediscode, $re);
            $this->redis->setTimeout($rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $re = unserialize($re);
        if ($re == '') {
            echo "文案参数信息错误";
            die;
        }
        //查询系统总设置信息
        $wx_vi = $this->redis->get("admin_set");
        if (empty($wx_vi)) {   //没有信息
            $sql["user_type"] = 999;   //构建查询条件
            $wx_vi = M("Password")->where($sql)->find();
            // $rediscode = $uv_type["sjcode"];  //构建redis唯一key值
            $wx_vi = serialize($wx_vi);          // 序列化
            $this->redis->set("admin_set", $wx_vi);
            $this->redis->setTimeout("admin_set", 30 * 1);
        }
        $wx_vi = unserialize($wx_vi);

        $is_weixin = $this->is_weixin();
        if ($re['article_type'] == 'QP_2.html') {
            //不限制浏览器
        } else {
            if (!$is_weixin) {
                echo "不好意思,这个链接我不让你浏览器打开";
                die;
            }
        }
        // }
        //查询域名信息
        $com_url = $_SERVER["HTTP_REFERER"];
        // $com_url = "https://www.baidu.com";
        $sui_code = $re["sui_code"];
        $usercode = $re["username"];
        $this->show_the_article($re, $wx_vi, $usercode, $sui_code, $com_url, $userinfo);

    }

    public function pcshow()
    {
        $canshu = I("get.code");
        $userinfo = I("get.userinfo");
        // dump($userinfo);die;
        if ($canshu == '') {
            echo "额,不好意思,你的链接没有参数啊.";
            die;
        }

        //查询信息
        $re = $this->redis->get($canshu);
        if (empty($re)) {   //没有信息
            $a["sui_code"] = $canshu;
            // dump($a);die;
            $re = M("Link")->where($a)->find();
            $rediscode = $re["sui_code"];  //构建redis唯一key值
            $re = serialize($re);          // 序列化
            $this->redis->set($rediscode, $re);
            $this->redis->setTimeout($rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $re = unserialize($re);
        if ($re == '') {
            // header("Location:http://back.ykdsht.cn/back/index?p=1e89e85d554cb1a68438006371cc0d5e");
            echo "文案参数信息错误";
            die;
        }
        // dump($re);die;
        //查询信息
        $wx_vi = $this->redis->get("admin_set");
        if (empty($wx_vi)) {   //没有信息
            $sql["user_type"] = 999;   //构建查询条件
            $wx_vi = M("Password")->where($sql)->find();
            // $rediscode = $uv_type["sjcode"];  //构建redis唯一key值
            $wx_vi = serialize($wx_vi);          // 序列化
            $this->redis->set("admin_set", $wx_vi);
            $this->redis->setTimeout("admin_set", 30 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $wx_vi = unserialize($wx_vi);
        //714注释
        if ($wx_vi["wx_visit"] == 0) {
            $is_weixin = $this->is_weixin();
            if (!$is_weixin) {
                echo "不好意思,这个链接我不让你浏览器打开";
                // echo "handler(123456789,'Https://www.qq.com')";
                die;
            }
        }
        //查询域名信息
        $com_url = "https://www.baidu.com";
        $sui_code = $re["sui_code"];
        $usercode = $re["username"];
        $this->creat_aritcle($usercode, $sui_code, $com_url, $userinfo);

    }

    //https协议地址
    public function show_page()
    {
        $sui_code = I("get.code");
        $usercode = I("get.ucode");
        $com_url = "https://www.baidu.com";
        $this->creat_aritcle($usercode, $sui_code, $com_url);
    }

    //检查IP访问次数
    protected function getIpVisitMuns($usercode, $IP, $type)
    {
        if ($type == 'muns') {  //获取访问改IP访问多少次
            $is_newIP = $this->redis->get('is_newIP' . $usercode . $IP);
            if (empty($is_newIP)) {   //没有信息
                //数据库匹配
                $SQL['iplist'] = $IP;
                $SQL['username'] = $usercode;
                $is_newIPArr = M("Iptables")->where($SQL)->find();
                if ($is_newIPArr) {
                    $is_newIPcode = 'is_newIP' . $usercode . $IP;
                    $is_newIP = $is_newIPArr["vi_muns"];          // 序列化
                    $this->redis->set($is_newIPcode, $is_newIP);
                    $this->redis->setTimeout($is_newIPcode, 3600 * 24 * 30);
                } else {
                    $is_newIPcode = 'is_newIP' . $usercode . $IP;
                    $is_newIP = 1;          // 序列化
                    $this->redis->set($is_newIPcode, $is_newIP);
                    $this->redis->setTimeout($is_newIPcode, 3600 * 24 * 30);
                }
            } else {
                //不是一次访问
                $is_newIPcode = 'is_newIP' . $usercode . $IP;
                $is_newIP = $is_newIP + 1;          // 序列化
                $this->redis->set($is_newIPcode, $is_newIP);
                $this->redis->setTimeout($is_newIPcode, 3600 * 24 * 30);
            }
            //5次记录到reids一次
            if ($is_newIP % 5 == 0) { //每隔5次记录一次到数据库
                //查询数据库是否有记录
                $sql["iplist"] = $IP;  //构建查询条件
                $sql["username"] = $usercode;  //构建查询条件
                $ipre = M("Iptables")->where($sql)->find();
                if ($ipre) {   //有记录,需要跟新
                    $saveIPlist["vi_muns"] = $ipre["vi_muns"] + 5;
                    $ipid["id"] = $ipre["id"];
                    $ipid["username"] = $usercode;
                    M("Iptables")->where($ipid)->save($saveIPlist);
                } else {  //没有记录,直接add
                    $saveIPlist["vi_muns"] = 5;
                    $saveIPlist["iplist"] = $IP;
                    $saveIPlist["username"] = $usercode;
                    M("Iptables")->add($saveIPlist);
                }
            }
        }
        return $is_newIP;
    }

    protected function MD5URL($url)
    {
        //失效时间,有效期固定为1800S,所以需要后调,控制有效期.
        $date = date("YmdHi", strtotime("-1500 seconds"));
        // 设定的秘钥,必须和阿里cdn保持一致
        $PrivateKey = "Mk7IbKB8tDvLsUVf3jQrd4FZ9O";
        //获取链接文件名,通过域名隔断
        $arr = explode('article.artgeek.com.cn', $url);
        $FileName = $arr[1];
        //构建加密链接
        $MD5code = $PrivateKey . $date . $FileName;
        $randstr = MD5($MD5code);
        $newurl = "https://article.artgeek.com.cn/" . $date . "/" . $randstr . $FileName;
        return $newurl;
    }

    protected function show_the_article($re, $wx_vi, $usercode, $sui_code, $com_url, $userlinkinfo)
    {
        header(" Expires: Mon, 26 Jul 1970 05:00:00 GMT ");
        header(" Last-Modified:" . gmdate(" D, d M Y H:i:s ") . "GMT ");
        header(" Cache-Control: no-cache, must-revalidate ");
        header(" Pragma: no-cache ");
        //落地页限制手机微信.
        // if (!$com_url) {
        // 	header("Location:http://www.qq.com");
        // 	die;
        // }
        // dump($wx_vi);die;
        //查询用户信息
        $useinfo = $this->redis->get($usercode);
        if (empty($useinfo)) {   //没有信息
            $usersjcode["sjcode"] = $usercode;  //构建查询条件
            $useinfo = M("Password")->where($usersjcode)->find();
            $rediscode = $usercode;  //构建redis唯一key值
            $useinfo = serialize($useinfo);          // 序列化
            $this->redis->set($rediscode, $useinfo);
            $this->redis->setTimeout($rediscode, 30 * 1);
        }
        $useinfo = unserialize($useinfo);
        //没有文案账户信息
        if (!$useinfo) {
            header("Location:http://www.baidu.com");
            die;
        }
        //查询统计
        $all_cnzz = $this->redis->get("all_cnzz_" . $usercode);
        if (empty($all_cnzz)) {   //没有信息
            $usercnzz["username"] = $re["username"];
            $all_cnzz = M("Allcnzz")->where($usercnzz)->order("id desc")->find();
            $all_cnzz_rediscode = "all_cnzz_" . $usercode;  //构建redis唯一key值
            $all_cnzz = serialize($all_cnzz);          // 序列化
            $this->redis->set($all_cnzz_rediscode, $all_cnzz);
            $this->redis->setTimeout($all_cnzz_rediscode, 300 * 1);
        }
        $all_cnzz = unserialize($all_cnzz);

        $IP = getIp();

        //获取html代码
        $HtmlCode = $this->GetHtmlCode('Index/' . $re['article_type']);
        $HtmlCode = str_replace("USERSETCNZZFORTOTAL", $cnzzrepla, $HtmlCode);
        $HtmlCode = str_replace("USERSETCNZZFORONELINK", $cnzzrepla, $HtmlCode);
        $HtmlCode = str_replace("ALLCNZZREPLACEFORMANAGE", $cnzzrepla, $HtmlCode);
        $HtmlCode = str_replace("SERVERTOTALCNZZ", $cnzzrepla, $HtmlCode);
        //替换引入的js
        $jsurl = $this->MD5URL('https://article.artgeek.com.cn/A_common/js/getarticleshowinfo_2.js');
        $visitByhttp = '<script src="' . $jsurl . '" ></script>';
        $js2url = $this->MD5URL('https://article.artgeek.com.cn/A_common/js/behavior.js');
        $http_show = '<script src="' . $js2url . '" ></script>';
        $HtmlCode = str_replace("VISITBYHTTPCODE", $visitByhttp, $HtmlCode);
        $HtmlCode = str_replace("VISITBYHTTPSSSSCODE", '', $HtmlCode);
        $HtmlCode = str_replace("SHOWUSERACTIONBYHTTPONLY", $http_show, $HtmlCode);
        $HtmlCode = str_replace("SHOWUSERACTIONBYHTTPSSSS", '', $HtmlCode);
        //去掉静默复制函数
        $HtmlCode = str_replace("JINGMOCODE", '', $HtmlCode);
        //静默复制input框
        $HtmlCode = str_replace("JIMOINPUTCOPY", '', $HtmlCode);


        //是否开启返回逻辑
        $str = '';
        $ipVisitMuns = $this->getIpVisitMuns($usercode, $IP, 'muns');
        //检查是否开启了全局返回.
        //查询总设置信息
        $serverSet = $this->redis->get("adminSet");
        if (empty($serverSet)) {   //没有信息
            $uscode["user_type"] = 999;  //构建查询条件
            $uscode["wxname"] = '0';  //构建查询条件
            $serverSet = M("Password")->where($uscode)->find();
            $serverSetcode = "adminSet";  //构建redis唯一key值
            $serverSet = serialize($serverSet);          // 序列化
            $this->redis->set($serverSetcode, $serverSet);
            $this->redis->setTimeout($serverSetcode, 20 * 1);
        }
        $serverSet = unserialize($serverSet);
        // dump($ipVisitMuns);
        // dump($serverSet);
        // dump($ipVisitMuns <= $serverSet['fanhuimuns']);
        // dump($serverSet['fanhuitype'] == 1);die;
        if ($serverSet['fanhuitype'] == 1) { //开启了全局返回
            //开启全局返回
            if ($ipVisitMuns <= $serverSet['fanhuimuns']) {

                //7-11点永远不挂
                if (date("Hi") < 1100 && date("Hi") > 0700) {
                    $adv_type = 0;
                } else {
                    //固定用户悬挂
                    //////////定制功能/////开始////限制阿助账号yuangong//////
                    $rangadvmuns = mt_rand(0, 100);
                    if ($rangadvmuns <= 100) { //挂一半概率
                        $adv_type = 1;//挂返回
                    } else {
                        $adv_type = 0;
                    }
                    $str = "'" . $serverSet['fanhui'] . "'";
                }
            } else {
                $adv_type = 0;
            }
        } else {
            if ($useinfo["is_adv"] == 1 && $ipVisitMuns <= $useinfo["cnzz_muns"]) { //开启了返回
                //查询信息
                $adv = $this->redis->get("adv:" . $usercode);
                if (empty($adv)) {   //没有信息
                    $adv_chooose["vi_type"] = 0;
                    $adv_chooose["username"] = $usercode;
                    $adv = M("Advlinks")->where($adv_chooose)->select();
                    $adv_rediscode = "adv:" . $usercode;  //构建redis唯一key值
                    $adv = serialize($adv);          // 序列化
                    $this->redis->set($adv_rediscode, $adv);
                    $this->redis->setTimeout($adv_rediscode, 30 * 1);
                }
                $adv = unserialize($adv);
                foreach ($adv as $value) {
                    if ($value["adv_link"] == '') {
                    } else {
                        $str .= "'" . $value["adv_link"] . "',";
                    }
                }
                $lenth = strlen($str);
                $str = substr($str, 0, $lenth - 1);
                //7-11点永远不挂
                if (date("Hi") < 1100 && date("Hi") > 0700) {
                    $adv_type = 0;
                } else {
                    $adv_type = 1;
                }
            } else {
                $adv_type = 0;
            }
        }


        $userfenliucnzz = $this->redis->get($userlinkinfo);
        if (empty($userfenliucnzz)) {   //没有信息
            $usernczz['sui_code'] = $userlinkinfo;
            $userfenliucnzz = M("Userlinkcnzz")->where($usernczz)->find();
            // $rediscode = $uv_type["sjcode"];  //构建redis唯一key值
            $userfenliucnzz = serialize($userfenliucnzz);          // 序列化
            $this->redis->set($userlinkinfo, $userfenliucnzz);
            $this->redis->setTimeout($userlinkinfo, 30 * 1);
        }
        $userfenliucnzz = unserialize($userfenliucnzz);
        if ($re["cut_mun"] == 0) {  //没有设定扣量.
            $showMycnzz = 0;
        } else {
            //有扣量.
            $m = $re["cut_mun"];
            $s = mt_rand(1, 100);
            if ($s <= $m) {  //扣量成功
                $re["cnzz"] = 0;
                $showMycnzz = 1;
            } else {   //不扣量
                $showMycnzz = 0;
            }
        }

        //检查扣量.
        $m = $userfenliucnzz["cut_mun"];
        $s = mt_rand(1, 100);
        // dump($userfenliucnzz);die;
        if ($s < $m) {  //扣量成功
            $fenliucnzz = '1';
            $kouliang = 1;
        } else {
            $fenliucnzz = $userfenliucnzz["cnzz"];
            $kouliang = 0;
        }
        $middlecnzz = I("get.middle");
        if ($middlecnzz == 1) {
            $fenliucnzz = '该链接分统计放在中间域名上';
        }
        //检查防刷
        $user_cnzz["fangshua"] = I("get.fangs");
        $linkusercode = I("get.usercode");
        $IS_visit = $this->checkIP($linkusercode);
        // dump($$user_cnzz["fangshua"]);die;
        if ($user_cnzz["fangshua"] == 1) {
            //检测IP的重复性
            if (!$IS_visit) {  //第一次访问
                //正常统计
            } else {
                //重复IP,不统计
                $fenliucnzz = '1';
            }
        }
        //记录一次用户点击//这是关于cnzz统计的.
        $saveRES = $this->savetheclick($userfenliucnzz, $IS_visit, $kouliang, $user_cnzz["fangshua"], $middlecnzz);
        //代码加密处理
        $aaa = base64_encode($HtmlCode);
        $muns = mt_rand(20, 500); //该变量禁止重复
        $sui_code = rand_str($muns);
        $aaa = $sui_code . $aaa;
        //获取index代码
        $IndexCode = $this->GetHtmlCode('Index/index.html');
        $this->assign('adv11', $str);
        //口令分割
        $kouStr = $this->getKouling();
        $this->assign('koulingcode', $kouStr);
        $this->assign('showMycnzz', $showMycnzz);
        $this->assign('user_vi_type', $user_vi_type["is_adv"]);
        // $adv_type = 0;
        $this->assign('adv_type', $adv_type);
        $this->assign('Mycnzz', $re["self_cnzz"]);  //扣量后总统计
        $this->assign('cnzz_self', $re["cnzz"]);  // 用户设定统计
        $this->assign('fenliucnzz', $fenliucnzz);  // 用户分流统计
        $this->assign('cnzz_total', $all_cnzz["all_cnzz"]); //整个账户的统计
        $this->assign('total_server', $wx_vi["totalcnzz"]); //整台服务器统计
        $this->assign('article', $aaa);
        $this->assign('muns', $muns);
        $this->assign('is_weixin_type', 'ok');
        $this->show($IndexCode);
    }

    protected function savetheclick($linkinfo, $IS_visit, $kouliang, $fangshua, $middlecnzz)
    {
        if ($middlecnzz == 1) {
            return false;
            die;
        }

        $IP = getIp();
        $date = date("Y-m-d");
        $visitinfo = $this->redis->get($date . $linkinfo['sui_code']);
        if (empty($visitinfo)) {   //没有信息,首次访问
            //建立访问数据
            $visitinfo['username'] = '';
            $visitinfo['code'] = '';
            $visitinfo['sui_code'] = $linkinfo['sui_code'];
            $visitinfo['openmuns'] = 0;
            $visitinfo['usernewip'] = 0;
            $visitinfo['useroldip'] = 0;
            $visitinfo['linknewip'] = 0;
            $visitinfo['linkoldip'] = 0;
            $visitinfo['clickmuns'] = 0;
            $visitinfo['showcnzzmuns'] = 0;
            $visitinfo['hiddencnzzmuns'] = 0;
            $visitinfo['var_time'] = date("Y-m-d");
            $rediscode = $date . $linkinfo['sui_code'];
            $visitinfo = serialize($visitinfo);
            $this->redis->set($rediscode, $visitinfo);
            $this->redis->setTimeout($rediscode, 3600 * 24);
        }
        //初始化的链接访问数据
        $visitinfo = unserialize($visitinfo);
        $visitinfo['openmuns'] = $visitinfo['openmuns'] + 1;

        //相对账户的重复性IP记录
        if (!$IS_visit) {  //新的的IP.相对账户而言
            $visitinfo['usernewip'] = $visitinfo['usernewip'] + 1;
        } else {
            $visitinfo['useroldip'] = $visitinfo['useroldip'] + 1;
        }
        //相对链接的重复性IP记录
        $linkiptype = $this->checkIP($linkinfo["sui_code"]);
        if (!$linkiptype) {  //新的IP.相对链接而言
            $visitinfo['linknewip'] = $visitinfo['linknewip'] + 1;
        } else {
            $visitinfo['linkoldip'] = $visitinfo['linkoldip'] + 1;
        }

        // dump($fangshua);die;
        if ($fangshua == 1) { //开启防刷
            if (!$IS_visit) {  //新的的IP.相对账户而言
                //检测单条链接ip重复性.
                if (!$linkiptype) {
                    $visitinfo['clickmuns'] = $visitinfo['clickmuns'] + 1;
                    if ($kouliang == 1) {  //本次被扣,点击和扣量加1
                        $visitinfo['hiddencnzzmuns'] = $visitinfo['hiddencnzzmuns'] + 1;
                    } else { //本次记录,点击和展示加1
                        $visitinfo['showcnzzmuns'] = $visitinfo['showcnzzmuns'] + 1;
                    }
                }
            }
        } else {
            //检测单条链接ip重复性.
            if (!$linkiptype) {  //针对链接是新量
                $visitinfo['clickmuns'] = $visitinfo['clickmuns'] + 1;
                if ($kouliang == 1) {  //本次被扣,点击和扣量加1
                    $visitinfo['hiddencnzzmuns'] = $visitinfo['hiddencnzzmuns'] + 1;
                } else { //本次记录,点击和展示加1
                    $visitinfo['showcnzzmuns'] = $visitinfo['showcnzzmuns'] + 1;
                }
            }
        }
        if ($visitinfo['openmuns'] == 10) {
            //满100次,添加一次数据库
            $SQL['sui_code'] = $linkinfo['sui_code'];
            $SQL['var_time'] = date('Y-m-d');
            $linkdata = M('Linkclick')->where($SQL)->find();
            if ($linkdata) {
                //有数据,就更新
                $linkdata['username'] = '';
                $linkdata['code'] = '';
                $linkdata['sui_code'] = $linkinfo['sui_code'];
                $linkdata['openmuns'] = $linkdata['openmuns'] + $visitinfo['openmuns'];
                $linkdata['usernewip'] = $linkdata['usernewip'] + $visitinfo['usernewip'];
                $linkdata['useroldip'] = $linkdata['useroldip'] + $visitinfo['useroldip'];
                $linkdata['linknewip'] = $linkdata['linknewip'] + $visitinfo['linknewip'];
                $linkdata['linkoldip'] = $linkdata['linkoldip'] + $visitinfo['linkoldip'];
                $linkdata['clickmuns'] = $linkdata['clickmuns'] + $visitinfo['clickmuns'];
                $linkdata['showcnzzmuns'] = $linkdata['showcnzzmuns'] + $visitinfo['showcnzzmuns'];
                $linkdata['hiddencnzzmuns'] = $linkdata['hiddencnzzmuns'] + $visitinfo['hiddencnzzmuns'];
                $id['id'] = $linkdata['id'];
                M('Linkclick')->where($id)->save($linkdata);
            } else {  //数据库中没有数据,直接add
                M('Linkclick')->add($visitinfo);
            }
            //更新完数据库后,对Redis进行充值
            $visitinfo['username'] = '';;
            $visitinfo['code'] = '';
            $visitinfo['sui_code'] = $linkinfo['sui_code'];
            $visitinfo['openmuns'] = 0;
            $visitinfo['usernewip'] = 0;
            $visitinfo['useroldip'] = 0;
            $visitinfo['linknewip'] = 0;
            $visitinfo['linkoldip'] = 0;
            $visitinfo['clickmuns'] = 0;
            $visitinfo['showcnzzmuns'] = 0;
            $visitinfo['hiddencnzzmuns'] = 0;
            $visitinfo['var_time'] = date("Y-m-d");
            $rediscode111 = $date . $linkinfo['sui_code'];
            $visitinfo = serialize($visitinfo);
            $this->redis->set($rediscode111, $visitinfo);
            $this->redis->setTimeout($rediscode111, 3600 * 24);
        } else {
            $rediscode22 = $date . $linkinfo['sui_code'];
            $visitinfo = serialize($visitinfo);
            $this->redis->set($rediscode22, $visitinfo);
            $this->redis->setTimeout($rediscode22, 3600 * 24);
        }
        return true;
    }

    public function is_weixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'WindowsWechat') !== false) {
            return false;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {
            return false;
        }
    }

    //统计用户行为
    public function GetUserAction()
    {
        header('Content-Type: text/html;charset=utf-8');
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
        if (IS_POST) { //直接收post访问
            $re = I("post.");
            $userinfo["userinfo"] = I("post.userinfo");
            $userinfo["var_time"] = date('Y-m-d');
            $pageState["pageState"] = I("post.pageState");
            $vist_type["visit_muns"] = I("post.visiti_muns");
            $closed["visit_type"] = I("post.visit_type");
            $IP = getIp();
            // //不记录滑动位置记录测试
            // $fv["vi_muns"] = I("post.visiti_muns");
            // $Mysql_data = M("Useraction")->where($userinfo)->find();
            // //记录本次IP位置数据
            // //处理滑动
            // if ($pageState["pageState"]  == 1) {   //关闭了

            // }else{
            // 	$slide = I("post.slide_location");
            // 	dump($slide);
            // 	if ($slide == '') {
            // 		$slidelocation = 0;
            // 	}else{
            // 		$slideArr = explode(",",$slide);
            // 		$slidelocation = max($slideArr);
            // 	}
            // 	dump($slidelocation);
            // 	//把滑动数据存起来.
            // 	$rediscode = 'slidelocation:'.$userinfo["userinfo"]."vartime".$userinfo["var_time"].$IP;  //构建redis唯一key值
            // 	$slidelocationData = $this->redis->get($rediscode);
            // 	dump($slidelocationData);
            // 	dump(empty($slidelocationData));
            // 	if (empty($slidelocationData)) {
            // 		//没数据
            // 		//那就存起来
            // 		$sildemax["max"] = $slidelocation;
            // 		$sildemax = serialize($sildemax);
            // 		$this->redis->set($rediscode,$sildemax);
            // 		//清除该用户的缓存数据
            // 		$this->redis->setTimeout($rediscode,10);
            // 	}
            // 	$slidelocationData = unserialize($slidelocationData);
            // 	dump($slidelocationData);die;
            // }
            // die;
            // if ($fv["vi_muns"] == 1) { //首次访问
            // 	if ($Mysql_data) {  //有数据
            // 		M("Useraction")->where($userinfo)->setInc("visit_muns");
            // 		echo json_encode(array('code'=>0,'msg'=>'首次访问成功'));
            // 	}else{
            // 		$Mysql_data["userinfo"] = I("post.userinfo");
            // 		$Mysql_data["visit_muns"] = 1;
            // 		$Mysql_data["visit_time"] = 0;
            // 		$Mysql_data["slide_muns"] = I("post.slide_muns");
            // 		$Mysql_data["longpress_qrcode"] = I("post.longpress_qrcode");
            // 		$Mysql_data["var_time"] = date('Y-m-d');
            // 		$re = M("Useraction")->add($Mysql_data);
            // 		if ($re) {
            // 			echo json_encode(array('code'=>0,'msg'=>'首次访问成功'));
            // 		}else{
            // 			echo json_encode(array('code'=>1001,'msg'=>'首次记录失败'));
            // 		}
            // 	}
            // }else{ //二次提交
            // 	//查出是否有数据
            // 	if ($Mysql_data) {  //有数据
            // 		$Mysql_data["visit_time"] = $Mysql_data["visit_time"]+5;
            // 		$Mysql_data["slide_muns"] = $Mysql_data["slide_muns"]+I("post.slide_muns");
            // 		$Mysql_data["longpress_qrcode"] +=I("post.longpress_qrcode");
            // 		$re = M("Useraction")->save($Mysql_data);
            // 		if ($re) {
            // 			echo json_encode(array('code'=>0,'msg'=>'成功'));
            // 		}else{
            // 			echo json_encode(array('code'=>1001,'msg'=>'11失败'));
            // 		}
            // 	}else{
            // 		echo json_encode(array('code'=>1001,'msg'=>'22失败'));
            // 	}
            // }
            // die;

            //记录测试数据
            // $SQL["userinfo"] = I("post.userinfo");
            // $SQL["visit_time"] = I("post.visit_time");
            // $SQL["slide_muns"] = I("post.slide_muns");
            // $SQL["slide_location"] = I("post.slide_location");
            // $SQL["longpress_qrcode"] = I("post.longpress_qrcode");
            // $SQL["visit_muns"] = I("post.visiti_muns");
            // $SQL["var_time"] = date('Y-m-d H:i:s');
            // $SQL["aa"] = $IP;
            // $SQL["bb"] = I("post.visit_type");
            // $SQL["cc"] = I("post.pageState");
            // $SQL["dd"] = I("post.dd");
            // $SQL["ee"] = I("post.ee");
            // $SQL["ff"] = I("post.ff");
            // $SQL["gg"] = I("post.gg");
            // M("Test")->add($SQL);
            // echo json_encode(array('code'=>0,'cmsg'=>'success',"senddata"=>$re,"visit_type"=>I("post.visit_type")));
            // die;


            //查询IP访问记录.一个IP访问2次
            // if ($vist_type["visit_muns"] == 1) { //首次访问
            // 	//查询有无访问记录
            // 	$iprevisit = $this->redis->get('visit:'.$userinfo["userinfo"].":".$IP.$userinfo["var_time"].'frist');
            // 	if (empty($iprevisit)) {
            // 		//记录IP访问状态
            // 		$iprevisit["ip"] = $IP;
            // 		$iprevisit["muns"] = 1;
            // 		$rediscode = 'visit:'.$userinfo["userinfo"].":".$IP.$userinfo["var_time"].'frist';  //构建redis唯一key值
            // 		$iprevisit = serialize($iprevisit);          // 序列化
            // 		$this->redis->set($rediscode,$iprevisit);
            // 		//存储24小时
            // 		$this->redis->setTimeout($rediscode,3600*24);
            // 	}else{
            // 		$iprevisit = unserialize($iprevisit);
            // 		$iprevisit["ip"] = $IP;
            // 		$iprevisit["muns"] = $iprevisit["muns"]+1;
            // 		$rediscode = 'visit:'.$userinfo["userinfo"].":".$IP.$userinfo["var_time"].'frist';  //构建redis唯一key值
            // 		$iprevisit = serialize($iprevisit);          // 序列化
            // 		$this->redis->set($rediscode,$iprevisit);
            // 		//存储24小时
            // 		$this->redis->setTimeout($rediscode,3600*24);
            // 	}
            // 	$iprevisit = unserialize($iprevisit);
            // 	if ($iprevisit["muns"] >= 3) {
            // 		echo json_encode(array('code'=>1001,'cmsg'=>'error',"data"=>'IP同一个IP访问超过2次',"senddata"=>$re,"visit_type"=>$visit_type["visit_type"]));
            // 		die;
            // 	}
            // }

            //查询是否未关页面,造成异常统计
            $visit_type["visit_type"] = I("post.visit_type");
            if ($visit_type["visit_type"] >= 50) {
                echo json_encode(array('code' => 1001, 'cmsg' => 'error', "data" => '链接访问超过200秒', "senddata" => $re, "visit_type" => $visit_type["visit_type"]));
                die;
            }
            if ($pageState["pageState"] == 1) {  //页面已经关闭
                // if ($closed["visit_type"] == 4) {  //页面已经关闭
                //
                //从reids中读取数据,开始存储.
                $Redis_data = $this->redis->get($userinfo["userinfo"] . ":" . $IP . $userinfo["var_time"]);
                // dump($Redis_data);
                //把redis数据置空.
                //存入redis
                $rediscode = $userinfo["userinfo"] . ":" . $IP . $userinfo["var_time"];  //构建redis唯一key值
                $getData = NULL;
                $getData = serialize($getData);          // 序列化
                $this->redis->set($rediscode, $getData);
                //清除该用户的缓存数据
                $this->redis->setTimeout($rediscode, 1);
                if (empty($Redis_data)) {
                    echo json_encode(array('code' => 1001, 'msg' => '数据记录过期了'));
                    die;
                } else {
                    //查询是否有过记录
                    //取出链接数据
                    $Mysql_data = $this->redis->get('userinfo:' . $userinfo["userinfo"] . "vartime" . $userinfo["var_time"]);
                    if (empty($Mysql_data)) {   //没有信息
                        $Mysql_data = M("Useraction")->where($userinfo)->find();
                        $Mysql_data = serialize($Mysql_data);
                    }
                    $Mysql_data = unserialize($Mysql_data);
                    // dump($Mysql_data);
                    if (empty($Mysql_data)) {  //redis中没有数据,第一次添加
                        $Redis_data = unserialize($Redis_data);
                        $Redis_data["userinfo"] = I("post.userinfo");
                        $Redis_data["visit_muns"] = 1;
                        $Redis_data["visit_time"] = $Redis_data["visit_time"] + I("post.visit_time");
                        $Redis_data["slide_muns"] = $Redis_data["slide_muns"] + I("post.slide_muns");
                        //处理滑动
                        $slide = I("post.slide_location");
                        if ($slide == '') {
                            $slidemuns = 0;
                        } else {
                            $slideArr = explode(",", $slide);
                            $slidemuns = max($slideArr);
                        }
                        if ($Redis_data["slide_location"] >= $slidemuns) {

                        } else {
                            $Redis_data["slide_location"] = $slidemuns;
                        }
                        $Redis_data["longpress_qrcode"] = $Redis_data["longpress_qrcode"] + I("post.longpress_qrcode");
                        //控制单次最大扫码次数
                        if ($Redis_data["longpress_qrcode"] >= 1) {
                            $Redis_data["longpress_qrcode"] = 1;
                        }
                        $Redis_data["var_time"] = date('Y-m-d');
                        // dump($Redis_data);
                        //新增
                        M("Useraction")->add($Redis_data);
                        //取出数据,存入redis
                        $getSQL["userinfo"] = $userinfo["userinfo"];
                        $getSQL["var_time"] = date('Y-m-d');
                        $getData = M("Useraction")->where($getSQL)->find();
                        // echo '111';
                        // dump($getData);
                        //存入redis
                        $rediscode = 'userinfo:' . $userinfo["userinfo"] . "vartime" . $userinfo["var_time"];  //构建redis唯一key值
                        $getData = serialize($getData);          // 序列化
                        $this->redis->set($rediscode, $getData);
                        //存储24小时
                        $this->redis->setTimeout($rediscode, 3600 * 24);
                        die;
                    } else {   //该条链接有数据
                        $Redis_data = unserialize($Redis_data);
                        $id["id"] = $Mysql_data["id"];
                        $Last_data["userinfo"] = I("post.userinfo");
                        $Last_data["visit_muns"] = $Mysql_data["visit_muns"] + $Redis_data["visit_muns"];
                        $Last_data["visit_time"] = $Mysql_data["visit_time"] + $Redis_data["visit_time"] + I("post.visit_time");
                        $Last_data["slide_muns"] = $Mysql_data["slide_muns"] + $Redis_data["slide_muns"] + I("post.slide_muns");
                        //处理滑动,此处有三个数据,提交的和redis需要对比大小.
                        //然后和原数据库相加
                        $slide = I("post.slide_location");
                        //得到提交的大小.
                        if ($slide == '') {
                            $slidemuns = 0;
                        } else {
                            $slideArr = explode(",", $slide);
                            $slidemuns = max($slideArr);
                        }
                        //得到提交的和redis的比较后的最大值.
                        if ($Redis_data["slide_location"] >= $slidemuns) {

                        } else {
                            $Redis_data["slide_location"] = $slidemuns;
                        }
                        $Last_data["slide_location"] = $Mysql_data["slide_location"] + $Redis_data["slide_location"];
                        //控制单次最大扫码次数
                        $maxSlide = $Redis_data["longpress_qrcode"] + I("post.longpress_qrcode");
                        if ($maxSlide >= 1) {
                            $maxSlide = 1;
                        }
                        $Last_data["longpress_qrcode"] = $Mysql_data["longpress_qrcode"] + $maxSlide;
                        $Last_data["var_time"] = date('Y-m-d');
                        unset($Last_data["id"]);
                        // dump($Last_data);
                        //更新
                        M("Useraction")->where($id)->save($Last_data);
                        $lasrredisdate = M("Useraction")->where($id)->find();
                        //存入缓存
                        //存入redis
                        // dump($lasrredisdate);
                        $rediscode = 'userinfo:' . $userinfo["userinfo"] . "vartime" . $userinfo["var_time"];  //构建redis唯一key值
                        $lasrredisdate = serialize($lasrredisdate);          // 序列化
                        $this->redis->set($rediscode, $lasrredisdate);
                        //存储24小时
                        $this->redis->setTimeout($rediscode, 3600 * 24);
                    }
                }
            } else {
                //页面没有关闭,通过redis暂存
                //判断是否是首次访问
                if ($vist_type["visit_muns"] == 1) { //首次访问
                    //构建唯一值
                    $FristVisit["userinfo"] = I("post.userinfo");
                    $FristVisit["visit_time"] = 0;
                    $FristVisit["slide_muns"] = I("post.slide_muns");
                    //处理滑动
                    $slide = I("post.slide_location");
                    if ($slide == '') {
                        $slidemuns = 0;
                    } else {
                        $slideArr = explode(",", $slide);
                        $slidemuns = max($slideArr);
                    }
                    $FristVisit["slide_location"] = $slidemuns;
                    $FristVisit["longpress_qrcode"] = I("post.longpress_qrcode");
                    $FristVisit["var_time"] = date('Y-m-d');
                    $FristVisit["visit_muns"] = 1;
                    //存入redis
                    $rediscode = $userinfo["userinfo"] . ":" . $IP . $userinfo["var_time"];  //构建redis唯一key值
                    $FristVisit = serialize($FristVisit);          // 序列化
                    $this->redis->set($rediscode, $FristVisit);
                    //存储24小时
                    $this->redis->setTimeout($rediscode, 60 * 4);
                    $FristVisit = unserialize($FristVisit);
                    echo json_encode(array('code' => 0, 'cmsg' => '记录成功,首次发送数据', "senddata" => $re, "redisdata" => $FristVisit));
                } else {   //有过访问,数据叠加
                    $SecVisit = $this->redis->get($userinfo["userinfo"] . ":" . $IP . $userinfo["var_time"]);
                    if (empty($SecVisit)) {   //没有信息
                        //此处需要重新添加 信息记录过期了.
                        $FristVisit["userinfo"] = I("post.userinfo");
                        $FristVisit["visit_time"] = 0;
                        $FristVisit["slide_muns"] = I("post.slide_muns");
                        //处理滑动
                        $slide = I("post.slide_location");
                        if ($slide == '') {
                            $slidemuns = 0;
                        } else {
                            $slideArr = explode(",", $slide);
                            $slidemuns = max($slideArr);
                        }
                        $FristVisit["slide_location"] = $slidemuns;
                        $FristVisit["longpress_qrcode"] = I("post.longpress_qrcode");
                        $FristVisit["var_time"] = date('Y-m-d');
                        $FristVisit["visit_muns"] = 1;
                        //存入redis
                        $rediscode = $userinfo["userinfo"] . ":" . $IP . $userinfo["var_time"];  //构建redis唯一key值
                        $FristVisit = serialize($FristVisit);          // 序列化
                        $this->redis->set($rediscode, $FristVisit);
                        //存储24小时
                        $this->redis->setTimeout($rediscode, 60 * 4);
                        $FristVisit = unserialize($FristVisit);
                        echo json_encode(array('code' => 0, 'cmsg' => '11111发送数成功', "senddata" => $re, "redisdata" => $FristVisit));
                    } else {
                        $SecVisit = unserialize($SecVisit);
                        $SecVisit["userinfo"] = I("post.userinfo");
                        $SecVisit["visit_muns"] = $SecVisit["visit_muns"];
                        $SecVisit["visit_time"] = $SecVisit["visit_time"] + 5;
                        $SecVisit["slide_muns"] = $SecVisit["slide_muns"] + I("post.slide_muns");
                        //处理滑动
                        $slide = I("post.slide_location");
                        if ($slide == '') {
                            $slidemuns = 0;
                        } else {
                            $slideArr = explode(",", $slide);
                            $slidemuns = max($slideArr);
                        }
                        if ($SecVisit["slide_location"] >= $slidemuns) {

                        } else {
                            $SecVisit["slide_location"] = $slidemuns;
                        }
                        $SecVisit["longpress_qrcode"] = $SecVisit["longpress_qrcode"] + I("post.longpress_qrcode");

                        //存入redis
                        $rediscode = $userinfo["userinfo"] . ":" . $IP . $userinfo["var_time"];  //构建redis唯一key值
                        // dump($rediscode);die;
                        $SecVisit = serialize($SecVisit);          // 序列化
                        $this->redis->set($rediscode, $SecVisit);
                        //存储24小时
                        $this->redis->setTimeout($rediscode, 60 * 4);
                        $SecVisit = unserialize($SecVisit);
                        echo json_encode(array('code' => 0, 'cmsg' => '记录成功', "senddata" => $re, "redisdata" => $SecVisit));
                    }
                }
            }
            die;

            //查询IP访问记录.

            $ipre = $this->redis->get($userinfo["userinfo"] . ":" . $IP . $userinfo["var_time"]);
            if (empty($ipre)) {   //没有信息
                //不查询,直接记录
                $ipre["ip"] = $IP;
                $ipre["muns"] = 1;
                $rediscode = $userinfo["userinfo"] . ":" . $IP . $userinfo["var_time"];  //构建redis唯一key值
                $ipre = serialize($ipre);          // 序列化
                $this->redis->set($rediscode, $ipre);
                //存储24小时
                $this->redis->setTimeout($rediscode, 3600 * 24);
            } else {
                //有过访问,更新redis
                $ipre = unserialize($ipre);
                $ipre["muns"] = $ipre["muns"] + 1;
                $rediscode = $userinfo["userinfo"] . ":" . $IP . $userinfo["var_time"];  //构建redis唯一key值
                $ipre = serialize($ipre);          // 序列化
                $this->redis->set($rediscode, $ipre);
                //存储24小时
                $this->redis->setTimeout($rediscode, 3600 * 24);
            }
            $ipre = unserialize($ipre);
            //判断访问次数
            if ($ipre["muns"] >= 80) {  //80代表可以访问两次 ,每次最长停留时间200秒
                echo json_encode(array('code' => 1001, 'cmsg' => 'error', "data" => 'IP访问超过80次,不在记录数据', "senddata" => $re, "visit_type" => $visit_type["visit_type"]));
                die;
            }
            //查询是否有过记录
            $linkid = $this->redis->get('userinfo:' . $userinfo["userinfo"] . "vartime" . $userinfo["var_time"]);
            if (empty($linkid)) {   //没有信息
                $linkid = M("Useraction")->where($userinfo)->find();
                $rediscode = 'userinfo:' . $userinfo["userinfo"] . "vartime" . $userinfo["var_time"];
                $linkid = serialize($linkid);          // 序列化
                $this->redis->set($rediscode, $linkid);
                $this->redis->setTimeout($rediscode, 20 * 1);
            }
            //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
            $linkid = unserialize($linkid);
            if (!$linkid) {  //没有数据
                //增加数据
                $userSQL["userinfo"] = I("post.userinfo");
                $userSQL["visit_time"] = I("post.visit_time");
                $userSQL["slide_muns"] = I("post.slide_muns");
                $userSQL["slide_location"] = I("post.slide_location");
                $userSQL["longpress_qrcode"] = I("post.longpress_qrcode");
                $userSQL["var_time"] = date('Y-m-d');
                $userSQL["visit_muns"] = I("post.visiti_muns");
                // dump($re);
                // dump($userSQL);die;
                M("Useraction")->add($userSQL);
                //查出id
                $linkid = M("Useraction")->where($userinfo)->find();
                //存入redis
                $rediscode = 'userinfo:' . $userinfo["userinfo"] . "vartime" . $userinfo["var_time"];
                $linkid = serialize($linkid);          // 序列化
                $this->redis->set($rediscode, $linkid);
                $this->redis->setTimeout($rediscode, 20 * 1);
                $linkid = unserialize($linkid);
                echo json_encode(array('code' => 0, 'cmsg' => 'success', "data" => $linkid, "senddata" => $re, "tipvistmuns" => $ipre["muns"], "visit_type" => $visit_type["visit_type"]));
            } else {
                //有数据,直接更新
                $linkidn["id"] = $linkid["id"];
                $linkidn["userinfo"] = I("post.userinfo");
                $linkidn["visit_time"] = $linkid["visit_time"] + I("post.visit_time");
                $linkidn["slide_muns"] = $linkid["slide_muns"] + I("post.slide_muns");
                $linkidn["slide_location"] = $linkid["slide_location"] + I("post.slide_location");
                $linkidn["longpress_qrcode"] = $linkid["longpress_qrcode"] + I("post.longpress_qrcode");
                $linkidn["visit_muns"] = $linkid["visit_muns"] + I("post.visiti_muns");
                $linkidn["var_time"] = $linkid["var_time"];
                M("Useraction")->save($linkidn);
                //存入redis
                $rediscode = 'userinfo:' . $userinfo["userinfo"] . "vartime" . $userinfo["var_time"];
                $linkidn = serialize($linkidn);          // 序列化
                $this->redis->set($rediscode, $linkidn);
                $this->redis->setTimeout($rediscode, 5 * 1);
                $linkid = unserialize($linkidn);
                echo json_encode(array('code' => 0, 'cmsg' => 'success', "data" => $linkid, "senddata" => $re, "tipvistmuns" => $ipre["muns"], "visit_type" => $visit_type["visit_type"]));
            }
        }
    }

    public function get_code()
    {
        header('Content-Type: text/html;charset=utf-8');
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
        // 设置此页面的过期时间(用格林威治时间表示)，只要是已经过去的日期即可。
        header(" Expires: Mon, 26 Jul 1970 05:00:00 GMT ");
        // 设置此页面的最后更新日期(用格林威治时间表示)为当天，可以强制浏览器获取最新资料
        header(" Last-Modified:" . gmdate(" D, d M Y H:i:s ") . "GMT ");
        // 告诉客户端浏览器不使用缓存，HTTP 1.1 协议
        header(" Cache-Control: no-cache, must-revalidate ");
        // 告诉客户端浏览器不使用缓存，兼容HTTP 1.0 协议
        header(" Pragma: no-cache ");
        $sui["sui_code"] = I("post.code");
        $canshu = $sui["sui_code"];

        //--------------------------redis开始----------------------------
        //查询信息
        $re = $this->redis->get($canshu);
        if (empty($re)) {   //没有信息
            $a["sui_code"] = $sui["sui_code"];;
            // dump($a);die;
            $re = M("Link")->where($a)->find();
            $rediscode = $re["sui_code"];  //构建redis唯一key值
            $re = serialize($re);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($rediscode, $re);
            //--------------------------缓存1小时----------------------------
            $this->redis->setTimeout($rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $re = unserialize($re);

        //--------------------------redis结束----------------------------
        // $re = M("Link")->where($sui)->find();
        $Qrcodetotal = $this->redis->get($re["username"] . ":" . $re["team_type"] . ":code");
        if (empty($Qrcodetotal)) {   //没有信息
            $team["team_type"] = $re["team_type"];
            $team["username"] = $re["username"];
            $team["is_use"] = 0;
            // $Qrcode = M("Qrcodemanager")->where($team)->order("rand()")->find();
            $Qrcodetotal = M("Qrcodemanager")->where($team)->select();
            $rediscode = $re["username"] . ":" . $re["team_type"] . ":code";  //构建redis唯一key值
            $Qrcodetotal = serialize($Qrcodetotal);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($rediscode, $Qrcodetotal);
            //--------------------------缓存1分钟----------------------------
            $this->redis->setTimeout($rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $Qrcodetotal = unserialize($Qrcodetotal);
        $sj = array_rand($Qrcodetotal, 1);
        $Qrcode = $Qrcodetotal[$sj];
        // dump($Qrcode);die;
        // $team["team_type"] = $re["team_type"];
        // $team["username"] = $re["username"];
        // $team["is_use"] = 0;
        // $Qrcode = M("Qrcodemanager")->where($team)->order("rand()")->find();
        // $usercnzz["username"] = $re["username"];
        // $all_cnzz = M("Allcnzz")->where($usercnzz)->order("id desc")->find();
        //--------------------------redis开始----------------------------
        //查询统计信息
        $all_cnzz = $this->redis->get("all_cnzz_" . $re["username"]);
        if (empty($all_cnzz)) {   //没有信息
            $usercnzz["username"] = $re["username"];
            $all_cnzz = M("Allcnzz")->where($usercnzz)->order("id desc")->find();
            $all_cnzz_rediscode = "all_cnzz_" . $re["username"];  //构建redis唯一key值
            $all_cnzz = serialize($all_cnzz);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($all_cnzz_rediscode, $all_cnzz);
            //--------------------------缓存1小时----------------------------
            $this->redis->setTimeout($all_cnzz_rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $all_cnzz = unserialize($all_cnzz);
        //--------------------------redis结束----------------------------
        if ($Qrcode) {
            echo json_encode(array('code' => 0, 'msg' => '成功', 'Qrcode' => $Qrcode["code"], 'wxcode' => $Qrcode["wxcode"]));
            // echo json_encode(array('code'=>0,'msg'=>'成功','Qrcode'=>$Qrcode["code"],"cnzz"=>$re["cnzz"],'wxcode'=>$Qrcode["wxcode"],"allcnzz"=>$all_cnzz["all_cnzz"]));
        } else {
            echo json_encode(array('code' => 1, 'msg' => '请重新进入页面'));
        }
    }

    public function get_code_kuaizhan()
    {
        header('Content-Type: text/html;charset=utf-8');
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
        $sui["sui_code"] = I("post.code");
        $canshu = $sui["sui_code"];
        // dump($canshu);die;
        //--------------------------redis开始----------------------------
        //查询信息
        $re = $this->redis->get($canshu);
        if (empty($re)) {   //没有信息
            $a["sui_code"] = $sui["sui_code"];;
            // dump($a);die;
            $re = M("Link")->where($a)->find();
            $rediscode = $re["sui_code"];  //构建redis唯一key值
            $re = serialize($re);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($rediscode, $re);
            //--------------------------缓存1小时----------------------------
            $this->redis->setTimeout($rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $re = unserialize($re);

        //--------------------------redis结束----------------------------
        // $re = M("Link")->where($sui)->find();
        $Qrcodetotal = $this->redis->get($re["username"] . ":" . $re["team_type"] . ":code");
        if (empty($Qrcodetotal)) {   //没有信息
            $team["team_type"] = $re["team_type"];
            $team["username"] = $re["username"];
            $team["is_use"] = 0;
            // $Qrcode = M("Qrcodemanager")->where($team)->order("rand()")->find();
            $Qrcodetotal = M("Qrcodemanager")->where($team)->select();
            $rediscode = $re["username"] . ":" . $re["team_type"] . ":code";  //构建redis唯一key值
            $Qrcodetotal = serialize($Qrcodetotal);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($rediscode, $Qrcodetotal);
            //--------------------------缓存1分钟----------------------------
            $this->redis->setTimeout($rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $Qrcodetotal = unserialize($Qrcodetotal);
        $sj = array_rand($Qrcodetotal, 1);
        $Qrcode = $Qrcodetotal[$sj];
        // dump($Qrcode);die;
        //查询统计信息
        $all_cnzz = $this->redis->get("all_cnzz_" . $re["username"]);
        if (empty($all_cnzz)) {   //没有信息
            $usercnzz["username"] = $re["username"];
            $all_cnzz = M("Allcnzz")->where($usercnzz)->order("id desc")->find();
            $all_cnzz_rediscode = "all_cnzz_" . $re["username"];  //构建redis唯一key值
            $all_cnzz = serialize($all_cnzz);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($all_cnzz_rediscode, $all_cnzz);
            //--------------------------缓存1小时----------------------------
            $this->redis->setTimeout($all_cnzz_rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $all_cnzz = unserialize($all_cnzz);
        //--------------------------redis结束----------------------------
        if (top_domain($Qrcode['code']) == 'uploads') { //老版本本地服务器/public/开头的图片
            //1009服务器
            // $Qrcode["code"] = "https://.5zhuangbi.com".$Qrcode['code'];
            //1017服务器
            $Qrcode["code"] = "https://1007ssl.chenpeipei.cn" . $Qrcode['code'];
        }
        if ($Qrcode) {
            echo json_encode(array('code' => 0, 'msg' => '成功', 'Qrcode' => $Qrcode["code"] . "?V=" . $this->sjCode(5), 'wxcode' => $Qrcode["wxcode"]));
            // echo json_encode(array('code'=>0,'msg'=>'成功','Qrcode'=>$Qrcode["code"]."?V=".$this->sjCode(5),"cnzz"=>$re["cnzz"],'wxcode'=>$Qrcode["wxcode"],"allcnzz"=>$all_cnzz["all_cnzz"]));
        } else {
            echo json_encode(array('code' => 1, 'msg' => '请重新进入页面'));
        }
    }

    //通过伪静态过来的获取信息
    public function get_info()
    {

        header('Content-Type: text/html;charset=utf-8');
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
        // 设置此页面的过期时间(用格林威治时间表示)，只要是已经过去的日期即可。
        // header ( " Expires: Mon, 26 Jul 1970 05:00:00 GMT " );
        // // 设置此页面的最后更新日期(用格林威治时间表示)为当天，可以强制浏览器获取最新资料
        // header ( " Last-Modified:" . gmdate ( " D, d M Y H:i:s " ). "GMT " );
        // // 告诉客户端浏览器不使用缓存，HTTP 1.1 协议
        // header ( " Cache-Control: no-cache, must-revalidate " );
        // // 告诉客户端浏览器不使用缓存，兼容HTTP 1.0 协议
        // header ( " Pragma: no-cache " );
        $sui["sui_code"] = I("post.code");
        $ipfix = I("post.ipfix");
        $sui["sui_code"] = substr($sui["sui_code"], 0, 8); //用于去掉链接后面的随机时间参数
        // dump($sui);die;

        //链接参数
        $re = $this->redis->get($sui);
        if (empty($re)) {   //没有信息
            $a["sui_code"] = $sui["sui_code"];;
            // dump($a);die;
            $re = M("Link")->where($a)->find();
            $rediscode = $re["sui_code"];  //构建redis唯一key值
            $re = serialize($re);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($rediscode, $re);
            //--------------------------缓存1小时----------------------------
            $this->redis->setTimeout($rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $re = unserialize($re);
        // dump($re);die;
        // $re = M("Link")->where($sui)->find();
        //二维码随机
        $Qrcodetotal = $this->redis->get($re["username"] . ":" . $re["team_type"] . ":code");
        if (empty($Qrcodetotal)) {   //没有信息
            $team["team_type"] = $re["team_type"];
            $team["username"] = $re["username"];
            $team["is_use"] = 0;
            // $Qrcode = M("Qrcodemanager")->where($team)->order("rand()")->find();
            $Qrcodetotal = M("Qrcodemanager")->where($team)->select();
            $rediscode = $re["username"] . ":" . $re["team_type"] . ":code";  //构建redis唯一key值
            $Qrcodetotal = serialize($Qrcodetotal);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($rediscode, $Qrcodetotal);
            //--------------------------缓存1分钟----------------------------
            $this->redis->setTimeout($rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $Qrcodetotal = unserialize($Qrcodetotal);
        if ($ipfix == '1') { //固定了二维码
            $IP = getIp();
            $fixQrcode = $this->redis->get("fixQrcode:" . $sui["sui_code"] . $IP);
            if (empty($fixQrcode)) {   //没有信息
                $sj = array_rand($Qrcodetotal, 1);
                $fixQrcode = $Qrcodetotal[$sj];
                $redisfinxcode = "fixQrcode:" . $sui["sui_code"] . $IP;
                $fixQrcode = serialize($fixQrcode);
                $this->redis->set($redisfinxcode, $fixQrcode);
                $this->redis->setTimeout($redisfinxcode, 3600 * 24 * 7);
            }
            //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
            $Qrcode = unserialize($fixQrcode);
        } else {  //没有固定二维码
            $sj = array_rand($Qrcodetotal, 1);
            $Qrcode = $Qrcodetotal[$sj];
        }
        // dump($Qrcode);die;

        // $team["team_type"] = $re["team_type"];
        // $team["username"] = $re["username"];
        // $team["is_use"] = 0;
        // $Qrcode = M("Qrcodemanager")->where($team)->order("rand()")->find();
        $usercnzz["username"] = $re["username"];
        // $all_cnzz = M("Allcnzz")->where($usercnzz)->order("id desc")->find();
        $all_cnzz = $this->redis->get("all_cnzz_" . $re["username"]);
        if (empty($all_cnzz)) {   //没有信息
            $usercnzz["username"] = $re["username"];
            $all_cnzz = M("Allcnzz")->where($usercnzz)->order("id desc")->find();
            $all_cnzz_rediscode = "all_cnzz_" . $re["username"];  //构建redis唯一key值
            $all_cnzz = serialize($all_cnzz);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($all_cnzz_rediscode, $all_cnzz);
            //--------------------------缓存1小时----------------------------
            $this->redis->setTimeout($all_cnzz_rediscode, 60 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $all_cnzz = unserialize($all_cnzz);
        if ($Qrcode) {
            echo json_encode(array('code' => 0, 'msg' => '成功', 'Qrcode' => $Qrcode["code"], 'wxcode' => $Qrcode["wxcode"]));
            // echo json_encode(array('code'=>0,'msg'=>'成功','Qrcode'=>$Qrcode["code"],"cnzz"=>$re["cnzz"],'wxcode'=>$Qrcode["wxcode"],"allcnzz"=>$all_cnzz["all_cnzz"],"sui_code"=>$sui["sui_code"]));
        } else {
            echo json_encode(array('code' => 1, 'msg' => '请重新进入页面'));
        }
    }

    //传输小说内容
    public function get_xiaoshuo()
    {
        header('Content-Type: text/html;charset=utf-8');
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
        $sui["sui_code"] = I("post.code");
        // dump($sui);die;
        // $canshu  = $sui["sui_code"];
        $canshu = substr($sui["sui_code"], 0, 8); //
        // dump($canshu);die;
        // $re = M("Link")->where($sui)->find();
        //--------------------------redis开始----------------------------
        //查询信息
        $re = $this->redis->get($canshu);
        if (empty($re)) {   //没有信息
            $a["sui_code"] = $sui["sui_code"];;
            // dump($a);die;
            $re = M("Link")->where($a)->find();
            $rediscode = $re["sui_code"];  //构建redis唯一key值
            $re = serialize($re);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($rediscode, $re);
            //--------------------------缓存1小时----------------------------
            $this->redis->setTimeout($rediscode, 10 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $re = unserialize($re);

        //--------------------------redis结束----------------------------
        // $team["team_type"] = $re["team_type"];
        // $team["username"] = $re["username"];
        // $team["is_use"] = 0;
        // $Qrcode = M("Xiaoshuo")->where($team)->order("rand()")->find();

        //--------------------------redis结束----------------------------
        // $re = M("Link")->where($sui)->find();
        // dump($re["username"]);die;

        $Qrcodetotal = $this->redis->get($re["username"] . ":" . $re["team_type"] . ":code");
        // $Qrcodetotal = $this->redis->set($re["username"].":".$re["team_type"].":code",null);
        // dump($Qrcodetotal);
        // dump($re["username"].":".$re["team_type"].":code");
        // dump(empty($Qrcodetotal));die;
        if (empty($Qrcodetotal)) {   //没有信息
            $team["team_type"] = $re["team_type"];
            $team["username"] = $re["username"];
            $team["is_use"] = 0;
            $Qrcodetotal = M("Xiaoshuo")->where($team)->select();
            $rediscode = $re["username"] . ":" . $re["team_type"] . ":code";  //构建redis唯一key值
            $Qrcodetotal = serialize($Qrcodetotal);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($rediscode, $Qrcodetotal);
            //--------------------------缓存2分钟----------------------------
            $this->redis->setTimeout($rediscode, 10 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $Qrcodetotal = unserialize($Qrcodetotal);
        $sj = array_rand($Qrcodetotal, 1);
        $Qrcode = $Qrcodetotal[$sj];
        // $usercnzz["username"] = $re["username"];
        // $all_cnzz = M("Allcnzz")->where($usercnzz)->order("id desc")->find();
        //--------------------------redis开始----------------------------
        //查询统计信息
        $all_cnzz = $this->redis->get("all_cnzz_" . $re["username"]);
        if (empty($all_cnzz)) {   //没有信息
            $usercnzz["username"] = $re["username"];
            $all_cnzz = M("Allcnzz")->where($usercnzz)->order("id desc")->find();
            $all_cnzz_rediscode = "all_cnzz_" . $re["username"];  //构建redis唯一key值
            $all_cnzz = serialize($all_cnzz);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set($all_cnzz_rediscode, $all_cnzz);
            //--------------------------缓存1小时----------------------------
            $this->redis->setTimeout($all_cnzz_rediscode, 10 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $all_cnzz = unserialize($all_cnzz);
        //--------------------------redis结束----------------------------
        if ($Qrcode) {
            echo json_encode(array('code' => 0, 'msg' => '成功', 'img' => $Qrcode["img_link"], "cnzz" => $re["cnzz"], 'xiaoshuo' => $Qrcode["xiaoshuo"], "xiaoshuo2" => $Qrcode["xiaoshuo2"], "article_link" => $Qrcode["article_link"], "title1" => $Qrcode["title1"]));
        } else {
            echo json_encode(array('code' => 1, 'msg' => '请重新进入页面'));
        }
    }

    protected function checkIP($usercode)
    {

        $IP = getIp();
        $taDate = date('Ymd');//时间标记
        $timecode = $this->redis->get("timecode:" . $taDate);
        if (empty($timecode)) {   //没有信息,说明已经过了24点时间标记,所有重新记录
            // $this->redis->flushall();
            //标记存下来
            $timecode["time"] = $taDate;
            //此处需要更新reids
            $rediscode111 = "timecode:" . $taDate;  //构建redis唯一key值
            $timecode = serialize($timecode);          // 序列化
            $this->redis->set($rediscode111, $timecode);
            $this->redis->setTimeout($rediscode111, 3600 * 24);
            //ip存下来
            $iplist["sjcode"] = $usercode;
            $iplist["iplist"] = $IP;
            //此处需要更新reids
            $rediscode = $usercode . ":" . $IP . ":link";  //构建redis唯一key值
            $ip_data = serialize($iplist);          // 序列化
            $this->redis->set($rediscode, $ip_data);
            $this->redis->setTimeout($rediscode, 3600 * 24);
            $iplist = unserialize($iplist);
            return false;
        } else {
            //查询信息
            $ipre3 = $this->redis->get($usercode . ":" . $IP . ":link");
            if (empty($ipre3)) {   //没有信息
                //存下来
                $iplist["sjcode"] = $usercode;
                $iplist["iplist"] = $IP;
                //此处需要更新reids
                $rediscode = $usercode . ":" . $IP . ":link";  //构建redis唯一key值
                $ip_data = serialize($iplist);          // 序列化
                $this->redis->set($rediscode, $ip_data);
                $this->redis->setTimeout($rediscode, 3600 * 24);
                $iplist = unserialize($iplist);
                return false;
            } else {
                return true;
            }
        }
    }

    public function retrunlocation()
    {
        header('Content-Type: text/html;charset=utf-8');
        header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
        $IP = getIp();
        $url = "https://api.map.baidu.com/location/ip?ip=" . $IP . "&ak=ZgH3jNbzHil3qVi0Ywf6sHoQUfImxbp2";;
        $content = $this->curl_get_baidu($url);
        $IpLocation = json_decode($content, true);
        if ($IpLocation["status"] == 0) {  //有数据返回
            //开始记录IP信息
            if ($IpLocation['content']['address_detail']['province'] == '') {
                echo json_encode(array('code' => 0, 'msg' => '目前'));
            } else {
                //有正确的数据返回
                echo json_encode(array('code' => 0, 'msg' => $IpLocation['content']['address_detail']['province']));
                // echo json_encode(array('code'=>0,'msg'=>$IpLocation['content']['address_detail']['city']));
            }
        } else { //API接口返不正常
            echo json_encode(array('code' => 0, 'msg' => '目前'));
        }
    }

    protected function curl_get_baidu($url)
    {
        $ch = curl_init();
        $timeout = 300;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $res = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);
        return $res;

    }

    //获取页面的html加密代码
    protected function GetHtmlCode($articletype)
    {
        $body = $this->redis->get("article:" . $articletype);
        if (empty($body)) {   //没有信息
            $article = "Application/Home/View/" . $articletype;
            $body = file_get_contents($article);
            //构建redis唯一key值
            $article_rediscode = "article:" . $articletype;
            // $ipre = serialize($ipre);      // 序列化
            $this->redis->set($article_rediscode, $body);
            $this->redis->setTimeout($article_rediscode, 30 * 1);
        }
        //缓存干扰码body内容
        $ganrao_body = $this->redis->get("ganrao_body");
        if (empty($ganrao_body)) {   //没有信息
            $sql["ganrao_type"] = "body";   //构建查询条件
            $ganrao_body = M("Ganrao")->where($sql)->select();
            $ganrao_body_code = "ganrao_body";  //构建redis唯一key值
            $ganrao_body = serialize($ganrao_body);          // 序列化
            $this->redis->set($ganrao_body_code, $ganrao_body);
            $this->redis->setTimeout($ganrao_body_code, 3600 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $ganrao_body = unserialize($ganrao_body);
        //缓存head内容
        $ganrao_head = $this->redis->get("ganrao_head");
        if (empty($ganrao_head)) {   //没有信息
            $sql["ganrao_type"] = "head";   //构建查询条件
            $ganrao_head = M("Ganrao")->where($sql)->select();
            $ganrao_head_code = "ganrao_head";  //构建redis唯一key值
            $ganrao_head = serialize($ganrao_head);          // 序列化
            $this->redis->set($ganrao_head_code, $ganrao_head);
            $this->redis->setTimeout($ganrao_head_code, 3600 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $ganrao_head = unserialize($ganrao_head);
        //缓存js内容
        $ganrao_js = $this->redis->get("ganrao_js");
        if (empty($ganrao_js)) {   //没有信息
            $sql["ganrao_type"] = "js";   //构建查询条件
            $ganrao_js = M("Ganrao")->where($sql)->select();
            $ganrao_js_code = "ganrao_js";  //构建redis唯一key值
            $ganrao_js = serialize($ganrao_js);          // 序列化
            $this->redis->set($ganrao_js_code, $ganrao_js);
            $this->redis->setTimeout($ganrao_js_code, 3600 * 1);
        }
        //将redis数据或者数据库查询出来的数据,反序列化为数组,直接使用.
        $ganrao_js = unserialize($ganrao_js);
        //开始随机字符串替换-----展示页面
        //展示页面的body替换
        $gr_sj = array_rand($ganrao_body, 1);
        $sjstr = $ganrao_body[$gr_sj];
        if (!$sjstr) {
            $replace_body["ganrao_type"] = "body";
            $sjstr = M("Ganrao")->where($replace_body)->order("rand()")->limit(1)->find();
        }
        $muns22 = rand(1, 100);
        if (1 <= $muns22 && $muns22 < 30) {
            $replace = "<span style='display:none'>" . $sjstr["no1"] . "</span>";
        } elseif (31 <= $muns22 && $muns22 < 60) {
            $replace = "<div style='display:none'>" . $sjstr["no1"] . "</div>";
        } elseif (61 <= $muns22 && $muns22 < 90) {
            $replace = "<p style='display:none'>" . $sjstr["no1"] . "</p>";
        } elseif (91 <= $muns22 && $muns22 <= 100) {
            $replace = "<H1 style='display:none'>" . $sjstr["no1"] . "</H1>";
        }
        $body = str_replace("RSWtdIfAbwVcpJFBZ93Uigor8u4PzDGO6jQlY7xLqMyekmKhas", $replace, $body);

        //替换展示页head的干扰码
        $gr_sj_head = array_rand($ganrao_head, 1);
        $sjstr_head_str = $ganrao_head[$gr_sj_head];
        if (!$sjstr_head_str) {
            $replace_index_head["ganrao_type"] = 'head';
            $sjstr_head_str = M("Ganrao")->where($replace_index_head)->order("rand()")->limit(1)->find();
        }
        $muns33 = rand(1, 100);
        if (1 <= $muns33 && $muns33 < 40) {
            $sjstr_head_replace = "<!--  " . $sjstr_head_str["no1"] . " -->";
        } elseif (41 <= $muns33 && $muns33 < 80) {
            $sjstr_head_replace = $sjstr_head_str["no1"];
        } elseif (81 <= $muns33 && $muns33 < 90) {
            $sjstr_head_replace = "<script>//" . $sjstr_head_str["no1"] . "</script>";
        } elseif (91 <= $muns33 && $muns33 <= 100) {
            $sjstr_head_replace = '';
        }
        $body = str_replace("htoOTNtdumlKUVysBkFbqJIaeLGfxpr2SwnQ6RzjvigDXYZ5MhEP", $sjstr_head_replace, $body);
        $body = str_replace("ht4RjzIC2lFcG6oM7vQfBYwNTsZJnmKDbXtrkUPgdWhxu5OLqSa3", $sjstr_head_replace, $body);

        //展示页面的js替换
        //替换js的干扰码
        $gr_sj_js = array_rand($ganrao_js, 1);
        $index_js_str = $ganrao_js[$gr_sj_js];
        if (!$index_js_str) {
            $replace_js["ganrao_type"] = 'js';
            $index_js_str = M("Ganrao")->where($replace_js)->order("rand()")->limit(1)->find();
        }

        $js_replace = "//" . $index_js_str["no1"];
        $body = str_replace("jsOTNtdumlKUVysBkFbqJIaeLGfxpr2SwnQ6RzjvigDXYZ5MhEP", $js_replace, $body);
        $body = str_replace("jsRLbliOs2rMGDYyx94vKhPkIA6oFzCuXwmp7BZJEeag5d8cQ3nV", $js_replace, $body);
        $preg = '/https:\/\/article.artgeek.com.cn[^\s*]*["|\']/i';
        preg_match_all($preg, $body, $matche, PREG_PATTERN_ORDER);
        // dump($matche);die;
        foreach ($matche[0] as $value) {
            $url = $value;
            //去掉最后一个字符"
            $url = substr($url, 0, -1);
            //失效时间,有效期固定为1800S,所以需要后调,控制有效期.
            $date = date("YmdHi", strtotime("-1500 seconds"));
            // 设定的秘钥,必须和阿里cdn保持一致
            $PrivateKey = "Mk7IbKB8tDvLsUVf3jQrd4FZ9O";
            //获取链接文件名,通过域名隔断
            $arr = explode('article.artgeek.com.cn', $url);
            $FileName = $arr[1];
            //构建加密链接
            $MD5code = $PrivateKey . $date . $FileName;
            $randstr = MD5($MD5code);
            $newurl = "https://article.artgeek.com.cn/" . $date . "/" . $randstr . $FileName;
            //替换页面中所有已有链接,变成鉴权URL
            $body = str_replace($url, $newurl, $body);
        }
        return $body;
    }

    //返回一个随机获取的口令
    protected function getKouling()
    {
        //获取汇总口令
        $koulingTotal = $this->redis->get('koulingTotal');
        if (empty($koulingTotal)) {   //没有信息
            $getSQL["type"] = 0;
            $getSQL["gettype"] = 0; //本站直接口令
            $koulingTotal = M("Kouling")->where($getSQL)->field('id,kouling')->select();
            $rediscodekoulingTotal = $koulingTotal;  //构建redis唯一key值
            $koulingTotal = serialize($koulingTotal);          // 序列化
            $this->redis->set($rediscodekoulingTotal, $koulingTotal);
            $this->redis->setTimeout($rediscodekoulingTotal, 20 * 1);
        }
        $koulingTotal = unserialize($koulingTotal);
        //随机一个口令
        $kRandKey = array_rand($koulingTotal);
        $kouStr = $koulingTotal[$kRandKey]["kouling"];
        if ($kouStr) {
            $kouID = $koulingTotal[$kRandKey]["id"];
            //记录一次调用
            $SaveMuns = $this->redis->get('havemuns' . $kouID);
            if (empty($SaveMuns)) {   //没有信息
                $SaveMuns = 1;
                $rediscodeSaveMuns = 'havemuns' . $kouID;
                $this->redis->set($rediscodeSaveMuns, $SaveMuns);
                $this->redis->setTimeout($rediscodeSaveMuns, 60 * 60 * 24 * 30);
            } else {
                $SaveMuns = $SaveMuns + 1;
                $rediscodeSaveMuns = 'havemuns' . $kouID;
                $this->redis->set($rediscodeSaveMuns, $SaveMuns);
                $this->redis->setTimeout($rediscodeSaveMuns, 60 * 60 * 24 * 30);
            }
            //判断调用次数
            if ($SaveMuns >= 50) { //记录一次到数据库
                $id['id'] = $kouID;
                M("Kouling")->where($id)->setInc('openmuns', 50);
                //清空次数
                $SaveMuns = 1;
                $rediscodeSaveMuns = 'havemuns' . $kouID;
                $this->redis->set($rediscodeSaveMuns, $SaveMuns);
                $this->redis->setTimeout($rediscodeSaveMuns, 60 * 60 * 24 * 30);
            }
        } else {
            $kouStr = '0';
        }
        return $kouStr;
    }

    protected function sjCode($n)
    {
        $arr = array_merge(range('a', 'z'), range('A', 'Z'), range(2, 9));
        shuffle($arr);
        $str = implode('', $arr);
        return substr($str, 0, $n);
    }

}