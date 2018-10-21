<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use think\Queue;
use think\Db;



/**
* 流量认证接口
*/
class Llauth extends LlfxApi
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $model = null;

    public function _initialize()
    {
        $this->model = new \app\api\model\Order;
        parent::_initialize();
    }

    public function index() {
        $this->success('请求成功');
    }

    public function order() {
        $sign = input('sign');
        $token = input('token');

        $orderData = $this->getRaw();
        $orderData =  file_get_contents("php://input") ; 
        if ($orderData == '') {
            $ret = $this->json_fail('9000001', '提交内容为空');
            return $ret;
        } 
        $orderData = json_decode($orderData); 
        if ($orderData == '') {
            $ret = $this->json_fail('9000001', '非json格式数据');
            return $ret;
        }
        
        $retC = object_array($orderData);
        unset($orderData);

        //        $ret = $this->checkDataValid($orderData);

        $data = $retC['payload']['data'];
        $payload = $retC['header'];
        

        $orderId = date('YmdHis').rand(1000,9999);
        
        $data_save['user'] = $data['user'];
        $data_save['mobile'] = $data['mobile'];
        $data_save['fluxnum'] = $data['fluxnum'];
        $data_save['orderno'] = $data['orderno'];
        $data_save['backurl'] = $data['backurl'];
        
        unset($data);
                

        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName  = 'app\api\job\ReceiveOrderQueue';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        //      php think queue:work --queue receiveOrderQueue --daemon
        $jobQueueName        = "receiveOrderQueue";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        //	$orderData             = [ 'ts' => time(), 'bizId' => uniqid()  ] ;
        // 4.将该任务推送到消息队列，等待对应的消费者去执行         
         
        $isPushed = Queue::push( $jobHandlerClassName , $data_save , $jobQueueName );
        unset($data_save);
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $isPushed !== false ){
            $info['errcode'] = '0';
            $info['errmsg'] = '提交成功';
            $data['orderid'] = $orderId;

        }else{
            $info['errcode'] = '9000001';
            $info['errmsg'] = '提交失败';
            $data['orderid'] = '';
        }
        $p['header'] = $info;
        $p['payload']['data'] = $data;
        return json($p);


    }

    protected function json_fail($errcode = '9000001', $errmsg='提交失败'){
        $info['errcode'] = $errcode;
        $info['errmsg'] = $errmsg;
        $data['orderid'] = '';
        $p['header'] = $info;
        $p['payload']['data'] = $data;
        return json($p);
    }



    /**
    * 来自上游的回调
    *
    */
    public function notify() {
        $sign = input('sign');
        $token = input('token');

        $orderData = $this->getRaw();
        $retC = json_decode($orderData);
        $retC = object_array($retC);
        $payload = $retC['payload']['data'];
        $payload = $retC['header'];

        $orderId = date('YmdHis').rand(1000,9999);

        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName  = 'app\api\job\NotifyOrderQueue';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName        = "notifyOrderQueue";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        //		$orderData             = [ 'ts' => time(), 'bizId' => uniqid()  ] ;
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        $isPushed = Queue::push( $jobHandlerClassName , $orderData , $jobQueueName );
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $isPushed !== false ){
            $info['errcode'] = '0';
            $info['errmsg'] = '提交成功';
            $data['orderid'] = $orderId;

        }else{
            $info['errcode'] = '9000001';
            $info['errmsg'] = '提交失败';
            $data['orderid'] = '';
        }
        $p['header'] = $info;
        $p['payload']['data'] = $data;
        return json($p);

    }

}
