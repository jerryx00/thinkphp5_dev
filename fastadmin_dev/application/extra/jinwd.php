<?php

//上传配置
return [
	'HLY_AUTH_URL' => 'auth.html',
	'HLY_GETNUM_URL' => 'http://183.207.215.209:8088/bss/getNum.html',
	'HLY_LOCKNUM_URL' => 'http://183.207.215.209:8088/bss/lockNum.html',
	'HLY_UNLOCK_URL' => 'http://183.207.215.209:8088/bss/unLockNum.html',
	'HLY_IDENCHECK_URL' => 'http://183.207.215.209:8088/bss/idenCheck.html',
	'HLY_ORDER_URL' => 'http://183.207.215.209:8088/bss/order.html',
	'HLY_ORDERCANCEL_URL' => 'http://183.207.215.209:8088/bss/ordercancel.html',
	'HLY_QUERY_URL' => 'http://183.207.215.209:8088/bss/query/orderInfo.html',
	//2018-09新加，身份认证
	//    身份证件认证接口
	'HLY_PIC_DISCERN' => 'http://183.207.215.209:8088/bss/custPicDiscern.html',
	//   人像比对接口
	'HLY_CUST_PHOTO_COMPARE' => 'http://183.207.215.209:8088/bss/custPhotoCompare.html',
	//    预约信息上传接口
	'HLY_UPLOAD_CUSTPIC' => 'http://183.207.215.209:8088/bss/uploadCustpic.html',


	//    'HLY_ORDERCHECK_URL' => 'http://183.207.215.209:8088/bss/orderCheck.html',

	'HLY_yyOrderCreate' => 'http://183.207.215.209:8088/bss/broadband/yyOrderCreate.html',
	'HLY_yyOrderInfo' => 'http://183.207.215.209:8088/bss/broadband/yyOrderInfo.html',
	//    4.8.12.    宽带网销创建订单接口
	'HLY_wxOrderCreate' => 'http://183.207.215.209:8088/bss/broadband/wxOrderCreate.html',
	//    4.8.13.    宽带网销订单查询接口
	'HLY_wxOrderInfo' => 'http://183.207.215.209:8088/bss/broadband/wxOrderInfo.html',
	//    4.8.14.    宽带网销订单取消接口
	'HLY_orderCancel' => 'http://183.207.215.209:8088/bss/broadband/orderCancel.html',

	//    4.8.15.    地址查询接口
	'HLY_addressSearch' => 'http://183.207.215.209:8088/bss/broadband/addressSearch.html',

	//    4.8.16.    宽带营销案查询接口
	'HLY_mmsdeal' => 'http://183.207.215.209:8088/bss/broadband/mmsdeal.html',
	//    4.8.17.    宽带校验
	'HLY_idenCheck' => 'http://183.207.215.209:8088/bss/broadband/idenCheck.html',
	//        4.8.18.    宽带信息查询
	'HLY_detail'  => 'http://183.207.215.209:8088/bss/broadband/detail.html',

	'HLY_goodcheck'  => 'http://183.207.215.209:8088/bss/broadband/goodcheck.html',

	'AppKey' => '13af4043889f48868e75a84f3e3b2505', // API Key
	'SecretKey' => "454193dc17c54752a47b539ee526bcb0", // Secret Key

	'AppKey_band' => '21db5216d7434530bc1cd8f9f7707089', // API Key
	'SecretKey_band' => "fcce0d45faf440c9b2345b318ef79552", // Secret Key

	'HLY_BAND_URL_PREFIX' => 'http://59.110.51.81/api/bandauth/',
	'HLY_HK_URL_PREFIX' => 'http://59.110.51.81/api/hkauth/',
];
