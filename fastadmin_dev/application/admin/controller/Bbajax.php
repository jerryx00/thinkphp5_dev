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
        }
        $this->general($params);
    }


    /**
    * 上传文件
    */
    public function upload()
    {
        $pm = input('param.');
        $cardtype = $pm['cardtype'];
        $lockid = $pm['id'];
        $acc_nbr = $pm['telnum'];
        unset($pm);

        Config::set('default_return_type', 'json');
        $file = $this->request->file('file');

        if (empty($file)) {
            $this->error(__('No file upload or server upload limit exceeded'));
        }

        //判断是否已经存在附件
        $sha1 = $file->hash();

        $upload = Config::get('upload');

        preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int)$upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
        $fileInfo = $file->getInfo();
        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        $mimetypeArr = explode(',', strtolower($upload['mimetype']));
        $typeArr = explode('/', $fileInfo['type']);

        //验证文件后缀
        if ($upload['mimetype'] !== '*' &&
            (
                !in_array($suffix, $mimetypeArr)
                || (stripos($typeArr[0] . '/', $upload['mimetype']) !== false && (!in_array($fileInfo['type'], $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr)))
            )
            ) {
                $this->error(__('Uploaded file format is limited'));
        }
        $replaceArr = [
            '{year}'     => date("Y"),
            '{mon}'      => date("m"),
            '{day}'      => date("d"),
            '{hour}'     => date("H"),
            '{min}'      => date("i"),
            '{sec}'      => date("s"),
            '{random}'   => Random::alnum(16),
            '{random32}' => Random::alnum(32),
            '{filename}' => $suffix ? substr($fileInfo['name'], 0, strripos($fileInfo['name'], '.')) : $fileInfo['name'],
            '{suffix}'   => $suffix,
            '{.suffix}'  => $suffix ? '.' . $suffix : '',
            '{filemd5}'  => md5_file($fileInfo['tmp_name']),
        ];
        $savekey = $upload['savekey'];
        $savekey = str_replace(array_keys($replaceArr), array_values($replaceArr), $savekey);

        $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
        $fileName = substr($savekey, strripos($savekey, '/') + 1);
        //
        $splInfo = $file->validate(['size' => $size])->move(ROOT_PATH . '/public' . $uploadDir, $fileName);

        if ($splInfo) {
            $imagewidth = $imageheight = 0;
            if (in_array($suffix, ['jpg','png','jpeg'])) {
                $imgInfo = getimagesize($splInfo->getPathname());
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }
            
             //获取base64
            $saveurl = ROOT_PATH . '/public' .$uploadDir . $splInfo->getSaveName();
            $base64 = $this->base64EncodeImage($saveurl);
            
            $params = array(
                'admin_id'    => (int)$this->auth->id,
                'user_id'     => 0,
                'filesize'    => $fileInfo['size'],
                'imagewidth'  => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype'   => $suffix,
                'imageframes' => 0,
                'mimetype'    => $fileInfo['type'],
                'url'         => $uploadDir . $splInfo->getSaveName(),
                'uploadtime'  => time(),
                'storage'     => 'local',
                'sha1'        => $sha1,
                'base64' => $base64,
                'cardtype' => $cardtype,
            );
            //保存到附件中
            $attachment = model("attachment");
            $attachment->data(array_filter($params));
            $ret = $attachment->save($params);
            $attachid = $attachment->id;
            
            //调用HLY接口              
            $Content = [];
            $picname = '';
            //                $Content[$k]['busiSeq'] = $k.$d[telnum];
            $Content['phone'] = $acc_nbr;
            $Content['picFile'] = $picFileInfo['base64'];
            $Content['picType'] = $cardtype;
            $Content['busiSeq'] = $acc_nbr;
            
            $ret = $this->callService('custPicDiscern', $xmlData);
            //保存到$hlylockednum表
            $hlylocknumModel = model("hlylockednum");            
            $data_locknum = [];
            $data_locknum[$cardtype.'_attachid'] = $attachid;
            $data_locknum[$cardtype.'picurl'] = $saveurl;
            $data_locknum[$cardtype.'code'] = $returnCode;
            $data_locknum[$cardtype.'msg'] = $returnMsg;
            
            $hlylocknumModel->data(array_filter($data_locknum));
            $ret = $hlylocknumModel->save($data_locknum, ['id'=>$lockid]);
            

            \think\Hook::listen("upload_after", $attachment);
            $this->success(__('Upload successful'), null, [
                'url' => $uploadDir . $splInfo->getSaveName()
            ]);
        } else {
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
    }

    protected function base64EncodeImage ($image_file, $isCarryDataBase ='0') {
        $base64_image = '';
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));

        if (!$isCarryDataBase){
            //           $pos = strpos($base64_image, 'data:image/png;base64');
            $len  = strlen('data:image/png;base64,') ;
            $base64_image = substr($base64_image, $len+1);

        }
        return $base64_image;
    }


}
