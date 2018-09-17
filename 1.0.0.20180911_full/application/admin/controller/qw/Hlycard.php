<?php

namespace app\admin\controller\qw;

use app\common\controller\Backend;

/**
* 表格完整示例
*
* @icon fa fa-table
* @remark 在使用Bootstrap-table中的常用方式,更多使用方式可查看:http://bootstrap-table.wenzhixin.net.cn/zh-cn/
*/
class Hlycard extends Backend
{

    protected $model = null;
    protected $noNeedRight = ['start', 'pause', 'change', 'detail', 'cxselect', 'searchlist'];

    public function _initialize()
    {
        parent::_initialize();
        //        $this->model = model('AdminLog');
        $this->model = new \app\admin\model\qw\Hlyorder;
    }

    /**
    * 查看
    */
    public function index()
    {
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(NULL);
            $total = $this->model
            ->where($where)
            ->order($sort, $order)
            ->count();
            $list = $this->model
            ->where($where)
            ->order($sort, $order)
            ->limit($offset, $limit)
            ->select();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    
     public function getnum(){
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
//                $this->error(__('Parameter %s can not be empty', ''));
            }

        }
//        return $this->view->fetch();

    }
}
