<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use fast\Random;
use think\addons\Service;
use think\Cache;
use think\Config;
use think\Db;
use think\Lang;
use app\admin\controller\qw\HlycardBase;
use app\admin\model\qw\Hlylockednum as Hlylockednum;
use app\admin\model\qw\Attachment as Attachment;
use \think\Session;

/**
* Ajax异步请求接口
* @internal
*/
class Hkajax extends HlycardBase
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
    * 上传文件
    */
    public function upload()
    {
        $pm = input('param.');
        $cardtype = $pm['cardtype'];
        $lockid = $pm['id'];
        $acc_nbr = $pm['telnum'];
        $idcard = $pm['idcard'];


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
                'idcard' => $idcard,
            );
            $retCust = $this->custPicDiscernUpload($params, $pm);
            \think\Hook::listen("upload_after", $retCust['attachment']);
            // -1    | 已存在正面照 

            if ($retCust['retCode'] == '0' ) {
                $this->success(__('校验通过'), null, [ 'url' => $params['url']]);
            }else {
                if (strpos($retCust['retMsg'], '已存在') !== false && $retCust['retCode'] == '-1') {
                    $this->success(__('校验通过'), null, [ 'url' => $params['url']]); 
                }
                //                $this->error(__($retCust['retMsg'].$retCust['retCode']), url('admin/qw/profile/indexf',['id'=>$lockid]),['url' => $params['url'], 'msg1'=>$retCust['retMsg'].$retCust['retCode']]);
                $this->error(__($retCust['retMsg'].$retCust['retCode']));
            }

        } else {
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
    }

    public function custPicDiscernUpload($params, $pm) {
        //保存到附件中
        $attachment = new Attachment;
        $vo = [];
        //            $vo = Attachment::get(['idcard'=>$params['idcard']]);
        $attachid = '';
        if (true != $vo) {
            $attachment->data(array_filter($params));
            $ret = $attachment->save($params);
            $attachid = $attachment->id;
        } else {
            $ret = Attachment::update($params, ['idcard'=>$params['idcard']]);
            $attachid = $vo['id']; 
        }

        //调用HLY接口              
        $Content = [];
        $picname = '';
        //                $Content[$k]['busiSeq'] = $k.$d[telnum];
        $Content['phone'] = $pm['telnum'];
        $Content['picFile'] = $params['base64'];
        $Content['picType'] = $pm['cardtype'];
        $Content['busiSeq'] = $pm['telnum'];
        
        unset($ret);
        $ret['retCode'] = '0' ;
//        $ret = $this->callService('custPicDiscern', $Content);
        $msg = isset($ret['retMsg'])? $ret['retMsg'] : ''; 
        if ($ret['retCode'] != '0') {
            $status = '1';
            $msg = $msg .'(' . $ret['retCode'].')';
        }
        //保存到$hlylockednum表
        $hlylocknumModel = new Hlylockednum;           
        $data_locknum = [];
        $cardtype = strtolower($pm['cardtype']);
        $data_locknum[$cardtype.'_attachid'] = $attachid;
        $data_locknum[$cardtype.'picurl'] = $params['url'];
        $data_locknum[$cardtype.'code'] = $ret['retCode'];
        $data_locknum[$cardtype.'msg'] = $msg;


        $hlylocknumModel->data(array_filter($data_locknum));             
        $retUpd = $hlylocknumModel::update($data_locknum, ['id'=>$pm['id']]);
        unset($data_locknum);
        unset($params);
        unset($pm);
        $ret['attachment'] = $attachment;
        unset($retUpd);
        return $ret;

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
