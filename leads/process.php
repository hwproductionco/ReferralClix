<?php
// SOFT LEADS - 3964930392664742
// QUALIFIED LEADS - 6612035669730343
// Bonus Points - 8486876881402269
// Referral Badge - 3742237909218557
// Referral 5 Badge - 5760331892766655
$email_check = '';
$return_json = '';
	
$action = empty($_POST['action']) ? $_GET['action'] : $_POST['action'];

if(!empty($action)){

include_once('classes/class.mailchimp.php');
include_once('classes/class.wufoo.php');
include_once('classes/class.stupeflix.php');
include_once('classes/class.destinationlife.php');
include_once('classes/ss.class.php');
include_once('classes/login.class.php');
include_once('classes/facebook/facebook.php');
include_once('classes/class.facebook.php');

switch($action){
	case "login":
	
		if(!empty($_GET['state']) && !empty($_GET['code'])){
			$login = new Login();
			if(ENABLE_FACEBOOK):
				$facebook = new Facebook(array(
    				'appId'  => FB_APP_ID,
    				'secret' => FB_APP_SECRET
				));
				$config = array(
    				'redirect_uri' => BASE_URL.'process.php?action=login',
    				'scope'    => 'email,publish_stream',
				);
				$fb = new SimpleFacebook($facebook, $config);
				if( ! $fb->isLogged() ) {
    				$loginUrl = $fb->getLoginUrl();
    				header("Location: ".$loginUrl);
    				exit;
				} else {
					$token = $fb->getApplicationAccessToken();
					$me = $fb->getUserProfileData();
					$me['token'] = $token;
					$login = new Login($me);
					$login->set('token',$me['token']);
    				header("Location: bonus.php");
    				exit;
				}
			endif;
		
		} else {
	
			$login = new Login();
		
		}
	
		if ($login->isLoggedIn()) {
			$return_json = '{"success":"bonus.php"}';
			echo $return_json;
			exit;
		} else {
			$return_json = '{"error":"Please login first."}';
			echo $return_json;
			exit;
		}
		
	break;
	
	case "register":
		try{
			require_once("config/db.php");
			
			if(isValidEmail($_POST['email']) && isValidEmail($_POST['customer_username']) && strtolower($_POST['email'])==strtolower($_POST['customer_username'])) $email_check = 'valid'; else $email_check = 'invalid';
			if($email_check=='invalid'){
				$return_json = '{"email_check":"' . $email_check . '"}';
				echo $return_json;
				exit;
			}
			if(checkUser($_POST['email'])){
				$return_json = '{"email_check":"existing"}';
				echo $return_json;
				exit;
			}
			if($_POST['customer_password']==$_POST['customer_password_verify']) $pass_check = 'valid'; else $pass_check = 'invalid';
			if($pass_check=='invalid'){
				$return_json = '{"pass_check":"' . $pass_check . '"}';
				echo $return_json;
				exit;
			}
			
			if(ENABLE_WUFOO):
			$wrapper = new WufooApi(WUFOO_API, WUFOO_SUBDOMAIN);
			$result = $wrapper->entryPost(WUFOO_REGISTER_FORM,$_POST);
			if($result->Success==0 && !empty($result->FieldErrors)){
				$err_arr = array();
				
				foreach($result->FieldErrors as $key => $err){
					$eid = $err->ID;
					$e = $err->ErrorText;
					$err_arr[] = $e;
				}
				$err_msg = implode("<br>",$err_arr);
				$return_json = '{"error":"'.$field_array[$eid].':'.$err_msg.'"}';
				echo $return_json;
				exit;
			} elseif($result->Success==0 && !empty($result->ErrorText)){
				$return_json = '{"error":"'.$result->ErrorText.'"}';
				echo $return_json;
				exit;
			}
			endif;
			
			if(ENABLE_CHIMP):
			$mc = new MCAPI(CHIMP_API,true);
			$mc->listSubscribe(CHIMP_REGISTER_LIST,$_POST['email']);
			endif;
			
			if(ENABLE_PITCHPOINT_REGISTER):
			$address = trim($_POST['street1'])." ".trim($_POST['street2']);
			$report = pullCredit(trim($_POST['first_name']),trim($_POST['last_name']),'',trim($address),trim($_POST['city']),trim($_POST['state']),trim($_POST['postal_code']),'000000000');
						
			$credit_id = $report['Credit ID'];	
			if(trim($report['Status'])=='NOTREADY') $wait=true; else $wait=false;
			if(TEST_MODE) $wait=false;
			while($wait):
				set_time_limit(0);
				sleep(6);
				$report = pullCredit(trim($_POST['first_name']),trim($_POST['last_name']),'',trim($address),trim($_POST['city']),trim($_POST['state']),trim($_POST['postal_code']),'000000000',trim($credit_id));
				
				if($report['Status']=='NOTREADY') $wait=true; else $wait=false;
			endwhile;
			endif;
						
			$user = getUserInfo($_SESSION['user_name']);
			$income_response = pullIncome(trim($_POST['postal_code']),$user->user_id);
			
			$_POST['credit_id'] = $credit_id;
			if(empty($_POST['custom_field_6'])) $_POST['custom_field_6'] = $income_response['census']['hhIncome'];					
			if(empty($_POST['custom_field_3'])) $_POST['custom_field_3'] = $report['Marital Status'];
			$_POST['custom_field_9'] = $report['Credit Report'];
			$_POST['custom_field_10'] = $report['Public Records'];
			$_POST['custom_field_11'] = $report['Negative Trades'];
			$_POST['custom_field_12'] = $report['Revolving Trades'];
			$_POST['custom_field_13'] = $report['Collections'];
			$_POST['custom_field_14'] = $report['Installment Trades'];
			$_POST['custom_field_15'] = $report['Tradelines'];
			$_POST['custom_field_16'] = $report['Historic Neg Occurrences'];
			$_POST['custom_field_17'] = $report['Mortgage Trades'];
			$_POST['custom_field_18'] = $report['Employer History'];
			$_POST['custom_field_19'] = $report['SSN'];
			$_POST['custom_field_22'] = $report['Employer'];
			$_POST['custom_field_23'] = $report['Employment Position'];		
						
			$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
			$ss->api_url = SS_API_URL;
			
			$amount = SIGNUP_BONUS;
			if( !empty($amount) ){
				$_POST['auto_add'] = $amount;
				$_POST['authorization'] = 'A Gift From Us';
				$_SESSION['signup'] = $amount;
			}
			
			$result = $ss->newCustomer('new',$_POST);
			$vars = json_decode($result,true);
			
			
			if($vars['@attributes']['status']=='success'){
				$_POST['code'] = $vars['customer']['code'];
				$login = new Login($me);
				
				$amount = promotion('9765',$vars['customer']['code'],'A Gift From Us');
				$_SESSION['signup'] = $amount;
				
				if(!empty($login->errors)){
					$return_json .= '{"error":"'.$login->errors[0].'"}';
				} else {
					$return_json .= '{"success":"'.$login->messages[0].'"}';
				}
			} elseif($vars['@attributes']['status']=='error'){
				$return_json .= '{"error":"'.$vars['error'].'"}';
			}
		} catch(Exception $e) {
			$return_json .= '{"error":"'.$e->getMessage().'"}';
		}
		echo $return_json;
	break;
	
	case "record":
		$login = new Login();
		$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
		$ss->api_url = SS_API_URL;
		if (!$login->isLoggedIn()) {
			echo '{"error":"session"}';
			exit;
		}
		$data['code'] = $_SESSION['code'];
		$data['campaign_id'] = USERS_CAMPAIGN;
		$data['amount'] = $_POST['amount'];
		$data['authorization'] = $_POST['description'];
		$data['send_transaction_email'] = 'Y'; 
		$result = $ss->recordTransaction($data,'points');
		$vars = json_decode($result,true);
		if($vars['@attributes']['status']=='success'){
			echo '{"success":"'.$vars['receipt'].'"}';
		} elseif($vars['@attributes']['status']=='error'){
			echo '{"error":"'.$vars['error'].'"}';
		}
	break;
	
	case "fbshare":
	
		$login = new Login();
		$uname = $_GET['uname'];
		$cname = $_GET['cname'];
		$phone = $_GET['phone'];
		$email = $_GET['email'];
		$referrer = $_GET['uid'];
		
		if(!empty($cname)) $name = explode(" ",$cname);
		
		$facebook = new Facebook(array(
    		'appId'  => FB_APP_ID,
    		'secret' => FB_APP_SECRET
		));
		$config = array(
    		'redirect_uri' => BASE_URL.'process.php?action=fbshare&uid='.$referrer.'&cname='.$cname.'&uname='.$uname.'&phone='.$phone.'&email='.$email,
    		'scope'    	   => 'email,publish_stream',
    		'display'	   => 'popup',
		);
		$fb = new SimpleFacebook($facebook, $config);
		if( !$fb->isLogged() ) {
			$loginUrl = $fb->getLoginUrl();
			header("Location: ".$loginUrl);
			exit;
		} else {
		
			$url = BASE_URL.'survey.php?uid='.$referrer;
			$url = urlencode($url);
			$surl = BASE_URL.'fb-invite.php?uid='.$uid.'&email='.$email.'&cname='.$cname.'&phone='.$phone.'&url='.$url;	
			$description = 'Your friend '.$name[0].' shared this because we both think that you could use a vacation! We want to share our excitement for a new travel company that saves you and your family thousands of dollars on accommodations around the world as well as provides discounts on things you use every day on vacation and at home.';
			
			$postData = array(
    			'message'     => 'Check out this Vacation deal',
    			//'picture'     => $turl, 
    			'link'        => $surl,
    			'name'        => 'Your friend '.$name[0].' thinks you deserve a vacation',
    			//'caption'     => '',
    			'description' => $description
			);
			$postId = $fb->postToWall($postData);
			
			$surl = BASE_URL.'fb-share.php?uid='.$uid.'&email='.$email.'&cname='.$cname.'&uname='.$uname.'&phone='.$phone.'&url='.$url;
			header("Location: ".$surl);
			exit;
		}
	
	break;
	
	case "fbpost":
		$login = new Login();
		if (!$login->isLoggedIn()) {
			echo '{"error":"session"}';
			exit;
		}
		
		$vid_url = $_GET['vid_url'];
		$turl = $_GET['turl'];
		$vid_name = $_GET['vid_name'];
		$msg = $_GET['msg'];
		$uid = $_GET['uid'];
		$vid_id = $_GET['video_id'];
				
		$facebook = new Facebook(array(
    		'appId'  => FB_APP_ID,
    		'secret' => FB_APP_SECRET
		));
		$config = array(
    		'redirect_uri' => BASE_URL.'process.php?action=login',
    		'scope'    	   => 'email,publish_stream',
    		'display'	   => 'popup',
		);
		$fb = new SimpleFacebook($facebook, $config);
		if( !$fb->isLogged() ) {
    		header("Location: bonus.php?view=myvideos&msg=".urlencode(htmlentities('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>You are not logged into facebook.</div>')));
    		exit;
		} else {
		 $user = $login->getUser($_SESSION['user_name']);
		 if($_GET['verified']==1):
			$cname = $user->first_name." ".$user->last_name;
			$email = $_SESSION['user_name'];
			
			if(!empty($cname)) $name = explode(" ",$cname);
			
			$url = BASE_URL.'video.php?uid='.$uid.'&vid_url='.$vid_url.'&turl='.$turl.'&vid_name='.$vid_name.'&cname='.$cname.'&referrer='.$email.'&msg='.$msg;
							
			if(empty($vid_name)) $vid_name = 'Your friend '.$name[0].' thinks you deserve a vacation.';
				
			$description = 'Your friend '.$name[0].' shared this because we both think that you could use a vacation! We want to share our excitement for a new travel company that saves you and your family thousands of dollars on accommodations around the world as well as provides discounts on things you use every day on vacation and at home.';
				
			$postData = array(
    			'message'     => $msg,
    			'picture'     => $turl, 
    			'link'        => $url,
    			'name'        => $vid_name,
    			//'caption'     => '',
    			'description' => $description
			);
			$postId = $fb->postToWall($postData);
			
			if($postId){
				header("Location: process.php?action=fbpost&verified=1&vid_id=".$vid_id."&postid=".$postId);
				exit;
			} else {
				header("Location: bonus.php?view=myvideos&msg=".urlencode(htmlentities('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>There was an error. Please try again and make sure you are logged into Facebook.</div>')));
				exit;
			}
		 else:
			
			
			//if(!checkVideo($user->user_id,$_GET['vid_id'])){

			$amount = promotion('9699',$user->user_code);
			$_SESSION['promo'] = $amount;
			
			//}
			
		 	header("Location: bonus.php?view=myvideos&msg=".urlencode(htmlentities('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Your video was shared successfully on Facebook.</div>')));
		 	exit;
		 
		 endif;
		}
		
	break;
	
	case "checkemail":
		$login = new Login();
		$email = trim($_POST['email']);
		$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
		$ss->api_url = SS_API_URL;
		$data = array(
			'email'=>$email
		);
		$result = $ss->searchCustomer($data,true);
		if($result['@attributes']['status'] != "no_match"){
			echo '{"error":"'.$email.'"}';
			exit;
		}
		
		if(strtolower($_SESSION['user_name'])!=strtolower($email)){
		$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$user = getUserInfo($_SESSION['user_name']);
		$con = $db->query("SELECT email FROM invites WHERE user_id = '".$user->user_id."'");
		while ($row = $con->fetch_assoc()) {
		 if($email!=$row['email']){
		 	
		 } else {
		 	echo '{"error":"'.$email.'"}';
			exit;
		 }
		}
		} else {
			echo '{"error":"'.$email.'"}';
			exit;
		}
		echo '{"success":"'.$email.'"}';
		exit;
	break;
	
	case "redeem":
		$login = new Login();
		if (!$login->isLoggedIn()) {
			echo '{"session":"expired"}';
			exit;
		}
		$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
		$ss->api_url = SS_API_URL;
		$user = $login->getUser($_SESSION['user_name']);
		$full_name = $user->first_name." ".$user->last_name;
		$rewards = $ss->listCampaignRewards(array('campaign_id'=>USERS_CAMPAIGN));
		foreach($rewards['rewards']['reward'] as $reward){
			if($reward['id']==$_POST['reward']){
				$reward_name = $reward['description'];
			}
		}
		$reward_id = $_POST['reward'];
		$data['code'] = $user->user_code;
		$data['campaign_id'] = USERS_CAMPAIGN;
		$data['reward_to_redeem'] = $reward_id;
		//$data['authorization'] = $reward_name;
		$result = $ss->redeemTransaction($data,'points');
		$vars = json_decode($result,true);
		if($vars['@attributes']['status']=='success'){
			$base_url = BASE_URL;
		
			$subject = '*** '.$full_name.' just redeemed: '.$reward_name;
			$email_template = file_get_contents('templates/vendor.template.php');
			$rarray = array('{vendor}','{company}','{name}','{url}','{base_url}');
			$varray = array(VENDOR_NAME,RESORT_NAME,$name,'https://rewards.clienttoolbox.com',$base_url);
			$email_template = str_replace($rarray,$varray,$email_template);
			addEmail($user_id,RESORT_EMAIL,VENDOR_NAME,VENDOR_EMAIL,$subject,$email_template);
			if(ENABLE_MANDRILL):
				sendMandrill($email_template,VENDOR_EMAIL,VENDOR_NAME,$subject,RESORT_EMAIL);
			else:
				mailer(RESORT_EMAIL,VENDOR_EMAIL,$subject,$email_template,VENDOR_NAME);
			endif;
			
			$subject = '*** '.$full_name.' Congratulations on Redeeming: '.$reward_name;
			$email_template = file_get_contents('templates/redeem.template.php');
			$rarray = array('{company}','{company_phone}','{name}','{reward}','{base_url}');
			$varray = array(RESORT_NAME,RESORT_PHONE,$name,$reward_name,$base_url);
			$email_template = str_replace($rarray,$varray,$email_template);
			addEmail($user_id,$_SESSION['user_name'],$full_name,RESORT_EMAIL,$subject,$email_template);
			if(ENABLE_MANDRILL):
				sendMandrill($email_template,RESORT_EMAIL,$full_name,$subject,$_SESSION['user_name']);
			else:
				mailer($_SESSION['user_name'],RESORT_EMAIL,$subject,$email_template,$full_name);
			endif;
			//$vars['receipt'] //Receipt
			echo '{"success":"true"}';
			exit;
			
		} elseif($vars['@attributes']['status']=='error'){
			echo '{"error":"'.$vars['error'].'"}';
			exit;
		}
	break;
	
	case "purchase":
		
		$login = new Login();
		if (!$login->isLoggedIn()) {
			echo '{"session":"expired"}';
			exit;
		}
		$user = getUserInfo($_SESSION['user_name']);
		require_once('classes/class.payflow.php');
		
		$PayFlow = new PayFlow(PAYPAL_VENDOR, PAYPAL_PARTNER, PAYPAL_USER, PAYPAL_PASS, 'single');

		$PayFlow->setEnvironment(PAYPAL_TEST); // test or live
		$PayFlow->setTransactionType('S'); // S = Sale transaction, R = Recurring, C = Credit, A = Authorization, D = Delayed Capture, V = Void, F = Voice Authorization, I = Inquiry, N = Duplicate transaction
		$PayFlow->setPaymentMethod('C'); // A = Automated clearinghouse, C = Credit card, D = Pinless debit, K = Telecheck, P = PayPal.
		$PayFlow->setPaymentCurrency(PAYPAL_CURRENCY); // 'USD', 'EUR', 'GBP', 'CAD', 'JPY', 'AUD'.

		$PayFlow->setAmount($_POST['amount'], FALSE);
		
		$PayFlow->setCCNumber($_POST['card_num']);
		$PayFlow->setCVV($_POST['cvv']);
		$PayFlow->setExpiration($_POST['expires_month'].$_POST['expires_year']);
		$PayFlow->setCreditCardName($_POST['card_name']);

		if(!empty($_POST['address2'])) $address = $_POST['address1'].' '.$_POST['address2']; else $address = $_POST['address1'];

		$PayFlow->setCustomerFirstName($_POST['first_name']);
		$PayFlow->setCustomerLastName($_POST['last_name']);
		$PayFlow->setCustomerAddress($address);
		$PayFlow->setCustomerCity($_POST['city']);
		$PayFlow->setCustomerState($_POST['state']);
		$PayFlow->setCustomerZip($_POST['postal_code']);
		$PayFlow->setCustomerCountry($_POST['country']);
		//$PayFlow->setCustomerPhone('212-123-1234');
		$PayFlow->setCustomerEmail($_POST['email']);
		$PayFlow->setPaymentComment('Purchase of '.$_POST['amount'].' Points');

		if($PayFlow->processTransaction()):
			$resp = $PayFlow->getResponse();
			
			print_r($PayFlow->debugNVP('array'));
			
			$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
			$ss->api_url = SS_API_URL;
			$data = array(
			 	'amount'=>$_POST['amount'],
			 	'code'=>$user->user_code,
				'campaign_id'=>USERS_CAMPAIGN
			);
			$result2 = $ss->recordTransaction($data,'points');
			
  			echo '{"success":"You successfully purchased '.$_POST['amount'].' points."}';
			exit;
		else:
			$resp = $PayFlow->getResponse();
  			echo '{"error":"An Error Occurred. '.$resp['RESPMSG'].'."}';
			exit;
		endif;
		unset($PayFlow);
	break;
	
	case "lead":
		try{
			require_once("config/db.php");
			$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
			$ss->api_url = SS_API_URL;
			if(isValidEmail($_POST['email']) && isValidEmail($_POST['customer_username']) && strtolower($_POST['email'])==strtolower($_POST['customer_username'])) $email_check = 'valid'; else $email_check = 'invalid';
			
			if($email_check=='invalid'){
				echo '{"email_check":"' . $email_check . '"}';
				exit;
			}
			if($_POST['customer_password']==$_POST['customer_password_verify']) $pass_check = 'valid'; else $pass_check = 'invalid';
			if($pass_check=='invalid'){
				echo '{"pass_check":"' . $pass_check . '"}';
				exit;
			}
			
			if(ENABLE_WUFOO):
			$wrapper = new WufooApi(WUFOO_API, WUFOO_SUBDOMAIN);
			$result = $wrapper->entryPost(WUFOO_SURVEY_FORM,$_POST);
			if($result->Success==0 && !empty($result->FieldErrors)){
				$err_arr = array();
				
				foreach($result->FieldErrors as $key => $err){
					$eid = $err->ID;
					$e = $err->ErrorText;
					$err_arr[] = $e;
				}
				$err_msg = implode("<br>",$err_arr);
				$return_json = '{"error":"'.$field_array[$eid].':'.$err_msg.'"}';
				echo $return_json;
				exit;
			} elseif($result->Success==0 && !empty($result->ErrorText)){
				$return_json = '{"error":"'.$result->ErrorText.'"}';
				echo $return_json;
				exit;
			}
			endif;
			
			if(ENABLE_CHIMP):
			$mc = new MCAPI(CHIMP_API,true);
			$mc->listSubscribe(CHIMP_LEAD_LIST,$_POST['email']);
			endif;
			
			$address = trim($_POST['street1'])." ".trim($_POST['street2']);
			$report = pullCredit(trim($_POST['first_name']),trim($_POST['last_name']),'',trim($address),trim($_POST['city']),trim($_POST['state']),trim($_POST['postal_code']),'000000000');
						
			$credit_id = $report['Credit ID'];	
			if(trim($report['Status'])=='NOTREADY') $wait=true; else $wait=false;
			if(TEST_MODE) $wait=false;
			while($wait):
				set_time_limit(0);
				sleep(6);
				$report = pullCredit(trim($_POST['first_name']),trim($_POST['last_name']),'',trim($address),trim($_POST['city']),trim($_POST['state']),trim($_POST['postal_code']),'000000000',trim($credit_id));
				
				if($report['Status']=='NOTREADY') $wait=true; else $wait=false;
			endwhile;
			
			$user = getUserInfo($_POST['custom_field_7']);
			$income_response = pullIncome(trim($_POST['postal_code']),$user->user_id);
			
			$_POST['creditid'] = $credit_id;
			if(empty($_POST['custom_field_6'])) $_POST['custom_field_6'] = $income_response['census']['hhIncome'];
			if(empty($_POST['custom_field_3'])) $_POST['custom_field_3'] = $report['Marital Status'];
			$_POST['custom_field_9'] = $report['Credit Report'];
			$_POST['custom_field_10'] = $report['Public Records'];
			$_POST['custom_field_11'] = $report['Negative Trades'];
			$_POST['custom_field_12'] = $report['Revolving Trades'];
			$_POST['custom_field_13'] = $report['Collections'];
			$_POST['custom_field_14'] = $report['Installment Trades'];
			$_POST['custom_field_15'] = $report['Tradelines'];
			$_POST['custom_field_16'] = $report['Historic Neg Occurrences'];
			$_POST['custom_field_17'] = $report['Mortgage Trades'];
			$_POST['custom_field_18'] = $report['Employer History'];
			$_POST['custom_field_19'] = $report['SSN'];
			$_POST['custom_field_22'] = $report['Employer'];
			$_POST['custom_field_23'] = $report['Employment Position'];
			

			$result = $ss->newCustomer('new',$_POST);
			$vars = json_decode($result,true);
			
			if($vars['@attributes']['status']=='success'){
				$_POST['code'] = $vars['customer']['code'];
				$dl = new DestinationLifestyles();
				$activation = $dl->getKey();
				$key = $activation['activationcode']['ActivationCode'];
				$base_url = BASE_URL;
				$cust_name = $_POST['first_name']." ".$_POST['last_name'];
				$subject = '*** '.$cust_name.' Congratulations! Your vacation is right around the corner.';
				$email_template = file_get_contents('templates/lead.template.php');
				$rarray = array('{company}','{company_phone}','{cust_name}','{base_url}','{activation_code}');
				$varray = array(RESORT_NAME,RESORT_PHONE,$cust_name,$base_url,$key);
				$email_template = str_replace($rarray,$varray,$email_template);
				addEmail($user_id,$_POST['email'],RESORT_NAME,RESORT_EMAIL,$subject,$email_template);
				if(ENABLE_MANDRILL):
					sendMandrill($email_template,RESORT_EMAIL,RESORT_NAME,$subject,$_POST['email']);
				else:
					mailer($_POST['email'],RESORT_EMAIL,$subject,$email_template,RESORT_NAME);
				endif;

				if(!empty($_POST['custom_field_7'])):
					$user = getUserInfo($_POST['custom_field_7']);
					$base_url = BASE_URL;
					$user_code = $user->user_code;
					$user_id = $user->user_id;
					$ref_cnt = getJoinedInvites($user_id);					
					$name = $user->first_name." ".$user->last_name;
					$subject = '*** '.RESORT_NAME.' you have another hot lead from '.VENDOR_NAME.'!';
					$email_template = file_get_contents('templates/referral.template.php');
					$rarray = array('{vendor}','{vendor_phone}','{company}','{name}','{url}','{base_url}');
					$varray = array(VENDOR_NAME,VENDOR_PHONE,RESORT_NAME,$name,VENDOR_URL,$base_url);
					$email_template = str_replace($rarray,$varray,$email_template);
					addEmail($user_id,RESORT_EMAIL,VENDOR_NAME,VENDOR_EMAIL,$subject,$email_template);
					if(ENABLE_MANDRILL):
						sendMandrill($email_template,VENDOR_EMAIL,VENDOR_NAME,$subject,RESORT_EMAIL);
					else:
						mailer(RESORT_EMAIL,VENDOR_EMAIL,$subject,$email_template,VENDOR_NAME);
					endif;
					$data = array(
						'promo_id'=>'7007',
						'send_transaction_email'=>'Y',
						'code'=>$user_code,
						'authorization'=>$_POST['email'],
						'campaign_id'=>USERS_CAMPAIGN
					);
					$ss->recordTransaction($data,'points');
					unset($data);
					if($_POST['fb']==1){ //!checkCampaign('3742237909218557',$user_code)
					 $data = array(
					 	'code'=>$user_code,
					 	'amount'=>'1',
						'campaign_id'=>'3742237909218557'
					 );
					 $result = $ss->recordTransaction($data,'points');
					 unset($data);
					 $data = array(
					 	'promo_id'=>'7713',
					 	'code'=>$user_code,
						'campaign_id'=>USERS_CAMPAIGN
					 );
					 $result2 = $ss->recordTransaction($data,'points');
					 unset($data);
					}
					if($ref_cnt==5 && $_POST['fb']==1){ //!checkCampaign('5760331892766655',$user_code)
					 $data = array(
					 	'code'=>$user_code,
					 	'amount'=>'1',
						'campaign_id'=>'5760331892766655'
					 );
					 $result = $ss->recordTransaction($data,'points');
					 unset($data);
					}
					updateInvite($user_id,$_POST['email'],$cust_name,$_POST['phone']);
				endif;
				$return_json = '{"success":"TRUE"}';
				echo $return_json;
				exit;
			} elseif($vars['@attributes']['status']=='error'){
				$return_json = '{"error":"'.$vars['error'].'"}';
				echo $return_json;
				exit;
			}
		} catch(Exception $e) {
			$return_json = '{"error":"'.$e->getMessage().'"}';
			echo $return_json;
			exit;
		}
	break;
	
	case "logout":
		
		$facebook = new Facebook(array(
    		'appId'  => FB_APP_ID,
    		'secret' => FB_APP_SECRET
		));
		$config = array(
    		'scope'    	   => 'email',
    		'display'	   => 'popup',
		);
		$fb = new SimpleFacebook($facebook, $config);
		if($fb->isLogged()) $fb->logout();
	
		$login = new Login();
		$login->logout();
		header("Location: index.php");
	break;
	
	case "contacts":
		
		include('inviter/openinviter.php');
		$inviter=new OpenInviter();
		$ers=array();$oks=array();

		if (empty($_POST['email_box'])) $ers['email']="Email Inactive !";
		if (empty($_POST['password_box'])) $ers['password']="Password Inactive !";
		if (empty($_POST['provider_box'])) $ers['provider']="Provider Inactive !";
		if (count($ers)==0) {
			$inviter->startPlugin($_POST['provider_box']);
			$internal=$inviter->getInternalError();
			if ($internal) {
				$ers['error']=$internal;
				$ers['email_box']=$_POST['email_box'];
				$ers['provider_box']=$_POST['provider_box'];
			} elseif (!$inviter->login($_POST['email_box'],$_POST['password_box'])) {
				$internal=$inviter->getInternalError();
				$ers['error']=($internal?$internal:"Login failed. Please check the email and password you have provided and try again later !");
				$ers['email_box']=$_POST['email_box'];
				$ers['provider_box']=$_POST['provider_box'];
			} elseif (false===$contacts=$inviter->getMyContacts()){
				$ers['error']="Unable to get contacts !";
				$ers['email_box']=$_POST['email_box'];
				$ers['provider_box']=$_POST['provider_box'];
			}else{
				session_start();
				$oks['oi_session_id']=$inviter->plugin->getSessionID();
				$oks['email_box']=$_POST['email_box'];
				$oks['provider_box']=$_POST['provider_box'];
				$_SESSION['contacts']=$contacts;
			}
		}
		if(empty($ers)){
			header("Location: bonus.php?view=invites&step=send_invites&oks=".base64_encode(serialize($oks)));
			exit;
		} else {
			header("Location: bonus.php?view=invites&step=get_contacts&ers=".base64_encode(serialize($ers)));
			exit;
		}
	
	break;
	case "resend_email":
	
		$login = new Login();
		if (!$login->isLoggedIn()) {
			header("Location: index.php");
			exit;
		}
		$url = BASE_URL.'survey.php?uid='.$_SESSION['user_name'];
		$subject = '*** Do not miss this opportunity '.$_POST['name'];
		$email_template = file_get_contents('templates/followup.template.php');
		$rarray = array('{company}','{company_phone}','{name}','{url}','{base_url}');
		$varray = array(RESORT_NAME,RESORT_PHONE,$_POST['name'],$url,BASE_URL);
		$email_template = str_replace($rarray,$varray,$email_template);
		
		if(ENABLE_MANDRILL){
			if(sendMandrill($email_template,$_SESSION['user_name'],RESORT_NAME,$subject,$_POST['email'])) $sendSuccess = true; else $sendSuccess = false;
		}else{
			if(mailer($_POST['email'],$_SESSION['user_name'],$subject,$email_template,RESORT_NAME)) $sendSuccess = true; else $sendSuccess = false;
		}
		if($sendSuccess){
			header("Location: bonus.php?view=myinvites&msg=".urlencode(htmlentities('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Message was resent successfully.</div>')) );
			exit;
		} else {
			header("Location: bonus.php?view=myinvites&msg=".urlencode(htmlentities('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Error: Message was NOT resent.</div>')) );
			exit;
		}
	
	break;
	case "send_invites":
		
		$login = new Login();
		if (!$login->isLoggedIn()) {
			echo '{"session":"expired"}';
			exit;
		}
		$user = getUserInfo($_SESSION['user_name']);
		$user_id = $user->user_id;
		$e_arr = array();
		include('inviter/openinviter.php');
		$inviter=new OpenInviter();
		$oi_services=$inviter->getPlugins();
		if (empty($_POST['provider_box'])) $ers['provider']='Provider Inactive !';
		else
			{
			$inviter->startPlugin($_POST['provider_box']);
			$internal=$inviter->getInternalError();
			if ($internal) $ers['internal']=$internal;
			else
				{
				if (empty($_POST['email_box'])) $ers['inviter']='Inviter information Inactive !';
				if (empty($_POST['oi_session_id'])) $ers['session_id']='No active session !';
				//if (empty($_POST['message_box'])) $ers['message_body']='Message Inactive !';
				else $_POST['message_box']=strip_tags($_POST['message_box']);
				$selected_contacts=array();$contacts=array();
				$message=array('subject'=>$inviter->settings['message_subject'],'body'=>$inviter->settings['message_body'],'attachment'=>"\n\rAttached message: \n\r".$_POST['message_box']);
				if ($inviter->showContacts())
					{
					foreach ($_POST as $key=>$val)
						if (strpos($key,'check_')!==false)
							$selected_contacts[$_POST['email_'.$val]]=$_POST['name_'.$val].'|'.$_POST['phone_'.$val];
						elseif (strpos($key,'email_')!==false)
							{
							$temp=explode('_',$key);$counter=$temp[1];
							if (is_numeric($temp[1])) $contacts[$val]=$_POST['name_'.$temp[1]].'|'.$_POST['phone_'.$temp[1]];
							}
					if (count($selected_contacts)==0) $ers['contacts']="You haven't selected any contacts to invite !";
					}
				}
			}
		if (count($ers)==0) {
			$sendMessage=$inviter->sendMessage($_POST['oi_session_id'],$message,$selected_contacts);
			$inviter->logout();
			if ($sendMessage===-1){
				foreach ($selected_contacts as $email=>$cname){
				
					if(checkUser($email) || checkInvites($email)){
						
						$e_arr[] = $email;
						
					} else {
					
						$name = $_POST['sender'];
						$url = $_POST['url'];
						if(!empty($cname)) {
							if(strpos($cname,'|')){
								$cname = explode('|',$cname);
							}
							$url .= '&cname='.$cname[0].'&phone='.$cname[1];
						}
						$url .= '&email='.$email;
						if(addInvite($user_id,$email,$cname[0],$cname[1])){
							$message = $_POST['message_box'];
							$subject = '*** Your friend '.$name.' has invited you to check this out.';
							$email_template = file_get_contents('templates/email.template.php');
							$rarray = array('{company}','{vendor}','{vendor_phone}','{name}','{url}','{base_url}','{message}');
							$varray = array(RESORT_NAME,VENDOR_NAME,VENDOR_PHONE,$name,$url,BASE_URL,$message);
							$email_template = str_replace($rarray,$varray,$email_template);
							addEmail($user_id,$email,RESORT_NAME,RESORT_EMAIL,$subject,$email_template);
							if(ENABLE_MANDRILL):
								sendMandrill($email_template,RESORT_EMAIL,RESORT_NAME,$subject,$email);
							else:
								mailer($email,RESORT_EMAIL,$subject,$email_template,RESORT_NAME);
							endif;
						} else {
							header("Location: bonus.php?view=invites&step=get_contacts&error=There were errors while adding the invite email to the database. DB Insert: ".$user_id.",".$email.",".$cname[0].",".$cname[1]);
							exit;
						}
						
					}
				}
				$_SESSION['existing'] = $e_arr;
				if(empty($e_arr)){
					header("Location: bonus.php?view=invites&step=get_contacts&success=1&errors=0");
					exit;
				} elseif(count($e_arr)>1) {
					header("Location: bonus.php?view=invites&step=get_contacts&success=1&errors=1");
					exit;
				} else {
					header("Location: bonus.php?view=invites&step=get_contacts&error=The email address already exists in our database. Please choose a different email address to invite.");
					exit;
				}
			} elseif ($sendMessage===false) {
				header("Location: bonus.php?view=invites&step=get_contacts&error=There were errors while sending your invites.<br>Please try again later!");		
				exit;
			} else {
			 	header("Location: bonus.php?view=invites&step=get_contacts&success=1");
				exit;
			}
		}
		echo '{"error","'.json_encode($ers).'"}';
		exit;
	
	break;
	case "videostatus":
		
		$status = $stupeflix->getStatus();
		echo $status;
		
	break;
	case "videomailer":
	
		$login = new Login();
		if (!$login->isLoggedIn()) {
			header("Location: index.php");
			exit;
		}
		$user_name = $_SESSION['user_name'];
		$user = getUserInfo($user_name);
		$uid = $user->user_id;
		
		/* PARAMETERS */
		parse_str($_GET['url']);
		$success_url = "bonus.php?view=myvideos&success=1";
		$from_email = RESORT_EMAIL;
		$name = $_POST['sender'];
		//$turl = $_POST['thumb'];
		//$vid_name = $_POST['vid_name'];
		//$vid_url = $_POST['vid_url'];
		//$cname = $_POST['name'];
		$email = $_POST['email'];
		$phone = $_POST['phone'];
		$message = $_POST['msg'];


		$subject = '*** Your friend '.$name.' has invited you to check this out.';
		$email_template = file_get_contents('templates/video.template.php');
		
		$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
		$ss->api_url = SS_API_URL;

		/* DO NOT EDIT BELOW */
		if(ENABLE_MANDRILL):

			if(isValidEmail(trim($email))){
			
				if(addVideoInvite($uid,trim($email),trim($cname),trim($phone))){
					$cname = explode(" ",$cname);
					$result = $ss->newCustomer('new',array('campaign_id'=>LEADS_CAMPAIGN,'first_name'=>$cname[0],'last_name'=>$cname[1],'email'=>$email,'phone'=>$phone,'custom_field_7'=>$_SESSION['user_name']));
				
					$url = BASE_URL.'video.php?uid='.$_SESSION['user_name'].'&email='.$email.'&vid_name='.$vid_name.'&turl='.$tmb_url.'&vid_url='.$vid_url.'&msg='.$message;

					if(!empty($cname)) $url = $url.'&cname='.$cname[0].' '.$cname[1].'&phone='.$phone;

					$rarray = array('{customer}','{company}','{vendor}','{name}','{url}','{base_url}','{message}');
					$varray = array($cname[0].' '.$cname[1],RESORT_NAME,VENDOR_NAME,$name,$url,BASE_URL,$message);
					$email_template = str_replace($rarray,$varray,$email_template);
				
					addEmail($user_id,$email,RESORT_NAME,$from_email,$subject,$email_template);
					if(sendMandrill($email_template,$from_email,RESORT_NAME,$subject,$email)) $sendSuccess = true; else $sendSuccess = false;
				} else {
					header("Location: bonus.php?view=myvideos&error=".urlencode(htmlentities('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>An error occurred. No invites were sent.</div>')));
					exit;
				}
			} else {
				header("Location: bonus.php?view=myvideos&error=".urlencode(htmlentities('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>The email address your trying to send to is invalid.</div>')));
			}
			if($sendSuccess) {
				header("Location: bonus.php?view=myvideos&msg=".urlencode(htmlentities('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Video Email Share was sent successfully.</div>')));
				exit;
			} else {
				header("Location: bonus.php?error=".urlencode(htmlentities('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>An error occurred. No invites were sent.</div>')));
				exit;
			}

		else:

			include('classes/class.phpmailer.php');
			$msg = new PHPMailer(true);
			try{
	
				$msg->From = $from_email;
				$msg->FromName = RESORT_NAME." <".$from_email.">";
				$msg->AddReplyTo($from_email,RESORT_NAME);
				$msg->AddAddress(NO_REPLY,VENDOR_NAME);
				
				if(isValidEmail(trim($email))){
					if(addInvite($uid,trim($email),trim($cname),trim($phone))){
						$cname = explode(" ",$cname);
						$result = $ss->newCustomer('new',array('campaign_id'=>LEADS_CAMPAIGN,'first_name'=>$cname[0],'last_name'=>$cname[1],'email'=>$email,'phone'=>$phone,'custom_field_7'=>$_SESSION['user_name']));
						$msg->AddBCC($email);
				
						if(!empty($cname)) $url = $url.'&cname='.$cname.'&phone='.$phone;
						$url .= '&email='.$email.'&vid_url='.$vid_url.'&vid_name='.$vid_name.'&turl='.$turl;
				
						$rarray = array('{customer}','{company}','{vendor}','{name}','{url}','{base_url}','{message}');
						$varray = array($cname[0].' '.$cname[1],RESORT_NAME,VENDOR_NAME,$name,$url,BASE_URL,$message);
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
						header("Location: bonus.php?view=myvideos&error=".urlencode('An error occurred. No invites were sent.'));
					}
				} else {
					header("Location: bonus.php?view=myvideos&error=".urlencode('The email address your trying to send to is invalid.'));
				}
				if($sendSuccess) {
					addEmail($user_id,$email,RESORT_NAME,$from_email,$subject,$email_template);
					header("Location: bonus.php?view=myvideos&msg=".urlencode('Video Email Share was sent successfully.'));
				} else {
					header("Location: bonus.php?error=".urlencode('An error occurred. No invites were sent.'));
				}

			} catch (phpmailerException $e) {
  				echo $e->errorMessage(); //Pretty error messages from PHPMailer
			} catch (Exception $e) {
  				echo $e->getMessage(); //Boring error messages from anything else!
			}

		endif;
	
	break;

}

} else {
	header('HTTP/1.1 404 Not Found');
	exit;
}
?>