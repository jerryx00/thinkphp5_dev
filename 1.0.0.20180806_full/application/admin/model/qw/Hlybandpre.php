<?php

namespace app\admin\model\qw;

use think\Model;
use think\Db;

class Hlybandpre extends Model
{
    // 表名
    protected $table = 'qw_hlybandpre';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;

    // 追加属性
    protected $append = [

    ];

     public function getRegionList()     {
       $list = Db::table('qw_hlyjscity')->field('areaid,areaname')->where(['status'=>1])->order('areaid')->select();
        $ret = [];
        foreach ($list as $k => $v) {
            $ret[$v['areaid']] = $v['areaname'];
        }
        return $ret;
    }

     public function getRegion()     {
        $list = Db::table('qw_hlyjscity')->field('areaid,areaname')->where(['status'=>1])->order('areaid')->select();

        return $list;
    }



    public function getOfferIdList()
    {
        $typeList = config('site.categorytype');
        foreach ($typeList as $k => &$v)
        {
            $v = __($v);
        }
//        dump($typeList);exit;
        return $typeList;
    }


    public function getRegionTextAttr($value, $data)
        {
            $value = $value ? $value : (isset($data['region']) ? $data['region'] : '');
            $valueArr = explode(',', $value);
            $list = $this->getRegionList();
            return implode(',', array_intersect_key($list, array_flip($valueArr)));
        }


        public function getOfferIdTextAttr($value, $data)
        {
            $value = $value ? $value : (isset($data['offer_id']) ? $data['offer_id'] : '');
            $valueArr = explode(',', $value);
            $list = $this->getOfferIdList();
            return implode(',', array_intersect_key($list, array_flip($valueArr)));
        }

        protected function setRegionAttr($value)
        {
            return is_array($value) ? implode(',', $value) : $value;
        }

        protected function setOfferIdAttr($value)
        {
            return is_array($value) ? implode(',', $value) : $value;
        }

public function getCreatedAtAttr($value)
    {
        return toDate($value);
    }

    public function getUpdatedAtAttr($value)
    {
        return toDate($value);
    }










}
