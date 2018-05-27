<?php


if (!function_exists('xml_encode')) {
    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id 数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    function xml_encode($data, $root = 'think', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8')
    {
        if (is_array($attr)) {
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml .= "<{$root}{$attr}>";
        $xml .= data_to_xml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }
}

if (!function_exists('data_to_xml')) {
    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @param string $item 数字索引时的节点名称
     * @param string $id 数字索引key转换为的属性名
     * @return string
     */
    function data_to_xml($data, $item = 'item', $id = 'id')
    {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
            $xml .= "</{$key}>";
        }
        return $xml;
    }
}

if (!function_exists('object_array')) {
    /**
     * PHP要用json的数据，通过json_decode转出来的数组并不是标准的array，
     * 所以需要用这个函数进行转换。
     * @param mixed $array
     * @return []
     */
    function object_array($array){
        if(is_object($array)){
            $array = (array)$array;
        }
        if(is_array($array)){
            foreach($array as $key=>$value){
                $array[$key] = object_array($value);
            }
        }
        return $array;
    }
}

if (!function_exists('get_hly_sign')) {
    /**
     * 获取和力云签名
     *
     * @param mixed $appkey
     * @param mixed $SecretKey
     * @param mixed $time
     */
    function get_hly_sign($time, $appkey = '', $SecretKey = '')
    {
        if ($appkey == '') {
            $appkey = config('AppKey');
        }
        if ($SecretKey == '') {
            $SecretKey = config('SecretKey');
        }
        $sign = md5($appkey . $SecretKey . $time);
        return $sign;
    }
}

if (!function_exists('http_post_hly')) {
    /**
     * 号卡提交
     *
     * @param mixed $url
     * @param mixed $requestXML
     * @param mixed $token
     * @param mixed $signatrue
     */
    function http_post_hly($url, $requestXML, $token = '', $signatrue = '')
    {
        $header = ["Content-type: application/xml"];

        if ($token != '') {
            $header = ["Content-type: application/xml", "4GGOGO-Auth-Token: " . $token];
        }
        if ($signatrue != '') {
            $header = ["Content-type: application/xml", "4GGOGO-Auth-Token: " . $token, "HTTP-X-4GGOGO-Signature: " . $signatrue];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXML);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //close
        curl_close($ch);
        return array($httpCode, $response);
    }
}

if (!function_exists('getMillisecond')) {
    function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }
}
