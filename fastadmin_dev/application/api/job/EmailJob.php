<?php
namespace app\index\job;

use think\queue\Job;
class EmailJob{
	public function fire(Job $job, $data){
		if($this->send_mail($data['config'],$data['toemail'],$data['name'],
		$data['subject'],$data['detail'])){
			$job->delete();        //发送完成出队
		}
		if ($job->attempts() > 2) {
			$job->delete();       //发送两次失败后出队
		}
	}
	public function failed($data){//发送失败写入系统日志
		$data['name']="邮件发送失败";
		$data['level']=1;
		$data['detail']="发送至邮箱$data[toemail]的主题$data[subject]邮件失败";
		$m=M('Log')->addOne($data);
	}
	private function send_mail($config,$tomail, $name, $subject = '', $body = '', $attachment = null) {//发送邮件函数
		$mail = new \PHPMailer();           //实例化PHPMailer对象
		$mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
		$mail->IsSMTP();                    // 设定使用SMTP服务
		$mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
		$mail->SMTPAuth = true;             // 启用 SMTP 验证功能
		$mail->SMTPSecure = 'ssl';          // 使用安全协议
		$mail->Host = $config['email_type']; // SMTP 服务器
		$mail->Port = $config['email_port'];                  // SMTP服务器的端口号
		$mail->Username = $config['email_username'];    // SMTP服务器用户名
		$mail->Password = $config['email_password'];     // SMTP服务器密码
		$mail->SetFrom($config['email_username'], $config['email_name']);
		$replyEmail = '';                   //留空则为发件人EMAIL
		$replyName = '';                    //回复名称（留空则为发件人名称）
		$mail->AddReplyTo($replyEmail, $replyName);
		$mail->Subject = $subject;
		$mail->MsgHTML($body);
		$mail->AddAddress($tomail, $name);
		if (is_array($attachment)) { // 添加附件
			foreach ($attachment as $file) {
				is_file($file) && $mail->AddAttachment($file);
			}
		}
		return ($mail->Send() ? true : $mail->ErrorInfo);
	}


	private function send($toemail,$name,$subject,$detail){
		//ClassName必须使用命名空间路径，同时不加@自动调用fire方法，加@可调用其他方法
		$emailJobClassName  = 'app\admin\job\EmailJob';
		$jobQueueName = "emailJobQueue";
		$jobData['config']=$this->EMAIL['value'];
		$jobData['toemail']=$toemail;
		$jobData['name']=$name;
		$jobData['subject']=$subject;
		$jobData['detail']=htmlspecialchars_decode($detail);
		$isPushed =  Queue::push( $emailJobClassName , $jobData , $jobQueueName);
	}

}
