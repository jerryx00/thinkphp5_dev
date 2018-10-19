<?php
/**
* 文件路径： \application\index\controller\JobTest.php
* 该控制器的业务代码中借助了thinkphp-queue 库，将一个消息推送到消息队列
*/
namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Token;

use think\Exception;
use think\Queue;

//http://localhost/index.php/job_test/actionWithHelloJob.html

class JobTest extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';
    /**
    * 一个使用了队列的 action
    */
    //php think queue:listen --queue helloJobQueue
    //http://localhost/demo/job_test/actionWithHelloJob.html
    public function actionWithHelloJob(){

        // 1.当前任务将由哪个类来负责处理。 
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName  = 'app\demo\job\Hello'; 
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName        = "helloJobQueue"; 
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        $jobData             = [ 'ts' => time(), 'bizId' => uniqid()  ] ;
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        $isPushed = Queue::push( $jobHandlerClassName , $jobData , $jobQueueName );    
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $isPushed !== false ){  
            echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
        }else{
            echo 'Oops, something went wrong.';
        }
    }

    //php think queue:listen --queue multiTaskJobQueue
    //http://localhost/demo/job_test/actionWithMultiTask.html
    public function actionWithMultiTask(){

        $taskType = input('taskType', 'taskA')   ;
        switch ($taskType) {
            case 'taskA':
                $jobHandlerClassName  = 'application\index\job\MultiTask@taskA';
                $jobDataArr = ['ts'    => date("Y-m-d H:i:s",time())];
                $jobQueueName = "multiTaskJobQueue";    
                break;
            case 'taskB':
                $jobHandlerClassName  = 'application\index\job\MultiTask@taskB';
                $jobDataArr = ['ts'    => date("Y-m-d H:i:s",time())];
                $jobQueueName = "multiTaskJobQueue";        
                break;
            default:
                break;
        }

        $isPushed = Queue::push($jobHandlerClassName, $jobDataArr, $jobQueueName);
        
        if ($isPushed !== false) {
            echo("the $taskType of MultiTask Job has been Pushed to ".$jobQueueName ."<br>");
        }else{
            throw new Exception("push a new $taskType of MultiTask Job Failed!");
        }
    }

    public function index(){

        echo "index";
    }
}