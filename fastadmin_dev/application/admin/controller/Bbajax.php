<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use fast\Random;
use think\addons\Service;
use think\Cache;
use think\Config;
use think\Db;
use think\Lang;
use app\admin\controller\qw\HlybandBase;
use \think\Session;

/**
* Ajax异步请求接口
* @internal
*/
class Bbajax extends HlybandBase
{

    protected $noNeedLogin = ['lang'];
    protected $noNeedRight = ['*'];
    protected $layout = '';
    protected $properties_of_area = [];
    protected $properties_of_types = [];

    public function _initialize()
    {
        parent::_initialize();

        //设置过滤方法
        $this->request->filter(['strip_tags', 'htmlspecialchars']);
    }

    /**
    * 加载语言包
    */
    public function lang()
    {
        header('Content-Type: application/javascript');
        $controllername = input("controllername");
        //默认只加载了控制器对应的语言名，你还根据控制器名来加载额外的语言包
        $this->loadlang($controllername);
        return jsonp(Lang::get(), 200, [], ['json_encode_param' => JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE]);
    }





    /**
    * 读取省市区数据,联动列表
    */
    public function province()
    {
        //id as value,name
        $params = [];
        $province = $this->request->get('province');

        $citylist = [];
        //		if ($province !== '') {
        $params['Type'] = '0';
        $params['Region'] = '南京';
        $params['AddressId'] = '99';
        $params['Address'] = '江苏';
        //		}
        $this->general($params);
    }

    public function city()
    {
        //id as value,name
        $params = [];
        $citylist = [];
        $addressid = $this->request->get('province');
        if ($addressid !== '' ) {
            $region_name = Db::table('qw_hlyjscity')->where(['areaid'=>$addressid])->value('areaname');
            Session::set('admin.region', $region_name);

            $params['Type'] = '1';
            $params['Region'] = Session::get('admin.region');
            $params['AddressId'] = $addressid;
            $params['Address'] = Session::get('admin.region');;
        }
        $this->general($params);
    }

    public function area()
    {
        //id as value,name
        $params = [];
        $citylist = [];
        $province = $this->request->get('province');
        $address = $this->request->get('address');

        $addressid = $this->request->get('city');
        if ($addressid !== '' ) {
            //		 Db::table('qw_hlyband')->where($params)->find();
            $params['Type'] = '2';
            $params['Region'] = Session::get('admin.region');;
            $params['AddressId'] = $addressid;
            $params['Address'] = Session::get('admin.an');
            $params['Limit'] = '2000';
        }
        $this->general($params);
    }
    public function street()
    {
        //id as value,name
        $params = [];
        $citylist = [];
        $province = $this->request->get('province');
        $address = $this->request->get('address');
        $city = $this->request->get('city');
        $addressid = $this->request->get('area');
        if ($addressid !== '' ) {
            $params['Type'] = '3';
            $params['Region'] = Session::get('admin.region');;
            $params['AddressId'] = $addressid;
            $params['Address'] = Session::get('admin.an');
            $params['Limit'] = '2000';
        }

        $this->general($params);
    }

    public function build()
    {
        //id as value,name
        $params = [];
        $citylist = [];
        $addressid = $this->request->get('street');

        if ($addressid !== '' ) {
            $params['Type'] = '3';
            $params['Region'] = Session::get('admin.region');;
            $params['AddressId'] = $addressid;
            $params['Address'] = Session::get('admin.an');
            $params['Limit'] = '2000';
        }
        $this->general($params);
    }

    public function unit()
    {
        //id as value,name
        $params = [];
        $citylist = [];
        $addressid = $this->request->get('build');

        if ($addressid !== '' ) {
            $params['Type'] = '3';
            $params['Region'] = Session::get('admin.region');;
            $params['AddressId'] = $addressid;
            $params['Address'] = Session::get('admin.an');
            $params['Limit'] = '2000';
        }
        $this->general($params);
    }
    public function room()
    {
        //id as value,name
        $params = [];
        $citylist = [];
        $addressid = $this->request->get('unit');

        if ($addressid !== '' ) {
            $params['Type'] = '3';
            $params['Region'] = Session::get('admin.region');;
            $params['AddressId'] = $addressid;
            $params['Address'] = Session::get('admin.an');
            $params['Limit'] = '2000';
        }
        $this->general($params);
    }

}
