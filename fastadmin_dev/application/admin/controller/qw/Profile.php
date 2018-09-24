<?php

namespace app\admin\controller\qw;

use app\admin\model\Admin;
use app\common\controller\Backend;
use app\admin\model\qw\Hlylockednum;
use fast\Random;
use think\Session;

/**
* 个人配置
*
* @icon fa fa-user
*/
class Profile extends Backend
{

    /**
    * 查看
    */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        $params = input('param.');
        $id = isset($params['id']) ? $params['id'] :'';
        $hlylockednum_model = new Hlylockednum();
        $vo = $hlylockednum_model->getLockNum($params);
        $this->view->assign("vo", $vo);     

        if ($this->request->isAjax())
        {
            $model = model('hlylockednum');
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $model
            ->where($where)
            ->where('id', $id)
            ->order($sort, $order)
            ->count();

            $list = $model
            ->where($where)
            ->where('admin_id', $this->auth->id)
            ->order($sort, $order)
            ->limit($offset, $limit)
            ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
    * 更新个人信息
    */
    public function indexf()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
        }else {
            $params = input('param.');    
        }

        $id = isset($params['id']) ? $params['id'] :'';
        $p = isset($params['p']) ? $params['p'] :'';
        $hlylockednum_model = new Hlylockednum();
        $vo = $hlylockednum_model->getLockNum($params);
        $this->view->assign("vo", $vo);
        if ($p != ''){
            dump($vo);   exit;
        }
        if (strpos($vo['zmsg'], '已存在') !== false && $vo['mcode'] == '-1') {
            $vo['zcode'] = '0';  
        }
        if ($vo['zcode'] != '0'){
            $this->error("您的身份证正面验证未通过".$vo['zcode'].$vo['zmsg']);
        }
        if ($vo['zpicurl'] == '' ){
            $this->error("您的身份证正面未上传".$vo['zcode'].$vo['zmsg']);
        }    
        if ($this->request->isAjax())
        {
            $model = model('hlylockednum');
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $model
            ->where($where)
            ->where('id', $id)
            ->order($sort, $order)
            ->count();

            $list = $model
            ->where($where)
            ->where('admin_id', $this->auth->id)
            ->order($sort, $order)
            ->limit($offset, $limit)
            ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        
        

        return $this->view->fetch();
    }
    public function indexm()
    {
        return $this->view->fetch();
    }


}
