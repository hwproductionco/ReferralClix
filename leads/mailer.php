<?php

include_once('classes/login.class.php');
$login = new Login();
if (!$login->isLoggedIn()) header("Location: index.php");

if($_POST['action']=='sendinvite'):

include('classes/ss.class.php');
$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
$ss->api_url = SS_API_URL;

$user = $login->getUser();
$uid = $user->user_id;

/* PARAMETERS */
$success_url = "bonus.php?view=invites&success=1";
$from_email = RESORT_EMAIL;
$name = $_POST['sender'];
$url = $_POST['url'];
$user = explode('=',$url);
$user_email = $user[1];
$emails = $_POST['emails'];
$message = $_POST['message'];


$subject = '*** Your friend '.$name.' has invited you to check this out.';
$email_template = file_get_contents('templates/email4.template.php');


/* DO NOT EDIT BELOW */
if(ENABLE_MANDRILL):

	$lines = explode(PHP_EOL, $emails);
	array_pop($lines);
	foreach($lines as $line){
		$line = explode(" - ",$line);
		if(isValidEmail(trim($line[2]))){
			
			if(addInvite($uid,trim($line[2]),trim($line[0]),trim($line[1]))){
				$cname = explode(" ",$line[0]);
				$result = $ss->newCustomer('new',array('campaign_id'=>LEADS_CAMPAIGN,'first_name'=>$cname[0],'last_name'=>$cname[1],'email'=>$line[2],'phone'=>$line[1],'custom_field_7'=>$_SESSION['user_name']));
				
				if(!empty($line[0])) $url = $url.'&cname='.$line[0].'&phone='.$line[1];
				$url = $url.'&email='.$line[2];

				$rarray = array('{email}','{cname}','{phone}','{customer}','{user_email}','{company}','{vendor}','{vendor_phone}','{name}','{url}','{base_url}','{message}');
				$varray = array($line[2],$line[0],$line[1],$cname[0],$user_email,RESORT_NAME,VENDOR_NAME,VENDOR_PHONE,$name,$url,BASE_URL,$message);
				$email_template = str_replace($rarray,$varray,$email_template);
				
				addEmail($uid,$line[2],$from_email,VENDOR_EMAIL,$subject,$email_template);
				if(sendMandrill($email_template,$from_email,RESORT_NAME,$subject,$line[2])) $sendSuccess = true; else $sendSuccess = false;
			
			} else {
				header("Location: bonus.php?view=invites&error=".urlencode('An error occurred. No invites were sent.'));
			}
		} else {
			header("Location: bonus.php?view=invites&error=".urlencode('The email address your trying to send to is invalid.'));
		}
	}
	if($sendSuccess) {
		header("Location: ".$success_url);
	} else {
		header("Location: bonus.php?error=".urlencode('An error occurred. No invites were sent.'));
	}

else:

include('classes/class.phpmailer.php');
$msg = new PHPMailer(true);
try{
	
	$msg->From = $from_email;
	$msg->FromName = RESORT_NAME." <".$from_email.">";
	$msg->AddReplyTo($from_email,RESORT_NAME);
	$msg->AddAddress(NO_REPLY,VENDOR_NAME);
	$lines = explode(PHP_EOL, $emails);
	array_pop($lines);
	foreach($lines as $line){
		$line = explode(" - ",$line);
		if(isValidEmail(trim($line[2]))){
			
			if(addInvite($uid,trim($line[2]),trim($line[0]),trim($line[1]))){
				$cname = explode(" ",$line[0]);
				$result = $ss->newCustomer('new',array('campaign_id'=>LEADS_CAMPAIGN,'first_name'=>$cname[0],'last_name'=>$cname[1],'email'=>$line[2],'phone'=>$line[1],'custom_field_7'=>$_SESSION['user_name']));
				$msg->AddBCC($line[2]);
				
				if(!empty($line[0])) $url = $url.'&cname='.$line[0].'&phone='.$line[1];
				$url = $url.'&email='.$line[2];
				
				$rarray = array('{customer}','{company}','{vendor}','{vendor_phone}','{name}','{url}','{base_url}','{message}');
				$varray = array($cname[0],RESORT_NAME,VENDOR_NAME,VENDOR_PHONE,$name,$url,BASE_URL,$message);
				$email_template = str_replace($rarray,$varray,$email_template);
	
				$msg->do_verp = true;
				$msg->Priority = 1;
				$msg->IsHTML(true);
				$msg->AddCustomHeader("X-MSMail-Priority: High");
				$msg->AddCustomHeader("Message-ID: <".time()."@".$_SERVER['SERVER_NAME'].">");
				$msg->AddCustomHeader("MIME-Version: 1.0");
				//$msg->AddCustomHeader("Content-Type: multipart/alternative; boundary=\"".md5(uniqid(time()))."\"");
				$msg->Subject = $subject;
				$msg->AltBody = strip_tags($email_template);
				$msg->Body = $email_template;
				$msg->WordWrap = 0;
				$sendSuccess = $msg->Send();
				$msg->ClearAddresses();
				
			} else {
				header("Location: bonus.php?view=invites&error=".urlencode('An error occurred. No invites were sent.'));
			}
		} else {
			header("Location: bonus.php?view=invites&error=".urlencode('The email address your trying to send to is invalid.'));
		}
	}
	if($sendSuccess) {
		addEmail($uid,$line[2],RESORT_NAME,$from_email,$subject,$email_template);
		header("Location: ".$success_url);
	} else {
		header("Location: bonus.php?error=".urlencode('An error occurred. No invites were sent.'));
	}

} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}

endif;

endif;
?>