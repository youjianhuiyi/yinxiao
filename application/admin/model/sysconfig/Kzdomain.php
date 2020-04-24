<?php

namespace app\admin\model\sysconfig;

use think\Model;
use traits\model\SoftDelete;

class Kzdomain extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'kz_domain';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'forbiddentime_text'
    ];
    

    



    public function getForbiddentimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['forbiddentime']) ? $data['forbiddentime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setForbiddentimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
