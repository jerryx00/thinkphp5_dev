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
protected $dataLimit = 'auth'; //默认基类中为false，表示不启用，可额外使用auth和personal两个值
protected $dataLimitField = 'uid'; //数据关联字段,当前控制器对应的模型表中必须存在该字段



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

    public function general($params, $url='addressSearch', $times=1)
	{
		$citylist = [];
		$citys = [];
		if ($times == 1){
			$citylist = $this->getAddr($url, $params);
		}   else {
			for($n=0;$n<$times;$n++){
				$params['Page'] = $n + 1;
				if (isset($params['Type']) && $params['Type'] == '3'){
					$citys[$n] = $this->getAddr($url, $params);
				} else {
					$citys[$n] = $this->getAddr($url, $params);
				}

				$citylist = array_merge($citylist, $citys[$n]);
			}
		}
		$this->success('', null, $citylist);
	}


}
