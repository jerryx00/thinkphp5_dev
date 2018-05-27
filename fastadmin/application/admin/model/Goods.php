<?php

namespace app\admin\model;

use think\Model;

class Goods extends Model
{
    // 表名
    protected $name = 'goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'ptype_text',
        'status_text'
    ];
    

    
    public function getPtypeList()
    {
        return ['月包' => __('月包'),'日包' => __('日包'),'7天包' => __('7天包')];
    }     

    public function getStatusList()
    {
        return ['待处理' => __('待处理'),'处理中' => __('处理中'),'待确认' => __('待确认'),'成功' => __('成功'),'失败' => __('失败')];
    }     


    public function getPtypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['ptype'];
        $list = $this->getPtypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $valueArr = explode(',', $value);
        $list = $this->getStatusList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }

    protected function setStatusAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }


}
