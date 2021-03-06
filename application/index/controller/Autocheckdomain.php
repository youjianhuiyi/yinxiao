<?php
namespace app\index\Controller;

use think\Cache;
use think\Controller;
use app\admin\model\sysconfig\Consumables as ConsumablesModel;
use app\admin\model\sysconfig\Ground as GroundModel;
use app\admin\model\sysconfig\Kzdomain as KzdomainModel;

/**
 * 检测域名防封
 * Class AutoCheckDomain
 * @package app\index\Controller
 */
class Autocheckdomain extends Controller
{

    protected $consumablesModel = null;
    protected $groundModel = null;
    protected $KzDomainModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->consumablesModel = new ConsumablesModel();
        $this->groundModel = new GroundModel();
        $this->KzDomainModel = new KzdomainModel();
    }


    public function test()
    {
//        echo <<<EOF
//    <script>
//    window.onload=test;
//        function test() {
//           for (var i= 0;i<=10;i++) {
//               alert(i);
//           }
//        }
//</script>
//EOF;
//        $this->consumablesModel->where('domain_url','gaopingzx.cn')->update(['is_inuse'=>1]);
    }

    /**
     * 查看数据
     */
    public function index()
    {
        $data = collection($this->consumablesModel->where(['is_forbidden'=>0,'is_inuse'=>1])->select())->toArray();
        $this->assign('data',$data);
        return $this->view->fetch();
    }

    /**
     * 异步落地域名检测
     */
    public function checkDomain()
    {
        //$checkid  为传过来的正在检测的id
        $checkId = $this->request->param('check_id');
        //查出所有域名
        $allConsumables = collection($this->consumablesModel->where(['is_forbidden'=>0,'is_inuse'=>1])->select())->toArray();
        $sort = [
            'direction' => 'SORT_ASC',
            'field' => 'id',
        ];
        $arrSort = [];
        foreach($allConsumables as $uniqid => $row){
            foreach($row as $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $allConsumables);
        }

        //获取最小的id和最大的id
        reset($allConsumables);
        $first_key = current($allConsumables);
        //第一元素的值,就是最小的id
        // $first_id = $first_key["id"];

        $min = $first_key["id"];
        //最后一个元素,最大的id值
        $last = end($allConsumables);
        $maxCheckId =$last["id"];
        //正在检测的id值,第一检测是从最小值开始
        if ($checkId == 0) {
            $checkId = $min;
            $aaa = "等于0";  //调式语句
        }else{
            //已经开始检测了,就在该值上加1即可.
            $checkId = $checkId+1;
            $aaa = "加1了"; //调式语句
        }
        //获取检测的域名
        $checkDomainList = $this->getRandDomain($min,$checkId,$maxCheckId,1);
        $checkRES = file_get_contents('http://1009.5zhuangbi.com/index.php/Home/Auto/CAPI/domain/'.$checkDomainList['domain_url']);
        $result = json_decode($checkRES,true);
        //根据结果采取不同的措施
        if ($result["code"] == '1') { //域名被封了.
            $re = $this->consumablesModel->where('id',$checkDomainList['id'])->update(['is_forbidden' => 1]);
            Cache::rm('luck_domain');
            if ($re) {
                $msg = '<span style="color:#ff0000">域名被封,设置成功</span>';
            }else{
                $msg = '<span style="color:red">域名被封,设置失败</span>';
            }
            $code = 1;
        } else {
            $code = 0;
            $msg = '域名正常';
        }

        //把数据传送给访问ajax
        echo json_encode([
            'code'      => $code,
            'msg'       => $msg,
            'domain'    => $checkDomainList['domain_url'],
            'now_id'    => $checkDomainList["id"],
            'check_id'  => $checkId,
            'min_id'    => $min["id"],
            'max_id'    => $maxCheckId,
            'fanhui'    => $checkDomainList,
            'aaa'       => $aaa
        ]);
    }

    /**
     * 异步入口域名检测
     */
    public function checkGroundDomain()
    {
        //$checkid  为传过来的正在检测的id
        $checkId = $this->request->param('check_id');
        //查出所有入口域名
        $allConsumables = collection($this->groundModel->where(['is_forbidden'=>0])->select())->toArray();
        $sort = [
            'direction' => 'SORT_ASC',
            'field' => 'id',
        ];
        $arrSort = [];
        foreach($allConsumables as $uniqid => $row){
            foreach($row as $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $allConsumables);
        }

        //获取最小的id和最大的id
        reset($allConsumables);
        $first_key = current($allConsumables);
        //第一元素的值,就是最小的id
        // $first_id = $first_key["id"];

        $min = $first_key["id"];
        //最后一个元素,最大的id值
        $last = end($allConsumables);
        $maxCheckId =$last["id"];
        //正在检测的id值,第一检测是从最小值开始
        if ($checkId == 0) {
            $checkId = $min;
            $aaa = "等于0";  //调式语句
        }else{
            //已经开始检测了,就在该值上加1即可.
            $checkId = $checkId+1;
            $aaa = "加1了"; //调式语句
        }
        //获取检测的域名
        $checkDomainList = $this->getRandDomain($min,$checkId,$maxCheckId,2);
        $checkRES = file_get_contents('http://1009.5zhuangbi.com/index.php/Home/Auto/CAPI/domain/'.$checkDomainList['domain_url']);
        $result = json_decode($checkRES,true);
        //根据结果采取不同的措施
        if ($result["code"] == '1') { //域名被封了.
            $re = $this->groundModel->where('id',$checkDomainList['id'])->update(['is_forbidden' => 1]);
            if ($re) {
                $msg = '<span style="color:#ff0000">域名被封,设置成功</span>';
            }else{
                $msg = '<span style="color:red">域名被封,设置失败</span>';
            }
            $code = 1;
        } else {
            $code = 0;
            $msg = '域名正常';
        }

        //把数据传送给访问ajax
        echo json_encode([
            'code'      => $code,
            'msg'       => $msg,
            'domain'    => $checkDomainList['domain_url'],
            'now_id'    => $checkDomainList["id"],
            'check_id'  => $checkId,
            'min_id'    => $min["id"],
            'max_id'    => $maxCheckId,
            'fanhui'    => $checkDomainList,
            'aaa'       => $aaa
        ]);
    }

    /**
     * 异步快站域名检测
     */
    public function checkKzDomain()
    {
        //$checkid  为传过来的正在检测的id
        $checkId = $this->request->param('check_id');
        //查出所有域名
        $allConsumables = collection($this->KzDomainModel->where(['is_forbidden'=>0])->select())->toArray();
        $sort = [
            'direction' => 'SORT_ASC',
            'field' => 'id',
        ];
        $arrSort = [];
        foreach($allConsumables as $uniqid => $row){
            foreach($row as $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $allConsumables);
        }

        //获取最小的id和最大的id
        reset($allConsumables);
        $first_key = current($allConsumables);
        //第一元素的值,就是最小的id
        // $first_id = $first_key["id"];

        $min = $first_key["id"];
        //最后一个元素,最大的id值
        $last = end($allConsumables);
        $maxCheckId =$last["id"];
        //正在检测的id值,第一检测是从最小值开始
        if ($checkId == 0) {
            $checkId = $min;
            $aaa = "等于0";  //调式语句
        }else{
            //已经开始检测了,就在该值上加1即可.
            $checkId = $checkId+1;
            $aaa = "加1了"; //调式语句
        }
        //获取检测的域名
        $checkDomainList = $this->getRandDomain($min,$checkId,$maxCheckId,3);
        $checkRES = file_get_contents('http://1009.5zhuangbi.com/index.php/Home/Auto/CAPI/domain/'.$checkDomainList['domain_url']);
        $result = json_decode($checkRES,true);
        //根据结果采取不同的措施
        if ($result["code"] == '1') { //域名被封了.
            $re = $this->KzDomainModel->where('id',$checkDomainList['id'])->update(['is_forbidden' => 1]);
            if ($re) {
                $msg = '<span style="color:#ff0000">域名被封,设置成功</span>';
            }else{
                $msg = '<span style="color:red">域名被封,设置失败</span>';
            }
            $code = 1;
        } else {
            $code = 0;
            $msg = '域名正常';
        }

        //把数据传送给访问ajax
        echo json_encode([
            'code'      => $code,
            'msg'       => $msg,
            'domain'    => $checkDomainList['domain_url'],
            'now_id'    => $checkDomainList["id"],
            'check_id'  => $checkId,
            'min_id'    => $min["id"],
            'max_id'    => $maxCheckId,
            'fanhui'    => $checkDomainList,
            'aaa'       => $aaa
        ]);
    }


    /**
     * id+1获取域名
     * @param $min integer 当前数据最小值ID
     * @param $n    integer 当前ID
     * @param $max  integer 当前数据最大值
     * @param $type integer 域名类型，1=落地，2=入口，3=快站
     * @return mixed
     */
    public function getRandDomain($min,$n,$max,$type)
    {

        if ($n > $max || $n<=$min) {  //已经超出检测范围,直接开始从最小id开始.
            $cid = $min;
        }else{
            $cid = $n;   //没有超出反问,就按照传过来的参数检测.
        }
        if ($type == 1) {
            $domain = $this->consumablesModel->get($cid);
        } elseif ($type == 2) {
            $domain = $this->groundModel->get($cid);
        } else {
            $domain = $this->KzDomainModel->get($cid);
        }
        //判断当前域名的状态，如果查询不到或者已经被封，则跳到下一条域名进行检测
        if (empty($domain) || $domain["is_forbidden"] == 1) {
            $next = $cid + 1;
            $nextMin = $min;
            $nextMax = $max;
            //自调用检查下一个.
            return $this->getRandDomain($nextMin,$next,$nextMax,$type);
        }else{
            //满足情况
            return $domain;
        }

    }


    /**
     * 调用域名检测
     * @param $str  string  json字符串
     * @param $url  string  请求的url地址
     * @param $second  int  请求最长时间
     * @return bool|string
     */
    public function checkApi($str, $url = 'http://1009.5zhuangbi.com/index.php/Home/Auto/CAPI/domain/', $second = 30)
    {
        $ch = curl_init($url);
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //设置 header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post 提交方式
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            echo "curl 出错，错误码:$error" . "<br>";
            return false;
        }
    }

}