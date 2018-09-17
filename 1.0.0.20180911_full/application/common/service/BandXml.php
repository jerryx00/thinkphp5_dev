<?php

namespace app\common\service;
use think\Cache;
use think\Db;
use think\Request;
use think\Response;

class BandXml extends HlyBase{



	public function auth($d) {
		$time = getMillisecond();
		$appkey = config('AppKey_band');
		$SecretKey = config('SecretKey_band');

		$sign = get_band_sign($time);

		$xmldata['Datetime'] = $time;
		$xmldata['Authorization']['AppKey'] = $appkey;
		$xmldata['Authorization']['sign'] = $sign;
		$url = config('HLY_AUTH_URL');

		$paras = xml_encode($xmldata, 'Request');


		list($result, $returnContent) = http_post_hly($url, $paras, '', '');


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
		$time = getMillisecond();
		$appkey = config('AppKey_band');
		$SecretKey = config('SecretKey_band');

		$sign = get_band_sign($time);

		$xmldata['Datetime'] = $time;
		$xmldata['Authorization']['AppKey'] = $appkey;
		$xmldata['Authorization']['sign'] = $sign;
		$xmldata['Authorization']['SecInterface'] = 'NET';
		$url = config('HLY_AUTH_URL');

		$paras = xml_encode($xmldata, 'Request');

		$url = config('HLY_AUTH_URL');
		list($result, $returnContent) = http_post_hly($url, $paras, '', '');
//		dump($xmldata);
//		dump($url);
//		dump($result);
//		dump($returnContent);
//
//		exit;
		$xml = getDataFromXml($returnContent);
		//          dump($xml);exit;
		$data['code'] = $result;
		$data['resp'] = $xml;


		//        dump($data);exit;
		return $data;
	}

	public function getToken(){
		$ret = $this->getTokenResponse();
		$token =  isset($ret['resp']['Authorization']['Token'])?$ret['resp']['Authorization']['Token']:'';
		return $token;

	}

	public function general($xmldataArr, $url){
		$retData = $this->getSignAndToken($xmldataArr);

		$req = isset($xmldataArr['Request']) ?$xmldataArr['Request'] : '';
		$paras = xml_encode($req, 'Request');

		list($result, $returnContent) = http_post_hly($url, $paras, $retData['token'], $retData['sign']);
//		dump($retData);
//		dump($url);
//		dump($paras);
//		dump($retData);  exit;

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
		//        dump($xmlArray);
		$time = '';
		if (!$useXmlTime) {
			$time = getMillisecond();
		} else {
			$time = isset($xmlArray['Request']['Datetime'])?$xmlArray['Request']['Datetime'] : '';
		}
		$sign = get_band_sign($time);
		return $sign;
	}

	private function getSignAndToken($xmlArray) {
		$ret['token'] = $this->getToken();
		$ret['sign'] = $this->getSign($xmlArray);
		return $ret;
	}
}
