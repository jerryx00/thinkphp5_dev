<?php

namespace app\admin\controller\qw;

use app\common\controller\Backend; 
use app\common\service\Hk;

/**
*
*
* @icon fa fa-circle-o
*/
class HlycardBase extends Backend
{

    /**
    * Hlyband模型对象
    * @var \app\admin\model\qw\Hlyband
    */
    protected $model = null;


    protected function callService($url, $params, $content='', $flag='1') {
        $lservice = \think\Loader::model('Hk','service');
        //        * 调用方法
        $ret = [];

        $ret = $lservice->general($url, $params, $content);
        $result = [];
        if ($flag == '1') {
            $result = isset($ret['Response']['Content']) ? $ret['Response']['Content'] : '';
            return $result;
        }   else {
            return $ret;
        }

    }


    public function general($params, $url='')
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
