<?php

namespace app\admin\command;

use app\common\controller\Backend; 
use Think\Db;
use Think\Request;


/**
*
*
* @icon fa fa-circle-o
*/
class FastAdmin5ba501eb55b29 extends HlycardBase
{

    /**
    * Hlyorder模型对象
    * @var \app\admin\model\qw\Hlyorder
    */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\qw\Hlyorder;

    }

    /**
    * 从和力云查询符合条件的号码
    * 
    */
    public function getnum(){
        $params = input('param.');
        $this->view->assign("regionList", $this->model->getRegion());
        $this->view->assign("offerList", $this->model->getOffer());
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            //dump($params);exit;
            if ($params) {
                $arr_region= explode('-',$params['Region']) ;
                $params['Region'] = $arr_region[1];

                //        *  调用service
                $lservice = \think\Loader::model('Hk','service');
                $params['Fitmod'] = $lservice->getFitmod($params['filter']);                
                unset($params['filter']);
                unset($params['mobile']);

                $ret = $this->callService('getNum', $params);
                $numList = $this->model->getNum($ret);


                $this->view->assign("numList", $numList);
                //if ($retCode == '0000') {
                //                    $this->success('号码查询成功', '/admin/qw/hlyorder/locknum');
                //                }  else {
                //                    $this->error('号码查询成功, 失败原因:'.$retMsg.'('.$retCode.')');
                //                }
            } else {
                $this->error(__('Parameter %s can not be empty', ''));
            }

        }
        return $this->view->fetch();

    }

    public function locknum(){
        $params = input('param.');
        $this->view->assign("regionList", $this->model->getRegion());

        $xmldata['Region'] = $params['region'];
        $xmldata['Telnum'] = $params['telnum']; 
        $xmldata['Type'] = '2';
        $OrgId = isset($params['OrgId'])?$params['OrgId']:'';
        if ($OrgId != '') {
            $xmldata['OrgId'] = $OrgId;  
        }        
        $ret = $this->callService('locknum', $xmldata);
        unset($xmldata);

        $retCode = '';
        $retCode = isset($ret['ReturnCode']) ? $ret['ReturnCode'] : '';
        $retMsg = isset($ret['ReturnMessage']) ? $ret['ReturnMessage'] : '';

        $retCode = '0';  

        $vo = Db::table('qw_hlylockednum')->where(['telnum'=>$params['telnum']])->find();
        $params['returncode'] = $retCode; 
        $params['returnmessage'] = $retMsg;
        if ($vo) {
            unset($d['created_at']);    
            $ret = Db::table('qw_hlylockednum')->where($filter)->update($params);
        } else {
            $id = Db::table('qw_hlylockednum')->insertGetId($params);
        }
        unset($params);
        if ($retCode == '0') { 
            $this->success('号码锁定成功', '/admin/qw/hlyorder/idencheck?id='.$id);
        }  else {
            $this->error('号码锁定成功, 失败原因:'.$retMsg.'('.$retCode.')');
        }

        //if ($this->request->isPost()) {
        //            $params = $this->request->post("row/a");
        //            //dump($params);exit;
        //            if ($params) {
        //                $retCode = '0000';
        //                if ($retCode == '0000') {
        //                    $this->success('号码查询成功', '/admin/qw/hlyorder/index');
        //                }  else {
        //                    $this->error('号码锁定成功, 失败原因:'.$retMsg.'('.$retCode.')');
        //                }
        //            } else {
        //                $this->error(__('Parameter %s can not be empty', ''));
        //            }
        //
        //        }
        //        return $this->view->fetch();

    }

    public function idencheck(){
        $params = input('param.');
        $id = isset($params['id']) ? $params['id'] :'';
        $this->view->assign("regionList", $this->model->getRegion());
        $vonum = $this->model->getNumFromDb($params);
        $this->view->assign("vonum", $vonum);
        unset($params);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            //dump($params);exit;
            if ($params) {
                $arr_region= explode('-',$params['region']) ;
                $params['region'] = $arr_region[1];

                $xmlData['IdCard'] = $params['idcard'];
                $xmlData['Mobile'] = $params['telnum'];
                $xmlData['Name'] = $params['username'];



                $ret = $this->callService('idenCheck', $xmlData, 'IdenCheck');
                unset($params);
                unset($xmlData);

                $retCode = isset($ret['ReturnCode']) ? $ret['ReturnCode'] : '';
                $retMsg = isset($ret['ReturnMessage']) ? $ret['ReturnMessage'] : '';

                if ($retCode == '0') { 
                    $this->success('身份验证成功', '/admin/qw/profile/index?id='.$id.'&cid='.$vonum['idcard']);
                }  else {
                    $this->error('身份验证成功, 失败原因:'.$retMsg.'('.$retCode.')');
                }


            } else {
                $this->error(__('Parameter %s can not be empty', ''));
            }

        }
        return $this->view->fetch();

    }

    /**
    * 上传照片
    * 
    */
    public function upload(){   
        $params = input('param.');
        $id = isset($params['id']) ? $params['id'] :'';
        $this->view->assign("vo", $params);
        if ($this->request->isPost()) {
            $vo = $this->model->getNumFromDb($params);
            //            $this->view->assign("vo", $vo);
            $params = $this->request->post("row/a");
            $picFileInfo = [];
            if (!empty($_FILES)) {
                $picFileInfo = $this->impPic($vo['telnum']);
                $Content = [];
                $picname = '';
                foreach ($picFileInfo as $k => $v) {
                    //                $Content[$k]['busiSeq'] = $k.$d[telnum];
                    $Content[$k]['phone'] = $vo['telnum'];

                    $Content[$k]['picFile'] = $v['base64'];

                    $Content[$k]['busiSeq'] = $$id.$vo['telnum'];
                    //身份证上传
                    if ($k == 'Z' ) {
                        $Content[$k]['picType'] = $k;
                        $ret = D('Hly','Service')->custPicDiscern($Content[$k]);

                        //-1已存在正面照
                        //                        dump($ret);
                        if ($ret['retCode'] != '0' && $ret['retCode'] != '-1') {
                            $this->error('对不起! 身份证正面验证失败-'.$ret['retCode'] .'(' . $ret['respmsg'].')');
                        }
                    }
                    unset($ret);
                    if ($k == 'F') {
                        $Content[$k]['picType'] = $k;
                        $ret = D('Hly','Service')->custPicDiscern($Content[$k]);
                        //                        dump($ret);
                        if ($ret['retCode'] != '0' && $ret['retCode'] != '-1') {
                            $this->error('对不起! 身份证反面验证失败-'.$ret['retCode'] .'(' . $ret['respmsg'].')');
                        }

                    }
                    unset($ret);

                }
            } else {
                $this->error(__('Parameter %s can not be empty', ''));   
            } 


        }
        return $this->view->fetch();
    }

    public function order(){
        $this->view->assign("regionList", $this->model->getRegion());
        $this->view->assign("vo",  $this->model->getNumFromDb($params));
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            //dump($params);exit;
            if ($params) {
                if ($retCode == '0000') {
                    $this->success('订单提交成功', '/admin/qw/hlyorder/index?'.$params);
                }  else {
                    $this->error('提交失败，失败原因:'.$retMsg.'('.$retCode.')');
                }
            } else {
                $this->error(__('Parameter %s can not be empty', ''));
            }

        }
        return $this->view->fetch();

    }


    public function impPic($accnbr) {

//        move_uploaded_file($_FILES["file"]["tmp_name"],$filename)
        $file_z = request()->file('Z');  
        $file_f = request()->file('F');  
        if(empty($file_z) ) {  
            $this->error('请选择上传身份证正面照片');  
        } 
        if(empty($file_f)) {  
            $this->error('请选择上传身份证反面照片');  
        } 
         
        // 移动到框架应用根目录/public/uploads/ 目录下  
        $info_z = $file_z->move(ROOT_PATH.'public'.DS.'\uploads'); 
        $info_f = $file_f->move(ROOT_PATH.'public'.DS.'\uploads'); 



        $base_info = [];   
        if (!empty($_FILES)) {

            Upload::uploadPicture($info_z,"",true,$rule);
            // 移动到框架应用根目录/public/uploads/ 目录下  
            $info = $file->move(ROOT_PATH.'public'.DS.'upload'); 
            //如果不清楚文件上传的具体键名，可以直接打印$info来查看  
            //获取文件（文件名），$info->getFilename()  ***********不同之处，笔记笔记哦
            //获取文件（日期/文件名），$info->getSaveName()  **********不同之处，笔记笔记哦
            $filename = $info->getSaveName();  //在测试的时候也可以直接打印文件名称来查看 

            $upload->maxSize  = 5242880 ;// 设置附件上传大小 ，不超过5M

            $upload->exts = array('jpg','png','jpeg');
            // 设置附件上传类型
            $upload->rootPath  =  $filepath; // 设置附件上传根目录
            $upload->saveName  =     'time';
            $upload->autoSub   =     false;
            if (!$info=$upload->upload()) {
                $this->error($upload->getError());
            }
            foreach ($info as $k => $v) {
                $v['savepath']=$filepath;
                $filename=$v['savepath'].$v['savename'];
                $fileName = iconv("utf-8", "gb2312", $filename);
                $base_info[$k]['base64'] = $this->base64EncodeImage($fileName);
                $base_info[$k]['filename'] = $fileName;
                $base_info[$k]['picname'] = $v['savename'];
            }
        }
        return $base_info;
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
