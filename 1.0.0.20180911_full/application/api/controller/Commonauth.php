<?php

namespace app\api\controller;

use app\common\controller\Api;


/**
* 宽带认证接口
*/
class Commonauth extends HlyApi
{

	protected $noNeedLogin = '*';
	protected $noNeedRight = '*';

	public function _initialize()
	{
		parent::_initialize();
	}

	public function index() {
		$this->success('请求成功');
	}
	/**
	* @return mixed
	* 获取token
	*/
	public function token() {
		
		//        *  调用service
		$lservice = \think\Loader::model('HlyXml','service');
		//        * 调用方法
		$ret = $lservice->getToken();

//		        dump($ret);exit;
		return $ret;
	}
    
    public function getToken() {
        $info = $this->getRaw();
        //        *  调用service
        $lservice = \think\Loader::model('HlyXml','service');
        //        * 调用方法
        $ret = $lservice->getTokenResponse();

//                dump($ret);exit;
        $this->resultXml($ret);
    }
    
    public function common($url){
        $sign = input('sign');
        $token = input('token');
//        dump($sign);
//        dump($token);
        $info = $this->getRaw();

//        dump($info);exit;
        //        *  调用service
        $lservice = \think\Loader::model('HlyXml','service');
        //        * 调用方法
        $ret = [];

        $ret = $lservice->general($info, $url);

//                dump($ret);exit;
        $this->resultXml($ret);
        
    } 
    
}
