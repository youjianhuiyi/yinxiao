<?php

namespace app\admin\model\production;

use think\Model;
use traits\model\SoftDelete;

class Production extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'production';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'pay_mode_text',
        'work_time_text',
        'status_text'
    ];
    

    
    public function getPayModeList()
    {
        return ['online' => __('Online'), 'offline' => __('Offline')];
    }

    public function getStatusList()
    {
        return ['up' => __('Up'), 'down' => __('Down')];
    }


    public function getPayModeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_mode']) ? $data['pay_mode'] : '');
        $list = $this->getPayModeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getWorkTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['work_time']) ? $data['work_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setWorkTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
