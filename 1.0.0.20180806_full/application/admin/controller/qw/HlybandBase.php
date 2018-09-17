<?php

namespace app\admin\controller\qw;

use app\common\controller\Backend;

/**
*
*
* @icon fa fa-circle-o
*/
class HlybandBase extends Backend
{

    /**
    * Hlyband模型对象
    * @var \app\admin\model\qw\Hlyband
    */
    protected $model = null;


    protected function callService($url, $params) {
        $lservice = \think\Loader::model('Band','service');
        //        * 调用方法
        $ret = [];

        $ret = $lservice->general($url, $params);
        return $ret;
    }

    protected function getAddr($url, $params) {
        $bbRegion = $params['Region'];

        $ret = $this->callService($url, $params);
        $retCode = isset($ret['Response']['Content']['retCode']) ? $ret['Response']['Content']['retCode'] :'';
        $Region = isset($ret['Response']['Content']['Region']) ? $ret['Response']['Content']['Region'] : '';


        $r = [];
        if ($Region == '') {
            return $r;
        }
        //        $r['id'] = '';
        $city  = isset($Region['item']) ? $Region['item']:'';
        if ($city !=''){
            foreach ($city as $k => $v) {
                $id = $v['@attributes']['id'];
                unset($v['@attributes']);
                $addr = $v['Address'];
                $v['id'] = $id;
                //			if ($bbRegion == $addr) {
                $r[$k]['value'] =  $v['AddressId'];
                $r[$k]['name'] =  $v['Address'];
                $r[$k] = array_merge($r[$k], $v);
                //获取type=2 时的一些关键信息
//                $addr_type_2 = [];
                if ($params['Type'] == '2') {
                    $r[$k]['name'] = isset($v['UptownName'])? $v['UptownName'] :$v['Address'];
                } else if ($params['Type'] == '3') {
                    $r[$k]['name'] = isset($v['AddressName'])? $v['AddressName'] :$v['Address'];
                }
            }
        } else {
        	$r[0] = $Region;
            $r[0]['value'] =  $Region['AddressId'];
            $r[0]['name'] =  isset($Region['AddressName'])? $Region['AddressName'] :$Region['Address'];
            //			$r[0] = array_merge($r[0], $Region);
        }
        unset($ret);
        unset($Region);
        return $r;
    }

    public function general($params, $url='addressSearch')
    {

        $citylist = $this->getAddr($url, $params);
        //		}
        //		$cnt = count($citylist);
        //		$citylist[$cnt]['value']='';
        //		$citylist[$cnt]['name']='以上全选';


        //$url = config('HLY_BAND_URL_PREFIX').'addressSearch';
        //        $citylist = $this->getAddr($url, $params);
        $this->success('', null, $citylist);
    }



}
