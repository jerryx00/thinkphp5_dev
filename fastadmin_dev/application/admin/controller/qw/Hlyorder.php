<?php

namespace app\admin\controller\qw;

use app\common\controller\Backend; 
use Think\Db;
use Think\Request;
use app\admin\model\Admin;
use app\admin\model\qw\Hlylockednum as Hlylockednum;


/**
*
*
* @icon fa fa-circle-o
*/
class Hlyorder extends HlycardBase
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
    public function add(){
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
                $params['Fitmod'] = $lservice->getFitmod($params);                
                unset($params['filter']);
                unset($params['mobile']);

                session('hk', $params);
                $this->success();

            } else {
                $this->error(__('Parameter %s can not be empty', ''));
            }

        }
        return $this->view->fetch();

    }

    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            $params = session('hk');
            //            session('hk', null);

            //        *  调用service
            $lservice = \think\Loader::model('Hk','service');              
            $ret = $this->callService('getNum', $params);
            $list = $this->model->getNum($ret, $params);

            //过滤
            //            $hkList = $lservice->filter($list, $params);

            $total = count($list); 


            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list, "params" =>$params);


            return json($result);
        }
        return $this->view->fetch();
    }

    /**
    * 编辑
    */
    public function edit($ids = NULL) 
    {
        $this->view->assign("regionList", $this->model->getRegion());
        $this->view->assign("offerList", $this->model->getOffer());
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
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

        $xmldata['Region'] = $params['region'];
        $xmldata['Telnum'] = $params['telnum']; 
        $xmldata['Type'] = '2';
        $OrgId = isset($params['OrgId'])?$params['OrgId']:'';
        if ($OrgId != '') {
            $xmldata['OrgId'] = $OrgId;  
        } 
        $ret['ReturnCode'] = '0';       
        //        $ret = $this->callService('locknum', $xmldata);
        unset($xmldata);


        $retCode = isset($ret['ReturnCode']) ? $ret['ReturnCode'] : '';
        $retMsg = isset($ret['ReturnMessage']) ? $ret['ReturnMessage'] : 'ok';

        $filter['telnum'] = $params['telnum'];
        $vo = Db::table('qw_hlylockednum')->where($filter)->find();
        $params['returncode'] = $retCode; 
        $params['returnmessage'] = $retMsg;
        $params['locked_at'] = time();
        $params['uid'] = (int)$this->auth->id;        

        if ($vo) {    
            $ret = $this->model->updLockNum($params, $filter);;
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
        // $this->view->assign("regionList", $this->model->getRegion());
        $vonum = $this->model->getLockNum($params);
        $this->view->assign("vonum", $vonum);
        unset($params);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            //dump($params);exit;
            if ($params) {
                //                $arr_region= explode('-',$params['region']) ;
                //                $params['region'] = $arr_region[1];

                $xmlData['IdCard'] = $params['idcard'];
                $xmlData['Mobile'] = $params['telnum'];
                $xmlData['Name'] = $params['username'];


                $ret['ReturnCode'] = '0000';
                //                $ret = $this->callService('idenCheck', $xmlData, 'IdenCheck');

                unset($xmlData);

                $retCode = isset($ret['ReturnCode']) ? $ret['ReturnCode'] : '';
                $retMsg = isset($ret['ReturnMessage']) ? $ret['ReturnMessage'] : '';

                $params['returncode'] = $retCode;
                $params['returnmessage'] = $retMsg;

                $this->model->updLockNum($params, ['id'=> $id]);
                unset($params);
                if ($retCode == '0000') { 
                    $this->success('身份验证成功', '/admin/qw/profile/index?id='.$id);
                }  else {
                    $this->error('身份验证成功, 失败原因:'.$retMsg.'('.$retCode.')');
                }


            } else {
                $this->error(__('Parameter %s can not be empty', ''));
            }

        }
        return $this->view->fetch();

    }


    public function order(){
        $params = input('param.');
        $id = isset($params['id']) ? $params['id'] :'';
        // $this->view->assign("regionList", $this->model->getRegion());

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $vonum = $this->model->getLockNum($params);
                $this->view->assign("vo",  $vonum);
                
        
                
            } else {
                $this->error(__('Parameter %s can not be empty', ''));
            }

        }
        return $this->view->fetch();

    }

}