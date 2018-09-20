<?php

namespace app\common\service;
use think\Cache;
use think\Db;
use think\Request;
use think\Response;
use think\Log;

class HlyXml extends HlyBase{



	public function auth($d) {
		$time = getMillisecond();
		$appkey = config('AppKey');
		$SecretKey = config('SecretKey');

		$sign = get_hly_sign($time);

		$xmldata['Datetime'] = $time;
		$xmldata['Authorization']['AppKey'] = $appkey;
		$xmldata['Authorization']['sign'] = $sign;
		$url = config('HLY_AUTH_URL');

		$paras = xml_encode($xmldata, 'Request');


		list($result, $returnContent) = http_post_hk($url, $paras, '', '');


		$data = $this->revertXml($result, $returnContent);

		return $data;
	}
	/**
	* 获取xml 格式返回的token数据
	*
	* @param mixed $d
	*/
	public function getTokenResponse() {

		$data['code'] = '';
		$data['resp'] = '';


		//if ($info['code'] != '200'){
		//           return $info;
		//        }

		//        dump($xml);exit;
		$time = getMillisecond();
		$appkey = config('AppKey');
		$SecretKey = config('SecretKey');

		$sign = get_hly_sign($time);

		$xmldata['Datetime'] = $time;
		$xmldata['Authorization']['AppKey'] = $appkey;
		$xmldata['Authorization']['sign'] = $sign;
		$url = config('HLY_AUTH_URL');
		//        dump($xmldata);exit;

		$paras = xml_encode($xmldata, 'Request');

//		$url = 'getToken';
		list($result, $returnContent) = http_post_hk($url, $paras, '', '');
		//dump($xmldata);
//		dump($url);
//		dump($result);
//		dump($returnContent);exit;
		$xml = getDataFromXml($returnContent);
		//          dump($xml);exit;
		$data['code'] = $result;
		$data['resp'] = $xml;


		//        dump($data);exit;
		return $data;
	}

	public function getToken(){
		$ret = $this->getTokenResponse();
		//        dump($ret);
		$token =  isset($ret['resp']['Authorization']['Token'])?$ret['resp']['Authorization']['Token']:'';
		return $token;

	}
	public function getNum($xmldataArr){
		$retData = $this->getSignAndToken($xmldataArr);
		$url = config('HLY_GETNUM_URL');

		$req = isset($xmldataArr['Request']) ?$xmldataArr['Request'] : '';
		$paras = xml_encode($req, 'Request');

		//        dump($paras);
		//        dump($retData);
		list($result, $returnContent) = http_post_hk($url, $paras, $retData['token'], $retData['sign']);
		//        dump($paras);
		//        dump($result);
		//        dump($returnContent);
		$data['code'] = $result;
		$xml = getDataFromXml($returnContent);
		//        dump($xml);
		$data['resp'] = $xml;


		//        $data['Response'] = isset($xml['Response'])?$xml['Response'] : '';
		//        dump($data);exit;
		return $data;
	}

	public function general($xmldataArr, $url){
		$retData = $this->getSignAndToken($xmldataArr);

		$req = isset($xmldataArr['Request']) ?$xmldataArr['Request'] : '';
		$paras = xml_encode($req, 'Request');

		//        dump($retData);
		list($result, $returnContent) = http_post_hk($url, $paras, $retData['token'], $retData['sign']);
		//        dump($url);
		//        dump($paras);
		//        dump($retData);

		//        dump($result);
		//        dump($returnContent);exit;
		$data['code'] = $result;
		$xml = getDataFromXml($returnContent);
		//        dump($xml);
		$data['resp'] = $xml;


		//        $data['Response'] = isset($xml['Response'])?$xml['Response'] : '';
		//        dump($data);exit;
		return $data;
	}


	/**
	* 获取sign
	*
	* @param mixed $xml
	*/
	private function getSign($xmlArray, $useXmlTime=true){
//		\think\Log::DEBUG('$xmlArray');
		$time = '';
		if (!$useXmlTime) {
			$time = getMillisecond();
		} else {
			$time = isset($xmlArray['Request']['Datetime'])?$xmlArray['Request']['Datetime'] : '';
		}
		//        dump($time);exit;
		$appkey = config('AppKey');
		$SecretKey = config('SecretKey');

		//        $token = $this->getToken();
		//dump($xmlArray);
		//dump($time);exit;
		$sign = get_hly_sign($time);
		//        $retData['token'] = $token;
		//        $retData['sign'] = $sign;
		return $sign;
	}

	private function getSignAndToken($xmlArray) {
		$ret['token'] = $this->getToken();
		$ret['sign'] = $this->getSign($xmlArray);
		return $ret;
	}
}
