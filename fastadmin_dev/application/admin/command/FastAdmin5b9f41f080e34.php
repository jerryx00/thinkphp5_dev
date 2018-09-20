<?php

namespace app\admin\command;
use think\Db;
use think\Session;


/**
*
*
* @icon fa fa-circle-o
*/
class FastAdmin5b9f41f080e34 extends HlybandBase
{

	/**
	* Hlyband模型对象
	* @var \app\admin\model\qw\Hlyband
	*/
	protected $model = null;
	protected $table = 'qw_hlyband';

	public function _initialize()
	{
		parent::_initialize();
		$this->model = new \app\admin\model\qw\Hlyband;
		$this->view->assign("regionList", $this->model->getRegion());
		$this->view->assign("regionList1", $this->model->getRegionList());
		$this->view->assign("typeList", $this->model->getOfferIdList());



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
					$this->success($retMsg, '/admin/qw/cxselect/'.$action.'?bid='.$bid, 1);
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


	public function address() {
		//设置过滤方法
		$this->request->filter(['strip_tags']);
		$bid = $this->request->param('bid');

		if ($bid !='') {
			//商品信息
			$list = Db::table('qw_hlyofferbb')->field('offer_id,offer_name')->where(['status'=>1])->select();
			$this->view->assign("list", $list);


			$this->view->assign("bid", $bid);
		} else {
			if (Session::get('admin.region') == '' || Session::get('admin.region') == '')
				//如果输入小区关键字，则跳转到第一步
				$this->redirect('/admin/qw/hlyband/idenCheckindex');
		}

		//   $province = $this->request->get('province');
		//        $city = $this->request->get('city');
		$params = input('param.');
		$row = $this->request->post("row/a");
		//
		if ($this->request->isAjax()) {


			//第一步信息，从数据库中获取
			$idenInfo = Db::table($this->table)->field('accnbr,custname,region,icno,type,comments')->where(['status'=>'1','id'=>$bid])->find();

			//重新获取小区属性
			$p['AddressId'] = $params['city'];
			$p['Region'] = '南京';
			$p['Type'] = '2';
			$p['Address'] = \think\Session::get('admin.an');
			$citylist = $this->getAddr('addressSearch', $p);
			//获取小区的宽带属性
			$properties_of_area = [];
			foreach ($citylist as $k => $v) {
				if ($v['AddressId'] == $params['area']){
					unset($v['id']);
					unset($v['value']);
					unset($v['name']);
					$properties_of_area = $v;
					break;
				}
			}
			unset($citylist);
			unset($p);
			unset($v);
			$order_para = $properties_of_area;
			$order_para['AreaId'] = $properties_of_area['AreaCode'];
			$order_para['AddressName'] = $properties_of_area['Address'];
			unset($order_para['AreaCode']);
			unset($order_para['Address']);
			unset($properties_of_area);
			if ($params) {
				$booking_id = date('YmdHis').$bid;
				$order_para['BookingId'] = $booking_id;
				$order_para['Region'] = Session::get('admin.region');
				$order_para['CustName'] = $idenInfo['custname'];
				$order_para['AccNbr'] = $idenInfo['accnbr'];
				$order_para['IcNo'] = $idenInfo['icno'];

				$order_para['CountyName'] = Session::get('admin.an');
				$order_para['Type'] = $idenInfo['type'] == '0' ? '新装' : '续费' ;

				$order_para['GoodCode'] = $row['GoodCode'];
				$order_para['Remark'] = $row['Remark'];
				$order_para['ContactName'] = $row['ContactName'];


				$url = 'create';
				$ret = [];
				$ret = $this->callService($url, $order_para);
				$retCode = isset($ret['Response']['Content']['resp__code']) ? $ret['Response']['Content']['resp__code'] :'';
				$retMsg = isset($ret['Response']['Content']['resp__msg']) ? $ret['Response']['Content']['resp__msg'] :'';
				if ($retCode == '0000') {
					$this->success('订单提交成功', '/admin/qw/hlyband/add?'.$params);
				}  else {
					$this->error('提交失败，失败原因:'.$retMsg.'('.$retCode.')');
				}

				//                $this->success('地址提交成功', '/admin/qw/hlyband/add?'.$params);
				//                redirect('/admin/qw/hlyband/add?',$params);
				//               $this->redirect('/admin/qw/hlyband/add?'.$params, $params);
			} else {
				$this->error('小区信息不能为空');
			}
		}  else {
			return $this->view->fetch();
		}



	}
	public function mmsdealindex() {
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
			//dump($params);exit;
			if ($params) {
				$url = 'mmsdeal';

				$ret = [];
				$ret = $this->callService($url, $params);

			}
			$this->error(__('Parameter %s can not be empty', ''));
		}


		return $this->view->fetch();
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
	* 续费宽带
	*
	*/
	public function renew() {
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
			//             $params['AddressId']='99';
			//dump($params);exit;
			if ($params) {
				$lservice = \think\Loader::model('Band','service');
				$ret = [];


				$d['Region']=$params['Region'];
				$d['Address']=$params['Address'];
				$d['AddressId']='99';
				$d['Type']='3';
				$ret = $this->callService('addressSearch', $d);
				$retCode = $ret['Response']['Content']['retCode'];
				$AddressName = isset($ret['Response']['Content']['Address']) ? $ret['Response']['Content']['Address'] : $params['Address'];
				unset($d);

				$d['mobile'] = $params['AccNbr'];
				$ret = $this->callService('detail', $d);

				$retCode = $ret['Response']['Content']['retCode'];
				$AddressName = isset($ret['Response']['Content']['Address']) ? $ret['Response']['Content']['Address'] : $params['Address'];
				$AddressId = isset($ret['Response']['Content']['AddressId'])? $ret['Response']['Content']['AddressId'] : '';
				$AreaId = isset($ret['Response']['Content']['AreaId']) ? $ret['Response']['Content']['AreaId'] : '';
				$AreaName = isset($ret['Response']['Content']['AreaName']) ? $ret['Response']['Content']['AreaName'] : '';
				$NetworkAccess = isset($ret['Response']['Content']['NetworkType']) ? $ret['Response']['Content']['NetworkType'] : '';
				$UptownId = isset($ret['Response']['Content']['UptownId']) ? $ret['Response']['Content']['UptownId'] : '';
				//              $CountyName = isset($ret['Response']['Content']['CountyName']) ? $ret['Response']['Content']['CountyName'] : $params['CountyName'];


				$params['AddressId'] = $AddressId;
				$params['Address'] = $AddressName;
				$params['AreaId'] = $AreaId;
				$params['AreaName'] = $AreaName;
				$params['NetworkAccess'] = $NetworkAccess;
				$params['UptownId'] = $UptownId;
				//                $params['CountyName'] = $CountyName;

				//                unset($ret);
				$retCre = $this->callService('create', $params);
				$retCode = $retCre['Response']['Content']['resp__code'];
				if ($retCode != '0000') {
					$this->error($retCre['Response']['Content']['resp__msg'].'('.$retCode.')');
				}

			}
			$this->error(__('Parameter %s can not be empty', ''));
		}


		return $this->view->fetch();
	}


}
