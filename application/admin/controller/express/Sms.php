<?php

namespace app\admin\controller\express;

use app\admin\controller\general\Config;
use app\common\controller\Backend;
use think\Cache;

/**
 * 短信发送管理
 *
 * @icon fa fa-circle-o
 */
class Sms extends Backend
{
    
    /**
     * Sms模型对象
     * @var \app\admin\model\express\Sms
     */
    protected $model = null;
    protected $sendSMSUrl = 'http://139.186.39.24:8081/user/send';
    protected $getSMSReport = 'http://139.186.39.24:8081/getReport';
    protected $getSMSGetReply = 'http://139.186.39.24:8081/getReply';
    protected $getSMSGetBalance = 'http://139.186.39.24:8081/user/getBalance';
    protected $account = 'lcwabh';
    protected $password = 'lcwabh0408';
    protected $apiUid = 'TMHC10270';
    protected $apiKey = 'f0743b11fa4abd1550c167cc40d81202';

    /**
     *  uid	    是	String	客户代号，uid 和 apikey 由管理员统一分配
     *  sign	是	String	根据平台分配的 apikey 进行组合，sign=md5(uidyyyyMMddHHmmss-“send”-apikey),32 位大写
     *  mobiles	是	String	手机号,群发时多个手机号以逗号分隔(最多同时发送 1000 个号码)
     *  content	是	String	短信内容，超过 70 个字符，每 67 个字符计一条短信
     *  timeStamp	是	String	时间戳,格式 YYYYMMDDHHmmss,不能小于当前时间 3分钟
     */

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\express\Sms;

        $timeStamp = date('YmdHis',time());
        $str = $this->apiUid.'-'.$timeStamp.'-'."balance".'-'.$this->apiKey;
        $sign = strtoupper(md5($str));
        $data ='account='.$this->account.'&password='.$this->password.'&sign='.$sign.'&timeStamp='.$timeStamp;
        //发送请求
        $result = $this->curlPostForm($data,$this->getSMSGetBalance);
        $data = json_decode($result,true);
        $this->assign('data',$data);

    }


    /**
     * 发送短信
     * @param $params array 订单信息数组
     * @return mixed
     */
    public function sendSMS($params)
    {
        $data ='account='.$this->account.'&password='.$this->password.'&mobiles='.$params['phone'].'&content='.urlencode($params['content']);
        //发送请求
        $result = $this->curlPostForm($data,$this->sendSMSUrl);
        Cache::set('send-sms',$result,300);
        $data = json_decode($result,true);
        if ($data['retCode'] == '000') {
            //表示发送成功
            $newData = [
                'order_id'  => $params['order_id'],
                'team_id'   => $params['team_id'],
                'admin_id'  => $params['admin_id'],
                'phone'     => $params['phone'],
                'status'    => 1,
                'msg'       => $params['content'],
                'return_data'=>$result
            ];
        } else {
            //表示发送失败
            $newData = [
                'order_id'  => $params['order_id'],
                'team_id'   => $params['team_id'],
                'admin_id'  => $params['admin_id'],
                'phone'     => $params['phone'],
                'status'    => 0,
                'msg'       => $params['content'],
                'return_data'=>$result
            ];
        }
        $this->model->isUpdate(false)->save($newData);
        return $data;
    }

//    /**
//     * 获取余额
//     */
//    public function getBalance()
//    {
//        $timeStamp = date('YmdHis',time());
//        $str = $this->apiUid.'-'.$timeStamp.'-'."balance".'-'.$this->apiKey;
//        $sign = strtoupper(md5($str));
//        $data ='account='.$this->account.'&password='.$this->password.'&sign='.$sign.'&timeStamp='.$timeStamp;
//        //发送请求
//        $result = $this->curlPostForm($data,$this->getSMSGetBalance);
//        Cache::set('sms-balance',$result,300);
//    }

    /**
     * 测试发送短信
     */
    public function testSendSMS()
    {
        $params = $this->request->param();
        $data ='account='.$this->account.'&password='.$this->password.'&mobiles='.$params['mobile'].'&content='.urlencode($params['content']);
        //发送请求
        $result = $this->curlPostForm($data,$this->sendSMSUrl);
        Cache::set('send-sms',$result,300);
        $data = json_decode($result,true);
        return $data;
    }
}
