<?php
/**
* 文件路径： \application\index\job\MultiTask.php
* 这是一个消费者类，用于处理 multiTaskJobQueue 队列中的任务
*/
namespace app\index\job;

use think\queue\Job;
use think\Model;
use think\Db;

class MultiTask {

    public function taskA(Job $job,$data){
        print("000000 .task(s) \n");
        print("Begin to run TaskA \n");

        $isJobDone = $this->_doTaskA($data);

        if ($isJobDone) {
            $job->delete();
            print("Info: TaskA of Job MultiTask has been done and deleted"."\n");
        }else{
            if ($job->attempts() > 3) {
                $job->delete();
            }
        }
    }

    public function taskB(Job $job,$data){
        print("Begin to run TaskB \n");

        $isJobDone = $this->_doTaskB($data);

        if ($isJobDone) {
            $job->delete();
            print("Info: TaskB of Job MultiTask has been done and deleted"."\n");
        }else{
            if ($job->attempts() > 2) {
                print("Info: TaskB of Job MultiTask has been deleted deleted from attempts > 2"."\n");
                $job->delete();
            }
        }
    }

    private function _doTaskA($data) {
        print("Info: doing TaskA of Job MultiTask "."\n");
        //        $data_upd['iftype'] = ['exp', 'iftype+1'];
        $data_upd['iftype'] = 'x';
        $filter['id'] = ['eq','1'];
        $rst = Db::table('qw_log')->where($filter)->update($data_upd);
        return $ret        ;
    }

    private function _doTaskB($data) {
        print("Info: doing TaskB of Job MultiTask "."\n");
        return true;
    }
}