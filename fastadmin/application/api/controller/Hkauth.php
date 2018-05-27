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
    public function auth() {
        //        *  调用service
        $lservice = \think\Loader::model('Hly','service');
        //        * 调用方法
        $ret = $lservice->auth($d=[]);
        
        //转换成json
        $data = $lservice->revert($ret);
        //return xml($result);
        $this->resultJwd($data);
    }
    
    public function getNum($d) {
        //        *  调用service
        $lservice = \think\Loader::model('Hly','service');
        //        * 调用方法
        $ret = $lservice->getNum($d);
        
//        $this->resultOfHly($ret);
        $this->result($ret);
    }
    
    public function idenCheck($d=[]) {
        if (count($d) == 0) {
            $d['idcard'] = input('idcard');
            $d['telnum'] = input('telnum');         
            $d['mobile'] = input('telnum');
            $d['username'] = input('username');
        }
        //        *  调用service
        $lservice = \think\Loader::model('Hly','service');
        //        * 调用方法
        $ret = $lservice->idenCheck($d);
        
        $this->resultOfHly($ret);
    }


    public function testToken() {
        $url = 'http://localhost/api/hkauth/auth.html';
        list($result, $returnContent) = http_post_hly($url, $paras=[], '', '');
        if ($result == '200'){
            $xml = (array)simplexml_load_string($returnContent); 
        }    
        $Authorization = object_array($xml['Authorization']);
        $token = $Authorization['Token'];

        return $token;
    }
}
