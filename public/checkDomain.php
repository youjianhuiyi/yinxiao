<?php 

namespace Home\Controller;
use Think\Controller;
class AutoController extends CommonController {
  public function __construct(){
    parent::__construct();
        $this->redis = new \Redis();//实例化redis
        $this->redis -> connect('127.0.0.1',6379); //链接reids
        $this->redis -> auth( C('REDIS') ); //链接密码
  }
 //多域名轮循检测
 public function StartCheckGetaDomain()
 {
    //是否已经开始检测
    //$checkid  为传过来的正在检测的id
    $checkid = I("post.nowid");
    //查出所有域名
    $rukoutyep["type"] =1;
    $AllGateDomain = M("Rukoudomain")->where($rukoutyep)->select();
    //一下操作就是问了排序,直接传进去数组$AllGateDomain就ok,其他不用动.
    $sort = array(
            'direction' => 'SORT_ASC', //排序SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'id',       //排序字段
    );
    $arrSort = array();
    foreach($AllGateDomain AS $uniqid => $row){
        foreach($row AS $key=>$value){
            $arrSort[$key][$uniqid] = $value;
        }
    }
    if($sort['direction']){
        array_multisort($arrSort[$sort['field']], constant($sort['direction']), $AllGateDomain);
    }
    //获取最小的id和最大的id
    reset($AllGateDomain);
    $first_key = current($AllGateDomain);
    //第一元素的值,就是最小的id
    // $first_id = $first_key["id"];

    $min["id"] = $first_key["id"];
    //最后一个元素,最大的id值
    $last = end($AllGateDomain);
    $Maxcheckid =$last["id"];
    //正在检测的id值,第一检测是从最小值开始
    if ($checkid == 0) {
        $checkid = $min["id"];
        $aaa = "等于0";  //调式语句
    }else{
        //已经开始检测了,就在该值上加1即可.
        $checkid = $checkid+1;
        $aaa = "加1了"; //调式语句
    }
    //获取检测的域名
    $Domain_rukou = $this->getRandDomain($min["id"],$checkid,$Maxcheckid);
    //获取检测的结果
    // $checkRES = $this->CheckDomainApi($Domain_rukou["domain"]);
    $checkRES = $this->checkDomainZong($Domain_rukou["domain"]);
    //根据结果采取不同的措施
    if ($checkRES["code"] == 9904) { //域名被封了.
        $id["domain"] = $Domain_rukou["domain"];
        $type["type"] = '2';
        $type["die_time"] = date("m-d H:i:s");
        $re = M("Rukoudomain")->where($id)->save($type);
        if ($re) {
            $msg = '<span style="color:#ff0000">域名被封,设置成功</span>';
        }else{
            $msg = '<span style="color:red"域名被封,设置失败</span>';
        }
        $code = 9;
    }elseif ($checkRES["code"] == 9900) {  //域名正常
        $code = 0;
        $msg = '域名正常';
    }elseif ($checkRES["code"] == 139) {  //没有查询权限
        $code = 0;
        $msg = '没有权限';
    }elseif ($checkRES["code"] == 402) {  //查询过快
        $code = 0;
        $msg = '查询过快';
    }elseif ($checkRES["code"] == 888) {  //未知错误
        $code = 0;
        $msg = '未知错误';
    }
    //一下数据,用于评估屏蔽速度,可以不需要理会
    //查出域名剩余个数
    $sql2["type"] = '0';
    $have_rukou_muns = M("Rukoudomain")->where($sql2)->count();
    //查出正在使用个数
    $sql2["type"] = '1';
    $isuse_rukou_domain_muns = M("Rukoudomain")->where($sql2)->count();
    //查出最近1小时屏蔽个数
    $sql2["type"] = '2';
    $sql2["die_time"]=array('gt',date("m-d H:i:s", strtotime("-1 hour")));
    $die_rukou_domain_muns = M("Rukoudomain")->where($sql2)->count();
    //查出最近24小时屏蔽个数
    $sql2["type"]= '2'  ;
    $sql2["die_time"]=array('gt',date("m-d H:i:s", strtotime("-1 day")));
    $die_rukou_domain_muns_day = M("Rukoudomain")->where($sql2)->count();
    //把数据传送给访问ajax
    echo json_encode(array(
        'code'=>$code,    //根据标记来确认html的操作,如果是9就刷新前端页面.
        'msg'=>$msg,        //   检测结果中文提示
        'domain'=>$Domain_rukou["domain"],  //此次检测的域名
        'have_rukou_muns'=>$have_rukou_muns, //还剩多少个域名没有启动
        "isuse_rukou_domain_muns"=>$isuse_rukou_domain_muns, //正在使用个数
        "die_rukou_domain_muns"=>$die_rukou_domain_muns,  // 已经屏蔽个数
        "die_rukou_domain_muns_day"=>$die_rukou_domain_muns_day, //当天屏蔽个数
        'nowid'=>$Domain_rukou["id"],     //ajax页面post过来的id
        'checkid'=>$checkid,   //  这次检测的id
        'minid'=>$min["id"],   //数组中最小id
        'maxid'=>$Maxcheckid,  //数组中最大id
        'aaa'=>$aaa,    //调试参数
        'fanhui'=>$Domain_rukou,   //调试参数 ,看看是否返回正常的域名.
    ));
 }

 //id+1获取域名
 public function getRandDomain($min,$n,$max)
 {

    if ($n > $max || $n<=$min) {  //已经超出检测范围,直接开始从最小id开始.
        $cid["id"] = $min;
    }else{
        $cid["id"] = $n;   //没有超出反问,就按照传过来的参数检测.
    }
    $domain = M("Rukoudomain")->where($cid)->find();
    //
    if ($domain == '' || $domain["type"] != '1') {
        $next = $cid["id"]+1;
        $nextmin = $min;
        $nextmax = $max;
        //自调用检查下一个.
        return $this->getRandDomain($nextmin,$next,$nextmax);
    }else{
        //满足情况
        return $domain;
    }

}