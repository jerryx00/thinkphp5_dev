<?php

namespace app\common\service;
use think\Cache;
use think\Db;
use think\Request;
use think\Response;

class Hk {  

    public function getFitmod($str) {
        $mobile = "%"; 
        if(trim($str) == "" || trim($str) == "%" || trim($str) == "?"){
            $mobile = "%";     //%匹配所有
        } else if (!$pos) {
            $mobile = $str;
        }
        else {
            $firstStr = substr($str, 0,1);
            $lastStr = substr($str, -1);
            if ($firstStr == "?" && $lastStr == "?") {
                $mobile = substr($str,1) ;
            } else {
                if ($firstStr == "?"){
                    $mobile = "_".substr($str,1) ;
                } else if ($lastStr == "?") {
                    $mobile = substr($str,0,-1)."_";
                } else {
                    //$arr = explode('?', $str);
                    //                   foreach ($$arr as $k => $v) {
                    //                     $mobile = $v;
                    //
                    //                   }
                    //  截取第一个斜杠前面的内容可以这样来：
                    $f = substr($str,0,strpos($str, '?'));
                    if ($f == "") {
                        $str = $str ."?";
                    }
                    // 截取第一个斜杠后面的内容可以这样来：

                    $e = substr($str,strpos($str,'?')+1);
                    $lefe_len = 11- strlen($f.$e);
                    for($i=0;$i<$lefe_len;$i++){
                        $mid = $mid."_";
                    }
                    $mobile = $f.$mid.$e;
                }
            }
        }        

        return $mobile;
    }

    public function general($url, $d, $content='Content') {
        $time = getMillisecond();
        $xmldata['Datetime'] = $time;
        $xmldata[$content] = $d;
        $paras = xml_encode($xmldata, 'Request');


        $tokenAndsign['token'] = '1';
        $tokenAndsign['sign'] = '2';

        //                dump($tokenAndsign);exit;
        list($result, $returnContent) = http_post_hk($url, $paras, $tokenAndsign['token'], $tokenAndsign['sign']);



        $data = getDataFromXml($returnContent);

        return $data;
    }

}
