<?php

namespace app\api\controller;

use app\common\controller\Api;
use Think\Db;

/**
* 示例接口
*/
class Demo extends Api
{

	//如果$noNeedLogin为空表示所有接口都需要登录才能请求
	//如果$noNeedRight为空表示所有接口都需要验证权限才能请求
	//如果接口已经设置无需登录,那也就无需鉴权了
	//
	// 无需登录的接口,*表示全部
	protected $noNeedLogin = ['test1','xml','mi','h'];
	// 无需鉴权的接口,*表示全部
	protected $noNeedRight = ['test2'];

	/**
	* 无需登录的接口
	*
	*/
	public function test1()
	{
		$this->success('返回成功', ['action' => 'test1']);
	}

	/**
	* 需要登录的接口
	*
	*/
	public function test2()
	{
		$this->success('返回成功', ['action' => 'test2']);
	}

	/**
	* 需要登录且需要验证有相应组的权限
	*
	*/
	public function test3()
	{
		$this->success('返回成功', ['action' => 'test3']);
	}
	public function xml(){
		$time = getMillisecond();
		$sign = get_hly_sign($time);
		$xmldata['Datetime'] = $time;
		$xmldata['Authorization']['AppKey'] = config('AppKey');
		$xmldata['Authorization']['sign'] = $sign;
		$paras = xml_encode($xmldata, 'Request');
		dump($paras);exit;
	}

	public function getRaw() {
		$postData =  file_get_contents("php://input") ;
		$xml = [];
		if (isset($postData)) {
			$xml = simplexml_load_string($postData);
		}
		$xml = object_array($xml);

		return $xml;
	}

	function mi() {
		$time = getMillisecond();
		echo $time;
	}

	function h(){
		$ret = [];
		$apikey = '';
		$secretkey = '';

		$raw = $this->getRaw();
		$goodcode = $raw['Content']['GoodCode'];

		$info=$this->request->header();
		$token = $info['4GGOGO-Auth-Token'];
		$sign = $info['HTTP-X-4GGOGO-Signature'];
		$time = $raw['Datetime'];

		$vo = Db::name('admin')->where(['token'=>$token])->find();
		if ($vo == flase) {
			$info['code'] = '1001';
			$info['msg'] = '用户不存在';
		} else {
           $token1 = $vo['token'];
           $apikey = $vo['apikey'];
           $secretkey = $vo['secretkey'];
		}
		$signchk =  MD5($apiKey).$secretKey.$time;
		if ($sign != $signchk){
			$info['code'] = '1002';
			$info['msg'] = 'apikey或secretkey校验失败';
		}


	}

}
