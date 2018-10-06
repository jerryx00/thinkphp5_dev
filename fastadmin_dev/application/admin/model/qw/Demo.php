<?php

namespace app\admin\model\qw;

use think\Model;

class Demo extends Model
{
    // 表名
    protected $table = 'qw_demo';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [

    ];
    

    







}
