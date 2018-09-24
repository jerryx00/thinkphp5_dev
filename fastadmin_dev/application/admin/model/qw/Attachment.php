<?php

namespace app\admin\model\qw;

use think\Model;

class Attachment extends Model
{

    // 表名
    protected $table = 'qw_attachment';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 定义字段类型
    protected $type = [
    ];

    public function setUploadtimeAttr($value)
    {
        return strtotime($value);
    }

}
