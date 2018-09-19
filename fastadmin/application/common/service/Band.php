<?php

namespace app\common\service;
use think\Cache;
use think\Db;
use think\Request;
use think\Response;

class Band {



	public function detail($d) {
		$url = 'detail';

		$time = getMillisecond();
		$xmldata['Datetime'] = $time;
		$xmldata['Content']['mobile'] = $d['accnbr'];
		$paras = xml_encode($xmldata, 'Request');


		$tokenAndsign['token'] = '1';
		$tokenAndsign['sign'] = '2';

		//                dump($tokenAndsign);exit;
		list($result, $returnContent) = http_post_hly($url, $paras, $tokenAndsign);



		$data = getDataFromXml($returnContent);

		return $data;
	}

	public function general($url, $d) {
		$time = getMillisecond();
		$xmldata['Datetime'] = $time;
		$xmldata['Content'] = $d;
		$paras = xml_encode($xmldata, 'Request');

		$tokenAndsign['token'] = '1';
		$tokenAndsign['sign'] = '2';

		//                dump($tokenAndsign);exit;
		list($result, $returnContent) = http_post_hly($url, $paras, $tokenAndsign['token'], $tokenAndsign['sign']);



		$data = getDataFromXml($returnContent);

		return $data;
	}

}
