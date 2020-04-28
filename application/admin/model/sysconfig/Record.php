<?php

namespace app\admin\model\sysconfig;

use think\Model;
use traits\model\SoftDelete;

class Record extends Model
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
    public function getPayIdText()
    {
        $xpay =  Xpay::column('pay_name','id');
        $rypay =  Rypay::column('pay_name','id');
        $pay =  pay::column('pay_name','id');
        return [$pay,$xpay,$rypay];
    }

    /**
     * 返回支付商户名称
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getPayIdTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_id']) ? $data['pay_id'] : '');
        $list = $this->getPayIdText();
        return isset($list[$data['pay_type']][$value]) ? $list[$data['pay_type']][$value] : '';
    }
    







}
