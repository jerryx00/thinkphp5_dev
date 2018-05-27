<?php

namespace app\admin\model;

use think\Model;

class Area extends Model
{
    // 表名
    protected $name = 'area';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'level_text'
    ];
    

    
    public function getLevelList()
    {
        return ['4' => __('Level 4')];
    }     


    public function getLevelTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['level'];
        $list = $this->getLevelList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
