<?php
namespace app\api\job;

/**
* 文件路径： \app\api\job\Hello.php
* 这是一个消费者类，用于处理 helloJobQueue 队列中的任务
*/
use think\queue\Job;
use think\Model;
use think\Db;

class ReceiveOrderQueue {


    /**
    * fire方法是消息队列默认调用的方法
    * @param Job            $job      当前的任务对象
    * @param array|mixed    $data     发布任务时自定义的数据
    */
    public function fire(Job $job,$data){

        // 如有必要,可以根据业务需求和数据库中的最新数据,判断该任务是否仍有必要执行.
        $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        if(!$isJobStillNeedToBeDone){
            $job->delete();
            print("<info Job has been run checkDatabaseToSeeIfJobNeedToBeDone and return false and deleted"."</info>\n");
            return;
        }

        $isJobDone = $this->dealOrderJob($data);

        if ($isJobDone['errcode'] == '0') {
            print("<info Job has been run success and deleted"."</info>\n");
            //如果任务执行成功， 记得删除任务
            $job->delete();
        }else{
            print("<info Job has been run success and deleted"."</info>\n");
            $job->delete();
        }

    }

    /**
    * 根据消息中的数据进行实际的业务处理
    * @param array|mixed    $data     发布任务时自定义的数据
    * @return boolean                 任务执行的结果
    */
    private function dealOrderJob($data) {

        $ret = true;
        print("<info>Hello Job Started.Fired at " . date('Y-m-d H:i:s')."; job Data is: ".var_export($data,true)."</info> \n");

        $info['errcode'] = '0';
        $info['errmsg'] = '';
        // 根据消息中的数据进行实际的业务处理...
        //检验黑白名单
        //        $ret1 = $this->checkIPValid();
        //检查订单商品是否存在
        //        $ret1 = $this->checkGoodsValid();
        //校验订单号是否重复提交
        //        $ret2 = $this->checkOrdernoValid();
        //检查费用是否充足
        //        $ret3 = $this->checkFeeValid();
        //入订单库
        $ret = Db::table('qw_order_init')->insert($data);
        print("<info>Hello Job is Done! ".$ret."</info> \n");
        if (!$ret){
            $info['errcode'] = '9000003';
            $info['errmsg'] = '提交失败';
            return $info;
        }

        //订单提交给供货商
        //$ret3 = $this->postToSupplier();
        //如果提交失败，再直接把订单丢到NotifyOrderQueue队列中




        //        if ($list !== false) {
        return $info;
        //        }  else {
        //            $info['errcode'] = '100001';
        //            $info['errmsg'] = '商品不存在';
        //            return $info;
        //        }
    }

    /**
    * 数组校验、检查、扣费等逻辑在此实现
    *
    * 有些消息在到达消费者时,可能已经不再需要执行了
    * @param array|mixed    $data     发布任务时自定义的数据
    * @return boolean                 任务执行的结果
    */
    private function checkDatabaseToSeeIfJobNeedToBeDone($data){
        return true;
    }

      /**
    * 该方法用于接收任务执行失败的通知，你可以发送邮件给相应的负责人员
    * @param $jobData  string|array|...      //发布任务时传递的 jobData 数据
    */
    public function failed($jobData){
        //    send_mail_to_somebody() ; 
        print("Warning: Job failed after max retries. job data is :".var_export($jobData,true)."\n"); 
    }

}