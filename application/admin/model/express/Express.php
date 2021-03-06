<?php

namespace app\admin\model\express;

use think\Model;
use traits\model\SoftDelete;

class Express extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'express';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    







}
