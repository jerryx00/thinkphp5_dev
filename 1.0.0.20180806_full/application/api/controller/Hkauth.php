<?php

namespace app\api\controller;

use app\common\controller\Api;


/**
* 号卡认证接口
*/
class Hkauth extends HlyApi
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

	public function getNum() {
		$sign = input('sign');
		$token = input('token');
				dump($sign);
		//		dump($token);exit;
		$info = $this->getRaw();

		//		dump($info);exit;
		//        *  调用service
		$lservice = \think\Loader::model('HlyXml','service');
		//        * 调用方法
		$ret = [];

		$ret = $lservice->getNum($info);

		//		        dump($ret);exit;
		$this->resultXml($ret);
	}

	public function lockNum(){
		$sign = input('sign');
		$token = input('token');
		//        dump($sign);
		//        dump($token);exit;
		$info = $this->getRaw();

		//        dump($info);exit;
		//        *  调用service
		$lservice = \think\Loader::model('HlyXml','service');
		//        * 调用方法
		$ret = [];

		$url = config('HLY_LOCKNUM_URL');
		$ret = $lservice->general($info, $url);

		//                dump($ret);exit;
		$this->resultXml($ret);

	}


	public function idenCheck(){
		$sign = input('sign');
		$token = input('token');
		//        dump($sign);
		//        dump($token);exit;
		$info = $this->getRaw();

		//        dump($info);exit;
		//        *  调用service
		$lservice = \think\Loader::model('HlyXml','service');
		//        * 调用方法
		$ret = [];

		$url = config('HLY_IDENCHECK_URL');
		$ret = $lservice->general($info, $url);

		//                dump($ret);exit;
		$this->resultXml($ret);

	}

	public function unLockNum(){
		$sign = input('sign');
		$token = input('token');
		//        dump($sign);
		//        dump($token);exit;
		$info = $this->getRaw();

		//        dump($info);exit;
		//        *  调用service
		$lservice = \think\Loader::model('HlyXml','service');
		//        * 调用方法
		$ret = [];

		$ret = $lservice->general($info, 'unLockNum');

		//                dump($ret);exit;
		$this->resultXml($ret);

	}


	public function order(){
		$sign = input('sign');
		$token = input('token');
		//        dump($sign);
		//        dump($token);exit;
		$info = $this->getRaw();

		//        dump($info);exit;
		//        *  调用service
		$lservice = \think\Loader::model('HlyXml','service');
		//        * 调用方法
		$ret = [];

		$url = config('HLY_ORDER_URL');
		$ret = $lservice->general($info, $url);

		//                dump($ret);exit;
		$this->resultXml($ret);

	}


	public function ordercancel(){
		$sign = input('sign');
		$token = input('token');
		//        dump($sign);
		//        dump($token);exit;
		$info = $this->getRaw();

		//        dump($info);exit;
		//        *  调用service
		$lservice = \think\Loader::model('HlyXml','service');
		//        * 调用方法
		$ret = [];

		$url = config('HLY_ORDERCANCEL_URL');
		$ret = $lservice->general($info, $url);

		//                dump($ret);exit;
		$this->resultXml($ret);

	}

	public function orderInfo(){
		$sign = input('sign');
		$token = input('token');

		$info = $this->getRaw();

		$lservice = \think\Loader::model('HlyXml','service');

		$ret = [];

		$url = config('HLY_QUERY_URL');
		$ret = $lservice->general($info, $url);

		//                dump($ret);exit;
		$this->resultXml($ret);

	}

	public function custPicDiscern(){
		$sign = input('sign');
		$token = input('token');

		$info = $this->getRaw();

		$lservice = \think\Loader::model('HlyXml','service');

		$ret = [];

		$url = config('HLY_PIC_DISCERN');
		$ret = $lservice->general($info, $url);

		//                dump($ret);exit;
		$this->resultXml($ret);

	}

	public function custPhotoCompare(){
		$sign = input('sign');
		$token = input('token');

		$info = $this->getRaw();

		$lservice = \think\Loader::model('HlyXml','service');

		$ret = [];

		$url = config('HLY_CUST_PHOTO_COMPARE');
		$ret = $lservice->general($info, $url);

		//                dump($ret);exit;
		$this->resultXml($ret);

	}

	public function uploadCustpic(){
		$sign = input('sign');
		$token = input('token');
		//        dump($sign);
		//        dump($token);exit;
		$info = $this->getRaw();

		//        dump($info);exit;
		//        *  调用service
		$lservice = \think\Loader::model('HlyXml','service');
		//        * 调用方法
		$ret = [];

		$url = config('HLY_UPLOAD_CUSTPIC');
		$ret = $lservice->general($info, $url);

		//                dump($ret);exit;
		$this->resultXml($ret);

	}




}
