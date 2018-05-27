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
class HlyApi extends Api
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }


    protected function resultOfHly($data, $msg = '', $code = 0, $type = "", array $header = [])
    {
        $code = $data['code'];
        $result = $data['resp'];

        // 如果未设置类型则自动判断
        //        $type = "xml";
        // 如果未设置类型则自动判断
        $type = $type ? $type : ($this->request->param(config('var_jsonp_handler')) ? 'jsonp' : $this->responseType);
        if ($type == "xml") {
            $resultXml =  $result ; 
            //将xml转成array  
            $result = (array)simplexml_load_string($resultXml);
            $result = object_array($result);
        }

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

    protected function resultJwd($data = null, $type = null, array $header = [])
    {
        $code = $data['code'];
        if ($code == '200'){
            $msg = 'ok';
        }  else {
            $msg = 'fail';
        }
        $result = [
            'Code' => $code,
            'Msg'  => $msg,
            'Time' => Request::instance()->server('REQUEST_TIME'),
            'Content' => $data['resp'],
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
    
    

}
