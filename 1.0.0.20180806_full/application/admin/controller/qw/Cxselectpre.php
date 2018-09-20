<?php

namespace app\admin\controller\qw;

use app\common\controller\Backend;
use  app\admin\controller\qw\HlybandBase;
use Think\Db;
use Think\Session;


/**
* 多级联动
*
* @icon fa fa-table
* @remark FastAdmin使用了jQuery-cxselect实现多级联动,更多文档请参考https://github.com/karsonzhang/cxSelect
*/
class Cxselectpre extends HlybandBase
{

	protected $model = null;
	protected $table = 'qw_hlybandpre';
	protected $commitCnt = 0;

	public function _initialize()
	{
		parent::_initialize();
	}

	public function  index() {
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
				//如果没输入小区关键字，则跳转到第一步
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
			$p['Region'] = Session::get('admin.region');;
			$p['Type'] = '2';
			$p['Address'] = \think\Session::get('admin.an');
			$citylist = $this->getAddr('addressSearch', $p);
			//获取小区的宽带属性
			$properties_of_area = [];
			foreach ($citylist as $k => $v) {
				if ($v['value'] == $params['area']){
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
			$order_para['NetworkAccess'] = $properties_of_area['NetworkType'];
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

				$order_para['CountyName'] = Session::get('admin.region');

				if ($idenInfo['type'] == '0') {
					$order_para['Type'] = '新装';
				} else if ($idenInfo['type'] == '1') {
					$order_para['Type'] = '续费';
				} else {
					$order_para['Type'] = $idenInfo['type'] ;
				}

				$order_para['GoodCode'] = $row['GoodCode'];
				$order_para['Remark'] = $row['Remark'];
				$order_para['ContactName'] = $row['ContactName'];
				$order_para['ContactPhone'] = $row['ContactPhone'];
				//==========校验商品 begin===================
				$order_check['region'] = Session::get('admin.region');
				$order_check['telnum'] = $idenInfo['accnbr'];
				$order_check['goodCode'] = $row['GoodCode'];
				$order_check['orderType'] = $idenInfo['type'] == '0' ? '1' : '2' ;

				$ret = $this->callService('goodcheck', $order_check);
				unset($order_check);

				$retCode = isset($ret['Response']['Content']['retCode']) ? $ret['Response']['Content']['retCode'] :'';
				$retMsg = isset($ret['Response']['Content']['retMsg']) ? $ret['Response']['Content']['retMsg'] :'';

				if ($retCode != '0') {
					//$this->error('商品校验失败:'.$retMsg.'('.$retCode.')');
				}
				unset($ret);
				//==========校验商品 end===================

				//==========多次提交，再次身份验证begin===================
				if ($this->commitCnt >1) {
					$order_check['type'] = Session::get('admin.region');
					$order_check['telnum'] = $idenInfo['accnbr'];
					$order_check['icNo'] = $row['GoodCode'];
					$order_check['name'] = $idenInfo['type'] == '0' ? '1' : '2' ;

					$ret = $this->callService('idenCheck', $order_check);
					unset($order_check);

					$retCode = isset($ret['Response']['Content']['retCode']) ? $ret['Response']['Content']['retCode'] :'';
					$retMsg = isset($ret['Response']['Content']['retMsg']) ? $ret['Response']['Content']['retMsg'] :'';

					if ($retCode != '0') {
						//$this->error($retMsg.'('.$retCode.')');
					}
				}
				//==========多次提交，再次身份验证end===================
                //===================================创建订单 begin=====================
				$url = 'yycreate';
				$ret = [];
				$ret = $this->callService($url, $order_para);
				$this->commitCnt = $this->commitCnt + 1;

				$retCode = isset($ret['Response']['Content']['resp__code']) ? $ret['Response']['Content']['resp__code'] :'';
				$retMsg = isset($ret['Response']['Content']['resp__msg']) ? $ret['Response']['Content']['resp__msg'] :'';
				$order_id = isset($ret['Response']['Content']['order__id']) ? $ret['Response']['Content']['order__id'] :'';
                 //===================================创建订单 end=====================
				//===================================保存数据 begin=====================
				$f_band['id'] = $bid;

				$d_band = $order_para;
				$d_band['orderid'] = $order_id;
				$d_band['resp_code'] = $retCode;
				$d_band['resp_msg'] = $retMsg;
				$d_band['areaid'] = $order_para['AreaId'];

				$d_band_new = [];
				foreach ($d_band as $k => $v) {
					$d_band_new[strtolower($k)] = $v;
				}

				$f = Db::table($this->table)->where($f_band)->update($d_band_new);
                //===================================保存数据 end=====================

				unset($order_para);
				unset($f_band);
				unset($d_band);
				unset($d_band_new);

				unset($ret);
				unset($list);
				if ($retCode == '0000') {
					$this->success();
					//					$this->success('订单提交成功', '/admin/qw/hlyband/add?'.$params);
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

	public function renew() {
		//设置过滤方法
		$this->request->filter(['strip_tags']);
		$bid = $this->request->param('bid');

		$list = Db::table('qw_hlyofferbb')->field('offer_id,offer_name')->where(['status'=>1,'btype'=>1])->select();
		$this->view->assign("list", $list);
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
			//dump($params);exit;
			if ($params) {
				//第一步信息，从数据库中获取
				$idenInfo = Db::table($this->table)->field('accnbr,custname,region,icno,type,comments')->where(['status'=>'1','id'=>$bid])->find();

				$booking_id = date('YmdHis').$bid;
				$order_para['BookingId'] = $booking_id;
				//  $order_para['Region'] = Session::get('admin.region');
				$order_para['CustName'] = $idenInfo['custname'];
				$order_para['AccNbr'] = $idenInfo['accnbr'];
				$order_para['IcNo'] = $idenInfo['icno'];


				$url = 'detail';
				$d_detail['mobile'] = $idenInfo['accnbr'];
				$ret = [];
				$ret = $this->callService($url, $d_detail);
				$retCode = isset($ret['Response']['Content']['retCode']) ? $ret['Response']['Content']['retCode'] :'';
				$retMsg = isset($ret['Response']['Content']['retMsg']) ? $ret['Response']['Content']['retMsg'] :'';

				$content = $ret['Response']['Content'];
				$order_para['Region'] = $content['Region'];
				$order_para['CountyName'] = $content['CountyName'];
				//
				//				if ($retCode != '0000') {
				//
				//					$this->success('订单提交成功', '/admin/qw/hlyband/add?'.$params);
				//				}  else {
				//					$this->error('提交失败，失败原因:'.$retMsg.'('.$retCode.')');
				//				}


				//            $order_para['CountyName'] = Session::get('admin.an');
				$order_para['Type'] = $idenInfo['type'] == '0' ? '新装' : '续费' ;

				$order_para['GoodCode'] = $params['GoodCode'];

				//==========校验商品====================
				$order_check['region'] = Session::get('admin.region');
				$order_check['telnum'] = $idenInfo['accnbr'];
				$order_check['goodCode'] = $row['GoodCode'];
				$order_check['orderType'] = $idenInfo['type'] == '0' ? '1' : '2' ;

				$ret = $this->goodcheck($order_check) ;
				unset($order_check);

				$retCode = isset($ret['Response']['Content']['retCode']) ? $ret['Response']['Content']['retCode'] :'';
				$retMsg = isset($ret['Response']['Content']['retCode']) ? $ret['Response']['Content']['retCode'] :'';
				if ($retCode != '0000') {
					//$this->error($retMsg.'('.$retCode.')');
				}
				unset($ret);
				//==============================

				$url = 'create';
				$ret = [];
				$ret = $this->callService($url, $order_para);
				$retCode = isset($ret['Response']['Content']['resp__code']) ? $ret['Response']['Content']['resp__code'] :'';
				$retMsg = isset($ret['Response']['Content']['resp__msg']) ? $ret['Response']['Content']['resp__msg'] :'';
				if ($retCode == '0000') {
					$this->success();
				}  else {
					$this->error('提交失败，失败原因:'.$retMsg.'('.$retCode.')');
				}
			}
		}

		return $this->view->fetch();
	}

	public function goodcheck($d) {
		$url = 'goodcheck';
		$ret = [];
		$ret = $this->callService($url, $d);
		return $ret;
	}


}
