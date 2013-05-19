<?php
$debug=false;
include_once('config/db.php');

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$con = $db->query("SELECT * FROM invites WHERE dateadded != '0000-00-00 00:00:00'");
$rows = array();
while($row = $con->fetch_row()) {
	if(strtotime("-2 day") == strtotime($row['dateadded')){
	
		$url = BASE_URL.'survey.php?uid='.$customer['email'];
		$full_name = $row['name'];
		$subject = '*** Dont miss this opportunity '.$full_name;
		$email_template = file_get_contents('templates/followup.template.php');
		$rarray = array('{company}','{company_phone}','{name}','{base_url}');
		$varray = array(RESORT_NAME,RESORT_PHONE,$full_name,$base_url);
		$email_template = str_replace($rarray,$varray,$email_template);
		
		if(ENABLE_MANDRILL):
			sendMandrill($email_template,RESORT_EMAIL,RESORT_NAME,$subject,$_POST['email']);
		else:
			if(mailer($_POST['email'],RESORT_EMAIL,$subject,$email_template,RESORT_NAME)){
				if($debug) echo 'email sent to '.$full_name.' ('.$row['email'].')'.' successfully. ';
			} else {
				if($debug) echo 'email was NOT sent to '.$full_name.' ('.$row['email'].')';
			}
		endif;
		
	}
	if(strtotime("-5 day") == strtotime($row['dateadded')){
	
		$url = BASE_URL.'survey.php?uid='.$customer['email'];
		$full_name = $row['name'];
		$subject = '*** Dont miss this opportunity '.$full_name;
		$email_template = file_get_contents('templates/followup.template.php');
		$rarray = array('{company}','{company_phone}','{name}','{base_url}');
		$varray = array(RESORT_NAME,RESORT_PHONE,$full_name,$base_url);
		$email_template = str_replace($rarray,$varray,$email_template);
		
		if(ENABLE_MANDRILL):
			sendMandrill($email_template,RESORT_EMAIL,RESORT_NAME,$subject,$_POST['email']);
		else:
			if(mailer($_POST['email'],RESORT_EMAIL,$subject,$email_template,RESORT_NAME)){
				if($debug) echo 'email sent to '.$full_name.' ('.$row['email'].')'.' successfully. ';
			} else {
				if($debug) echo 'email was NOT sent to '.$full_name.' ('.$row['email'].')';
			}
		endif;
		
	}
	if(strtotime("-15 day") == strtotime($row['dateadded')){
	
		$url = BASE_URL.'survey.php?uid='.$customer['email'];
		$full_name = $row['name'];
		$subject = '*** Dont miss this opportunity '.$full_name;
		$email_template = file_get_contents('templates/followup.template.php');
		$rarray = array('{company}','{company_phone}','{name}','{base_url}');
		$varray = array(RESORT_NAME,RESORT_PHONE,$full_name,$base_url);
		$email_template = str_replace($rarray,$varray,$email_template);
		
		if(ENABLE_MANDRILL):
			sendMandrill($email_template,RESORT_EMAIL,RESORT_NAME,$subject,$_POST['email']);
		else:
			if(mailer($_POST['email'],RESORT_EMAIL,$subject,$email_template,RESORT_NAME)){
				if($debug) echo 'email sent to '.$full_name.' ('.$row['email'].')'.' successfully. ';
			} else {
				if($debug) echo 'email was NOT sent to '.$full_name.' ('.$row['email'].')';
			}
		endif;
		
	}
	if(strtotime("-30 day") == strtotime($row['dateadded')){
	
		$url = BASE_URL.'survey.php?uid='.$customer['email'];
		$full_name = $row['name'];
		$subject = '*** Dont miss this opportunity '.$full_name;
		$email_template = file_get_contents('templates/followup.template.php');
		$rarray = array('{company}','{company_phone}','{name}','{base_url}');
		$varray = array(RESORT_NAME,RESORT_PHONE,$full_name,$base_url);
		$email_template = str_replace($rarray,$varray,$email_template);
		
		if(ENABLE_MANDRILL):
			sendMandrill($email_template,RESORT_EMAIL,RESORT_NAME,$subject,$_POST['email']);
		else:
			if(mailer($_POST['email'],RESORT_EMAIL,$subject,$email_template,RESORT_NAME)){
				if($debug) echo 'email sent to '.$full_name.' ('.$row['email'].')'.' successfully. ';
			} else {
				if($debug) echo 'email was NOT sent to '.$full_name.' ('.$row['email'].')';
			}
		endif;
		
	}
	if(strtotime("-90 day") == strtotime($row['dateadded')){
	
		$url = BASE_URL.'survey.php?uid='.$customer['email'];
		$full_name = $row['name'];
		$subject = '*** Dont miss this opportunity '.$full_name;
		$email_template = file_get_contents('templates/followup.template.php');
		$rarray = array('{company}','{company_phone}','{name}','{base_url}');
		$varray = array(RESORT_NAME,RESORT_PHONE,$full_name,$base_url);
		$email_template = str_replace($rarray,$varray,$email_template);
		
		if(ENABLE_MANDRILL):
			sendMandrill($email_template,RESORT_EMAIL,RESORT_NAME,$subject,$_POST['email']);
		else:
			if(mailer($_POST['email'],RESORT_EMAIL,$subject,$email_template,RESORT_NAME)){
				if($debug) echo 'email sent to '.$full_name.' ('.$row['email'].')'.' successfully. ';
			} else {
				if($debug) echo 'email was NOT sent to '.$full_name.' ('.$row['email'].')';
			}
		endif;
		
	}
	if(strtotime("-120 day") == strtotime($row['dateadded')){
	
		$url = BASE_URL.'survey.php?uid='.$customer['email'];
		$full_name = $row['name'];
		$subject = '*** Dont miss this opportunity '.$full_name;
		$email_template = file_get_contents('templates/followup.template.php');
		$rarray = array('{company}','{company_phone}','{name}','{base_url}');
		$varray = array(RESORT_NAME,RESORT_PHONE,$full_name,$base_url);
		$email_template = str_replace($rarray,$varray,$email_template);
		
		if(ENABLE_MANDRILL):
			sendMandrill($email_template,RESORT_EMAIL,RESORT_NAME,$subject,$_POST['email']);
		else:
			if(mailer($_POST['email'],RESORT_EMAIL,$subject,$email_template,RESORT_NAME)){
				if($debug) echo 'email sent to '.$full_name.' ('.$row['email'].')'.' successfully. ';
			} else {
				if($debug) echo 'email was NOT sent to '.$full_name.' ('.$row['email'].')';
			}
		endif;
		
	}
}
$con->close();
$db->close();
?>