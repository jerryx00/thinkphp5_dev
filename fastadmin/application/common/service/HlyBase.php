<?php
namespace app\common\service;

class HlyBase
{
    public function revert_nouse($result, $returnContent) {
        $returnContent = str_replace(array("\r\n", "\r", "\n"), "", $returnContent);   
        if ($result == '200'){
            $xml = (array)simplexml_load_string($returnContent); 
        }

        $data = [];
        $data['code'] = $result;
        $data['resp'] = $returnContent;
    
        return $data;
    }
    
    public function revert($ret) {
        //将xml转成array  
        $result = (array)simplexml_load_string($ret['resp']);
        
        $data['code'] = $ret['code'];
        $data['resp'] = object_array($result);         
    
        return $data;
    }

}