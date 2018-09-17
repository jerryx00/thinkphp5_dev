<?php

namespace app\admin\model\qw;

use think\Model;
use think\Db;
use EasyWeChat\Support\XML;

class Hlyorder extends Model
{
    // 表名
    protected $table = 'qw_hlylockednum';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;

    // 追加属性
    protected $append = [

    ];
    
    public function getRegion()     {
        $list = Db::table('qw_hlyjscity')->field('areaid,areaname')->where(['status'=>1])->order('areaid')->select();
          
        return $list;
    } 
    
     public function getOffer()
    {
         $list = Db::table('qw_hlyoffer')->field('offer_id,offer_name,price')->where(['status'=>1])->order('offer_id')->select();
          
        return $list;
    }   
    
   
}
