<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Version;
use app\common\library\Auth;
use think\Config;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Hook;
use think\Lang;
use think\Loader;
use think\Request;
use think\Response;

/**
* 公共接口
*/
class LlfxApi extends Api
{

	protected $noNeedLogin = '*';
	protected $noNeedRight = '*';

	public function _initialize()
	{
		parent::_initialize();
	}


	protected function resultOfHly($data, $msg = '', $code = 0, $type = "xml", array $header = [])
	{
		$result = [
			'code' => $code,
			'msg'  => $msg,
			'time' => Request::instance()->server('REQUEST_TIME'),
			'data' => $data,
		];
		// 如果未设置类型则自动判断
		$type = $type ? $type : ($this->request->param(config('var_jsonp_handler')) ? 'jsonp' : $this->responseType);

		if (isset($header['statuscode']))
		{
			$code = $header['statuscode'];
			unset($header['statuscode']);
		}
		else
		{
			//未设置状态码,根据code值判断
			$code = $code >= 1000 || $code < 200 ? 200 : $code;
		}
		$response = Response::create($result, $type, $code)->header($header);
		throw new HttpResponseException($response);
	}


	protected function resultXml($data = [], $type = 'xml', array $header = [])
	{
		$code = $data['code'];
		//        dump($data);exit;
		if ($code == '200'){
			$msg = 'ok';
		}  else {
			$msg = 'fail';
		}
		$result = [
			'Code' => $code,
			'Msg'  => $msg,
			'Time' => Request::instance()->server('REQUEST_TIME'),
			'Response' => $data['resp'],
		];
		// 如果未设置类型则自动判断
		$type = $type ? $type : ($this->request->param(config('var_jsonp_handler')) ? 'jsonp' : $this->responseType);

		if (isset($header['statuscode']))
		{
			$code = $header['statuscode'];
			unset($header['statuscode']);
		}
		else
		{
			//未设置状态码,根据code值判断
			$code = $code >= 1000 || $code < 200 ? 200 : $code;
		}

		$response = Response::create($result, $type, $code)->header($header);
		//        dump($response);exit;
		throw new HttpResponseException($response);
	}

	public function checkValid($xml)
	{
		//主要用来返回检查是否有错误
		$info['code'] = '0';
		$info['msg'] = '';
		//原始的请求
		$info['Request'] = $xml;

		return $info;

	}
	public function checkFee($xml){
		$goodPrice = '299';
		$info=$this->request->header($xml);
	}

	public function getRaw() {
		$postData =  file_get_contents("php://input") ;
		$info = $this->checkValid($postData);
		$info['errcode'] = '0';
		return $info;
	}


}
