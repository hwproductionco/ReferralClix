<?php
/********************************************************************************/
/*																				*/
/*			CUSTOM FUNCTIONS FOR OPERATIONAL PURPOSES							*/
/*																				*/
/*																				*/
/********************************************************************************/

function pullIncome($zipcode,$user_id=NULL){
	require_once('config/db.php');
	if($zipcode=="60750") $zipcode = '33919';
	$data = array('username'=>PITCHPOINT_USER,'password'=>PITCHPOINT_PASS,'zipcode'=>trim($zipcode),'clientReference'=>'test');
	$data = http_build_query($data);
	$data = str_replace("%21","!",$data);
	//$url = PITCHPOINT_URL.'?'.$data;
	$context_options = array(
	'http' => array (
		'method' => 'POST',
		'header'=> "Content-Type: application/x-www-form-urlencoded",
		'content' => $data
	));
	$context = stream_context_create($context_options);
	$response = file_get_contents(PITCHPOINT_URL, false, $context);
	//die(print_r($context_options).print_r($response));
	$resp_array = json_decode(json_encode((array) simplexml_load_string($response)),1);
	return $resp_array;
}

function pullCredit($firstname,$lastname,$middle,$address,$city,$state,$zipcode,$ssn,$identifier=''){
	require_once('config/db.php');
	
	if(!empty($identifier)):
		
	$data = file_get_contents("templates/creditXML_poll.xml");
	$rarray = array('{identifer}','{userid}','{password}','{firstname}','{middle}','{lastname}','{address}','{city}','{state}','{zipcode}','{ssn}','{creditid}');
	$varray = array(CREDIT_PARTY_ID,CREDIT_USER,CREDIT_PASS,strtoupper($firstname),strtoupper($middle),strtoupper($lastname),strtoupper($address),strtoupper($city),strtoupper($state),strtoupper($zipcode),'000000000',$identifier);
	$xml_template = str_replace($rarray,$varray,$data);
	$arr= array('xmlparam' => $xml_template);
	$array= http_build_query($arr);
	$context_options = array(
	'http' => array (
		'method' => 'POST',
		'header'=> "Content-Type: application/x-www-form-urlencoded"."Content-Length: ".strlen($array) ."",
		'content' => $array
	));
	$context = stream_context_create($context_options);
	$response = file_get_contents(CREDIT_URL, false, $context);
	$ca = json_decode(json_encode((array) simplexml_load_string($response)),1);	
	$x=0;
	$emp_history = $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_FILE']['_BORROWER']['EMPLOYER'];
	$cnt = count($emp_history);
	foreach($emp_history as $job){
		if($cnt>1 && $x!=$cnt-1) $c = ', '; else $c = '';
		$history .= $job['@attributes']['_Name'].$c;
		$x++;
	}
	
	$credit_array = array(
		"Credit ID" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['@attributes']['CreditReportIdentifier'],
		"Status" => $ca['RESPONSE']['STATUS']['@attributes']['_Description'],
		"Credit Report" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SCORE']['@attributes']['_Value'],
		"Public Records" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][1]['@attributes']['_Value'],
		"Negative Trades" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][3]['@attributes']['_Value'],
		"Revolving Trades" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][7]['@attributes']['_Value'],
		"Collections" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][2]['@attributes']['_Value'],
		"Installment Trades" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][8]['@attributes']['_Value'],
		"Tradelines" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][6]['@attributes']['_Value'],
		"Historic Neg Occurrences" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][5]['@attributes']['_Value'],
		"Mortgage Trades" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][9]['@attributes']['_Value'],
		"Employer History" => $history,
		"Employment Position" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_FILE']['_BORROWER']['EMPLOYER'][0]['@attributes']['EmploymentPositionDescription'],
		"Employer" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_FILE']['_BORROWER']['EMPLOYER'][0]['@attributes']['_Name'],
		"SSN" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_FILE']['_BORROWER']['@attributes']['_SSN'],
		"Marital Status" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['BORROWER']['@attributes']['MaritalStatusType']
	);
		
	else:
	
	$data = file_get_contents("templates/creditXML.xml");
	$rarray = array('{identifer}','{userid}','{password}','{firstname}','{middle}','{lastname}','{address}','{city}','{state}','{zipcode}','{ssn}');
	$varray = array(CREDIT_PARTY_ID,CREDIT_USER,CREDIT_PASS,strtoupper($firstname),strtoupper($middle),strtoupper($lastname),strtoupper($address),strtoupper($city),strtoupper($state),strtoupper($zipcode),'000000000');
	$xml_template = str_replace($rarray,$varray,$data);
	$arr= array('xmlparam' => $xml_template);
	$array= http_build_query($arr);
	$context_options = array(
	'http' => array (
		'method' => 'POST',
		'header'=> "Content-Type: application/x-www-form-urlencoded"."Content-Length: ".strlen($array) ."",
		'content' => $array
	));
	$context = stream_context_create($context_options);
	$response = file_get_contents(CREDIT_URL, false, $context);
	$ca = json_decode(json_encode((array) simplexml_load_string($response)),1);
			
	$x=0;
	$emp_history = $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_FILE']['_BORROWER']['EMPLOYER'];
	$cnt = count($emp_history);
	foreach($emp_history as $job){
		if($cnt>1 && $x!=$cnt-1) $c = ', '; else $c = '';
		$history .= $job['@attributes']['_Name'].$c;
		$x++;
	}
	
	//die(print_r($ca));
	$credit_array = array(
		"Credit ID" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['@attributes']['CreditReportIdentifier'],
		"Status" => $ca['RESPONSE']['STATUS']['@attributes']['_Description'],
		"Credit Report" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SCORE']['@attributes']['_Value'],
		"Public Records" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][1]['@attributes']['_Value'],
		"Negative Trades" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][3]['@attributes']['_Value'],
		"Revolving Trades" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][7]['@attributes']['_Value'],
		"Collections" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][2]['@attributes']['_Value'],
		"Installment Trades" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][8]['@attributes']['_Value'],
		"Tradelines" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][6]['@attributes']['_Value'],
		"Historic Neg Occurrences" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][5]['@attributes']['_Value'],
		"Mortgage Trades" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_SUMMARY'][0]['_DATA_SET'][9]['@attributes']['_Value'],
		"Employer History" => $history,
		"Employment Position" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_FILE']['_BORROWER']['EMPLOYER'][0]['@attributes']['EmploymentPositionDescription'],
		"Employer" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_FILE']['_BORROWER']['EMPLOYER'][0]['@attributes']['_Name'],
		"SSN" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['CREDIT_FILE']['_BORROWER']['@attributes']['_SSN'],
		"Marital Status" => $ca['RESPONSE']['RESPONSE_DATA']['CREDIT_RESPONSE']['BORROWER']['@attributes']['MaritalStatusType']
	);
	endif;
	return $credit_array;
}

function isValidEmail($email){
    return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}

function updateVideoInvite($user_id,$email,$name='',$phone=''){	
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$email = $db->real_escape_string($email);
	$user_id = $db->real_escape_string($user_id);
	$con = $db->query("SELECT * FROM video_invites WHERE user_id = '".$user_id."' AND email = '".trim($email)."'");
	$result = $con->fetch_row();
	if($result){
		$db->query("UPDATE video_invites SET status = 'joined' WHERE user_id = '".$user_id."' AND email = '".trim($email)."';");
		$db->query("DELETE FROM emails WHERE user_id = '".$user_id."' AND email = '".trim($email)."'");
		$con->close();
		$db->close();
		return true;
		exit;
	} else {
		$db->query("INSERT INTO video_invites (user_id,status,email,name,phone) VALUES('".$user_id."','joined','".trim($email)."','".trim($name)."','".trim($phone)."')");
		$db->query("DELETE FROM emails WHERE user_id = '".$user_id."' AND email = '".trim($email)."'");
		$con->close();
		$db->close();
		return false;
		exit;
	}
}

function updateInvite($user_id,$email,$name='',$phone=''){	
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$email = $db->real_escape_string($email);
	$user_id = $db->real_escape_string($user_id);
	$con = $db->query("SELECT * FROM invites WHERE user_id = '".$user_id."' AND email = '".trim($email)."'");
	$result = $con->fetch_row();
	if($result){
		$db->query("UPDATE invites SET status = 'joined' WHERE user_id = '".$user_id."' AND email = '".trim($email)."';");
		$db->query("DELETE FROM emails WHERE user_id = '".$user_id."' AND email = '".trim($email)."'");
		$con->close();
		$db->close();
		return true;
		exit;
	} else {
		$db->query("INSERT INTO invites (user_id,status,email,name,phone) VALUES('".$user_id."','joined','".trim($email)."','".trim($name)."','".trim($phone)."')");
		$db->query("DELETE FROM emails WHERE user_id = '".$user_id."' AND email = '".trim($email)."'");
		$con->close();
		$db->close();
		return false;
		exit;
	}
}

function getReferrals($user_id=false){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if($user_id){
		$con = $db->query("SELECT user_id,count(*) as cnt FROM invites WHERE user_id = '".$user_id."' status = 'joined' GROUP BY user_id");
	} else {
		$con = $db->query("SELECT user_id,count(*) as cnt FROM invites WHERE status = 'joined' GROUP BY user_id LIMIT 0,5");
	}
	while($row = $con->fetch_row()) {
		$referrals[] = $row;
	}
	$con->close();
	return $referrals;
}

function checkVideo($user_id,$vid_id){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT * FROM videos WHERE video_id = '".$vid_id."' AND user_id = '".$user_id."'");
	$result = $con->fetch_row();
	if(!$result) return false; else return true;
}

function updateVideo($user_id,$vid_name,$vid_url,$vid_id,$thumb_url){
	$dataadded = time();
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT * FROM videos WHERE video_id = '".$vid_id."' AND user_id = '".$user_id."'");
	$result = $con->fetch_row();
	if(!$result){
		$db->query("INSERT INTO videos (user_id,video_url,video_id,thumb_url,video_name,dateadded) VALUES('".$user_id."','".$vid_url."','".$vid_id."','".$thumb_url."','".$vid_name."','".$dateadded."')");
		$con->close();
		$db->close();
		return true;
		exit;
	}
	return false;
}

function addEmail($user_id,$email,$sender_name,$sender_email,$subject,$template){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$user_id = $db->real_escape_string($user_id);
	$email = $db->real_escape_string($email);
	$sender_name = $db->real_escape_string($sender_name);
	$sender_email = $db->real_escape_string($sender_email);
	$subject = $db->real_escape_string($subject);
	$template = $db->real_escape_string($template);
	$con = $db->query("SELECT * FROM emails WHERE user_id = '".$user_id."' AND email = '".trim($email)."';");
	$result = $con->fetch_object();
	if(empty($result->email)){
		$db->query("INSERT INTO emails (user_id,email,sender_name,sender_email,subject,template,timessent) VALUES('".$user_id."','".trim($email)."','".$sender_name."','".trim($sender_email)."','".$subject."','".$template."','1')");
		$con->close();
		$db->close();
		return true;
		exit;
	} else {
		$cnt = $result->timessent;
		$cnt = $cnt+1;
		$db->query("UPDATE emails SET timessent = '".$cnt."' WHERE user_id = '".$user_id."' AND email = '".trim($email)."'");
		$con->close();
		$db->close();
		return false;
		exit;
	}
}

function sendMandrill($html,$from_email,$from_name,$subject,$to){
	require_once('classes/class.mandrill.php');
	$Mandrill = new Mandrill(MANDRILL_API);
	$params = array(
        "html" => $html,
        "text" => null,
        "from_email" => $from_email,
        "from_name" => $from_name,
        "subject" => $subject,
        "to" => array(array("email" => $to)),
        "track_opens" => true,
        "track_clicks" => true,
        "auto_text" => true
	);
	$result = $Mandrill->messages->send($params, true);
	if($result[0]['status']=='error') return false; else return true;
}

function addInvite($user_id,$email,$name='',$phone=''){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$email = $db->real_escape_string($email);
	$user_id = $db->real_escape_string($user_id);
	$name = $db->real_escape_string($name);
	$phone = $db->real_escape_string($phone);
	$con = $db->query("SELECT * FROM invites WHERE user_id = '".$user_id."' AND email = '".trim($email)."';");
	$result = $con->fetch_object();
	if(empty($result->email)){
		$db->query("INSERT INTO invites (user_id,name,email,phone) VALUES('".$user_id."','".$name."','".trim($email)."','".$phone."')");
		$con->close();
		$db->close();
		return true;
		exit;
	} else {
		$con->close();
		$db->close();
		return false;
		exit;
	}
}

function addVideoInvite($user_id,$email,$name='',$phone=''){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$email = $db->real_escape_string($email);
	$user_id = $db->real_escape_string($user_id);
	$name = $db->real_escape_string($name);
	$phone = $db->real_escape_string($phone);
	$con = $db->query("SELECT * FROM video_invites WHERE user_id = '".$user_id."' AND email = '".trim($email)."';");
	$result = $con->fetch_object();
	if(empty($result->email)){
		$db->query("INSERT INTO video_invites (user_id,name,email,phone) VALUES('".$user_id."','".$name."','".trim($email)."','".$phone."')");
		$con->close();
		$db->close();
		return true;
		exit;
	} else {
		$con->close();
		$db->close();
		return false;
		exit;
	}
}

function checkVideoInvites($email){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT * FROM video_invites WHERE email = '".$email."';");
	$rows = array();
	while($row = $con->fetch_row()) {
  		$rows[]=$row;
	}
	$con->close();
	$db->close();
	if($rows){
		return $rows;
		exit;
	} else {
		return false;
		exit;
	}
}

function checkInvites($email){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT * FROM invites WHERE email = '".$email."';");
	$rows = array();
	while($row = $con->fetch_row()) {
  		$rows[]=$row;
	}
	$con->close();
	$db->close();
	if($rows){
		return $rows;
		exit;
	} else {
		return false;
		exit;
	}
}

function getInvites($user_id){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT id,name,email,phone,status FROM invites WHERE user_id = '".$user_id."';");
	$rows = array();
	while($row = $con->fetch_row()) {
  		$rows[]=$row;
	}
	$con->close();
	$db->close();
	if($rows){
		return $rows;
		exit;
	} else {
		return 0;
		exit;
	}
}

function getVideos($user_id){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT video_name,video_url,thumb_url,video_id FROM videos WHERE user_id = '".$user_id."';");
	$rows = array();
	while($row = $con->fetch_row()) {
  		$rows[]=$row;
	}
	$con->close();
	$db->close();
	if($rows){
		return $rows;
		exit;
	} else {
		return 0;
		exit;
	}
}

function getPendingVideoInvites($user_id){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT COUNT(*) as cnt FROM video_invites WHERE user_id = '".$user_id."' and status = 'pending';");
	$result = $con->fetch_object();
	$con->close();
	$db->close();
	if($result) return $result->cnt; else return 0;
}

function getPendingInvites($user_id){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT COUNT(*) as cnt FROM invites WHERE user_id = '".$user_id."' and status = 'pending';");
	$result = $con->fetch_object();
	$con->close();
	$db->close();
	if($result) return $result->cnt; else return 0;
}

function getJoinedVideoInvites($user_id){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT COUNT(*) as cnt FROM video_invites WHERE user_id = '".$user_id."' and status = 'joined';");
	$result = $con->fetch_object();
	$con->close();
	$db->close();
	if($result) return $result->cnt; else return 0;
}

function getJoinedInvites($user_id){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT COUNT(*) as cnt FROM invites WHERE user_id = '".$user_id."' and status = 'joined';");
	$result = $con->fetch_object();
	$con->close();
	$db->close();
	if($result) return $result->cnt; else return 0;
}

function checkCampaign($campaign_id,$code){
	require_once('classes/ss.class.php');
	$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
	$ss->api_url = SS_API_URL;
	$data = array(
		'code'=>$code,
		'hide_customer_field'=>'Y'
	);
	$result = $ss->getCustomerInfo($data, true);
	if($result['@attributes']['status']=='success'){
		foreach($result['campaigns'] as $c){
			$c_id = $c['campaign']['id'];
			if($campaign_id==$c_id) return true;
		}
	} else {
		return false;
	}
	return false;
}

function checkUser($email){
	require_once('classes/ss.class.php');
	$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
	$ss->api_url = SS_API_URL;
	$data = array(
		'exact_match' => 'insensitive',
		'email'=>$email
	);
	$result = $ss->searchCustomer($data, true);
	if($result['@attributes']['status']=='no_match') return false;
	if(!empty($result['customer'][0])){
		foreach($result['customer'] as $customer){
			$firstname = trim($customer['first_name']);
			if(!empty($firstname)) return true;
		}
	} else {
		$firstname = trim($result['customer']['first_name']);
		if(!empty($firstname)) return true;
	}
	return false;
}

function getUserByID($user_id){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$con = $db->query("SELECT user_name,first_name,last_name,user_code FROM users WHERE user_id = '".$user_id."';");
    $row = $con->fetch_row();
	$con->close();
    $db->close();
    return $row;
}

function getUserInfo($username){
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$qry = "SELECT user_id,user_name,first_name,last_name,user_code FROM users WHERE user_name = '".$username."';";
    $checklogin = $db->query($qry);
    $result_row = $checklogin->fetch_object();
    $db->close();
    return $result_row;
}

function unsetValue(array $array, $value, $strict = TRUE){
    if(($key = array_search($value, $array, $strict)) !== FALSE) {
        unset($array[$key]);
    }
    return $array;
}

function promotion($promo_id,$code,$description=''){
	require_once('classes/ss.class.php');
	$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
	$ss->api_url = SS_API_URL;
	$data = array(
				'promo_id'=>$promo_id,
				'code'=>$code,
				'campaign_id'=>USERS_CAMPAIGN
			 );
	if(!empty($description)) $data['authorization'] = $description;
	$result = $ss->recordTransaction($data,'points');
	$vars = json_decode($result,true);
	if($vars['@attributes']['status']=='error') return false; else return $vars['receipt']['transaction']['promotion']['amount'];
}

function mailer($to,$from,$subject,$html,$from_name=''){
	require_once('classes/class.phpmailer.php');
	$msg = new PHPMailer(true);
	try{
		if(is_array($to)){
			foreach($to as $t){
				$msg->AddAddress($t);
			}
		} elseif(!empty($to)) {
			$msg->AddAddress($to);
		}
  		if(!empty($from)){
  			$msg->From = $from;
			if(!empty($from_name)) $msg->FromName = $from_name." <".$from.">";
  		}
  		$msg->AddReplyTo($from,$from_name);
  		$msg->do_verp = true;
		$msg->Priority = 1;
		$msg->IsHTML(true);
		$msg->AddCustomHeader("X-MSMail-Priority: High");
		$msg->AddCustomHeader("Message-ID: <".time()."@".$_SERVER['SERVER_NAME'].">");
		$msg->AddCustomHeader("MIME-Version: 1.0");
		//$msg->AddCustomHeader("Content-Type: multipart/alternative; boundary=\"".md5(uniqid(time()))."\"");
  		$msg->Subject = $subject;
		$msg->AltBody = strip_tags($email_template);
		$msg->Body = $html;
		$msg->WordWrap = 0;
		$sendSuccess = $msg->Send();
		$msg->ClearAddresses();
   		$msg->ClearAllRecipients();
  		$msg->ClearReplyTos();
	} catch (phpmailerException $e) {
  		die($e->errorMessage()); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
  		die($e->getMessage()); //Boring error messages from anything else!
	}
}
?>