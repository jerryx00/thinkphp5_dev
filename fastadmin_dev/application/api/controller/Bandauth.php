<?php

namespace app\api\controller;

use app\common\controller\Api;


/**
* 宽带认证接口
*/
class Bandauth extends CommonBand
{

	protected $noNeedLogin = '*';
	protected $noNeedRight = '*';

	public function _initialize()
	{
		parent::_initialize();
	}

	public function getToken() {
        $info = $this->getRaw();
        //        *  调用service
        $lservice = \think\Loader::model('BandXml','service');
        //        * 调用方法
        $ret = $lservice->getTokenResponse();

//                dump($ret);exit;
        $this->resultXml($ret);
    }

    /**
    * 宽带信息查询
    *
    */
    public function detail(){
        $url = config('HLY_detail');
        $ret = $this->common($url);
    }
    /**
    * 地址查询
    *
    */
    public function addressSearch(){
        $url = config('HLY_addressSearch');
        $ret = $this->common($url);
    }
    /**
    * 宽带营销案查询接口
    *
    */
    public function mmsdeal(){
        $url = config('HLY_mmsdeal');
        $ret = $this->common($url);
    }

    /**
    * 宽带校验
    *
    */
    public function idenCheck(){
        $url = config('HLY_idenCheck');
        $ret = $this->common($url);
    }

    /**
    * 宽带网销创建订单接口
    *
    */
     public function create(){
        $url = config('HLY_wxOrderCreate');
        $ret = $this->common($url);
    }
    /**
    * 宽带网销订单查询接口
    *
    */
     public function info(){
        $url = config('HLY_wxOrderInfo');
        $ret = $this->common($url);
    }
    /**
    * 宽带网销订单取消接口
    *  暂可不实现
    */
     public function cancel(){
        $url = config('HLY_orderCancel');
        $ret = $this->common($url);
    }

    /**
    * 宽带商品校验
    *
    */
    public function goodcheck(){
        $url = config('HLY_goodcheck');
        $ret = $this->common($url);
    }


    /**
    * 宽带预约创建订单接口
    *
    */
     public function yycreate(){
        $url = config('HLY_yyOrderCreate');
        $ret = $this->common($url);
    }
    /**
    * 宽带预约订单查询接口
    *
    */
     public function yyinfo(){
        $url = config('HLY_yyOrderInfo');
        $ret = $this->common($url);
    }




}
