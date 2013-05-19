<?php
include_once('classes/ss.class.php');
include_once('classes/login.class.php');
$login = new Login();

//$url = urlencode($_GET['url']);
$cname = $_GET['cname'];
$phone = $_GET['phone'];
$email = $_GET['email'];
$referrer = $_GET['uid'];
$social = $_GET['s'];

switch($social){
	case "facebook":
	
		$url = BASE_URL.'fb-invite.php?uid='.$uid.'&email='.$email.'&cname='.$cname.'&phone='.$phone.'&url='.$url;
		if($lead) $url = ''; else $url;
		$share_url = BASE_URL.'share.php?uid='.$uid.'&email='.$email.'&cname='.$cname.'&phone='.$phone;
		
		$url = $login->shortUrl($url);
		header('Location: http://www.facebook.com/sharer.php?u='.$url['id']);
		exit();
		
	break;
	case "twitter":
	
		
	
	break;
	default:
		
		header("HTTP/1.0 404 Not Found");
		exit;
			
	break;
}
?>