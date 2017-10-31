<?php

namespace jards\eventsapi;

require_once __DIR__.'/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Handler, which sends a mail for every event
 *
 */
class MailOnEventListener implements EventListener{
	
	/**
	 * Send a mail using PHPMailer
	 * @param string $to mail address to send to 
	 * @param string $subject the subject of the mail
	 * @param string $message the body of the mail
	 */
	public function sendmail($to, $subject, $message){
		$config = parse_ini_file(__DIR__.'/../configs/mail.cnf');
	
		$mail=new PHPMailer();
		$mail->CharSet = 'UTF-8';
	
		$mail->IsSMTP();
		$mail->Host       = $config['Host'];
	
		$mail->SMTPSecure = $config['SMTPSecure'];
		$mail->Port       = intval($config['Port']);
		$mail->SMTPDebug  = 0;
		$mail->SMTPAuth   = true;
	
		if($config['verify_peer']==false){
			$mail->SMTPOptions = array(
					'ssl' => array(
							'verify_peer' => false
					)
			);
		}
	
		$mail->Username   = $config['Username'];
		$mail->Password   = $config['Password'];
	
		$mail->SetFrom($config['Username'], 'jardsserver');
		$mail->Subject    = $subject;
		$mail->MsgHTML($message);
	
		$mail->AddAddress($to, $to);
	
		$mail->send();
	}
	
	
	/**
	 * 
	 * @param unknown $event
	 */
	public function handleEvent($event){
		$this->sendmail('carstenkarbach@gmx.de', 'New event received', json_encode($event));
	}
}

?>