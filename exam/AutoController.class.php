<?php

namespace Home\Controller;

use Think\Controller;

class AutoController extends CommonController
{

    public function __construct()
    {
        parent::__construct();
        $this->redis = new \Redis();//实例化redis
        $this->redis->connect('127.0.0.1', 6379); //链接reids
        $this->redis->auth(C('REDIS')); //链接密码
    }

    //API检测接口,给一折用
    public function CAPI()
    {
        $domian = I("get.domain");
        if ($domian == '') {
            echo json_encode(array('code' => 4, 'msg' => '没有数据', 'domian' => $domian));
            die;
        }
        $checkRES = $this->checkDomainZong($domian);
        if ($checkRES["code"] == 9904) { //域名被封了.
            echo json_encode(array('code' => 1, 'msg' => '域名被封', 'domian' => $domian));
        } elseif ($checkRES["code"] == 9900) {  //域名正常
            echo json_encode(array('code' => 0, 'msg' => '域名正常', 'domian' => $domian));
        } elseif ($checkRES["code"] == 139) {  //没有查询权限
            echo json_encode(array('code' => 3, 'msg' => '未知错误', 'domian' => $domian));
        } elseif ($checkRES["code"] == 402) {  //查询过快
            echo json_encode(array('code' => 3, 'msg' => '未知错误', 'domian' => $domian));
        } elseif ($checkRES["code"] == 888) {  //未知错误
            echo json_encode(array('code' => 3, 'msg' => '未知错误', 'domian' => $domian));
        }
    }

    //展示屏蔽域名
    public function ShowDieDomain()
    {
        $type = I("get.type");
        $server_id = I("get.server_id");
        if ($type == 'rukou') {
            $SQL["type"] = 2;
            // $SQL["die_time"]=array('gt',date("m-d H:i:s", strtotime("-1 day")))  ;
            $totalDomain = M("Rukoudomain")->where($SQL)->order("id desc")->limit(20)->select();
        } elseif ($type == 'midlle') {
            $SQL["type"] = 2;
            // $SQL["die_time"]=array('gt',date("m-d H:i:s", strtotime("-1 day")))  ;
            $totalDomain = M("Domain")->where($SQL)->order("id desc")->limit(20)->select();
        } elseif ($type == 'luodi') {
            $SQL["type"] = 2;
            // $SQL["die_time"]=array('gt',date("m-d H:i:s", strtotime("-1 day")))  ;
            $SQL["server_id"] = $server_id;
            $totalDomain = M("Articledomain")->where($SQL)->order("id desc")->limit(20)->select();
            $totalDomain_one = M("Articledomain")->where($SQL)->order("id desc")->limit(20)->select();
        } elseif ($type == 'kuaizhan') {
            $SQL["type"] = 2;
            // $SQL["die_time"]=array('gt',date("m-d H:i:s", strtotime("-1 day")))  ;
            $totalDomain = M("Luodilink")->where($SQL)->order("id desc")->limit(20)->select();
            foreach ($totalDomain as $key => $value) {
                $totalDomain[$key]['domain'] = $value['link'];
            }
        } elseif ($type == 'kuaizhanrukou') {
            $SQL["type"] = 2;
            // $SQL["die_time"]=array('gt',date("m-d H:i:s", strtotime("-1 day")))  ;
            $totalDomain = M("kzrukou")->where($SQL)->order("id desc")->limit(20)->select();
        }
        $this->time = date("m-d H:i:s", strtotime('-1 hours'));
        $this->totalDomain = $totalDomain;
        // dump($totalDomain);die;
        $this->display();
    }

    public function CheckDomainType()
    {
        //查出服务器编号构建服务器查询列表
        $serverMuns = M("Serverid")->select();
        $this->serverMuns = $serverMuns;
        $this->display();
    }

    //快站检测
    public function StartCheckKuaizZhanDomain()
    {
        //查出中间域名
        $sql["type"] = '1';
        $middledomaindata = M("Luodilink")->where($sql)->find();
        // $checkRES = $this->CheckDomainApi($middledomaindata["link"]);
        $checkRES = $this->checkDomainZong($middledomaindata["link"]);
        if ($checkRES["code"] == 9904) { //域名被封了.
            //修改状态
            //关闭中间域名
            $ss["link"] = $middledomaindata["link"];
            $die_type["type"] = 2;
            $die_type["die_time"] = date("m-d H:i:s");
            $middle_domain_die = M("Luodilink")->where($ss)->save($die_type);
            //取出新中间域名
            $st["type"] = 0;
            $start_middle = M("Luodilink")->where($st)->order("id asc")->find();
            //检查中间域名屏蔽
            if (!$start_middle) {  //没有中间域名了.
                $msg = '<span style="color:red;">域名被封-无剩余中间域名</span>';
            } else {
                //删除中间域名
                //启用新的中间域名
                $a["id"] = $start_middle["id"];
                $b["type"] = 1;
                $b["start_time"] = date("m-d H:i:s");
                //在域名中启用
                $setRES = M("Luodilink")->where($a)->save($b);
                if ($setRES) {
                    $msg = '<span style="color:red;">域名被封-替换成功</span>';
                } else {
                    $msg = '<span style="color:red;">域名被封-替换失败</span>';
                }
            }
            $code = 9;
        } elseif ($checkRES["code"] == 9900) {  //域名正常
            $code = 0;
            $msg = '域名正常';
        } elseif ($checkRES["code"] == 139) {  //没有查询权限
            $code = 0;
            $msg = '没有查询权限';
        } elseif ($checkRES["code"] == 402) {  //查询过快
            $code = 0;
            $msg = '查询过快';
        } elseif ($checkRES["code"] == 888) {  //未知错误
            $code = 0;
            $msg = '未知错误';
        }
        echo json_encode(array('code' => $code, 'msg' => $msg, 'domain' => $middledomaindata["link"]));
    }

    //中间域名检测接口
    public function StartCheckMiddleDomain()
    {

        //定时刷新
        $time = date("is");
        // $time = substr($time,-3);
        // dump($time);die;
        // if ( 1710 <$time && $time < 1715) {  //每隔10分钟的位数是5的时候,刷新一次页面,
        // 	echo json_encode(array('code'=>11,'msg'=>'参数错误','domain'=>$domain));
        //     exit;
        // }
        //查出中间域名
        $sql["type"] = '1';
        $sql["use_type"] = 1;
        $middledomaindata = M("Domain")->where($sql)->find();
        // $checkRES = $this->CheckDomainApi($middledomaindata["domain"]);
        $checkRES = $this->checkDomainZong($middledomaindata["domain"]);
        if ($checkRES["code"] == 9904) { //域名被封了.
            //修改状态
            //关闭中间域名
            $ss["domain"] = $middledomaindata["domain"];
            $die_type["type"] = 2;
            $die_type["use_type"] = 0;
            $die_type["die_time"] = date("m-d H:i:s");
            $middle_domain_die = M("Domain")->where($ss)->save($die_type);
            //取出新中间域名
            $st["type"] = 0;
            $start_middle = M("Domain")->where($st)->order("id asc")->find();
            //检查中间域名屏蔽
            if (!$start_middle) {  //没有中间域名了.
                $msg = '<span style="color:red;">域名被封-无剩余中间域名</span>';
            } else {
                //删除中间域名
                //启用新的中间域名
                $a["id"] = $start_middle["id"];
                $b["type"] = 1;
                $b["use_type"] = 1;
                $b["start_time"] = date("m-d H:i:s");
                $b["today_time"] = date("Y-m-d");
                //在域名中启用
                $setRES = M("Domain")->where($a)->save($b);
                if ($setRES) {
                    $msg = '<span style="color:red;">域名被封-替换成功</span>';
                } else {
                    $msg = '<span style="color:red;">域名被封-替换失败</span>';
                }
            }
            $code = 9;
        } elseif ($checkRES["code"] == 9900) {  //域名正常
            $code = 0;
            $msg = '域名正常';
        } elseif ($checkRES["code"] == 139) {  //没有查询权限
            $code = 0;
            $msg = '没有查询权限';
        } elseif ($checkRES["code"] == 402) {  //查询过快
            $code = 0;
            $msg = '查询过快';
        } elseif ($checkRES["code"] == 888) {  //未知错误
            $code = 0;
            $msg = '未知错误';
        }
        //查出中间域名剩余个数
        $sql2["type"] = '0';
        $have_middle_muns = M("Domain")->where($sql2)->count();
        //查出中间最近1小时屏蔽个数
        $sql2["type"] = '2';
        $sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 hour")));
        $die_middle_domain_muns = M("Domain")->where($sql2)->count();
        //查出中间最近24小时屏蔽个数
        $sql2["type"] = 2;
        $sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 day")));
        $die_middle_domain_muns_day = M("Domain")->where($sql2)->count();
        echo json_encode(array('code' => $code, 'msg' => $msg, 'domain' => $middledomaindata["domain"], 'have_middle_muns' => $have_middle_muns, "die_middle_domain_muns" => $die_middle_domain_muns, "die_middle_domain_muns_day" => $die_middle_domain_muns_day));
    }

    //落地页面.
    public function StartCheckLuodiDomain()
    {
        //获取服务器ID
        $server_id = I("post.serverid");
        //查询这服务器Id对应的落地域名.
        $domainsql["server_id"] = $server_id;
        $domainsql["type"] = 1;
        $Luodidomaindata = M("Articledomain")->where($domainsql)->find();
        //开始检测
        // $checkRES = $this->CheckDomainApi($Luodidomaindata["domain"]);
        $checkRES = $this->checkDomainZong($Luodidomaindata["domain"]);
        if ($checkRES["code"] == 9904) { //域名被封了.
            //查出域名和服务器ID
            $luo["domain"] = $Luodidomaindata["domain"];
            $luo["server_id"] = $server_id;
            $setType["type"] = 2;
            $setType["die_time"] = date("m-d H:i:s");
            $chang_res = M("Articledomain")->where($luo)->save($setType);
            //启用新域名
            $newdomainsql["server_id"] = $server_id;
            $newdomainsql["type"] = 0;
            $newLuoDi = M("Articledomain")->where($newdomainsql)->order("id asc")->find();
            //修改域名状态
            $newMiddletypesql["id"] = $newLuoDi["id"];
            $newMiddletype['type'] = 1;
            $newMiddletype['start_time'] = date("m-d H:i:s");
            $chang_new_res11 = M("Articledomain")->where($newMiddletypesql)->save($newMiddletype);
            //修改所有链接的域名.
            $luodiLinkDomain["domain"] = $newLuoDi["domain"];
            $luodiLinkDomainsql["server_id"] = $server_id;
            $chang_new_res22 = M("Link")->where($luodiLinkDomainsql)->save($luodiLinkDomain);
            if ($chang_new_res11 && $chang_new_res22) {
                $msg = '域名被封,替换成功';
            } else {
                $msg = '域名被封,替换失败';
            }
            $code = 9;
        } elseif ($checkRES["code"] == 9900) {  //域名正常
            $code = 0;
            $msg = '域名正常';
        } elseif ($checkRES["code"] == 139) {  //没有查询权限
            $code = 0;
            $msg = '没有查询权限';
        } elseif ($checkRES["code"] == 402) {  //查询过快
            $code = 0;
            $msg = '查询过快';
        } elseif ($checkRES["code"] == 888) {  //未知错误
            $code = 0;
            $msg = '未知错误';
        }
        //查出域名剩余个数
        $sql2["type"] = '0';
        $sql2["server_id"] = $server_id;
        $have_luodi_muns = M("Articledomain")->where($sql2)->count();
        //查出中间最近1小时屏蔽个数
        $sql2["type"] = '2';
        $sql2["server_id"] = $server_id;
        $sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 hour")));
        $die_luodi_domain_muns = M("Articledomain")->where($sql2)->count();
        //查出中间最近24小时屏蔽个数
        $sql2["type"] = 2;
        $sql2["server_id"] = $server_id;
        $sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 day")));
        $die_luodi_domain_muns_day = M("Articledomain")->where($sql2)->count();
        // echo json_encode(array('code'=>0,'msg'=>$server_id));
        echo json_encode(array('code' => $code, 'msg' => $msg, 'domain' => $Luodidomaindata["domain"], 'have_luodi_muns' => $have_luodi_muns, "die_luodi_domain_muns" => $die_luodi_domain_muns, "die_luodi_domain_muns_day" => $die_luodi_domain_muns_day));
    }

    //id+1获取域名
    public function getRandDomain($min, $n, $max)
    {

        if ($n > $max || $n <= $min) {  //已经超出检测范围,直接开始从最小id开始.
            $cid["id"] = $min;
        } else {
            $cid["id"] = $n;   //没有超出反问,就按照传过来的参数检测.
        }
        $domain = M("Rukoudomain")->where($cid)->find();
        //
        if ($domain == '' || $domain["type"] != '1') {
            $next = $cid["id"] + 1;
            $nextmin = $min;
            $nextmax = $max;
            return $this->getRandDomain($nextmin, $next, $nextmax);
        } else {
            //满足情况
            return $domain;
        }
    }

    public function getRandDomainKuaizhan($min, $n, $max)
    {

        if ($n > $max || $n <= $min) {  //已经超出检测范围,直接开始从最小id开始.
            $cid["id"] = $min;
        } else {
            $cid["id"] = $n;   //没有超出反问,就按照传过来的参数检测.
        }
        $domain = M("kzrukou")->where($cid)->find();
        //
        if ($domain == '' || $domain["type"] != '1') {
            $next = $cid["id"] + 1;
            $nextmin = $min;
            $nextmax = $max;
            return $this->getRandDomainKuaizhan($nextmin, $next, $nextmax);
        } else {
            //满足情况
            return $domain;
        }
    }

    //外链的检测
    public function OutLinkDomainCheck()
    {
        if (IS_POST) {
            //获取正在检测的数据
            $nowcheckid = I("post.nowidoutlink");
            $maxid = I("post.maxidoutlink");
            $checkArr = I("post.checkArr");
        } else {
            $nowcheckid = 0;
        }
        //取出所有外链的链接
        $oulinksql["server_id"] = '0';
        $oulinksql["link"] = array("neq", "middle_domain_check");
        $outlinkdata = M("Link")->where($oulinksql)->group("domain")->field("id,domain")->select();
        // dump($outlinkdata);die;
        $str = '';
        foreach ($outlinkdata as $value) {
            $str .= $value['id'] . '/';
        }
        $str = substr($str, 0, -1);   //构建成了一个有id组成的字符串.
        $arr = explode("/", $str); //id的数组
        $maxid = count($arr);  //最大元素值
        // dump($arr);die;
        if ($nowcheckid == 999) {   //第一次检测,从0开始
            $nowcheckid = 0;
        } else {   //已经开始检测了.
            //判断已经到了哪一个
            if ($nowcheckid + 1 >= $maxid) {  //已经到最后一个数据
                $nowcheckid = 0;
            } else {
                $nowcheckid = $nowcheckid + 1;
            }
        }
        //获取第一个域名
        $fristDomainSql["id"] = $arr[$nowcheckid];
        $fristDomainData = M("Link")->where($fristDomainSql)->find();
        // $checkRES = $this->CheckDomainApi($fristDomainData["domain"]);
        $checkRES = $this->checkDomainZong($fristDomainData["domain"]);
        if ($checkRES["code"] == 9904) { //域名被封了.
            //查出域名使用用户
            $ss["domain"] = $fristDomainData["domain"];
            $user_domain = M("Link")->where($ss)->select();
            $biaojia1 = 0;
            $biaojia2 = 0;
            $nolinkuser = '';
            foreach ($user_domain as $value) {
                //检查是否为备用链接
                $user_code["code"] = $value["code"];
                $user_code["domain"] = $value["domain"];
                $re = M("Checkdomain")->where($user_code)->find();
                if (!$re) {
                    //域名不在备用表里,什么都不需要做.
                } else {
                    //屏蔽的是备用表里的域名
                    $id["id"] = $re["id"];
                    $type["type"] = 2;  //屏蔽了.
                    $type["die_time"] = date("m-d H:i:s");
                    //修改该域名状态
                    M("Checkdomain")->where($id)->save($type);
                }
                //获取下一个备用链接
                $do["code"] = $value["code"];
                $do["type"] = 0;
                $res = M("Checkdomain")->where($do)->order("id asc")->find();
                if (!$res) {
                    //没有备用链接了.
                    $biaojia1 = $biaojia1 + 1;
                    $nolinkuser .= $value['code'] . '/';
                    //修改链接状态,不允许访问.
                    $visit_code["code"] = $value["code"];
                    $visit_type["visit_type"] = 1;
                    M("Link")->where($visit_code)->save($visit_type);
                } else {
                    //将新链接写入link表
                    $code["code"] = $res["code"];
                    $link["link"] = $res["adv_link"];
                    $link["domain"] = $res["domain"];
                    $link["visit_type"] = 0; //将访问状态重新替换成0 ,可以访问.
                    M("Link")->where($code)->save($link);
                    //修改备用域名链接状态表
                    $id["id"] = $res["id"];
                    $cy["type"] = 1;
                    $cy["start_time"] = date("m-d H:i:s");
                    M("Checkdomain")->where($id)->save($cy);
                    $biaojia2 = $biaojia2 + 1;
                }
            }
            if ($biaojia1 > 0 && $biaojia2 == 0) {  //有没有域名的状态
                $msg = '<span style="color:red;">链接被封-' . $nolinkuser . '无备用链接</span>';
            } elseif ($biaojia2 > 0) { //有替换状态 需要刷新
                $msg = '<span style="color:red;">已经替换成功</span>';
            }
            $code = 9;
        } elseif ($checkRES["code"] == 9900) {  //域名正常
            //查出所有用户---看是否有备用链接
            $usercode["code"] = array("neq", 'middle_domain_check');
            $usercode["server_id"] = '0';
            $user_code_info = M("Link")->where($usercode)->select();
            $user_backup_link_muns = '';
            foreach ($user_code_info as $value) {
                //获取备用链接状态
                $code["code"] = $value["code"];
                if ($value["code"] == 'server_domain' || $value["server_id"] != '0') {

                } else {
                    $code["type"] = 0;
                    $muns = M("Checkdomain")->where($code)->count();
                    if ($muns == 0) {  //没有备用链接了
                        $user_backup_link_muns .= $value["code"] . '&nbsp;&nbsp;&nbsp;&nbsp;';
                    } else {
                        //检查有无两个同时启动
                        $code1["code"] = $value["code"];
                        $code1["type"] = 1;
                        $start_muns = M("Checkdomain")->where($code1)->count();
                        // dump($start_muns);die;
                        if ($start_muns >= 2) { //同时启动了两个
                            //查出两个
                            $start_link = M("Checkdomain")->where($code1)->select();
                            foreach ($start_link as $value1) {
                                $start_id["id"] = $value1["id"];
                                $start_type["type"] = 0;
                                $start_type["start_time"] = '0';
                                M("Checkdomain")->where($start_id)->save($start_type);
                            }
                            //重新启动一个

                            $restart_link = M("Checkdomain")->where($code)->order("id asc")->find();
                            $restart_link_id["id"] = $restart_link["id"];
                            $restart_link_type["type"] = 1;
                            $restart_link_type["start_time"] = date("m-d H:i:s");
                            M("Checkdomain")->where($restart_link_id)->save($restart_link_type);
                            //写入到link表
                            //将新链接写入link表
                            $code2["code"] = $restart_link["code"];
                            $link["link"] = $restart_link["adv_link"];
                            $link["domain"] = $restart_link["domain"];
                            $link["visit_type"] = 0; //将访问状态重新替换成0 ,可以访问.
                            $re = M("Link")->where($code2)->save($link);
                            $redis_link = M("Link")->where($code2)->find();
                            //重写redis链接状态
                            //--------------------------redis开始---------------------------
                            $rediscode = $redis_link["sui_code"];  //构建redis唯一key值
                            $redis_link = serialize($redis_link);          // 序列化
                            //--------------------------存入redis----------------------------
                            $this->redis->set($rediscode, $redis_link);
                            //--------------------------缓存1小时----------------------------
                            $this->redis->setTimeout($rediscode, 60 * 1);

                            //--------------------------redis结束----------------------------

                        }
                    }
                }
            }
            $code = 0;
            $msg = '域名正常';
        } elseif ($checkRES["code"] == 139) {  //没有查询权限
            $code = 0;
            $msg = '没有权限';
        } elseif ($checkRES["code"] == 402) {  //查询过快
            $code = 0;
            $msg = '查询过快';
        } elseif ($checkRES["code"] == 888) {  //未知错误
            $code = 0;
            $msg = '未知错误';
        }
        echo json_encode(array(
            'code' => $code,
            'msg' => $msg,
            'domain' => $fristDomainData["domain"],
            'nowidoutlink' => $nowcheckid,   //目前正在检测的
            'checkArr' => $arr,     //检测数组
            'maxidoutlink' => $maxid,  //  检测个数
            'shijiID' => $fristDomainSql["id"],  //  检测个数
            "user_backup_link_muns" => '用户:<span style="color:red;">' . $user_backup_link_muns . '</span>没有备用链接了<br>'
        ));

    }

    //借权检测
    public function KuaizhanDomain()
    {
        //是否已经开始检测
        $checkid = I("post.kznowid");
        // $checkid = 1;
        // $Maxcheckid = I("post.maxid");
        //查出所有入口
        $rukoutyep["type"] = 1;
        $AllGateDomain = M("Kzrukou")->where($rukoutyep)->select();
        // dump($AllGateDomain);die;
        $sort = array(
            'direction' => 'SORT_ASC', //排序SORT_DESC 降序；SORT_ASC 升序
            'field' => 'id',       //排序字段
        );
        $arrSort = array();
        foreach ($AllGateDomain as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if ($sort['direction']) {
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $AllGateDomain);
        }
        //获取第一个ID
        reset($AllGateDomain);
        $first_key = current($AllGateDomain);
        $first_id = $first_key["id"];
        $min["id"] = $first_key["id"];
        //最后一个元素
        $last = end($AllGateDomain);
        $Maxcheckid = $last["id"];
        //正在检测的
        if ($checkid == 0) {
            $checkid = $min["id"];
            $aaa = "等于0";
        } else {
            $checkid = $checkid + 1;
            $aaa = "加1了";
        }

        $Domain_rukou = $this->getRandDomainKuaizhan($min["id"], $checkid, $Maxcheckid);
        // dump($Domain_rukou);die;
        // dump($Domain_rukou);die;
        // echo json_encode(array('code'=>0,'msg'=>$Domain_rukou,'minid'=>$min["id"],"nowid"=>$Domain_rukou["id"],"nowid11"=>$Domain_rukou["id"],"maxid"=>$Maxcheckid,"aaa"=>$aaa,'CheckRES'=>$checkRES));die;
        // die;
        $checkRES = $this->checkDomainZong($Domain_rukou["domain"]);
        // $checkRES = $this->CheckDomainApi($Domain_rukou["domain"]);
        // dump($checkRES);die;
        if ($checkRES["code"] == 9904) { //域名被封了.
            $id["domain"] = $Domain_rukou["domain"];
            $type["type"] = '2';
            $type["die_time"] = date("m-d H:i:s");
            $re = M("Kzrukou")->where($id)->save($type);
            if ($re) {
                $msg = '<span style="color:red">域名被封,设置成功</span>';
            } else {
                $msg = '<span style="color:red"域名被封,设置失败</span>';
            }
            $code = 9;
        } elseif ($checkRES["code"] == 9900) {  //域名正常
            $code = 0;
            $msg = '域名正常';
        } elseif ($checkRES["code"] == 139) {  //没有查询权限
            $code = 0;
            $msg = '没有权限';
        } elseif ($checkRES["code"] == 402) {  //查询过快
            $code = 0;
            $msg = '查询过快';
        } elseif ($checkRES["code"] == 888) {  //未知错误
            $code = 0;
            $msg = '未知错误';
        }

        //查出域名剩余个数
        $sql2["type"] = '0';
        $have_rukou_muns = M("Kzrukou")->where($sql2)->count();
        //查出正在使用个数
        $sql2["type"] = '1';
        $isuse_rukou_domain_muns = M("Kzrukou")->where($sql2)->count();
        //查出最近1小时屏蔽个数
        $sql2["type"] = '2';
        $sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 hour")));
        $die_rukou_domain_muns = M("Kzrukou")->where($sql2)->count();
        //查出最近24小时屏蔽个数
        $sql2["type"] = '2';
        $sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 day")));
        $die_rukou_domain_muns_day = M("Kzrukou")->where($sql2)->count();
        echo json_encode(array(
            'code' => $code,
            'msg' => $msg,
            'domain' => $Domain_rukou["domain"],
            'have_rukou_muns' => $have_rukou_muns,
            "isuse_rukou_domain_muns" => $isuse_rukou_domain_muns,
            "die_rukou_domain_muns" => $die_rukou_domain_muns,
            "die_rukou_domain_muns_day" => $die_rukou_domain_muns_day,
            'kznowid' => $Domain_rukou["id"],
            'checkid' => $checkid,
            'kzminid' => $min["id"],
            'kzmaxid' => $Maxcheckid,
            'aaa' => $aaa,
            'fanhui' => $Domain_rukou,
        ));
    }

    //入口域名检测
    public function StartCheckGetaDomain()
    {
        //是否已经开始检测
        $checkid = I("post.nowid");
        // $checkid = 235;
        // $Maxcheckid = I("post.maxid");
        //查出所有入口
        $rukoutyep["type"] = 1;
        $AllGateDomain = M("Rukoudomain")->where($rukoutyep)->select();
        // dump($AllGateDomain);die;
        $sort = array(
            'direction' => 'SORT_ASC', //排序SORT_DESC 降序；SORT_ASC 升序
            'field' => 'id',       //排序字段
        );
        $arrSort = array();
        foreach ($AllGateDomain as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if ($sort['direction']) {
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $AllGateDomain);
        }
        //获取第一个ID
        reset($AllGateDomain);
        $first_key = current($AllGateDomain);
        $first_id = $first_key["id"];
        $min["id"] = $first_key["id"];
        //最后一个元素
        $last = end($AllGateDomain);
        $Maxcheckid = $last["id"];
        //正在检测的
        if ($checkid == 0) {
            $checkid = $min["id"];
            $aaa = "等于0";
        } else {
            $checkid = $checkid + 1;
            $aaa = "加1了";
        }

        $Domain_rukou = $this->getRandDomain($min["id"], $checkid, $Maxcheckid);

        // $checkRES = $this->CheckDomainApi($Domain_rukou["domain"]);
        $checkRES = $this->checkDomainZong($Domain_rukou["domain"]);

        if ($checkRES["code"] == 9904) { //域名被封了.
            $id["domain"] = $Domain_rukou["domain"];
            $type["type"] = '2';
            $type["die_time"] = date("m-d H:i:s");
            $re = M("Rukoudomain")->where($id)->save($type);
            if ($re) {
                $msg = '<span style="color:red">域名被封,设置成功</span>';
            } else {
                $msg = '<span style="color:red"域名被封,设置失败</span>';
            }
            $code = 9;
        } elseif ($checkRES["code"] == 9900) {  //域名正常
            $code = 0;
            $msg = '域名正常';
        } elseif ($checkRES["code"] == 139) {  //没有查询权限
            $code = 0;
            $msg = '没有权限';
        } elseif ($checkRES["code"] == 402) {  //查询过快
            $code = 0;
            $msg = '查询过快';
        } elseif ($checkRES["code"] == 888) {  //未知错误
            $code = 0;
            $msg = '未知错误';
        }

        //查出域名剩余个数
        $sql2["type"] = '0';
        $have_rukou_muns = M("Rukoudomain")->where($sql2)->count();
        //查出正在使用个数
        $sql2["type"] = '1';
        $isuse_rukou_domain_muns = M("Rukoudomain")->where($sql2)->count();
        //查出最近1小时屏蔽个数
        $sql2["type"] = '2';
        $sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 hour")));
        $die_rukou_domain_muns = M("Rukoudomain")->where($sql2)->count();
        //查出最近24小时屏蔽个数
        $sql2["type"] = '2';
        $sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 day")));
        $die_rukou_domain_muns_day = M("Rukoudomain")->where($sql2)->count();
        echo json_encode(array(
            'code' => $code,
            'msg' => $msg,
            'domain' => $Domain_rukou["domain"],
            'have_rukou_muns' => $have_rukou_muns,
            "isuse_rukou_domain_muns" => $isuse_rukou_domain_muns,
            "die_rukou_domain_muns" => $die_rukou_domain_muns,
            "die_rukou_domain_muns_day" => $die_rukou_domain_muns_day,
            'nowid' => $Domain_rukou["id"],
            'checkid' => $checkid,
            'minid' => $min["id"],
            'maxid' => $Maxcheckid,
            'aaa' => $aaa,
            'fanhui' => $Domain_rukou,
        ));
    }

    public function checkDomainZong($domain = 'lffwkjd.cn')
    {
        $id["id"] = 1;
        $url = M("Password")->where($id)->find();
        $api_url = $url["checkdomain"] . 'http://' . $domain;

        $content = $this->get_msg($api_url);
        $data = json_decode($content, true);
        // dump($data);die;
        if ($data["status"] == 1) {
            return $re = array(
                'code' => 9904,
                'msg' => "域名被封"
            );
        } elseif ($data["status"] == 0) {
            return $re = array(
                'code' => 9900,
                'msg' => "域名访问正常"
            );
        } elseif ($data["status"] == 2) {
            return $re = array(
                'code' => 139,
                'msg' => "没有查询权限"
            );
        } elseif ($data["status"] == 402) {
            return $re = array(
                'code' => 402,
                'msg' => "查询过快"
            );
        } else {
            return $re = array(
                'code' => 888,
                'msg' => "未知错误"
            );
        }

    }

    //域名检测返回接口
    public function CheckDomainApi($domain)
    {
        $id["id"] = 1;
        $url = M("Password")->where($id)->find();
        $api_url = $url["checkdomain"] . $domain;
        $content = $this->get_msg($api_url);
        $data = json_decode($content, true);
        if ($data["code"] == 9904) {
            return $re = array(
                'code' => 9904,
                'msg' => "域名被封"
            );
        } elseif ($data["code"] == 9900) {
            return $re = array(
                'code' => 9900,
                'msg' => "域名访问正常"
            );
        } elseif ($data["code"] == 139) {
            return $re = array(
                'code' => 139,
                'msg' => "没有查询权限"
            );
        } elseif ($data["code"] == 402) {
            return $re = array(
                'code' => 402,
                'msg' => "查询过快"
            );
        } else {
            return $re = array(
                'code' => 888,
                'msg' => "未知错误"
            );
        }
    }

    public function start()
    {
        $domain = M("Link")->field("code,domain,link")->order("code asc")->select();
        $this->re = $domain;
        //构建批量检测的数组
        // $check_link = M("Link")->field("domain,link")->select();
        $check_link = M("Link")->field("domain")->select();
        foreach ($check_link as $key => $value) {
            $check[] = $value["domain"];
            if ($value["link"] == 'middle_domain_check') {

            } else {
                $check[] = $value["link"];
            }
        }
        foreach ($check as $key => $value) {
            if ($value == '') {
                unset($check[$key]);
            }
        }
        $check = array_unique($check);
        $check = array_values($check);
        // dump($check);
        $muns = count($check);
        // dump($muns);die;
        $this->check = json_encode($check); //查询数组
        $this->muns = $muns; //查询个数
        $this->display();
    }

    //系统自检
    public function check_self_set()
    {
        //系统自检开始
        //1-检查中间域名情况
        //检查中间域名启用个数
        $middle_domain_type["type"] = 1;
        $middle_domain_type["use_type"] = 1;
        $middle_domain_muns = M("Domain")->where($middle_domain_type)->count();
        if ($middle_domain_muns == 1) { //中间域名个数正常
            //啥也不做
            $middle_domain_change_type = 0;
        } else {  //中间域名个数不正常,那就重置中间域名,还有检测链接
            //查出所有中间域名
            $middle_domain = M("Domain")->where($middle_domain_type)->select();
            //挨个重置状态
            foreach ($middle_domain as $value) {
                $id["id"] = $value["id"];
                //修改状态
                $change_middle_domain_type["type"] = 0;
                $change_middle_domain_type["use_type"] = 0;
                $change_middle_domain_type["start_time"] = '';
                M("Domain")->where($id)->save($change_middle_domain_type);
            }
            //重置完成后,获取新的中间域名
            $get_middle_domain["type"] = 0;
            $get_middle_domain["use_type"] = 0;
            $get_new_middle_domain = M("Domain")->where($get_middle_domain)->order("id desc")->find();
            if (!$get_new_middle_domain) {  //没有中间域名了.
                echo json_encode(array('code' => 1, 'msg' => '没有中间域名了'));
                die;
            }
            //修改该域名状态
            $change_id["id"] = $get_new_middle_domain["id"];
            $change_type["type"] = 1;
            $change_type["use_type"] = 1;
            $change_type["start_time"] = date("m-d H:i:s");
            M("Domain")->where($change_id)->save($change_type);
            //修改后,增加中间域名检测链接
            //修改中间域名链接
            $middle_domain_link["code"] = "middle_domain_check";
            $middle_domain_link["username"] = "0";
            $middle_domain_link["sui_code"] = "middle_domain_check";
            $middle_domain_link["link"] = "middle_domain_check";
            $middle_domain_link["cnzz"] = "middle_domain_check";
            $middle_domain_link["self_cnzz"] = "middle_domain_check";
            $middle_domain_link["self_cnzz_title"] = "middle_domain_check";
            $new_middle_domain["domain"] = $get_new_middle_domain["domain"];
            M("Link")->where($middle_domain_link)->save($new_middle_domain);
            //修改中间域名redis
            $redis_new_middle_domain = serialize($get_new_middle_domain);          // 序列化
            //--------------------------存入redis----------------------------
            $this->redis->set("middle:domain", $redis_new_middle_domain);
            //--------------------------缓存1小时----------------------------
            $this->redis->setTimeout("middle:domain", 60 * 1);
        }
        //检查link表中的中间域名.
        $middleDomainSql["code"] = "middle_domain_check";
        $middleDomainSql["sui_code"] = "middle_domain_check";
        $middleDomainMuns = M("Link")->where($middleDomainSql)->count();
        if ($middleDomainMuns != 1) {  //不正常
            //先删除
            M("Link")->where($middleDomainSql)->delete();
            //再增加
            $middle_domain_link2["code"] = "middle_domain_check";
            $middle_domain_link2["username"] = "0";
            $middle_domain_link2["sui_code"] = "middle_domain_check";
            $middle_domain_link2["link"] = "middle_domain_check";
            $middle_domain_link2["cnzz"] = "middle_domain_check";
            $middle_domain_link2["self_cnzz"] = "middle_domain_check";
            $middle_domain_link2["self_cnzz_title"] = "middle_domain_check";
            $middle_domain_type1["type"] = 1;
            $middle_domain_type1["use_type"] = 1;
            $middle_domain = M("Domain")->where($middle_domain_type1)->find();
            $middle_domain_link2["domain"] = $middle_domain["domain"];
            M("Link")->add($middle_domain_link2);
        } else {

        }


        //检查备用链接情况
        //查出所有用户
        $usercode["code"] = array("neq", 'middle_domain_check');
        $user_code_info = M("Link")->where($usercode)->select();
        $user_backup_link_muns = '';
        foreach ($user_code_info as $value) {
            //获取备用链接状态
            $code["code"] = $value["code"];
            if ($value["code"] == 'server_domain' || $value["server_id"] != '') {

            } else {
                $code["type"] = 0;
                $muns = M("Checkdomain")->where($code)->count();
                if ($muns == 0) {  //没有备用链接了
                    $user_backup_link_muns .= $value["code"] . '&nbsp;&nbsp;&nbsp;&nbsp;';
                } else {
                    //检查有无两个同时启动
                    $code1["code"] = $value["code"];
                    $code1["type"] = 1;
                    $start_muns = M("Checkdomain")->where($code1)->count();
                    // dump($start_muns);die;
                    if ($start_muns >= 2) { //同时启动了两个
                        //查出两个
                        $start_link = M("Checkdomain")->where($code1)->select();
                        foreach ($start_link as $value) {
                            $start_id["id"] = $value["id"];
                            $start_type["type"] = 0;
                            $start_type["start_time"] = '0';
                            M("Checkdomain")->where($start_id)->save($start_type);
                        }
                        //重新启动一个

                        $restart_link = M("Checkdomain")->where($code)->order("id asc")->find();
                        $restart_link_id["id"] = $restart_link["id"];
                        $restart_link_type["type"] = 1;
                        $restart_link_type["start_time"] = date("m-d H:i:s");
                        M("Checkdomain")->where($restart_link_id)->save($restart_link_type);
                        //写入到link表
                        //将新链接写入link表
                        $code2["code"] = $restart_link["code"];
                        $link["link"] = $restart_link["adv_link"];
                        $link["domain"] = $restart_link["domain"];
                        $link["visit_type"] = 0; //将访问状态重新替换成0 ,可以访问.
                        M("Link")->where($code2)->save($link);
                        $redis_link = M("Link")->where($code2)->find();
                        //重写redis链接状态
                        //--------------------------redis开始---------------------------
                        $rediscode = $redis_link["sui_code"];  //构建redis唯一key值
                        $redis_link = serialize($redis_link);          // 序列化
                        //--------------------------存入redis----------------------------
                        $this->redis->set($rediscode, $redis_link);
                        //--------------------------缓存1小时----------------------------
                        $this->redis->setTimeout($rediscode, 60 * 1);

                        //--------------------------redis结束----------------------------

                    }
                }
            }
        }
        //检查备用链接有无两个同时启用的情


        // $this->checkServerDomain();


        if ($user_backup_link_muns == '') {
            echo json_encode(array('code' => 0, 'msg' => '自检完成<br>中间域名数量正常<br>备用链接数量正常<br>'));
        } else {
            echo json_encode(array('code' => 0, 'msg' => '自检完成<br>中间域名数量正常<br>用户:<span style="color:red;">' . $user_backup_link_muns . '</span>没有备用链接了<br>'));
        }
    }

    public function checkServerDomain()
    {
        $sererId = M("Serverid")->select();
        foreach ($sererId as $value) {
            $server_id["link"] = $value["server_id"];
            $server_id["cnzz"] = $value["server_id"];
            $server_id["code"] = "server_domain";
            $server_id["sui_code"] = "server_domain";
            $ServerDomainRe = M("Link")->where($server_id)->find();
            $ServerDomain = $ServerDomainRe["domain"];
            //查出目前正在使用的落地域名
            $ArticledomainType["type"] = 1;
            $ArticledomainType["server_id"] = $value["server_id"];
            $Articledomain = M("Articledomain")->where($ArticledomainType)->find();
            //检查server_domain域名
            if ($ServerDomain == '' || $ServerDomain != $Articledomain["domain"]) {
                //落地域名为空
                $server_id_domain["domain"] = $Articledomain["domain"];
                $re1 = M("Link")->where($server_id)->save($server_id_domain);
            } else {

            }
            //检查用户的链接和用户域名的状态
            $UserArtcleDomain['domain'] = array("neq", $Articledomain["domain"]);
            $UserArtcleDomain['server_id'] = $value["server_id"];
            $UserArtcleLink = M("Link")->where($UserArtcleDomain)->select();
            if ($UserArtcleLink) {
                foreach ($UserArtcleLink as $UserArtcleLinkvalue) {
                    $ChangArtcleDomainId['id'] = $UserArtcleLinkvalue["id"];
                    $old_domain = top_domain($UserArtcleLinkvalue["link"]);
                    $news_domain_rep = trim($Articledomain["domain"]);
                    $new_link = str_replace($old_domain, $news_domain_rep, $url);
                    //存入数据库
                    $user_link_data["domain"] = trim($Articledomain["domain"]);
                    $user_link_data["link"] = $new_link;
                    $user_link_data["visit_type"] = 0;
                    M("Link")->where($ChangArtcleDomainId)->save($user_link_data);
                }
            } else {

            }
        }

    }

    public function check_domain()
    {
        $domain = I("post.domain");
        $id["id"] = 1;
        $url = M("Password")->where($id)->find();
        $api_url = $url["checkdomain"] . $domain;
        $content = $this->get_msg($api_url);
        $data = json_decode($content, true);

        if (strrpos($content, "参数错误") > 0) {
            echo json_encode(array('code' => 0, 'msg' => '参数错误'));
            exit;
        }

        if (strrpos($content, "频率") > 0) {
            echo json_encode(array('code' => 0, 'msg' => '频率过快'));
            exit;
        }

        if ($data['status'] == 2) {
            echo json_encode(array('code' => 0, 'msg' => '域名被封'));
        } else if ($data['status'] == 0) {
            echo json_encode(array('code' => 0, 'msg' => '域名正常'));
        } else if ($data['status'] == 3) {
            echo json_encode(array('code' => 0, 'msg' => '查询失败'));
        }
    }

    public function start_auto_check1()
    {
        $time = date("is");
        $time = substr($time, -3);
        // dump($time);die;
        if (710 < $time && $time < 725) {  //每隔10分钟的位数是5的时候,刷新一次页面,
            echo json_encode(array('code' => 8, 'msg' => '参数错误', 'domain' => $domain));
            exit;
        }
        $domain = I("post.domain");
        //检查是否是空域名
        if ($domain == '') {  //直接重新刷新
            echo json_encode(array('code' => 1, 'msg' => '<span style="color:red;">出现空域名-请检查广告</span>'));
            die;
        }
        $id["id"] = 1;
        $url = M("Password")->where($id)->find();
        // $api_url = $url["checkdomain"].$domain ;
        $api_url = "http://wz5.tkc8.com/manage/api/check?token=81b5d6225dec44943f641305d711d052&url=" . $domain;
        $content = $this->get_msg($api_url);
        $data = json_decode($content, true);
        if ($data["code"] == 9904) {
            //参数传递
            //判断是链接还是域名
            if (substr($domain, 0, 7) == 'http://' || substr($domain, 0, 8) == 'https://') {
                //为链接屏蔽了.
                //查出链接的域名
                $link_domian = top_domain($domain);
                //查出域名使用用户
                $ss["domain"] = $link_domian;
                $user_domain = M("Link")->where($ss)->select();
                $biaojia1 = 0;
                $biaojia2 = 0;
                foreach ($user_domain as $value) {
                    //检查是否为备用链接
                    $user_code["code"] = $value["code"];
                    $user_code["domain"] = $value["domain"];
                    $re = M("Checkdomain")->where($user_code)->find();
                    if (!$re) {
                        //域名不在备用表里,什么都不需要做.
                    } else {
                        //屏蔽的是备用表里的域名
                        $id["id"] = $re["id"];
                        $type["type"] = 2;  //屏蔽了.
                        $type["die_time"] = date("m-d H:i:s");
                        //修改该域名状态
                        M("Checkdomain")->where($id)->save($type);
                    }
                    //获取下一个备用链接
                    $do["code"] = $value["code"];
                    $do["type"] = 0;
                    $res = M("Checkdomain")->where($do)->order("id asc")->find();
                    if (!$res) {
                        //没有备用链接了.
                        $biaojia1 = $biaojia1 + 1;
                        //修改链接状态,不允许访问.
                        $visit_code["code"] = $value["code"];
                        $visit_type["visit_type"] = 1;
                        M("Link")->where($visit_code)->save($visit_type);
                    } else {
                        //将新链接写入link表
                        $code["code"] = $res["code"];
                        $link["link"] = $res["adv_link"];
                        $link["domain"] = $res["domain"];
                        $link["visit_type"] = 0; //将访问状态重新替换成0 ,可以访问.
                        M("Link")->where($code)->save($link);
                        //修改备用域名链接状态表
                        $id["id"] = $res["id"];
                        $cy["type"] = 1;
                        $cy["start_time"] = date("m-d H:i:s");
                        M("Checkdomain")->where($id)->save($cy);
                        $biaojia2 = $biaojia2 + 1;
                    }
                }
                if ($biaojia1 > 0 && $biaojia2 == 0) {  //有没有域名的状态
                    echo json_encode(array('code' => 0, 'msg' => '<span style="color:red;">链接被封-请检查是否有备用链接</span>', 'domain' => $domain));
                    die;
                } elseif ($biaojia2 > 0) { //有替换状态 需要刷新
                    echo json_encode(array('code' => 9, 'msg' => '<span style="color:red;">已经替换成功</span>', 'domain' => $domain));
                    die;
                }
            } else {

                //判断是否为中间域名
                $wertwet["domain"] = $domain;
                $wertwet["code"] = "middle_domain_check";
                $wertwet["sui_code"] = "middle_domain_check";
                $wertwet["link"] = "middle_domain_check";
                // dump($wertwet);
                $middle_domain = M("Link")->where($wertwet)->find();
                // dump($middle_domain);die;
                if ($middle_domain) { //是中间域名
                    //重新启用新的中间域名
                    //关闭中间域名
                    $ss["domain"] = I("post.domain");
                    $die_type["type"] = 2;
                    $die_type["use_type"] = 0;
                    $die_type["die_time"] = date("m-d H:i:s");
                    $middle_domain_die = M("Domain")->where($ss)->save($die_type);
                    //取出新中间域名
                    $st["type"] = 0;
                    $start_middle = M("Domain")->where($st)->order("id asc")->find();
                    //检查中间域名屏蔽
                    if (!$start_middle) {  //没有中间域名了.
                        echo json_encode(array('code' => 0, 'msg' => '<span style="color:red;">中间域名被封-请检查是否有备用域名</span>', 'domain' => $domain));
                    } else {
                        //删除中间域名
                        $middle_domain_id["id"] = $middle_domain["id"];
                        M("Link")->where($middle_domain_id)->delete();
                        //启用新的中间域名
                        $a["id"] = $start_middle["id"];
                        $b["type"] = 1;
                        $b["use_type"] = 1;
                        $b["start_time"] = date("m-d H:i:s");
                        $b["today_time"] = date("Y-m-d");
                        //在域名中启用
                        M("Domain")->where($a)->save($b);
                        //更新屏蔽链接
                        $g["code"] = 'middle_domain_check';
                        $g["sui_code"] = 'middle_domain_check';
                        $g["domain"] = $start_middle['domain'];
                        $g["link"] = 'middle_domain_check';
                        $g["cnzz"] = 'middle_domain_check';
                        $g["self_cnzz"] = 'middle_domain_check';
                        $g["self_cnzz_title"] = 'middle_domain_check';
                        $g["self_cnzz_titlecut_mun"] = 0;
                        $g["visit_type"] = 0;
                        $g["var_time"] = date("Y-m-d");
                        $g["type"] = 0;
                        M("Link")->add($g);
                        //刷新页面
                        echo json_encode(array('code' => 9, 'msg' => '<span style="color:red;">域名被封-请检查是否有备用链接</span>', 'domain' => $domain));
                    }

                }

                //检查是不是用户落地页域


                //判断是否为落地页
                $server_sql["domain"] = $domain;
                $server_sql["code"] = "server_domain";
                $server_sql["sui_code"] = "server_domain";
                $server_domain_info = M("Link")->where($server_sql)->find();
                // dump(M()->_sql());

                // dump($server_domain_info);die;
                if ($server_domain_info) {   // 是落地文案域名
                    //查出接口域名
                    $get_port_domain["domain"] = $server_domain_info["domain"];
                    $get_port_domain["server_id"] = $server_domain_info["link"];
                    $port_domain = M("Articledomain")->where($get_port_domain)->find();
                    // dump(M()->_sql());
                    // dump($port_domain);

                    //开始替换
                    //屏蔽现有的.
                    $article_domain["domain"] = $domain;
                    $article_domain["server_id"] = $server_domain_info["link"];
                    $chang_article_domain["type"] = 2;
                    $chang_article_domain["die_time"] = date("m-d H:i:s");
                    $chang_res = M("Articledomain")->where($article_domain)->save($chang_article_domain);
                    //组成访问地址
                    $port_url = "http://" . $port_domain["port_domain"] . "/index.php/Home/Auto/chang_article_domain/domain/" . $domain;
                    // dump($port_url);die;
                    //告知对面服务器
                    $content = $this->go_article_domain($port_url);
                    $data = json_decode($content, true);
                    if ($data["code"] == "0") {   //屏蔽完成,获取新域名
                        $new_domain["domain"] = $data["domain"];
                        //启用新域名
                        ////更新新域名有问题.
                        $chang_info["type"] = 1;
                        $chang_info["start_time"] = date("m-d H:i:s");
                        $new_article_domain_id["domain"] = $new_domain["domain"];
                        $new_article_domain_id["server_id"] = $port_domain["server_id"];
                        // dump($new_article_domain_id);
                        $sdf = M("Articledomain")->where($new_article_domain_id)->save($chang_info);
                        // dump(M()->_sql());
                        // dump($sdf);
                        //写入link表并替换主域名
                        $server_domain_link_sql["code"] = 'server_domain';
                        $server_domain_link_sql["sui_code"] = 'server_domain';
                        $server_domain_link_sql["link"] = $port_domain["server_id"];
                        $server_domain_link_sql["cnzz"] = $port_domain["server_id"];
                        $server_domain_link_sql["self_cnzz"] = $port_domain["server_id"];
                        $server_domain_link_sql["self_cnzz_title"] = $port_domain["server_id"];
                        $server_domain_link_sql["server_id"] = "server_domain";
                        $server_domain_link["domain"] = $new_domain["domain"];
                        $fghjkl = M("Link")->where($server_domain_link_sql)->save($server_domain_link);
                        // dump($fghjkl);
                        //替换所有该服务器上用户的链接域名
                        //取出链接.

                        $user_adv_link["server_id"] = $port_domain["server_id"];
                        $user_link = M("Link")->where($user_adv_link)->select();
                        // dump($user_link);die;
                        if ($user_link) {
                            foreach ($user_link as $value) {
                                $url = $value["link"];
                                $old_domain = top_domain($url);
                                $news_domain_rep = $new_domain["domain"];
                                $new_link = str_replace($old_domain, $news_domain_rep, $url);
                                //存入数据库
                                $user_link_id["id"] = $value["id"];
                                $user_link_data["domain"] = $new_domain["domain"];
                                $user_link_data["link"] = $new_link;
                                M("Link")->where($user_link_id)->save($user_link_data);
                            }
                            echo json_encode(array('code' => 9, 'msg' => '<span ">替换完成了.</span>', 'domain' => $domain));
                            die;
                        } else {
                            echo json_encode(array('code' => 9, 'msg' => '<span ">替换完成了.</span>', 'domain' => $domain));
                            die;
                        }
                    } else {
                        echo json_encode(array('code' => 0, 'msg' => "错误信息:" . $data["msg"], 'domain' => $domain));
                        die;
                    }


                }
                // if ($chang_res) {   //屏蔽成功,获取下一个,并修改状态
                // 	$select_info["type"] = 0;
                // 	$select_info["server_id"] = $server_domain_info["server_id"];

                // 	$new_article_domain = M("Articledomain")->where($select_info)->order("id asc")->find();
                // 	//修改状态
                // 	$chang_info["type"] = 1;
                // 	$chang_info["start_time"] = date("m-d H:i:s");
                // 	$new_article_domain_id["id"] = $new_article_domain["id"];
                // 	M("Articledomain")->where($new_article_domain_id)->save($chang_info);
                // 	//写入link表并替换主域名

                // 	$server_domain_link_sql["code"] = 'server_domian';
                // 	$server_domain_link_sql["sui_code"] = 'server_domian';
                // 	$server_domain_link_sql["link"] = $new_article_domain["server_id"];
                // 	$server_domain_link_sql["cnzz"] = $new_article_domain["server_id"];
                // 	$server_domain_link_sql["self_cnzz"] = $new_article_domain["server_id"];
                // 	$server_domain_link_sql["self_cnzz_title"] = $new_article_domain["server_id"];
                // 	$server_domain_link_sql["server_id"] = "server_domian";
                // 	$server_domain_link["domain"] = $new_article_domain["domain"];
                // 	M("Link")->where($server_domain_link_sql)->save($server_domain_link);
                // 	//替换所有该服务器上用户的链接域名
                // 	//取出链接.

                // 	$user_adv_link["server_id"] = $new_article_domain["server_id"];
                // 	$user_link = M("Link")->where($user_adv_link)->select();
                // 	foreach ($user_link as $value) {
                // 		$url = $value["link"];
                // 		$old_domain = top_domain($url);
                // 		$news_domain = $new_article_domain["domain"];
                // 		$new_link = str_replace($top_domain,$news_domain,$url);
                // 		//存入数据库
                // 		$user_link_id["id"] = $value["id"];
                // 		$user_link_data["domain"] = $new_article_domain["domain"];
                // 		$user_link_data["link"] = $new_link;
                // 		M("Link")->where($user_link_id)->save($user_link_data);
                // 	}
                // 	//告知文案服务器域名屏蔽了.
                // 	//组成访问地址
                // 	$port_url = "http://".$port_domain["port_domain"]."/index.php/Home/Auto/chang_article_domain/domain/".$domain;
                // 	//告知对面服务器
                // 	$content = $this->go_article_domain($port_url);
                // 			$data = json_decode($content,true);
                // 			if($data["code"] == "0"){  //表示替换成功
                // 				echo json_encode(array('code'=>0,'msg'=>'<span style="color:red;">域名被封-文案域名替换失败,请检查</span>','domain'=>$domain));
                // 			die;
                // 			}else{
                // 				echo json_encode(array('code'=>9,'msg'=>'<span ">替换完成了.</span>','domain'=>$domain));
                // 			die;
                // 			}
                // }


                //查出域名使用用户
                $ss["domain"] = I("post.domain");
                $user_domain = M("Link")->where($ss)->select();
                $biaojia1 = 0;
                $biaojia2 = 0;
                foreach ($user_domain as $value) {
                    //检查是否为备用链接
                    $user_code["code"] = $value["code"];
                    $user_code["domain"] = $value["domain"];
                    $re = M("Checkdomain")->where($user_code)->find();
                    if (!$re) {
                        //域名不在备用表里,什么都不需要做.
                    } else {
                        //屏蔽的是备用表里的域名
                        $id["id"] = $re["id"];
                        $type["type"] = 2;  //屏蔽了.
                        $type["die_time"] = date("m-d H:i:s");
                        //修改该域名状态
                        M("Checkdomain")->where($id)->save($type);
                    }
                    //获取下一个备用链接
                    $do["code"] = $value["code"];
                    $do["type"] = 0;
                    $res = M("Checkdomain")->where($do)->order("id asc")->find();
                    if (!$res) {
                        //没有备用链接了.
                        $biaojia1 = $biaojia1 + 1;
                        //修改链接状态,不允许访问.
                        $visit_code["code"] = $value["code"];
                        $visit_type["visit_type"] = 1;
                        M("Link")->where($visit_code)->save($visit_type);
                    } else {
                        //将新链接写入link表
                        $code["code"] = $res["code"];
                        $link["link"] = $res["adv_link"];
                        $link["domain"] = $res["domain"];
                        $link["visit_type"] = 0; //将访问状态重新替换成0 ,可以访问.
                        M("Link")->where($code)->save($link);
                        //修改备用域名链接状态表
                        $id["id"] = $res["id"];
                        $cy["type"] = 1;
                        $cy["start_time"] = date("m-d H:i:s");
                        M("Checkdomain")->where($id)->save($cy);
                        $biaojia2 = $biaojia2 + 1;
                    }
                }
                if ($biaojia1 > 0 && $biaojia2 == 0) {  //有没有域名的状态
                    echo json_encode(array('code' => 0, 'msg' => '<span style="color:red;">域名被封-请检查是否有备用链接</span>', 'domain' => $domain));
                    die;
                } elseif ($biaojia2 > 0) { //有替换状态 需要刷新
                    echo json_encode(array('code' => 9, 'msg' => '<span style="color:red;">域名被封-请检查是否有备用链接</span>', 'domain' => $domain));
                    die;
                }
            }
        } elseif ($data["code"] == 9900) {
            if (substr($domain, 0, 7) == 'http://' || substr($domain, 0, 8) == 'https://') {
                // dump($domain);
                $res = $this->check_link_sever($domain);
                // $res = "0";
                echo json_encode(array('code' => 1, 'msg' => '<span style="color:green;">链接访问正常</span>', 'domain' => $domain . "<br />" . $res));
            } else {
                echo json_encode(array('code' => 1, 'msg' => '<span style="color:green;">域名访问正常</span>', 'domain' => $domain));
            }
        } elseif ($data["code"] == 139) {
            echo json_encode(array('code' => 1, 'msg' => '<span style="color:red;">没有查询权限</span>', 'domain' => $domain));
        } elseif ($data["code"] == 402) {
            echo json_encode(array('code' => 8, 'msg' => '<span style="color:red;">查询过快</span>', 'domain' => $domain));
        } else {
            echo json_encode(array('code' => 8, 'msg' => '<span style="color:red;">未知错误</span>', 'domain' => $domain));
        }
    }

    public function start_auto_check()
    {
        $time = date("is");
        $time = substr($time, -3);
        // dump($time);die;
        if (710 < $time && $time < 725) {  //每隔10分钟的位数是5的时候,刷新一次页面,
            echo json_encode(array('code' => 8, 'msg' => '参数错误', 'domain' => $domain));
            exit;
        }
        $domain = I("post.domain");
        //检查是否是空域名
        if ($domain == '') {  //直接重新刷新
            echo json_encode(array('code' => 9, 'msg' => '<span style="color:red;">域名被封-请检查是否有备用链接</span>'));
            die;
        }
        $id["id"] = 1;
        $url = M("Password")->where($id)->find();
        $api_url = $url["checkdomain"] . $domain;

        $content = $this->get_msg($api_url);
        $data = json_decode($content, true);

        if (strrpos($content, "参数错误") > 0) {
            echo json_encode(array('code' => 3, 'msg' => '参数错误', 'domain' => $domain));
            exit;
        }

        if (strrpos($content, "频率") > 0) {
            echo json_encode(array('code' => 8, 'msg' => '频率过快', 'domain' => $domain));
            exit;
        }

        if ($data['status'] == 2) {
            //参数传递
            //判断是链接还是域名
            if (substr($domain, 0, 7) == 'http://' || substr($domain, 0, 8) == 'https://') {
                //为链接屏蔽了.
                //查出链接的域名
                $link_domian = top_domain($domain);
                //查出域名使用用户
                $ss["domain"] = $link_domian;
                $user_domain = M("Link")->where($ss)->select();
                $biaojia1 = 0;
                $biaojia2 = 0;
                foreach ($user_domain as $value) {
                    //检查是否为备用链接
                    $user_code["code"] = $value["code"];
                    $user_code["domain"] = $value["domain"];
                    $re = M("Checkdomain")->where($user_code)->find();
                    if (!$re) {
                        //域名不在备用表里,什么都不需要做.
                    } else {
                        //屏蔽的是备用表里的域名
                        $id["id"] = $re["id"];
                        $type["type"] = 2;  //屏蔽了.
                        $type["die_time"] = date("m-d H:i:s");
                        //修改该域名状态
                        M("Checkdomain")->where($id)->save($type);
                    }
                    //获取下一个备用链接
                    $do["code"] = $value["code"];
                    $do["type"] = 0;
                    $res = M("Checkdomain")->where($do)->order("id asc")->find();
                    if (!$res) {
                        //没有备用链接了.
                        $biaojia1 = $biaojia1 + 1;
                        //修改链接状态,不允许访问.
                        $visit_code["code"] = $value["code"];
                        $visit_type["visit_type"] = 1;
                        M("Link")->where($visit_code)->save($visit_type);
                    } else {
                        //将新链接写入link表
                        $code["code"] = $res["code"];
                        $link["link"] = $res["adv_link"];
                        $link["domain"] = $res["domain"];
                        $link["visit_type"] = 0; //将访问状态重新替换成0 ,可以访问.
                        M("Link")->where($code)->save($link);
                        //修改备用域名链接状态表
                        $id["id"] = $res["id"];
                        $cy["type"] = 1;
                        $cy["start_time"] = date("m-d H:i:s");
                        M("Checkdomain")->where($id)->save($cy);
                        $biaojia2 = $biaojia2 + 1;
                    }
                }
                if ($biaojia1 > 0 && $biaojia2 == 0) {  //有没有域名的状态
                    echo json_encode(array('code' => 0, 'msg' => '<span style="color:red;">链接被封-请检查是否有备用链接</span>', 'domain' => $domain));
                    die;
                } elseif ($biaojia2 > 0) { //有替换状态 需要刷新
                    echo json_encode(array('code' => 9, 'msg' => '<span style="color:red;">已经替换成功</span>', 'domain' => $domain));
                    die;
                }
            } else {
                //判断是否为中间域名
                $wertwet["domain"] = $domain;
                $wertwet["code"] = "middle_domain_check";
                $wertwet["sui_code"] = "middle_domain_check";
                $wertwet["link"] = "middle_domain_check";
                // dump($wertwet);
                $middle_domain = M("Link")->where($wertwet)->find();
                if ($middle_domain) { //是中间域名
                    //重新启用新的中间域名
                    //关闭中间域名
                    $ss["domain"] = I("post.domain");
                    $die_type["type"] = 2;
                    $die_type["use_type"] = 0;
                    $die_type["die_time"] = date("m-d H:i:s");
                    $middle_domain_die = M("Domain")->where($ss)->save($die_type);
                    //取出新中间域名
                    $st["type"] = 0;
                    $start_middle = M("Domain")->where($st)->order("id asc")->find();
                    //检查中间域名屏蔽
                    if (!$start_middle) {  //没有中间域名了.
                        echo json_encode(array('code' => 0, 'msg' => '<span style="color:red;">中间域名被封-请检查是否有备用域名</span>', 'domain' => $domain));
                    } else {
                        //删除中间域名
                        $middle_domain_id["id"] = $middle_domain["id"];
                        M("Link")->where($middle_domain_id)->delete();
                        //启用新的中间域名
                        $a["id"] = $start_middle["id"];
                        $b["type"] = 1;
                        $b["use_type"] = 1;
                        $b["start_time"] = date("m-d H:i:s");
                        $b["today_time"] = date("Y-m-d");
                        //在域名中启用
                        M("Domain")->where($a)->save($b);
                        //更新屏蔽链接
                        $g["code"] = 'middle_domain_check';
                        $g["sui_code"] = 'middle_domain_check';
                        $g["domain"] = $start_middle['domain'];
                        $g["link"] = 'middle_domain_check';
                        $g["cnzz"] = 'middle_domain_check';
                        $g["self_cnzz"] = 'middle_domain_check';
                        $g["self_cnzz_title"] = 'middle_domain_check';
                        $g["self_cnzz_titlecut_mun"] = 0;
                        $g["visit_type"] = 0;
                        $g["var_time"] = date("Y-m-d");
                        $g["type"] = 0;
                        M("Link")->add($g);
                        //刷新页面
                        echo json_encode(array('code' => 9, 'msg' => '<span style="color:red;">域名被封-请检查是否有备用链接</span>', 'domain' => $domain));
                    }

                }
                //查出域名使用用户
                $ss["domain"] = I("post.domain");
                $user_domain = M("Link")->where($ss)->select();
                $biaojia1 = 0;
                $biaojia2 = 0;
                foreach ($user_domain as $value) {
                    //检查是否为备用链接
                    $user_code["code"] = $value["code"];
                    $user_code["domain"] = $value["domain"];
                    $re = M("Checkdomain")->where($user_code)->find();
                    if (!$re) {
                        //域名不在备用表里,什么都不需要做.
                    } else {
                        //屏蔽的是备用表里的域名
                        $id["id"] = $re["id"];
                        $type["type"] = 2;  //屏蔽了.
                        $type["die_time"] = date("m-d H:i:s");
                        //修改该域名状态
                        M("Checkdomain")->where($id)->save($type);
                    }
                    //获取下一个备用链接
                    $do["code"] = $value["code"];
                    $do["type"] = 0;
                    $res = M("Checkdomain")->where($do)->order("id asc")->find();
                    if (!$res) {
                        //没有备用链接了.
                        $biaojia1 = $biaojia1 + 1;
                        //修改链接状态,不允许访问.
                        $visit_code["code"] = $value["code"];
                        $visit_type["visit_type"] = 1;
                        M("Link")->where($visit_code)->save($visit_type);
                    } else {
                        //将新链接写入link表
                        $code["code"] = $res["code"];
                        $link["link"] = $res["adv_link"];
                        $link["domain"] = $res["domain"];
                        $link["visit_type"] = 0; //将访问状态重新替换成0 ,可以访问.
                        M("Link")->where($code)->save($link);
                        //修改备用域名链接状态表
                        $id["id"] = $res["id"];
                        $cy["type"] = 1;
                        $cy["start_time"] = date("m-d H:i:s");
                        M("Checkdomain")->where($id)->save($cy);
                        $biaojia2 = $biaojia2 + 1;
                    }
                }
                if ($biaojia1 > 0 && $biaojia2 == 0) {  //有没有域名的状态
                    echo json_encode(array('code' => 0, 'msg' => '<span style="color:red;">域名被封-请检查是否有备用链接</span>', 'domain' => $domain));
                    die;
                } elseif ($biaojia2 > 0) { //有替换状态 需要刷新
                    echo json_encode(array('code' => 9, 'msg' => '<span style="color:red;">域名被封-请检查是否有备用链接</span>', 'domain' => $domain));
                    die;
                }
            }

        } else if ($data['status'] == 0) {
            if (substr($domain, 0, 7) == 'http://' || substr($domain, 0, 8) == 'https://') {
                // dump($domain);
                $res = $this->check_link_sever($domain);
                // $res = "0";
                echo json_encode(array('code' => 1, 'msg' => '<span style="color:green;">链接查询正常</span>', 'domain' => $domain . "<br />" . $res));
            } else {
                echo json_encode(array('code' => 1, 'msg' => '<span style="color:green;">域名查询正常</span>', 'domain' => $domain));
            }
        } else if ($data['status'] == 3) {
            echo json_encode(array('code' => 2, 'msg' => '查询失败', 'domain' => $domain));
        }
    }

    //手动替换链接
    public function change_link()
    {
        //参数传递
        $ss["code"] = I("post.code");
        $ss["domain"] = I("post.domain");
        //检查是否为备用链接
        $re = M("Checkdomain")->where($ss)->find();
        if (!$re) {
            //域名不在备用表里,什么都不需要做.
        } else {
            //屏蔽的是备用表里的域名
            $id["id"] = $re["id"];
            $type["type"] = 2;  //屏蔽了.
            $type["die_time"] = date("m-d H:i:s");
            //修改该域名状态
            M("Checkdomain")->where($id)->save($type);
        }
        //获取下一个备用链接
        $do["code"] = $ss["code"];
        $do["type"] = 0;
        $re = M("Checkdomain")->where($do)->order("id asc")->find();
        if (!$re) {
            //修改链接状态,不允许访问.
            $visit_code["code"] = $ss["code"];
            $visit_type["visit_type"] = 1;
            M("Link")->where($visit_code)->save($visit_type);
            echo json_encode(array('code' => 0, 'msg' => '<span style="color:red;">域名被封-没有备用链接了</span>', 'domain' => $domain));
            die;
        }
        //将新链接写入link表
        $code["code"] = $re["code"];
        $link["link"] = $re["adv_link"];
        $link["domain"] = $re["domain"];
        $link["visit_type"] = 0; //将访问状态重新替换成0 ,可以访问.
        M("Link")->where($code)->save($link);
        //修改备用域名链接状态表
        $id["id"] = $re["id"];
        $cy["type"] = 1;
        $cy["start_time"] = date("m-d H:i:s");
        M("Checkdomain")->where($id)->save($cy);
        echo json_encode(array('code' => 0, 'msg' => '域名被封-成功替换', 'domain' => $domain));

    }

    public function start_test()
    {
        $wertwet["domain"] = "b6qm2f.cn";
        // dump($asa);
        // $wertwet["username"] = "0";
        $wertwet["code"] = "middle_domain_check";
        $wertwet["sui_code"] = "middle_domain_check";
        $wertwet["link"] = "middle_domain_check";
        dump($wertwet);
        $middle_domain = M("Link")->where($wertwet)->count();
        dump($middle_domain);
        die;
        if ($middle_domain) {
            echo '1';
        } else {
            echo '2';
        }
    }

    //接受来自文案系统的域名
    public function add_article_domain()
    {
        $domain_str = I("get.article_domain");
        $server_id["server_id"] = I("get.server_id");
        $domain = explode("-", $domain_str);
        $server_info = M("Serverid")->where($server_id)->find();
        foreach ($domain as $value) {
            if ($value != '') {
                $check_domain["port_domain"] = $server_info["port_domain"];
                $check_domain["domain"] = $value;
                $check_domain_res = M("Articledomain")->where($check_domain)->find();
                if ($check_domain_res) {

                } else {
                    $article_domain["server_id"] = $server_info["server_id"];
                    $article_domain["domain"] = $value;
                    $article_domain["type"] = 0;
                    $article_domain["port_domain"] = $server_info["port_domain"];
                    $article_domain["add_time"] = date("m-d");
                    $article_domain["start_time"] = '';
                    $article_domain["die_time"] = '';
                    M("Articledomain")->add($article_domain);
                }
            }
        }
        echo json_encode(array('code' => 0, 'msg' => 'success'));
    }

    //返回用户备注信息
    public function GetUserTipInfo()
    {
        $userInfo = M("User")->field("sjcode,tips")->select();
        if ($userInfo) {
            echo json_encode(array('code' => 0, 'msg' => $userInfo));
        } else {
            echo json_encode(array('code' => 1120, 'msg' => "获取失败"));
        }
    }

    //系统近况
    public function system_all_info()
    {

        $server = M("Serverid")->select();
        //快站域名
        $kztype["type"] = 0;
        $this->kuaizhang_no_die = M("Luodilink")->where($kztype)->count();
        $kztypesql["type"] = 2;
        $this->kuaizhang_die_all = M("Luodilink")->where($kztypesql)->count();
        $kztypesql["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 hour")));
        $this->kuaizhang_die_one = M("Luodilink")->where($kztypesql)->count();
        $kztypesql["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 day")));
        $this->kuaizhang_die_day = M("Luodilink")->where($kztypesql)->count();
        //中间域名剩余
        $type["type"] = 0;
        $middle_domain_muns = M("Domain")->where($type)->count();

        $die_middle_domain_sql["type"] = 2;
        //共计屏蔽
        $die_middle_domain_muns_all = M("Domain")->where($die_middle_domain_sql)->count();
        //最近一小时屏蔽
        $die_middle_domain_sql["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 hour")));
        $die_middle_domain_muns = M("Domain")->where($die_middle_domain_sql)->count();
        //24小时
        $die_middle_domain_sql2["type"] = 2;
        $die_middle_domain_sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 day")));
        $die_middle_domain_muns_day = M("Domain")->where($die_middle_domain_sql2)->count();
        $server_domain_info = array();
        foreach ($server as $value) {
            $domain_type["server_id"] = $value["server_id"];
            $domain_type["type"] = 0;
            $article_domain = M("Articledomain")->where($domain_type)->select();
            $server_domain_info[$value["server_id"]]["domain_muns"] = count($article_domain);
            $server_domain_info[$value["server_id"]]["server_id"] = $value["server_id"];
            $article_domain_die_sql["type"] = 2;
            $article_domain_die_sql["server_id"] = $value["server_id"];
            $article_domain_die_sql["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 hour")));
            $server_domain_info[$value["server_id"]]["die_domain"] = M("Articledomain")->where($article_domain_die_sql)->count();
            // dump(M()->_sql());
            // dump($server_domain_info);die;

            $article_domain_die_sql2["type"] = 2;
            $article_domain_die_sql2["server_id"] = $value["server_id"];
            $article_domain_die_sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 day")));
            $server_domain_info[$value["server_id"]]["die_domain_day"] = M("Articledomain")->where($article_domain_die_sql2)->count();
            //总共
            $article_domain_die_sql3["type"] = 2;
            $article_domain_die_sql3["server_id"] = $value["server_id"];
            $server_domain_info[$value["server_id"]]["all_die_domain_day"] = M("Articledomain")->where($article_domain_die_sql3)->count();
            //同步落地页面的与入口的域名
            $PortSql["server_id"] = $value["server_id"];
            // $PortSql["server_id"]  = '1017';
            //查出接口域名
            $PortDomain = M("Serverid")->where($PortSql)->find();
            $Url = "http://" . $PortDomain['port_domain'] . "/index.php/Home/API/UpDataDomainRedis";
            //查出所有入口域名
            $link = M("Rukoudomain")->field("domain")->select();
            $rukoulink = M("Gatedomain")->field("domain")->select();
            $middledomainlistSQL['type'] = array('neq', 2);
            $middledomainlist = M("Domain")->where($middledomainlistSQL)->field("domain")->select();
            $totallinkdomain = array_merge($link, $rukoulink, $middledomainlist);
            // dump($totallinkdomain);die;

            $totallinkdomain = array_merge($link, $middledomainlist);
            $domainStr = '';
            foreach ($totallinkdomain as $value22) {
                if ($value22['domain']) {
                    $domainStr .= $value22['domain'] . '/';
                }
            }
            // dump($totallinkdomain);die;
            $canshu = array(
                "domainData" => $domainStr,
            );
            $content = $this->post_msg($Url, $canshu);
            $data = json_decode($content, true);
            if ($data["code"] != 0 || $data == '') {
                $listcode[$value["server_id"]]['code'] = 1;
                $listcode[$value["server_id"]]['server_id'] = $value["server_id"];
            } else {
                $listcode[$value["server_id"]]['code'] = 0;
                $listcode[$value["server_id"]]['server_id'] = $value["server_id"];
            }

        }
        // dump($listcode);die;
        // dump($server_domain_info);die;
        // 用户入口域名
        $user_gate_domain_sql["type"] = 0;
        $this->user_gate_domain = M("Rukoudomain")->where($user_gate_domain_sql)->count();
        //正在使用入口
        $user_gate_domain_sql4["type"] = 1;
        $this->user_gate_domain_muns = M("Rukoudomain")->where($user_gate_domain_sql4)->count();
        //用户入口1小时屏蔽
        $user_gate_domain_sql2["type"] = 2;
        $user_gate_domain_sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 hour")));
        $this->user_gate_domain_die_onehours = M("Rukoudomain")->where($user_gate_domain_sql2)->count();
        //用户入口24小时屏蔽
        $user_gate_domain_sql3["type"] = 2;
        $user_gate_domain_sql3["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 day")));
        $this->user_gate_domain_die_oneday = M("Rukoudomain")->where($user_gate_domain_sql3)->count();
        //借权
        //入口域名
        $kuaizhan_gate_domain_sql["type"] = 0;
        $this->kuaizhan_gate_domain = M("Kzrukou")->where($kuaizhan_gate_domain_sql)->count();
        //正在使用入口
        $kuaizhan_gate_domain_sql4["type"] = 1;
        $this->kuaizhan_gate_domain_muns = M("Kzrukou")->where($kuaizhan_gate_domain_sql4)->count();
        //用户入口1小时屏蔽
        $kuaizhan_gate_domain_sql2["type"] = 2;
        $kuaizhan_gate_domain_sql2["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 hour")));
        $this->kuaizhan_gate_domain_die_onehours = M("Kzrukou")->where($kuaizhan_gate_domain_sql2)->count();
        //用户入口24小时屏蔽
        $kuaizhan_gate_domain_sql3["type"] = 2;
        $kuaizhan_gate_domain_sql3["die_time"] = array('gt', date("m-d H:i:s", strtotime("-1 day")));
        $this->kuaizhan_gate_domain_die_oneday = M("Kzrukou")->where($kuaizhan_gate_domain_sql3)->count();
        //用户入口24小时屏蔽
        $kuaizhan_die_middle_domain_muns_all_sql3["type"] = 2;
        // $kuaizhan_die_middle_domain_muns_all_sql3["die_time"]=array('gt',date("m-d H:i:s"))  ;
        $this->kuaizhan_die_middle_domain_muns_all = M("Kzrukou")->where($kuaizhan_die_middle_domain_muns_all_sql3)->count();
        $server_muns = count($server);
        $this->listcode = $listcode;
        $this->server_muns = $server_muns;
        $this->middle_domain_muns = $middle_domain_muns;
        $this->die_middle_domain_muns_all = $die_middle_domain_muns_all;
        $this->server_domain_info = $server_domain_info;
        $this->die_middle_domain_muns = $die_middle_domain_muns;
        $this->die_middle_domain_muns_day = $die_middle_domain_muns_day;
        $this->display();
    }

    function go_article_domain($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }
    }

    function get_msg($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }
    }

    function post_msg($url, $comdata)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, false);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        $post_data = $comdata;
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);


        //执行命令
        $data = curl_exec($curl);
        if ($data) {
            curl_close($curl);
            return $data;
        } else {
            $error = curl_errno($curl);
            curl_close($curl);
            return false;
        }
    }

}