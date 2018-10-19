<?php
namespace app\api\job;

/**
* 文件路径： \app\demo\job\Hello.php
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
		if(!isJobStillNeedToBeDone){
			$job->delete();
			print("<info Job has been checked fail and deleted"."</info>\n");
			return;
		}

		$isJobDone = $this->dealOrderJob($data);

		if ($isJobDone['errcode'] == '0') {

			//如果任务执行成功， 记得删除任务
			$job->delete();

		}else{

			//			if ($job->attempts() > 3) {
			//通过这个方法可以检查这个任务已经重试了几次了

			//				$job->delete();
			// 也可以重新发布这个任务
			//print("<info>Hello Job will be availabe again after 2s."."</info>\n");
			//$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
			//			}
			$job->delete();
		}

	}

	/**
	* 根据消息中的数据进行实际的业务处理
	* @param array|mixed    $data     发布任务时自定义的数据
	* @return boolean                 任务执行的结果
	*/
	private function dealOrderJob($data) {

		print("<info>Hello Job Started. job Data is: ".var_export($data,true)."</info> \n");
		print("<info>Hello Job is Fired at " . date('Y-m-d H:i:s') ."</info> \n");
		print("<info>Hello Job is Done!"."</info> \n");

		$info['errcode'] = '0';
		$info['errmsg'] = '';
		// 根据消息中的数据进行实际的业务处理...
		$list = Db::table('qw_goods_yt')->where(['status'=>1])->order('created_at')->select();
		if ($list !== false) {
			return $info;
		}  else {
			$info['errcode'] = '100001';
			$info['errmsg'] = '商品不存在';
			return $info;
		}
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
	* Log a failed job into storage.
	* @param  \Think\Queue\Job $job
	* @return array
	*/
	protected function logFailedJob(Job $job)
	{
		//将原来的 queue.failed' 修改为 'queue_failed' 才可以触发任务失败回调
		if (Hook::listen('queue_failed', $job, null, true)) {
			$job->delete();
			$job->failed();
		}

		return ['job' => $job, 'failed' => true];
	}

}