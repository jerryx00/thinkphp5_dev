<?php

namespace app\admin\model\qw;

use think\Model;
use think\Db;
use EasyWeChat\Support\XML;

class Hlyorder extends Model
{
    // 表名
    protected $table = 'qw_hlyorder';

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
    public function getOffer()     {
        $list = Db::table('qw_hlyoffer')->field('offer_id,offer_name,price')->where(['status'=>1])->order('offer_id')->select();

        return $list;
    }

    public function getNum($ret) {
        $retCode = '';
        $retCode = isset($ret['Response']['Content']['ReturnCode']) ? $ret['Response']['Content']['ReturnCode'] : '';
        $retMsg = isset($ret['Response']['Content']['ReturnMessage']) ? $ret['Response']['Content']['ReturnMessage'] : '';

        $Region = isset($ret['Response']['Content']['TelnumList']) ?$ret['Response']['Content']['TelnumList'] : '';
        $city  = isset($Region['item']) ? $Region['item']:'';
        $r = [];
        if ($city !=''){
            foreach ($city as $k => $v) {
                $id = $v['@attributes']['id'];
                unset($v['@attributes']);
                $addr = $v['TelNum'];
                $v['id'] = $id;
                $r[$k] = $v;
            }
        }
        unset($ret);
        unset($Region);
        return $r;
    }







}
