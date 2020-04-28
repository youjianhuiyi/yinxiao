<?php

namespace app\admin\model\data;

use app\admin\model\Admin;
use app\admin\model\sysconfig\Xpay;
use think\Model;
use traits\model\SoftDelete;

class PayRecord extends Model
{
    use SoftDelete;

    // 表名
    protected $name = 'pay_record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'pay_id_text'
    ];

    /**
     * 获取支付商户名称
     * @return array|bool|string
     */
    public function getPayIdName()
    {
        return  Xpay::column('pay_name','id');
    }

    /**
     * 返回支付商户名称
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getPayIdTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_id_text']) ? $data['pay_id_text'] : '');
        $list = $this->getPayIdName();
//        dump($value);
//        dump($list);
//        dump($data);die;
        return isset($list[$value]) ? $list[$value] : '';
    }


    







}
