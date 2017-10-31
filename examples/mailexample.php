<?php
use PHPMailer\PHPMailer\PHPMailer;
require_once __DIR__.'/../vendor/autoload.php';

/**
 * Test your mail configuration with this script 
 */

function sendmail($to, $subject, $message){
	$config = parse_ini_file(__DIR__.'/../configs/mail.cnf');
	
	$mail=new PHPMailer();
	$mail->CharSet = 'UTF-8';
	
	$mail->IsSMTP();
	$mail->Host       = $config['Host'];
	
	$mail->SMTPSecure = $config['SMTPSecure'];
	$mail->Port       = intval($config['Port']);
	$mail->SMTPDebug  = 4;
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


$date = (new DateTime())->format('r');
$body = 'This is the message '.$date;

$subject = 'hi';

$config = parse_ini_file(__DIR__.'/../configs/mail.cnf');
sendmail($config['TARGET'], $subject, $body);

?>