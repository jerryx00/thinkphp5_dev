<?php

namespace app\admin\controller\qw;
use think\Db;
use think\Session;


/**
*
*
* @icon fa fa-circle-o
*/
class Hlybandpre extends Hlyband
{

	/**
	* Hlyband模型对象
	* @var \app\admin\model\qw\Hlyband
	*/
	protected $model = null;
	protected $table = 'qw_hlybandpre';

	public function _initialize()
	{
		parent::_initialize();
		$this->model = new \app\admin\model\qw\Hlybandpre;
		$this->view->assign("regionList", $this->model->getRegion());
		$this->view->assign("regionList1", $this->model->getRegionList());
		$this->view->assign("typeList", $this->model->getOfferIdList());



	}

	/**
	* 新装宽带
	*
	*/
	public function add() {
		$params = input('param.');
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
			if ($params) {
				$url = 'create';
				$ret = [];
				$ret = $this->callService($url, $params);

			} else {
				$this->error(__('Parameter %s can not be empty', ''));
			}
		} else {
			if (!isset($params['bid'])) {
				$this->redirect(url('idenCheckindex'));
			}
			$filter['id'] = $params['bid'];
			$vo = Db::table($this->table)->where($filter)->find();
			$this->view->assign("vo", $vo);
		}


		return $this->view->fetch();
	}

	/**
	* 身份校验
	* 呈现和提交 都是下面逻辑，
	* 通过post区分
	*
	*/
	public function idencheckindex() {
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
			//          $p = $this->request->param('param');
			//dump($params);exit;
			if ($params) {

				$url = 'idenCheck';

				$ret = [];
				$areaname = $params['address'];
				unset($params['address']);
				$ret = $this->callService($url, $params);

				$retCode = isset($ret['Response']['Content']['retCode']) ? $ret['Response']['Content']['retCode'] : '';
				$retMsg = isset($ret['Response']['Content']['retMsg']) ? $ret['Response']['Content']['retMsg'] : '';

				$bid = '0';
				Session::set('admin.an',$areaname);

				$data['created_at'] = time();
				$data['custname'] = $params['name'];
				$data['accnbr'] = $params['telnum'];
				$data['comments'] = $areaname;
				$data['icno'] = $params['icNo'];
				$data['type'] = $params['type'];

				$filter['custname'] = $data['custname'];
				$filter['accnbr'] = $data['accnbr'];
				$filter['icno'] = $data['icno'];
				$filter['type'] = $data['type'];
				$vo = Db::table($this->table)->where($filter)->find();
				if (!$vo) {
					$bid = Db::table($this->table)->insertGetId($data);
				}  else {
					$f = Db::table($this->table)->where($filter)->update(['updated_at' => $data['created_at']]);
					$bid = $vo['id'];
					unset($vo);
				}

				unset($data);
				unset($filter);
				unset($vo);

				$action = 'index';
				//新增逻辑
				if ($params['type'] == '1'){
					//					$action = 'renew';
				}

				if ($retCode == '0') {

					//                    $getdata = http_build_query($params);
					$this->success($retMsg, '/admin/qw/cxselectpre/'.$action.'?bid='.$bid, 1);
					//                    $this->success($retMsg, '/admin/qw/hlyband/address?bid='.$bid, 1);
				} else {
					if ($retCode == '') {
						$retMsg = '请检查网络是否正常';
					}
					$this->error($retMsg.'('.$retCode.')');
				}


			} else {
				$this->error(__('Parameter %s can not be empty', ''));
			}

		}

		return $this->view->fetch();
	}



}
