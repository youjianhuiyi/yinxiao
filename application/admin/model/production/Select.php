<?php

namespace app\admin\model\production;

use think\Model;
use traits\model\SoftDelete;

class Select extends Model
{
    use SoftDelete;

    // 表名
    protected $name = 'production_select';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'url'
    ];

    public function getUrl()
    {
        return Production::column('modulefile','id');
    }

    public function getUrlAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['production_id']) ? $data['production_id'] : '');
        $list = $this->getUrl();
        return isset($list[$value]) ? $list[$value] : '';
    }
    

    







}
