<?php
$debug=false;
include_once('config/db.php');
include_once('classes/ss.class.php');

$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
$ss->api_url = SS_API_URL;

$date_start = date("Y-m-d", strtotime( '-1 days' ) );
//$date_end = '2012-11-06'; //date('Y-m-d',time()) 

$customers = $ss->customerBirthdayReport(array( 'selected_campaigns'=>USERS_CAMPAIGN,'date_start'=>$date_start )); //,'date_end'=>$date_end

if(!empty($customers['customer']['customer_code'])):

	$data = array(
		'promo_id'=>'7923',
		'authorization'=>'Happy Birthday +50 Points',
		'code'=>$customers['customer']['customer_code'],
		'campaign_id'=>USERS_CAMPAIGN
		);
	
	$result = $ss->recordTransaction($data,'points');
	
	$url = BASE_URL.'survey.php?uid='.$customers['customer']['email'];
	$full_name = $customer['first_name'].' '.$customers['customer']['last_name'];
	$subject = '*** Happy Birthday '.$full_name;
	$email_template = file_get_contents('templates/birthday.template.php');
	$rarray = array('{company_phone}','{company_email}','{company}','{name}','{birthday}','{url}','{base_url}');
	$varray = array(RESORT_PHONE,RESORT_EMAIL,RESORT_NAME,$full_name,$customers['customer']['custom_date'],$url,$base_url);
	$email_template = str_replace($rarray,$varray,$email_template);
	if(ENABLE_MANDRILL){
		sendMandrill($email_template,RESORT_EMAIL,RESORT_NAME,$subject,$customers['customer']['email']);
		if($debug) echo 'Email sent to '.$customers['customer']['email'].' successfully via Mandrill.';
	}else{
		mailer($customer['email'],RESORT_EMAIL,$subject,$email_template,RESORT_NAME);
		if($debug) echo 'Email sent to '.$customers['customer']['email'].' successfully via mailer.';
	}
	
	$subject = '*** '.$full_name.' just received birthday points.';
	$email_template = file_get_contents('templates/vendor.template.php');
	$rarray = array('{vendor}','{company}','{name}','{url}','{base_url}');
	$varray = array(VENDOR_NAME,RESORT_NAME,$full_name,'https://rewards.clienttoolbox.com',$base_url);
	$email_template = str_replace($rarray,$varray,$email_template);
	if(ENABLE_MANDRILL){
		sendMandrill($email_template,VENDOR_EMAIL,VENDOR_NAME,$subject,RESORT_EMAIL);
	}else{
		mailer(RESORT_EMAIL,VENDOR_EMAIL,$subject,$email_template,VENDOR_NAME);
	}

elseif(!empty($customers['customer'])):

 foreach($customers['customer'] as $customer) {
	
	$data = array(
		'promo_id'=>'7923',
		'authorization'=>'Happy Birthday +50 Points',
		'code'=>$customer['customer_code'],
		'campaign_id'=>USERS_CAMPAIGN
		);
	
	$result = $ss->recordTransaction($data,'points');
	
	$url = BASE_URL.'survey.php?uid='.$customer['email'];
	$full_name = $customer['first_name'].' '.$customer['last_name'];
	$subject = '*** Happy Birthday '.$full_name;
	$email_template = file_get_contents('templates/birthday.template.php');
	$rarray = array('{company_phone}','{company_email}','{company}','{name}','{birthday}','{url}','{base_url}');
	$varray = array(RESORT_PHONE,RESORT_EMAIL,RESORT_NAME,$full_name,$customer['custom_date'],$url,$base_url);
	$email_template = str_replace($rarray,$varray,$email_template);
	if(ENABLE_MANDRILL){
		sendMandrill($email_template,RESORT_EMAIL,RESORT_NAME,$subject,$customer['email']);
		if($debug) echo 'Email sent to '.$customer['email'].' successfully via Mandrill.';
	}else{
		mailer($customer['email'],RESORT_EMAIL,$subject,$email_template,RESORT_NAME);
		if($debug) echo 'Email sent to '.$customer['email'].' successfully via mailer.';
	}
	
	$subject = '*** '.$full_name.' just received birthday points.';
	$email_template = file_get_contents('templates/vendor.template.php');
	$rarray = array('{vendor}','{company}','{name}','{url}','{base_url}');
	$varray = array(VENDOR_NAME,RESORT_NAME,$full_name,'https://rewards.clienttoolbox.com',$base_url);
	$email_template = str_replace($rarray,$varray,$email_template);
	if(ENABLE_MANDRILL){
		sendMandrill($email_template,VENDOR_EMAIL,VENDOR_NAME,$subject,RESORT_EMAIL);
	}else{
		mailer(RESORT_EMAIL,VENDOR_EMAIL,$subject,$email_template,VENDOR_NAME);
	}
	
 }

else:

	echo 'No birthdays found';

endif;
?>