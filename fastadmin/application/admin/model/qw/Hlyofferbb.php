<?php

namespace app\admin\model\qw;

use think\Model;

class Hlyofferbb extends Model
{
    // 表名
    protected $table = 'qw_hlyofferbb';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [

    ];

public function getStatusAttr($value)
    {
        return $value=='1' ? '上架' : '下架';
    }

    

    







}
