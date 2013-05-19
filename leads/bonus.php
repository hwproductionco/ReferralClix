<?php
error_reporting(E_ERROR);
ini_set('display_errors', '1');

session_start();

include_once('classes/login.class.php');
include_once('classes/ss.class.php');
include_once('classes/class.payflow.php');
include_once('classes/facebook/facebook.php');
include_once('classes/class.facebook.php');
include_once('inviter/openinviter.php');

$inviter=new OpenInviter();
$inviter->showContacts = TRUE;

if($_GET['errors']==1){
 $e_arr = $_SESSION['existing'];
 unset($_SESSION['existing']);
}

$view = empty($_GET['view']) ? 'dashboard' : $_GET['view'];

if(!empty($_GET['step'])){ 
	$step = trim($_GET['step']);
	$active = true;
} else {
	$step = 'get_contacts'; 
	$active = false;
}
!empty($_GET['ers']) ? $ers = unserialize(stripslashes(base64_decode($_GET['ers']))) : $ers = '';
!empty($_GET['oks']) ? $oks = unserialize(stripslashes(base64_decode($_GET['oks']))) : $oks = '';

$login = new Login();
if (!$login->isLoggedIn()) header("Location: index.php");
$user = $login->getUser();

$invites = getInvites($user->user_id);
$videos = getVideos($user->user_id);
$joined_cnt = getJoinedInvites($user->user_id);
$invites_cnt = getPendingInvites($user->user_id);

$user_name = $_SESSION['user_name'];
$post = array('code'=>$user->user_code);
$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
$ss->api_url = SS_API_URL;
$info = $ss->getCustomerInfo($post);

$rewards = $ss->listCampaignRewards(array('campaign_id'=>USERS_CAMPAIGN));
$promos = $ss->listCampaignPromotions(array('campaign_id'=>USERS_CAMPAIGN));

$referrals = getReferrals();
//print_r($promos);
//print_r($info);
$balance = 0;
$cumulative = 0;
if(empty($info['campaigns']['campaign']['id'])){
	foreach($info['campaigns']['campaign'] as $c){
		if(USERS_CAMPAIGN==$c['id']){
			$balance += $c['balance'];
			foreach($rewards['rewards']['reward'] as $rw){
				if($rw['level']<=$balance){
					$reward_array[$c['id']] += 1; 
				}
			}
			
			$cumulative += $c['cumulative'];
		}
	}
} else {
	$balance = $info['campaigns']['campaign']['balance'];
	foreach($rewards['rewards']['reward'] as $rw){
		if($rw['level']<=$balance){
			$reward_array[$info['campaigns']['campaign']['id']] += 1; 
		}
	}
	
	$cumulative = $info['campaigns']['campaign']['cumulative'];	
}


$url = BASE_URL.'survey.php?uid='.$user_name;
$short_url = $login->shortUrl($url);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <title>Owner Referrals</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/font-awesome.css">
        <link rel="stylesheet" href="css/bootstrap.icon-large.min.css">
		
		<link rel="stylesheet" href="http://releases.flowplayer.org/5.3.2/skin/minimalist.css"/>
		<!--<link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet">-->
        
        <!--[if lt IE 9]>
            <script src="js/html5-3.6-respond-1.1.0.min.js"></script>
            <script language="javascript" type="text/javascript" src="js/excanvas.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <i class="icon icon-reorder"></i> Menu
                    </a>
                    <a class="brand" href="index.php"><?=RESORT_NAME;?></a>
                    <div class="nav-collapse collapse">
                        <ul class="nav">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="#about">About</a></li>
                            <li><a href="#contact">Contact</a></li>
                        </ul>
                        <a href="index.php" class="btn btn-danger pull-right">Register / Login</a>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
            <div class="sub-navbar">
                <div class="container">
                    <p><i class="icon icon-user"></i> Hello, <?php echo $info['customer']['first_name'].' '.$info['customer']['last_name'];?>. Thanks for logging in. <a href="https://www.facebook.com/resortloyalty/" title="Like us on Facebook" class="pull-right"><img src="assets/fb.png" alt="Facebook" /></a></p>
                </div>
            </div>
        </div>

        <div class="container wrapper">
            <div class="row">
                <div class="span3">
                    <ul class="nav nav-tabs nav-stacked" id="main-menu">
                        <li<? if($view=="dashboard") echo ' class="active"';?>><a href="bonus.php"><i class="icon icon-home"></i> Dashboard</a></li>
                        <li id="inviteLink">
                        	<a href="#" data-target="#reg_invite" class="accordion-toggle" data-parent="#inviteLink"><i class="icon icon-check"></i> Inviter</a>
                        	<ul class="nav nav-tabs nav-stacked<? if($view!="invites" && $view!="myinvites") echo ' collapse';?>" id="reg_invite">
                        		<li<? if($view=="invites") echo ' class="active"';?>><a href="bonus.php?view=invites"><i class="icon icon-check"></i> Refer Someone</a></li>
                        		<!--<li<? if($view=="facebook") echo ' class="active"';?> id="facebookLink"><a href="https://www.facebook.com/resortloyalty/app_207451179363944" target="_blank"><i class="icon icon-thumbs-up"></i> Facebook Inviter</a></li>-->
            					<li<? if($view=="myinvites") echo ' class="active"';?>><a href="bonus.php?view=myinvites"><i class="icon icon-user"></i> My Invites</a></li>
                        	</ul>
                        </li>
                        <li id="menu1">
                        	<a href="#" data-target="#vid_invite" class="accordion-toggle" data-parent="#menu1"><i class="icon icon-facetime-video"></i> Video Inviter</a>
                        	<ul class="nav nav-tabs nav-stacked<? if($view!="videohome" && $view!="myvideos") echo ' collapse';?>" id="vid_invite">
                        		<li<? if($view=="videohome") echo ' class="active"';?>><a href="bonus.php?view=videohome"><i class="icon icon-facetime-video"></i> Make a Video</a></li>
            					<li<? if($view=="myvideos") echo ' class="active"';?>><a href="bonus.php?view=myvideos"><i class="icon icon-user"></i> My Videos</a></li>
                        	</ul>
                        </li>
                        <li<? if($view=="rewards") echo ' class="active"';?> id="rewardsLink"><a href="bonus.php?view=rewards"><i class="icon icon-certificate"></i> Rewards</a></li>
                        <li<? if($view=="promos") echo ' class="active"';?>><a href="bonus.php?view=promos"><i class="icon icon-tags"></i> Promotions</a></li>
                        <li><a href="process.php?action=logout&logout=1"><i class="icon icon-signout"></i> Log out</a></li>
                    </ul><!--/nav-stacked menu-->
                    <hr>
                    <p class="hidden-phone"><strong>WHAT ARE BONUS POINTS?</strong><br />Bonus Points are our way of saying thank you for spreading the love and sharing your experience with our club! For doing that, you and your friends/family will earn amazing rewards. No gimmicks, No Fuss!</p>
                    <hr class="hidden-phone" />
                    <h2>Top Referrers</h2>
                    <table>
                    <thead>
                    	<tr>
                    		<th align="left" class="span2">Name</th><th align="right">Referrals</th>
                    	</tr>
                    </thead>
                    <tbody>
                    	<?php 
                    	foreach($referrals as $ref){
                    		$ref_u = getUserByID($ref['0']);
                    		echo '<tr><td class="span2">'.$ref_u['1'].' '.$ref_u['2'].'</td><td class="span1">'.$ref['1'].'</td></tr>';
                    	}
                    	?>
                    </tbody>
                    </table>
                	
                	
      
                </div><!--/span3 column-->

                <div class="span9 content">
                	
                	<?php
                	switch($view){
                	
                	case "dashboard":
                	
                	foreach($rewards['rewards']['reward'] as $r){
						if($balance>$r['level']) $has_rewards = true;
					}
                	?>
                    <div class="banner">
                        <h2>Welcome back, <?php echo $info['customer']['first_name'];?>!</h2>
                        <p>The more people you refer the more insane rewards you will earn. We believe in a little give and take especially on the giving side.</p>
                    </div>
                     
                     
                    <div class="row">
                    	<div class="span5">
                    	
                    <table class="table">
                    <thead>
                    	<tr>
                    		<th style="background: #b4ddb4; /* Old browsers */
background: -moz-linear-gradient(top,  #b4ddb4 0%, #83c783 17%, #52b152 33%, #008a00 67%, #005700 83%, #002400 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#b4ddb4), color-stop(17%,#83c783), color-stop(33%,#52b152), color-stop(67%,#008a00), color-stop(83%,#005700), color-stop(100%,#002400)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #b4ddb4 0%,#83c783 17%,#52b152 33%,#008a00 67%,#005700 83%,#002400 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #b4ddb4 0%,#83c783 17%,#52b152 33%,#008a00 67%,#005700 83%,#002400 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #b4ddb4 0%,#83c783 17%,#52b152 33%,#008a00 67%,#005700 83%,#002400 100%); /* IE10+ */
background: linear-gradient(to bottom,  #b4ddb4 0%,#83c783 17%,#52b152 33%,#008a00 67%,#005700 83%,#002400 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b4ddb4', endColorstr='#002400',GradientType=0 ); /* IE6-9 */
border:1px solid #e0e0e0;padding:10px;color:#fff;font-size:1.5em;font-weight:normal;" colspan="2">Invite, Share and Earn Rewards</th>
                    	</tr>
                    </thead>
                    <tbody>
                    	<tr>
                    		<td style="text-align:center;padding:10px;border:1px solid #e0e0e0;background: #ffffff; /* Old browsers */
background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 99%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(99%,#e5e5e5)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* IE10+ */
background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 99%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
">
                    			<!--<a class="btn btn-danger btn-large btn-block rounded" data-toggle="collapse" style="margin-bottom:10px;cursor:hand;" data-target="#accordian">View Promotions and Rewards</a>-->
                    			 <div class="btn-group" style="padding-bottom:3px">
                    				<a href="bonus.php?view=invites" class="btn btn-success btn-large span2" style="padding-bottom:7%;padding-top:7%;margin:0;float:none;" title="" id="inviteBtn">Invite Friends<br />Now<br /><i class="icon icon-invite"></i></a><a href="bonus.php?view=videohome" class="btn btn-info btn-large span2" style="padding-bottom:7%;padding-top:7%;float:none;margin:0;" title="" id="videoBtn">Share a<br />Vacation Video<br /><i class="icon icon-video"></i></a>
                    			 </div>
                    	
                    		</td>
                    	</tr>
                    	</tbody>
                    	</table>
                    
                    	</div>
                    	<div class="span4">
                    	
                    <table class="table">
                    <thead>
                    	<tr>
                    		<th style="background: #b4ddb4; /* Old browsers */
background: -moz-linear-gradient(top,  #b4ddb4 0%, #83c783 17%, #52b152 33%, #008a00 67%, #005700 83%, #002400 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#b4ddb4), color-stop(17%,#83c783), color-stop(33%,#52b152), color-stop(67%,#008a00), color-stop(83%,#005700), color-stop(100%,#002400)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #b4ddb4 0%,#83c783 17%,#52b152 33%,#008a00 67%,#005700 83%,#002400 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #b4ddb4 0%,#83c783 17%,#52b152 33%,#008a00 67%,#005700 83%,#002400 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #b4ddb4 0%,#83c783 17%,#52b152 33%,#008a00 67%,#005700 83%,#002400 100%); /* IE10+ */
background: linear-gradient(to bottom,  #b4ddb4 0%,#83c783 17%,#52b152 33%,#008a00 67%,#005700 83%,#002400 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b4ddb4', endColorstr='#002400',GradientType=0 ); /* IE6-9 */
border:1px solid #e0e0e0;padding:10px;color:#fff;font-size:1.5em;font-weight:normal;" colspan="3">Rewards Status</th>
                    	</tr>
                    </thead>
                    <tbody>
                    	<tr>
                    		<td style="padding:10px;margin-left:0px;border:1px solid #e0e0e0;border-right:0;background: #ffffff; /* Old browsers */
background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 99%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(99%,#e5e5e5)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* IE10+ */
background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 99%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
padding-left:15px;"><i class="icon reward"></i> <span style="font-size:1.5em;margin-top:15px;display:block;">Rewards</span></td>
							<td style="padding:10px;margin-left:0px;background: #ffffff; /* Old browsers */
background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 99%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(99%,#e5e5e5)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* IE10+ */
background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 99%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
"><img src="images/sep.png" alt="" /></td>
                    		<td style="padding:10px;border:1px solid #e0e0e0;font-size:2em;border-left:0px;background: #ffffff; /* Old browsers */
background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 99%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(99%,#e5e5e5)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* IE10+ */
background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 99%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
"><?=(count($reward_array[USERS_CAMPAIGN])>0) ? $reward_array[USERS_CAMPAIGN] : 0;?></td>
                    	</tr>
                    	<tr>
                    		<td colspan="3" style="text-align:center;padding:10px;border:1px solid #e0e0e0;background: #ffffff; /* Old browsers */
background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 99%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(99%,#e5e5e5)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* IE10+ */
background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 99%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
">
                    			<?php
                    			if(count($reward_array[USERS_CAMPAIGN])>0):
                    			?><p style="font-size:1.6em;" align="center">You have rewards waiting for you</p><a href="bonus.php?view=rewards" class="btn btn-success">View Rewards</a><br /><a href="#" data-toggle="collapse" style="margin-top:5px;cursor:hand;" data-target="#accordian">View Promotions and Rewards</a>
                    			<?php 
                    			else:
                    			?>
                    			<p style="font-size:1.6em;" align="center">You have not earned any rewards yet</p><a href="bonus.php?view=invites" class="btn btn-success">Refer More Friends</a><br /><a href="#" data-toggle="collapse" style="margin-top:5px;cursor:hand;" data-target="#accordian">View Promotions and Rewards</a>
                    			<?php endif; ?>
                    		</td>
                    	</tr>
                    </tbody>
                    </table>
                    	</div>
                    </div>
                    
                    <div class="row mt15">
                     <div id="accordian" class="collapse out">
                     	<div class="span4">
                     		<table class="table table-striped">
         					<tr><td width="70%"><strong>Promotions</strong></td><td width="30%" align="center"><strong>Points</strong></td></tr>
         					<?php
         					foreach($promos['promotions']['promotion'] as $promotion){
         						echo '<tr><td>'.$promotion['description'].'</td><td align="center">'.$promotion['value'].'</td></tr>';
         					}
         					?>
         					</table>
         				</div>
         				<div class="span5">
         					<table class="table table-striped">
         					<tr><td width="70%"><strong>Rewards</strong></td><td width="30%" align="center"><strong>Points</strong></td></tr>
        					<?php
        					foreach($rewards['rewards']['reward'] as $reward){
        						echo '<tr><td>'.$reward['description'].'</td><td align="center">'.$reward['level'].' Points</div>';
							}
        					?>
        					</table>        					
         				</div>
         			 </div>
         			</div>
                    
                    
                    <h3>Your Details:</h3>
                    <div class="well display stats" style="padding:10px;border:1px solid #e0e0e0;background: #ffffff; /* Old browsers */
background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 99%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(99%,#e5e5e5)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 99%); /* IE10+ */
background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 99%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */">
                        <div class="row-fluid">
                            <div class="span3">
                                <h2><?=$joined_cnt;?></h2><br />
                                <small>Joined Invites</small>
                            </div>
                            <div class="span3">
                                <h2><?=$invites_cnt;?></h2><br />
                                <small>Pending Invites</small>
                            </div>
                            <div class="span3">
                                <h2><?=$balance;?></h2><br />
                                <small>Bonus Points Balance</small>
                            </div>
                            <div class="span3">
                                <h2><?=$cumulative;?></h2><br />
                                <small>Bonus Points Earned</small>
                            </div>
                        </div>
                    </div>

                    <h3>Account Activity:</h3>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amt</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
					$post = array(
						'campaign_id'=>USERS_CAMPAIGN,
						'code'=>$user->user_code,
						'transactions_number'=>11
					);
					$result = $ss->getCustomerHistory($post);
					$vars = json_decode($result,true);
					$x=0;
					//$trans = array_pop($vars['campaign']['customer']['transactions']);
					//if(count($trans)<2) $trans = array_pop($vars['campaign']['customer']['transaction']);
					if($vars['@attributes']['status']=='success'){
					 if(!empty($vars['campaign']['customer']['transactions']['transaction']['id'])){
						 foreach($vars['campaign']['customer']['transactions'] as $t){
						 	if($x!=count($vars['campaign']['customer']['transactions'])+1):
							if($t['redeemed']=='Y'){
								if($t['amount']==0){
									$type = '';
									$amt = '';
								} else {
									$type = 'Redeemed';
									$amt = $t['amount'];
								}
								$desc = empty($t['authorization']) ? '' : $t['authorization'];
								echo '<tr><td>'.$t['date'].'</td><td>'.$desc.'</td><td>'.$type.'</td><td>'.$amt.'</td></tr>';		
							} else {
								if($t['amount']==0){
									$type = '';
									$amt = '';
								} else {
									$type = 'Earned';
									$amt = $t['amount'];
								}
								$desc = empty($t['authorization']) ? '' : $t['authorization'];
								echo '<tr><td>'.$t['date'].'</td><td>'.$desc.'</td><td>'.$type.'</td><td>'.$amt.'</td></tr>';
							}
							endif;
							$x++;
						 }						
					 } else {
					 	foreach($vars['campaign']['customer']['transactions']['transaction'] as $t){
					 		if($x!=count($vars['campaign']['customer']['transactions'])+1):
							if($t['redeemed']=='Y'){
								if($t['amount']==0){
									$type = '';
									$amt = '';
								} else {
									$type = 'Redeemed';
									$amt = $t['amount'];
								}
								$desc = empty($t['authorization']) ? '' : $t['authorization'];
								echo '<tr><td>'.$t['date'].'</td><td>'.$desc.'</td><td>'.$type.'</td><td>'.$amt.'</td></tr>';		
							} else {
								if($t['amount']==0){
									$type = '';
									$amt = '';
								} else {
									$type = 'Earned';
									$amt = $t['amount'];
								}
								$desc = empty($t['authorization']) ? '' : $t['authorization'];
								echo '<tr><td>'.$t['date'].'</td><td>'.$desc.'</td><td>'.$type.'</td><td>'.$amt.'</td></tr>';
							}
							endif;
							$x++;
						 }	
					 }
					}
				?>
                        </tbody>
                    </table>
                 <?php
                 break;
                 case "promos": // PROMOTIONS --------------------------------------------------------------------------
                 ?>
                 
                 	<div class="banner">
                	<h2>Current Promotions</h2>
                	<p>We reward you for being you and staying social. View the current promotions below and see how you can gain more points.</p>
                	</div>
					<hr>
					 <div class="row">
					 <div class="span6">
                 	<table class="table table-striped">
         				<tr><td class="span4"><strong>Promotions</strong></td><td class="span2" align="center"><strong>Bonus Points</strong></td></tr>
         				<?php
         				foreach($promos['promotions']['promotion'] as $promotion){
         					echo '<tr><td width="200">'.$promotion['description'].'</td><td align="center">'.$promotion['value'].'</td></tr>';
         				}
         				?>
         			</table>
         			</div>
         			</div>
         			
                 <?php
                 break;
                 case "invites": // INVITER ----------------------------------------------------------------------------
              
                 ?>
         		
				<div class="banner">
                	<h2>Invite your Friends and Family</h2>
                	<p>Add Email Addresses individually or Import them using the tabs below.</p>
                </div>
				<hr>
				
				<?php 
				if($_GET['success']=='1' && empty($e_arr)) {
					echo '<div class="alert alert-success">Your invite(s) were sent successfully.</div>';
				}  elseif($_GET['success']==1 && !empty($e_arr)) {
					
					echo '<div class="alert alert-success">Some of your invite(s) were sent successfully. These emails were already invited: '.implode(",",$e_arr).'</div>';
				} 
				?>
  				<?php if($_GET['error']) echo '<div class="alert alert-error">'.$_GET['error'].'</div>'; ?>
				
				<div class="well">
       
                <ul class="nav nav-tabs">
    				<li<?php if(empty($active)) echo ' class="active"';?>><a href="#email" data-toggle="tab">Invite by Email</a></li>
    				<li<?php if($active) echo ' class="active"';?>><a href="#invite" data-toggle="tab">Email Contact Inviter</a></li>
    				<li><a href="https://www.facebook.com/resortloyalty/app_207451179363944" target="_blank">Invite by Facebook</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane<?php if(empty($active)) echo ' active';?>" id="email">
						<form method="post" action="<?=BASE_URL;?>mailer.php" name="sendInvite" id="inviteForm">
                			<input type="hidden" name="sender" value="<?php echo $info['customer']['first_name'].' '.$info['customer']['last_name'];?>" />
                			<input type="hidden" name="url" value="<?php if($url) echo $url;?>" />
                			<input type="hidden" name="action" value="sendinvite" />
                			<div class="row">
							 <div class="span3">
								<strong>Email Address</strong>
								<input type="text" name="email" class="span3" placeholder="email@address.com" id="email_add" />
								<strong>Full Name</strong>
								<input type="text" name="name" id="name_add" class="span3" placeholder="(optional)" />
								<strong>Phone #</strong>
								<input type="text" name="phone" id="phone_add" class="span3" placeholder="(optional)" />
								<button type="button" class="btn" id="addemail">Add Contact</button>
							 </div>
							 <div class="span5">
								<strong>Your Added Contacts: (read only - limit 100)</strong>
                				<textarea name="emails" id="emails" rows="8" readonly="readonly" class="span5"></textarea>
							 </div>
							</div>
							<div class="clearfix"></div>
							<hr />
							<div class="clearfix"></div>
							<strong>Your Personalized Message:</strong><br /><select class="span4" onchange="$('#message').val(this.value);">
							<option value="">Select a Predefined Message</option>
							<option value="I wish I never had to leave">I wish I never had to leave</option>
							<option value="Tell my office I'm never coming home!">Tell my office I'm never coming home!</option>
							<option value="I can't wait to go back!">I can't wait to go back!</option>
							<option value="Best resort experience we ever had!">Best resort experience we ever had!</option>
							<option value="My vacation was so fun, you should come next time!">My vacation was so fun, you should come next time!</option>
							<option value="You have to experience this resort for yourself!">You have to experience this resort for yourself!</option>
							<option value="I was so relaxed I got a big sunburn!">I was so relaxed I got a big sunburn!</option>
							<option value="We had an absolute blast!">We had an absolute blast!</option>
							<option value="Best Vacation Ever!">Best Vacation Ever!</option>
							<option value="We were treated like royalty!">We were treated like royalty!</option>
							</select><br />
                			<textarea name="message" id="message" class="span8" rows="6" placeholder="Write a personalized message here (optional)"></textarea>
                			<br /><br />
                			<input name="sbtBtn" class="btn btn-large btn-success" value="Invite by Email" type="submit" />
                			<input name="clear" class="btn pull-right" type="reset" value="Reset Form" />
             				</form>
					</div>
					<div class="tab-pane<?php if($active) echo ' active';?>" id="invite">
						<form method="post" action="<?=BASE_URL;?>process.php" name="emailInviter" id="emailInviter">
                		<input type="hidden" name="sender" value="<?php echo $info['customer']['first_name'].' '.$info['customer']['last_name'];?>" />
                		<input type="hidden" name="url" value="<?php if($url) echo $url;?>" />
						<?php
						$oi_services=$inviter->getPlugins();
						
						//print_r($oks['contacts']);	
						if (isset($oks['provider_box'])) {
						if (isset($oi_services['email'][$oks['provider_box']])) $plugType='email';
						elseif (isset($oi_services['social'][$oks['provider_box']])) $plugType='social';
						else $plugType='';
						} else $plugType = '';
						
						function ers($ers){
							if (!empty($ers)){
								$contents="<table cellspacing='0' cellpadding='0' class='table' align='center'><tr><td valign='middle' style='padding:3px' valign='middle'><img src='inviter/images/ers.gif'></td><td valign='middle' style='color:red;padding:5px;'>";
								$contents.="{$ers['error']}<br >";
								$contents.="</td></tr></table><br >";
								return $contents;
							}
						}
	
						function oks($oks){
							if (!empty($oks)){
								$contents="<table border='0' cellspacing='0' cellpadding='10' class='table' align='center'><tr><td valign='middle' valign='middle'><img src='inviter/images/oks.gif' ></td><td valign='middle' style='color:#5897FE;padding:5px;'>	";
								foreach ($oks as $key=>$msg)
									$contents.="{$msg}<br >";
									$contents.="</td></tr></table><br >";
								return $contents;
							}
						}

			$contents="<script type='text/javascript'>
	function toggleAll(element) 
	{
	var form = document.forms.emailInviter, z = 0;
	for(z=0; z<form.length;z++)
		{
		if(form[z].type == 'checkbox')
			form[z].checked = element.checked;
	   	}
	}
	</script>";
	$contents.=ers($ers);
	if ($step=="get_contacts"){
		
		$contents.="<label for='provider_box'>Email Provider</label>
		<select class='span4' name='provider_box'><option value=''></option>";
		foreach ($oi_services as $type=>$providers)	
			{
			$contents.="<optgroup label='{$inviter->pluginTypes[$type]}'>";
			foreach ($providers as $provider=>$details)
				$contents.="<option value='{$provider}'".($ers['provider_box']==$provider?' selected':'').">{$details['name']}</option>";
			$contents.="</optgroup>";
			}
		$contents.="</select>
		<label for='email_box'>Email</label>
		<input class='span4' type='text' name='email_box' value='{$ers['email_box']}' />
		<label for='password_box'>Password</label>
		<input class='span4' type='password' name='password_box' value='' />
		<hr>
		<input class='btn btn-success btn-large' type='submit' data-loading-text='Processing...' name='import' value='Grab Contacts' />
		<input type='hidden' name='action' value='contacts' /><small class='pull-right'><a href='http://openinviter.com/'>Powered by OpenInviter.com</a></small>";
		
	} elseif ($step=="send_invites"){
			
			if (!$inviter->showContacts()){
			$contacts=$_SESSION['contacts'];
			unset($_SESSION['contacts']);

			if(count($contacts)!=0) $add = "<input type='checkbox' onChange='toggleAll(this)' name='toggle_all' title='Select/Deselect all' checked> Select All"; else $add = '';

			$contents.="<table class='table table-striped' width='100%' align='center' cellspacing='0' cellpadding='0'><tr><td colspan='".($plugType=='email'? "3":"2")."'><strong>Select Your Contacts</strong> ".$add."</td></tr>";
			if (count($contacts)==0)
				$contents.="<tr><td align='center' colspan='".($plugType=='email'? "3":"2")."'>You do not have any contacts in your address book.</td></tr></table>";
			else
				{
				$contents.="<tr><td><strong>Invite</strong></td><td><strong>Name</strong></td><td><strong>Phone</strong></td>".($plugType == 'email' ?"<td><strong>E-mail</strong></td>":"")."</tr>";
				$counter=0;
				foreach ($contacts as $email=>$name)
					{
					$n = explode("|",$name);
					$name = $n[0];
					$phone = $n[1];
					$counter++;
					$contents.="<tr><td><input name='check_{$counter}' value='{$counter}' type='checkbox' checked><input type='hidden' name='email_{$counter}' value='{$email}'><input type='hidden' name='name_{$counter}' value='{$name}'><input type='hidden' name='phone_{$counter}' value='{$phone}'></td><td>{$name}</td><td>{$phone}</td>".($plugType == 'email' ?"<td>{$email}</td>":"")."</tr>";
					}
				$contents.="</table>";
				}
			
			$contents.="<strong>Your Personalized Message:</strong><br /><select class=\"span4\" onchange=\"$('#message').val(this.value);\">
							<option value=\"\">Select a Predefined Message</option>
							<option value=\"I wish I never had to leave\">I wish I never had to leave</option>
							<option value=\"Tell my office I'm never coming home!\">Tell my office I'm never coming home!</option>
							<option value=\"I can't wait to go back!\">I can't wait to go back!</option>
							<option value=\"Best resort experience we ever had!\">Best resort experience we ever had!</option>
							<option value=\"My vacation was so fun, you should come next time!\">My vacation was so fun, you should come next time!</option>
							<option value=\"You have to experience this resort for yourself!\">You have to experience this resort for yourself!</option>
							<option value=\"I was so relaxed I got a big sunburn!\">I was so relaxed I got a big sunburn!</option>
							<option value=\"We had an absolute blast!\">We had an absolute blast!</option>
							<option value=\"Best Vacation Ever!\">Best Vacation Ever!</option>
							<option value=\"We were treated like royalty!\">We were treated like royalty!</option>
							</select><br />
                <textarea name='message_box' class='span8' rows='6' placeholder='Write a personalized message here (optional)'></textarea>
                <br /><br />
				<input type='submit' name='send' value='Send Invites' class='btn btn-success btn-large' /><input type='button' name='goback' class='btn pull-right' value='Go Back' onclick='document.location.href=\"bonus.php?view=invites&step=get_contacts\"' />";
			
			} else {
			
				$contents.="<strong>Send invites to my contacts in ".strtoupper($oks['provider_box'])."<strong><br /><br />";
	
				$contents.="<strong>Your Personalized Message:</strong><br /><select class=\"span4\" onchange=\"$('#message_box').val(this.value);\">
							<option value=\"\">Select a Predefined Message</option>
							<option value=\"I wish I never had to leave\">I wish I never had to leave</option>
							<option value=\"Tell my office I'm never coming home!\">Tell my office I'm never coming home!</option>
							<option value=\"I can't wait to go back!\">I can't wait to go back!</option>
							<option value=\"Best resort experience we ever had!\">Best resort experience we ever had!</option>
							<option value=\"My vacation was so fun, you should come next time!\">My vacation was so fun, you should come next time!</option>
							<option value=\"You have to experience this resort for yourself!\">You have to experience this resort for yourself!</option>
							<option value=\"I was so relaxed I got a big sunburn!\">I was so relaxed I got a big sunburn!</option>
							<option value=\"We had an absolute blast!\">We had an absolute blast!</option>
							<option value=\"Best Vacation Ever!\">Best Vacation Ever!</option>
							<option value=\"We were treated like royalty!\">We were treated like royalty!</option>
							</select><br />
                <textarea name='message_box' id='message_box' class='span8' rows='6' placeholder='Write a personalized message here (optional)'></textarea>
                <br /><br />
				<input type='submit' name='send' value='Send Invites' class='import' />";
			
			}
			
			$contents.="<input type='hidden' name='action' value='send_invites' />
			<input type='hidden' name='provider_box' value='{$oks['provider_box']}' />
			<input type='hidden' name='email_box' value='{$oks['email_box']}' />
			<input type='hidden' name='oi_session_id' value='{$oks['oi_session_id']}' />";
	}

echo $contents;
?>
			</form>
				</div>             
                </div>
                </div>
                 
                 <?php
                 break;
                 case "purchase": // PURCHASE --------------------------------------------------------------------------
                 ?>

                 <div class="banner">
                 	<h2>Purchase Bonus Points</h2>
                 	<p>Do you like instant gratification? Reward yourself now by purchasing bonus points if you are a little short!</p>
                 </div>
                 <hr>
                 <div class="well">
                 	<h4>Purchase Amount: <?=$_GET['amount'];?> points ($<?=$_GET['amount'];?>)</h4>
                 	<div class="clearboth"><br /></div>
                 	<form action="<?=BASE_URL;?>process.php" id="purchaseForm" name="purchasePaypal">
                 		<input type="hidden" name="amount" value="<?=$_GET['amount'];?>" />
                 		<input type="hidden" name="action" value="purchase" />
                 		<input type="hidden" name="email" value="<?=$_SESSION['user_name'];?>" />
                 		<h4>Billing Address</h4>
                 		<div class="controls controls-row">
                 			<input type="text" name="first_name" id="first_name" class="span4" placeholder="First Name">
                 			<input type="text" name="last_name" id="last_name" class="span4" placeholder="Last Name">
                 		</div>
                 		<div class="controls controls-row">
                 			<input type="text" name="address1" id="address1" class="span4" placeholder="Address">
                 			<input type="text" name="address2" class="span4" placeholder="Address 2">
                 		</div>
                 		<div class="controls controls-row">
							<select id="country_select" id="country" name="country" class="span4">
							<option value="">Select a Country</option>
							<option value="AF">Afghanistan</option>
							<option value="AX">Åland Islands</option>
							<option value="AL">Albania</option>
							<option value="DZ">Algeria</option>
							<option value="AS">American Samoa</option>
							<option value="AD">Andorra</option>
							<option value="AO">Angola</option>
							<option value="AI">Anguilla</option>
							<option value="AQ">Antarctica</option>
							<option value="AG">Antigua and Barbuda</option>
							<option value="AR">Argentina</option>
							<option value="AM">Armenia</option>
							<option value="AW">Aruba</option>
							<option value="AU">Australia</option>
							<option value="AT">Austria</option>
							<option value="AZ">Azerbaijan</option>
							<option value="BS">Bahamas</option>
							<option value="BH">Bahrain</option>
							<option value="BD">Bangladesh</option>
							<option value="BB">Barbados</option>
							<option value="BY">Belarus</option>
							<option value="BE">Belgium</option>
							<option value="BZ">Belize</option>
							<option value="BJ">Benin</option>
							<option value="BM">Bermuda</option>
							<option value="BT">Bhutan</option>
							<option value="BO">Bolivia</option>
							<option value="BA">Bosnia and Herzegovina</option>
							<option value="BW">Botswana</option>
							<option value="BV">Bouvet Island</option>
							<option value="BR">Brazil</option>
							<option value="IO">British Indian Ocean Territory</option>
							<option value="BN">Brunei Darussalam</option>
							<option value="BG">Bulgaria</option>
							<option value="BF">Burkina Faso</option>
							<option value="BI">Burundi</option>
							<option value="KH">Cambodia</option>
							<option value="CM">Cameroon</option>
							<option value="CA">Canada</option>
							<option value="CV">Cape Verde</option>
							<option value="KY">Cayman Islands</option>
							<option value="CF">Central African Republic</option>
							<option value="TD">Chad</option>
							<option value="CL">Chile</option>
							<option value="CN">China</option>
							<option value="CX">Christmas Island</option>
							<option value="CC">Cocos (Keeling) Islands</option>
							<option value="CO">Colombia</option>
							<option value="KM">Comoros</option>
							<option value="CG">Congo</option>
							<option value="CD">Congo, The Democratic Republic of The</option>
							<option value="CK">Cook Islands</option>
							<option value="CR">Costa Rica</option>
							<option value="CI">Cote D'ivoire</option>
							<option value="HR">Croatia</option>
							<option value="CU">Cuba</option>
							<option value="CY">Cyprus</option>
							<option value="CZ">Czech Republic</option>
							<option value="DK">Denmark</option>
							<option value="DJ">Djibouti</option>
							<option value="DM">Dominica</option>
							<option value="DO">Dominican Republic</option>
							<option value="EC">Ecuador</option>
							<option value="EG">Egypt</option>
							<option value="SV">El Salvador</option>
							<option value="GQ">Equatorial Guinea</option>
							<option value="ER">Eritrea</option>
							<option value="EE">Estonia</option>
							<option value="ET">Ethiopia</option>
							<option value="FK">Falkland Islands (Malvinas)</option>
							<option value="FO">Faroe Islands</option>
							<option value="FJ">Fiji</option>
							<option value="FI">Finland</option>
							<option value="FR">France</option>
							<option value="GF">French Guiana</option>
							<option value="PF">French Polynesia</option>
							<option value="TF">French Southern Territories</option>
							<option value="GA">Gabon</option>
							<option value="GM">Gambia</option>
							<option value="GE">Georgia</option>
							<option value="DE">Germany</option>
							<option value="GH">Ghana</option>
							<option value="GI">Gibraltar</option>
							<option value="GR">Greece</option>
							<option value="GL">Greenland</option>
							<option value="GD">Grenada</option>
							<option value="GP">Guadeloupe</option>
							<option value="GU">Guam</option>
							<option value="GT">Guatemala</option>
							<option value="GG">Guernsey</option>
							<option value="GN">Guinea</option>
							<option value="GW">Guinea-bissau</option>
							<option value="GY">Guyana</option>
							<option value="HT">Haiti</option>
							<option value="HM">Heard Island and Mcdonald Islands</option>
							<option value="VA">Holy See (Vatican City State)</option>
							<option value="HN">Honduras</option>
							<option value="HK">Hong Kong</option>
							<option value="HU">Hungary</option>
							<option value="IS">Iceland</option>
							<option value="IN">India</option>
							<option value="ID">Indonesia</option>
							<option value="IR">Iran, Islamic Republic of</option>
							<option value="IQ">Iraq</option>
							<option value="IE">Ireland</option>
							<option value="IM">Isle of Man</option>
							<option value="IL">Israel</option>
							<option value="IT">Italy</option>
							<option value="JM">Jamaica</option>
							<option value="JP">Japan</option>
							<option value="JE">Jersey</option>
							<option value="JO">Jordan</option>
							<option value="KZ">Kazakhstan</option>
							<option value="KE">Kenya</option>
							<option value="KI">Kiribati</option>
							<option value="KP">Korea, Democratic People's Republic of</option>
							<option value="KR">Korea, Republic of</option>
							<option value="KW">Kuwait</option>
							<option value="KG">Kyrgyzstan</option>
							<option value="LA">Lao People's Democratic Republic</option>
							<option value="LV">Latvia</option>
							<option value="LB">Lebanon</option>
							<option value="LS">Lesotho</option>
							<option value="LR">Liberia</option>
							<option value="LY">Libyan Arab Jamahiriya</option>
							<option value="LI">Liechtenstein</option>
							<option value="LT">Lithuania</option>
							<option value="LU">Luxembourg</option>
							<option value="MO">Macao</option>
							<option value="MK">Macedonia, The Former Yugoslav Republic of</option>
							<option value="MG">Madagascar</option>
							<option value="MW">Malawi</option>
							<option value="MY">Malaysia</option>
							<option value="MV">Maldives</option>
							<option value="ML">Mali</option>
							<option value="MT">Malta</option>
							<option value="MH">Marshall Islands</option>
							<option value="MQ">Martinique</option>
							<option value="MR">Mauritania</option>
							<option value="MU">Mauritius</option>
							<option value="YT">Mayotte</option>
							<option value="MX">Mexico</option>
							<option value="FM">Micronesia, Federated States of</option>
							<option value="MD">Moldova, Republic of</option>
							<option value="MC">Monaco</option>
							<option value="MN">Mongolia</option>
							<option value="ME">Montenegro</option>
							<option value="MS">Montserrat</option>
							<option value="MA">Morocco</option>
							<option value="MZ">Mozambique</option>
							<option value="MM">Myanmar</option>
							<option value="NA">Namibia</option>
							<option value="NR">Nauru</option>
							<option value="NP">Nepal</option>
							<option value="NL">Netherlands</option>
							<option value="AN">Netherlands Antilles</option>
							<option value="NC">New Caledonia</option>
							<option value="NZ">New Zealand</option>
							<option value="NI">Nicaragua</option>
							<option value="NE">Niger</option>
							<option value="NG">Nigeria</option>
							<option value="NU">Niue</option>
							<option value="NF">Norfolk Island</option>
							<option value="MP">Northern Mariana Islands</option>
							<option value="NO">Norway</option>
							<option value="OM">Oman</option>
							<option value="PK">Pakistan</option>
							<option value="PW">Palau</option>
							<option value="PS">Palestinian Territory, Occupied</option>
							<option value="PA">Panama</option>
							<option value="PG">Papua New Guinea</option>
							<option value="PY">Paraguay</option>
							<option value="PE">Peru</option>
							<option value="PH">Philippines</option>
							<option value="PN">Pitcairn</option>
							<option value="PL">Poland</option>
							<option value="PT">Portugal</option>
							<option value="PR">Puerto Rico</option>
							<option value="QA">Qatar</option>
							<option value="RE">Reunion</option>
							<option value="RO">Romania</option>
							<option value="RU">Russian Federation</option>
							<option value="RW">Rwanda</option>
							<option value="SH">Saint Helena</option>
							<option value="KN">Saint Kitts and Nevis</option>
							<option value="LC">Saint Lucia</option>
							<option value="PM">Saint Pierre and Miquelon</option>
							<option value="VC">Saint Vincent and The Grenadines</option>
							<option value="WS">Samoa</option>
							<option value="SM">San Marino</option>
							<option value="ST">Sao Tome and Principe</option>
							<option value="SA">Saudi Arabia</option>
							<option value="SN">Senegal</option>
							<option value="RS">Serbia</option>
							<option value="SC">Seychelles</option>
							<option value="SL">Sierra Leone</option>
							<option value="SG">Singapore</option>
							<option value="SK">Slovakia</option>
							<option value="SI">Slovenia</option>
							<option value="SB">Solomon Islands</option>
							<option value="SO">Somalia</option>
							<option value="ZA">South Africa</option>
							<option value="GS">South Georgia and The South Sandwich Islands</option>
							<option value="ES">Spain</option>
							<option value="LK">Sri Lanka</option>
							<option value="SD">Sudan</option>
							<option value="SR">Suriname</option>
							<option value="SJ">Svalbard and Jan Mayen</option>
							<option value="SZ">Swaziland</option>
							<option value="SE">Sweden</option>
							<option value="CH">Switzerland</option>
							<option value="SY">Syrian Arab Republic</option>
							<option value="TW">Taiwan, Province of China</option>
							<option value="TJ">Tajikistan</option>
							<option value="TZ">Tanzania, United Republic of</option>
							<option value="TH">Thailand</option>
							<option value="TL">Timor-leste</option>
							<option value="TG">Togo</option>
							<option value="TK">Tokelau</option>
							<option value="TO">Tonga</option>
							<option value="TT">Trinidad and Tobago</option>
							<option value="TN">Tunisia</option>
							<option value="TR">Turkey</option>
							<option value="TM">Turkmenistan</option>
							<option value="TC">Turks and Caicos Islands</option>
							<option value="TV">Tuvalu</option>
							<option value="UG">Uganda</option>
							<option value="UA">Ukraine</option>
							<option value="AE">United Arab Emirates</option>
							<option value="GB">United Kingdom</option>
							<option value="US">United States</option>
							<option value="UM">United States Minor Outlying Islands</option>
							<option value="UY">Uruguay</option>
							<option value="UZ">Uzbekistan</option>
							<option value="VU">Vanuatu</option>
							<option value="VE">Venezuela</option>
							<option value="VN">Viet Nam</option>
							<option value="VG">Virgin Islands, British</option>
							<option value="VI">Virgin Islands, U.S.</option>
							<option value="WF">Wallis and Futuna</option>
							<option value="EH">Western Sahara</option>
							<option value="YE">Yemen</option>
							<option value="ZM">Zambia</option>
							<option value="ZW">Zimbabwe</option>
							</select>
                 			<input type="text" name="city" id="city" class="span4" placeholder="Town/City">
                 		</div>
                 		<div class="controls controls-row">
                 			<input type="text" name="state" id="state" class="span4" placeholder="State/Province">
                 			<input type="text" name="postal_code" id="postal_code" maxlength="6" class="span2" placeholder="Postal Code">
                 		</div>
                 		<div class="clearboth"><br /><br /></div>
                 		<h4>Credit Card Information</h4>
                 		<label for="card_name">Full Name on Card  <span class="text-error">*</span></label>
                 		<input type="text" name="card_name" id="card_name" class="span4" value="">
                 		<label for="card_num">Credit Card Number  <span class="text-error">*</span></label>
                		<input type="text" name="card_num" id="card_num" class="span4" value="">
                		<label for="expires_month">Expires  <span class="text-error">*</span></label>
                 		<select name="expires_month" id="expires_month" class="input-small">
                			<?php
                				for($i=1;$i<=12;$i++){
                					if(strlen($i)==1) $i = '0'.$i;
                					echo '<option value="'.$i.'">'.$i.'</option>';
                				}
                			?>
                		</select> / <select name="expires_year" id="expires_year" class="input-small">
                			<?php
                				for($i=date('Y');$i<=date('Y')+6;$i++){
                					echo '<option value="'.$i.'">'.$i.'</option>';
                				}
                			?>
                		</select>
                		<label for="cvv">CVV2  <span class="text-error">*</span></label>
                		<input type="text" name="cvv" id="cvv2" class="input-small" placeholder="CVV2">
                		<hr>
                		<p>By clicking "Purchase" below you hereby agree to our <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>.</p>
                		<input type="submit" name="sbtbtn" value="Purchase" class="btn btn-large btn-success">
                		<a href="<?=BASE_URL;?>bonus.php?view=rewards" class="btn pull-right">Go Back</a>
                 	</form>
                 </div>
                 
                 <?php
                 break;
                 case "rewards": // REWARDS ----------------------------------------------------------------------------
                 ?>
                 
                 <div class="banner">
                 	<h2>Bonus Rewards</h2>
                 	<p>Use your bonus points to redeem rewards. Active rewards are in Blue.</p>
                 </div>
                 
                 <div class="row">
                 <div class="span3"><h4>Available Points:</h4><pre style="text-align:center;font-size:1.2em;"><?=$balance;?></pre></div>
                 <div class="span5 offset1"><p>Do you like instant gratification? Reward yourself now by purchasing bonus points if you are a little short!</p><select name="amount" id="amt" class="span4" onchange="if(this.value!='') document.location='bonus.php?view=purchase&amount='+this.value;"><option value="">Select an amount to purchase</option><option value="50">50 Bonus Points = $50</option><option value="100">100 Bonus Points = $100</option><option value="150">150 Bonus Points = $150</option><option value="200">200 Bonus Points = $200</option><option value="250">250 Bonus Points = $250</option></select></div>
                 </div>

        		<hr>
        		<h4>Reward Levels</h4>
        		<p>Click on a reward below to redeem. Your available rewards are in blue. Grey means you do not have enough points to redeem.</p>
        		<ul class="thumbnails">
        		<?php
        		foreach($rewards['rewards']['reward'] as $reward){
        			if($balance >= $reward['level']){
        				echo '<li class="span4"><a href="#" class="redeem" id="'.$reward['id'].'" title="Redeem '.$reward['description'].'">'.$reward['description'].' = '.$reward['level'].' Points</a></li>';
        			} else {
        				echo '<li class="span4"><a class="redeeminactive" id="'.$reward['id'].'" onclick="TINY.box.show({html:\'Sorry, you have not earned enough points to redeem '.$reward['description'].'<br />\',animate:true,close:true})">'.$reward['description'].' = '.$reward['level'].' Points</a></li>';
					}
				}
        		?>
        		</ul>
                 
                 <?php
                 break;
                 case "myinvites": // MY INVITES -------------------------------------------------------------------------
                 ?>
                 
                 <div class="banner">
                 	<h2>My Invites</h2>
                 	<p>Below is your list of people you have invited and their status.</p>
                 </div>
                 
                 <?php echo html_entity_decode(urldecode($_GET['msg'])); ?>
                 
                <table class="table table-striped">
                <thead>
         		<tr><th><strong>Name</strong></th><th><strong>Email</strong></th><th align="center"><strong>Phone</strong></th><th><strong>Status</strong></th><th><strong>Action</strong></th></tr>
         		</thead>
         		<tbody>
         		<?php
         		foreach($invites as $i){
         			if($i[4]=='pending') $add = '<form action="process.php" name="resendEmail'.$i[0].'" method="post"><input type="hidden" name="action" value="resend_email" /><input type="hidden" name="name" value="'.$i[1].'" /><input type="hidden" name="email" value="'.$i[2].'" /><button class="btn btn-warning mt20" type="submit">Resend</button></form>'; else $add = '';
         			echo '<tr><td width="200">'.$i[1].'</td><td>'.$i[2].'</td><td>'.$i[3].'</td><td>'.$i[4].'</td><td>'.$add.'</td></tr>';
         		}
         		?>
         		</tbody>
         		</table>
         		
         		 <?php
                 break;
                 case "myvideos": // MY VIDEOS -------------------------------------------------------------------------
                 ?>
                 
                 <div class="banner">
                 	<h2>My Videos</h2>
                 	<p>Below is your list of videos you share to earn points and rewards.</p>
                 </div>
                <?php echo html_entity_decode(urldecode($_GET['msg']));?>
                <?php echo html_entity_decode(urldecode($_GET['error']));?>
                <table class="table table-striped">
                <thead>
         		<tr><th class="span4"><strong>Name</strong></th><th class="span5"><strong>Link</strong></th><th class="span3">Action</th></tr>
         		</thead>
         		<tbody>
         		<?php
         		foreach($videos as $v){
         			$user_name = $_SESSION['user_name'];
         			$url_orig = BASE_URL.'video.php?uid='.$user_name.'&vid_name='.$v[0].'&turl='.$v[2].'&vid_url='.$v[1];
         			$u = $login->shortUrl($url_orig);
         			$url = $u['id'];
         			
         			$link = '<a href="'.$v[1].'">'.$v[1].'</a>';
         			echo '<tr><td class="span4">'.$v[0].'<br /><img style="width:150px;" src="'.$v[2].'" alt="" /></td><td class="span5"><div class="panel"><a href="video.php?vid_url='.$v[1].'&uid='.$user->user_id.'&vid_name='.$v[0].'&turl='.$v[2].'">View My Video Page</a><br />url: '.$url.'</a></td><td class="span3"><a href="vid_id='.$v[3].'&vid_url='.$v[1].'&uid='.$user->user_id.'&vid_name='.$v[0].'&turl='.$v[2].'" data-toggle="modal" id="fbshare" data-target="#startShare" class="btn btn-large btn-primary"><i class="icon-info-sign"></i>&nbsp;Share Video</a></td></td></tr>';
         		}
         		?>
         		</tbody>
         		</table>
         		
         		 <?php
                 break;
                 case "videothemes":
                 ?>
                 
                 <div class="banner">
                 	<h2>Video Theme Selector</h2>
                 	<p>Select a video theme below to start with.</p>
                 </div>
                 
                 <div class="row">
    				<div class="span3">
    					<a href="bonus.php?view=videomaker&definition=<?=urlencode('{"content":{"video":[{"addons":{"text":[{"text":"Courtesy of RCI","type":"TextAddon"}]},"duration":5,"kenburns":{"mode":"fit"},"orientation":"portrait","thumb_url":"https://dragon.stupeflix.com/task-um63o6avkqglo-image.thumb-HOXJA46CEGNU2Q5MMDCXCXNVII/","type":"Image","url":"http://st3.stupeflix.com/file/NVA0toaRyrBwjxN6FgGj5BcN.png"},{"addons":{},"duration":"auto","location":{"center":"40.8574813,-74.42701920000002","lat":40.8574813,"lng":-74.42701920000002,"search":"Parsippany, New Jersey","zoom":6},"type":"Map"}]},"music_sync":true,"name":"My Vacation Video","pace":"normal","theme":"ID","type":"Project"}');?>" class="theme_select" title="outline"><img src="images/video_thumbs/outline.jpg" class="shadow video_thumb" alt="Outline" /></a><br />
    					<strong>Outline</strong>
    				</div>
    				<div class="span3">
    					<a href="bonus.php?view=videomaker&definition=<?=urlencode('{"content":{"video":[{"addons":{"text":[{"text":"Courtesy of RCI","type":"TextAddon"}]},"duration":5,"kenburns":{"mode":"fit"},"orientation":"portrait","thumb_url":"https://dragon.stupeflix.com/task-um63o6avkqglo-image.thumb-HOXJA46CEGNU2Q5MMDCXCXNVII/","type":"Image","url":"http://st3.stupeflix.com/file/NVA0toaRyrBwjxN6FgGj5BcN.png"},{"addons":{"text":[{"text":"Parsippany, New Jersey","type":"TextAddon"}]},"duration":"auto","location":{"center":"40.8574813,-74.42701920000002","lat":40.8574813,"lng":-74.42701920000002,"search":"Parsippany, New Jersey","zoom":6},"type":"Map"}]},"music_sync":true,"name":"My Vacation Video","pace":"normal","theme":"ID","type":"Project"}');?>" class="theme_select" title="tiles"><img src="images/video_thumbs/tiles.jpg" class="shadow video_thumb" alt="Tiles" /></a><br />
    					<strong>Tiles</strong>
    				</div>
    				<div class="span3">
    					<a href="bonus.php?view=videomaker&definition=<?=urlencode('{"content":{"video":[{"addons":{"text":[{"text":"Courtesy of RCI","type":"TextAddon"}]},"duration":5,"kenburns":{"mode":"fit"},"orientation":"portrait","thumb_url":"https://dragon.stupeflix.com/task-um63o6avkqglo-image.thumb-HOXJA46CEGNU2Q5MMDCXCXNVII/","type":"Image","url":"http://st3.stupeflix.com/file/NVA0toaRyrBwjxN6FgGj5BcN.png"},{"addons":{},"duration":"auto","location":{"center":"40.8574813,-74.42701920000002","lat":40.8574813,"lng":-74.42701920000002,"search":"Parsippany, New Jersey","zoom":6},"type":"Map"}]},"music_sync":true,"name":"My Vacation Video","pace":"normal","theme":"ID","type":"Project"}');?>" class="theme_select" title="classic"><img src="images/video_thumbs/classic.jpg" class="shadow video_thumb" alt="Classic" /></a><br />
    					<strong>Classic</strong>
    				</div>
    			</div>
    			<p><br /></p>
    			<div class="row">
    				<div class="span3">
    					<a href="bonus.php?view=videomaker&definition=<?=urlencode('{"content":{"video":[{"addons":{"text":[{"text":"Courtesy of RCI","type":"TextAddon"}]},"duration":5,"kenburns":{"mode":"fit"},"orientation":"portrait","thumb_url":"https://dragon.stupeflix.com/task-um63o6avkqglo-image.thumb-HOXJA46CEGNU2Q5MMDCXCXNVII/","type":"Image","url":"http://st3.stupeflix.com/file/NVA0toaRyrBwjxN6FgGj5BcN.png"},{"addons":{},"duration":"auto","location":{"center":"40.8574813,-74.42701920000002","lat":40.8574813,"lng":-74.42701920000002,"search":"Parsippany, New Jersey","zoom":6},"type":"Map"}]},"music_sync":true,"name":"My Vacation Video","pace":"normal","theme":"ID","type":"Project"}');?>" class="theme_select" title="scrapbook"><img src="images/video_thumbs/scrapbook.jpg" class="shadow video_thumb" alt="Scrapbook" /></a><br />
    					<strong>Scrapbook</strong>
    				</div>
    				<div class="span3">
    					<a href="bonus.php?view=videomaker&definition=<?=urlencode('{"content":{"video":[{"addons":{"text":[{"text":"Courtesy of RCI","type":"TextAddon"}]},"duration":5,"kenburns":{"mode":"fit"},"orientation":"portrait","thumb_url":"https://dragon.stupeflix.com/task-um63o6avkqglo-image.thumb-HOXJA46CEGNU2Q5MMDCXCXNVII/","type":"Image","url":"http://st3.stupeflix.com/file/NVA0toaRyrBwjxN6FgGj5BcN.png"},{"addons":{},"duration":"auto","location":{"center":"40.8574813,-74.42701920000002","lat":40.8574813,"lng":-74.42701920000002,"search":"Parsippany, New Jersey","zoom":6},"type":"Map"}]},"music_sync":true,"name":"My Vacation Video","pace":"normal","theme":"ID","type":"Project"}');?>" class="theme_select" title="unreel"><img src="images/video_thumbs/unreel.jpg" class="shadow video_thumb" alt="Unreel" /></a><br />
    					<strong>Unreel</strong>
    				</div>
    				<div class="span3">
    					<a href="bonus.php?view=videomaker&definition=<?=urlencode('{"content":{"video":[{"addons":{"text":[{"text":"Courtesy of RCI","type":"TextAddon"}]},"duration":5,"kenburns":{"mode":"fit"},"orientation":"portrait","thumb_url":"https://dragon.stupeflix.com/task-um63o6avkqglo-image.thumb-HOXJA46CEGNU2Q5MMDCXCXNVII/","type":"Image","url":"http://st3.stupeflix.com/file/NVA0toaRyrBwjxN6FgGj5BcN.png"},{"addons":{},"duration":"auto","location":{"center":"40.8574813,-74.42701920000002","lat":40.8574813,"lng":-74.42701920000002,"search":"Parsippany, New Jersey","zoom":6},"type":"Map"}]},"music_sync":true,"name":"My Vacation Video","pace":"normal","theme":"ID","type":"Project"}');?>" class="theme_select" title="blueprint"><img src="images/video_thumbs/blueprint.jpg" class="shadow video_thumb" alt="Blueprint" /></a><br />
    					<strong>Blueprint</strong>
    				</div>
    			</div>
    			<p><br /></p>
    			<div class="row">
    				<div class="span3">
    					<a href="bonus.php?view=videomaker&definition=<?=urlencode('{"content":{"video":[{"addons":{"text":[{"text":"Courtesy of RCI","type":"TextAddon"}]},"duration":5,"kenburns":{"mode":"fit"},"orientation":"portrait","thumb_url":"https://dragon.stupeflix.com/task-um63o6avkqglo-image.thumb-HOXJA46CEGNU2Q5MMDCXCXNVII/","type":"Image","url":"http://st3.stupeflix.com/file/NVA0toaRyrBwjxN6FgGj5BcN.png"},{"addons":{},"duration":"auto","location":{"center":"40.8574813,-74.42701920000002","lat":40.8574813,"lng":-74.42701920000002,"search":"Parsippany, New Jersey","zoom":6},"type":"Map"}]},"music_sync":true,"name":"My Vacation Video","pace":"normal","theme":"ID","type":"Project"}');?>" class="theme_select" title="comics"><img src="images/video_thumbs/comics.jpg" class="shadow video_thumb" alt="Comics" /></a><br />
    					<strong>Comics</strong>
    				</div>
    				<div class="span3">
    					<a href="bonus.php?view=videomaker&definition=<?=urlencode('{"content":{"video":[{"addons":{"text":[{"text":"Courtesy of RCI","type":"TextAddon"}]},"duration":5,"kenburns":{"mode":"fit"},"orientation":"portrait","thumb_url":"https://dragon.stupeflix.com/task-um63o6avkqglo-image.thumb-HOXJA46CEGNU2Q5MMDCXCXNVII/","type":"Image","url":"http://st3.stupeflix.com/file/NVA0toaRyrBwjxN6FgGj5BcN.png"},{"addons":{},"duration":"auto","location":{"center":"40.8574813,-74.42701920000002","lat":40.8574813,"lng":-74.42701920000002,"search":"Parsippany, New Jersey","zoom":6},"type":"Map"}]},"music_sync":true,"name":"My Vacation Video","pace":"normal","theme":"ID","type":"Project"}');?>" class="theme_select" title="party"><img src="images/video_thumbs/party.jpg" class="shadow video_thumb" alt="Party" /></a><br />
    					<strong>Party</strong>
    				</div>
    				<div class="span3">
    					<a href="bonus.php?view=videomaker&definition=<?=urlencode('{"content":{"video":[{"addons":{"text":[{"text":"Courtesy of RCI","type":"TextAddon"}]},"duration":5,"kenburns":{"mode":"fit"},"orientation":"portrait","thumb_url":"https://dragon.stupeflix.com/task-um63o6avkqglo-image.thumb-HOXJA46CEGNU2Q5MMDCXCXNVII/","type":"Image","url":"http://st3.stupeflix.com/file/NVA0toaRyrBwjxN6FgGj5BcN.png"},{"addons":{},"duration":"auto","location":{"center":"40.8574813,-74.42701920000002","lat":40.8574813,"lng":-74.42701920000002,"search":"Parsippany, New Jersey","zoom":6},"type":"Map"}]},"music_sync":true,"name":"My Vacation Video","pace":"normal","theme":"ID","type":"Project"}');?>" class="theme_select" title="1901"><img src="images/video_thumbs/1901.jpg" class="shadow video_thumb" alt="1901" /></a><br />
    					<strong>1901</strong>
    				</div>
    			</div>
                 
                 <?php
                 break;
                 case "videohome":
                 ?>
                 
                 <div class="row">
    						<div class="span9">
    						
                 
                 		<div id="myCarousel" class="carousel slide">
    					
    					<!-- Carousel items -->
    					<div class="carousel-inner">
    					<div class="active item">
    						<h2 class="white text-shadow">Create and Share a Video</h2>
				 			<p class="white text-shadow">Create and share your vacation video and earn points</p>
    						<img src="images/banners/3.jpg" alt="Your Vacation Video" class="rounded-bottom" />
    						<div class="carousel-caption center"><a href="?view=videothemes" style="padding:15px;margin-bottom:40px;font-size:1.8em;color:#fff;" class="btn btn-large btn-primary shadow">Make a Video Now</a></div>
    					</div>
    					<div class="item">
    						<h2 class="white text-shadow">Share more Earn more</h2>
				 			<p class="white text-shadow">The more videos you share the more you can earn</p>
    						<img src="images/banners/2.jpg" alt="Your Vacation Video" class="rounded-bottom" />
    						<div class="carousel-caption center"><a href="?view=videothemes" style="padding:15px;margin-bottom:40px;font-size:1.8em;color:#fff;" class="btn btn-large btn-primary shadow">Make a Video Now</a></div>
    					</div>
    					<div class="item">
    						<h2 class="white text-shadow">Stunning Vacation Videos</h2>
				 			<p class="white text-shadow">Create beautifully designed vacation videos</p>
    						<img src="images/banners/1.jpg" alt="Your Vacation Video" class="rounded-bottom" />
    						<div class="carousel-caption center"><a href="?view=videothemes" style="padding:15px;margin-bottom:40px;font-size:1.8em;color:#fff;" class="btn btn-large btn-primary shadow">Make a Video Now</a></div>
    					</div>
    					<div class="item">
    						<h2 class="white text-shadow">Earn points for every share</h2>
				 			<p class="white text-shadow">Every video you post will earn you points</p>
    						<img src="images/banners/4.jpg" alt="Your Vacation Video" class="rounded-bottom" />
    						<div class="carousel-caption center"><a href="?view=videothemes" style="padding:15px;margin-bottom:40px;font-size:1.8em;color:#fff;" class="btn btn-large btn-primary shadow">Make a Video Now</a></div>
    					</div>
    					</div>
    					
    					<ol class="carousel-indicators shadow">
                  			<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                  			<li data-target="#myCarousel" data-slide-to="1"></li>
                  			<li data-target="#myCarousel" data-slide-to="2"></li>
                  			<li data-target="#myCarousel" data-slide-to="3"></li>
                		</ol>
    					
    					<!-- Carousel nav -->
    					<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
    					<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
    					</div>
    					
    						</div>
    					</div>
    					
    					<div class="row">
    						<div class="span9">
    							<h3><span class="left">It's easy as </span><img class="left" src="images/1-2-3.jpg" alt="1-2-3" /><span class="left"> to get started, all you do is:</span></h3>
    							<p><ol style="font-size:1.6em;line-height:2em;"><li>Create a travel video with your photos</li><li>Share your video on Facebook, Twitter or Email</li><li>You and your friends receive amazing rewards</li></ol></p>
    						</div>
    					</div>
                 
                  
                 <?php
                 break;
                 case "videomaker":
                 ?>	
                 
                 <div class="banner">
                 	<h2>Video Maker</h2>
                 	<p>Below you can select photos from your computer or social network, select a music track and create your video by clicking export.</p>
                 </div>
                 
                 <div class="row">
                 	<div class="span12">
                 		<img src="images/how-to.jpg" class="span9" style="margin-left:0px;" alt="How To Steps" />
                 	</div>
                 </div>
                 
                 <hr>
                 
                 <div class="row-fluid">
                 	<div class="span12">
                 		<?php
                 			$url = 'http://studio.stupeflix.com/factory/1j49YTz8Ql/?definition='.urldecode($_GET['definition']).'&target=_parent&method=get&action=http://www.ownerreferrals.com/video_thanks.php';
                 			
                 		
                 		echo '<iframe class=\'span12\' height=\'400\' border=\'0\' src=\''.stripslashes($url).'\' name=\'video_maker\' style=\'border:1px solid #eee;border-style:collapse;\'></iframe>';
                 		?>
                 	</div>
                 </div>
                 <!--
                 <div class="well">
                 	<div class="video_status alert alert-block" style="display:none;"><img src="images/loading.gif" alt="Processing" /> <span class="video_text">Processing Video...</span></div>
                 	<form name="videoMaker" action="#" id="video_maker">
                 		<input type="hidden" name="resource" id="video_resource" value="<?=time();?>" />
                 		<input type="hidden" name="action" value="video" />
                 		<input type="hidden" name="user" id="video_user" value="<?=$_SESSION['user_name'];?>" />
                 		<h4>Video Details</h4>
                 		<div class="controls controls-row">
                 			<label for="title">Title</label>
                 			<input type="text" name="title" id="title" class="span4" placeholder="Your video title">
                 		</div>
                 		<div class="controls controls-row">
                 			<label for="description">Description</label>
                 			<textarea name="description" class="span8" rows="3" placeholder="Your video description"></textarea>
                 		</div>
                 		<div class="controls controls-row">
                 			<label for="tags">Tags</label>
                 			<input type="text" name="tags" id="tags" class="span4" placeholder="Your video tags">
                 		</div>
                 		<hr />
                 		<input type="submit" name="sbtbtn" value="Create Video" class="btn btn-large btn-success">
                 	</form>
                 </div>	
                 	-->
                 
                 
                 <?php
                 break;
                 }
                 ?>
                    
                </div><!--/span9-->
            </div><!--/row-->
            
            <hr>

            <footer>
                <p>&copy; <?=RESORT_NAME;?> 2012</p>
                <a title="Real Time Web Analytics" href="http://clicky.com/100589920"><img alt="Real Time Web Analytics" src="//static.getclicky.com/media/links/badge.gif" border="0" /></a>
				<noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/100589920ns.gif" /></p></noscript>
            </footer>
            
        </div> <!-- /container wrapper -->
        
        <div id="startShare" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" class="modal hide fade">
				<form action="process.php" method="post" name="shareForm" id="shareForm">
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    				<h3 id="myModalLabel">Share Video with Friends</h3>
    			</div>
    			<div class="modal-body">
   	 				<center><h4>Why should your friends stay here?</h4>
   	 				<select class="span4" onchange="$('#message').val(this.value);">
							<option value="">Select a Predefined Message</option>
							<option value="I wish I never had to leave">I wish I never had to leave</option>
							<option value="Tell my office I'm never coming home!">Tell my office I'm never coming home!</option>
							<option value="I can't wait to go back!">I can't wait to go back!</option>
							<option value="Best resort experience we ever had!">Best resort experience we ever had!</option>
							<option value="My vacation was so fun, you should come next time!">My vacation was so fun, you should come next time!</option>
							<option value="You have to experience this resort for yourself!">You have to experience this resort for yourself!</option>
							<option value="I was so relaxed I got a big sunburn!">I was so relaxed I got a big sunburn!</option>
							<option value="We had an absolute blast!">We had an absolute blast!</option>
							<option value="Best Vacation Ever!">Best Vacation Ever!</option>
							<option value="We were treated like royalty!">We were treated like royalty!</option>
							</select><br />
   	 				<textarea name="message" id="message" class="span4" rows="6" placeholder="Write a personalized message here (optional)"></textarea>
   	 				<br /><button type="submit" id="shareBtn" class="btn btn-primary">Share</button></center>
    			</div>
    			<div class="modal-footer">
    				<button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
    			</div>
    			</form>
    		</div>
    		
    		<div id="shareOptions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" class="modal hide fade">
				<form action="process.php" method="post" name="shareVideo">
				<input type="hidden" id="share_msg" value="" />
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    				<h3 id="myModalLabel">Share Video with Friends</h3>
    			</div>
    			<div class="modal-body">
    				<center>Your video is available at<br /><a href="<?=$url;?>" target="_blank"><?=$url;?></a></center>
   	 				<a href="#" id="fbshare_link" class="btn btn-large span4 mt10 offset1"><i class=" icon-facebook"></i>&nbsp;&nbsp;Share to Facebook</a>
					<a href="" id="emailBtn" class="btn btn-large span4 mt10 offset1"><i class="icon-envelope"></i>&nbsp;&nbsp;&nbsp;Share via Email</a>
					
					<a href="<?=$vid_url;?>" class="btn btn-large span4 mt10 download offset1"><i class="icon-download"></i>&nbsp;&nbsp;&nbsp;Download Video</a>
    			</div>
    			<div class="modal-footer">
    				<button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
    			</div>
    			</form>
    		</div>

			<div id="emailVideo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" class="modal hide fade">
				<form action="process.php" method="post" name="sendVideoEmail" id="sendVideoEmail">
				<input type="hidden" name="msg" id="s_msg" value="" />
				<input type="hidden" id="s_url" name="url" value="" />
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    				<h3 id="myModalLabel">Email Your Video</h3>
    			</div>
    			<div class="modal-body offset1">
    				<input type="hidden" name="action" value="videomailer" />
   	 				<input type="hidden" name="uname" value="<?php echo $info['customer']['first_name'].' '.$info['customer']['last_name'];?>" />
                	<input type="hidden" name="vid_url" value="<?php echo $vid_url;?>" />
                	<input type="hidden" name="thumb" value="<?php echo $tmb_url; ?>" />
   	 				<p>Recipient Name</p>
   	 				<input type="text" name="name" placeholder="(optional)" />
   	 				<p>Recipient Email Address</p>
   	 				<input type="text" name="email" placeholder="email@address.com" />
   	 				<p>Recipient Phone #</p>
   	 				<input type="text" id="model_phone" name="phone" placeholder="(optional)" />
    			</div>
    			<div class="modal-footer">
    				<button type="button" class="btn btn-secondary left" id="goback" name="backBtn">Go Back</button>
     				<button type="submit" class="btn btn-primary">Send Email</button>
    			</div>
    			</form>
    		</div>
		
		<div id="ajax-loading" style="display:none;">Processing…<br /><br /><img src="assets/ajax-loader.gif" /></div>
		
		<script src="//static.getclicky.com/js" type="text/javascript"></script>
		<script type="text/javascript">try{ clicky.init(100589920); }catch(e){}</script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/jquery.cookie.js"></script>
        <script src="js/bootstrap.tour.js"></script>
        <script src="js/animatedcollapse.js"></script>
        <script src="js/script.js"></script>
        <script type="text/javascript">
		var _gaq = _gaq || [];
		var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';
		_gaq.push(['_require', 'inpage_linkid', pluginUrl]);
		_gaq.push(['_setAccount', '<?=GOOGLE_ID;?>']);
		_gaq.push(['_setDomainName','<?=GOOGLE_DOMAIN;?>']);
		_gaq.push(['_setCookiePath', '<?=GOOGLE_PATH;?>']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
  
 		</script>
        <script type="text/javascript" src="js/tinybox.min.js"></script>
		<script src="http://releases.flowplayer.org/5.3.2/flowplayer.min.js"></script>
		<!--<script src="http://vjs.zencdn.net/c/video.js"></script>-->
		
        <script src="js/jquery.maskedinput-1.3.min.js" type="text/javascript"></script>
		<script type="text/javascript">
		$(function() {
			$.mask.definitions['~'] = "[+-]";
			$("#phone_add").mask("(999) 999-9999");
			$("#model_phone").mask("(999) 999-9999");
		});
		</script>
        <script>
    $(document).ready(function(){
		$('form#shareForm').submit(function(e){
			e.preventDefault();
			$('#startShare').modal('hide');
			$('#shareOptions').modal('show');
			$('#share_msg').val( $('#message').val() );
			<?php print_r($login->deshortUrl($url)); ?>
			$('#fbshare_link').attr('href','<?=BASE_URL;?>process.php?action=fbpost&msg='+$('#message').val()+'&'+$('#fbshare').attr('href'));
			//$('#fbshare').attr('href','https://www.facebook.com/sharer.php?u=<?=$url;?>&t=' + $('#message').val());
		});
		$('#emailBtn').click(function(e){
			e.preventDefault();
			$('#shareOptions').modal('hide');
			$('#emailVideo').modal('show');
			$('#s_msg').val( $('#share_msg').val() );
			$('#s_url').val( $('#fbshare').attr('href') );
		});
		$('#goback').click(function(e){
			e.preventDefault();
			$('#emailVideo').modal('hide');
			$('#shareOptions').modal('show');
			$('#share_msg').val( $('#s_msg').val() );
		});
		
		$('#main-menu').each(function() {
			$(this).on('click', '.accordion-toggle', function(event) {
        		event.stopPropagation();
        		var $this = $(this);

        		var parent = $this.data('parent');
        		var actives = parent && $(parent).find('.collapse.in');

        		// From bootstrap itself
        		if (actives && actives.length) {
           			hasData = actives.data('collapse');
            		if (hasData && hasData.transitioning) return;
            		actives.collapse('hide');
       			}

        		var target = $this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, ''); //strip for ie7
        
        		$(target).collapse('toggle');
    		});
		});
		
		
	});    
        
        
	$(function(){
	
	$('.carousel').carousel();
	
	<?php if($has_rewards): ?>	
	/*var tour = new Tour();
	tour.addStep({
  		element: "#rewardsLink",
  		placement: "bottom",
  		reflex: true,
  		next: -1,
  		prev: -1,
  		animation: true,
 		content: "You have rewards waiting for you.",
 		onShow: function (tour) {
 			setTimeout(function(){
				tour.next()
 				tour.showStep(1)
        	}, 6000)
 		}
	});
	tour.restart();*/
	<?php 
	$has_rewards = false;
	endif; 
	?>
	
	<?php
	if(!empty($_SESSION['signup'])){
	?>
	 
	
	 TINY.box.show({
	 	html:'<center><strong style="font-size:2em;padding-bottom:10px;">Congratulations!</strong><br /><font style="font-size:1.4em;">You just earned <?=$_SESSION['signup']?> points</font><br /><img src="images/success.png" alt="Account Activation" /></center>',
	 	animate:true,
	 	height:180,
	 	close:true,
	 	closejs:function(){
	 		
	 		var tour = new Tour();
	 		tour.addStep({
  				element: "#inviteBtn",
  				placement: "bottom",
  				reflex: true,
  				next: -1,
  				prev: -1,
  				animation: true,
 				content: "Click here to start earning points and rewarding friends and family.",
 				onShow: function (tour) {
 					setTimeout(function(){
 						tour.next()
 						tour.showStep(1)
        	 		}, 6000)
 				},
 				onHide: function (tour) {
 					$('.popover').fadeOut('slow')
 				}
			});
			tour.addStep({
  				element: "#videoBtn",
  				placement: "bottom",
  				reflex: true,
  				next: -1,
  				prev: -1,
  				animation: true,
 				content: "Create stunning videos and earn points for sharing.",
 				onShow: function (tour) {
 					setTimeout(function(){
        	 			tour.next()
 						tour.showStep(2)
        	 		}, 6000)
 				},
 				onHide: function (tour) {
 					$('.popover').fadeOut('slow')
 				}
			});
			/*tour.addStep({
  				element: "#facebookLink",
  				placement: "bottom",
  				reflex: true,
  				next: -1,
  				prev: -1,
  				animation: true,
 				content: "You can also invite through Facebook! check it out.",
 				onShow: function (tour) {
 					setTimeout(function(){
        	 			tour.end()
        	 		}, 6000)
 				},
 				onHide: function (tour) {
 					//$(tour).fadeOut(2000)
 				}
			});*/
			tour.restart();
	 		
	 	}
	 });
	<?php
	unset($_SESSION['signup']);
	}
	?>
	
	$(".theme_select").click(function(e){
	e.preventDefault();
	var selected = $(this).attr("title");
	var url = $(this).attr("href");
	var themes = {
			"outline": [{
				"title":"Outline",
				"thumb":"http://assets.stupeflix.com/editor/templates/outline.jpg",
				"url":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/0i6BM/jEyu72hakkVgvQtgfXCf/youtube/movie.mp4",
				"id":9,
				"description":"Outline will take your pics and videos for a classy ride, sliced and diced by lively white stripes."
			}],
			"tiles": [{
				"title":"Tiles",
				"thumb":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/25SSD/GBU5bubFDMY6Ml9JU16g/youtube/thumb.jpg",
				"url":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/25SSD/GBU5bubFDMY6Ml9JU16g/youtube/movie.mp4",
				"id":29,
				"description":"Turn your photos & clips into a mesmerizing video that looks like a movie trailer!"
			}],
			"classic": [{
				"title":"Classic",
				"thumb":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/BndfN/X1VDDwBlLkYWGPOYHUZo/youtube/thumb.jpg",
				"url":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/BndfN/X1VDDwBlLkYWGPOYHUZo/youtube/movie.mp4",
				"id":15,
				"description":"The original classic video theme: a slideshow with soft effects and elegant transitions."
			}],
			"scrapbook": [{
				"title":"Scrapbook",
				"thumb":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/e49pm/5UjA2aATw36CEt2cGAih/youtube/thumb.jpg",
				"url":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/e49pm/5UjA2aATw36CEt2cGAih/youtube/movie.mp4",
				"id":16,
				"description":"Scrapbook arranges your pictures and videos into a virtual scrapbook, bringing memories back to life."
			}],
			"unreel": [{
				"title":"Unreel",
				"thumb":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/o6LRH/jyzEnilix5SuTaiKkQXG/youtube/thumb.jpg",
				"url":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/o6LRH/jyzEnilix5SuTaiKkQXG/youtube/movie.mp4",
				"id":17,
				"description":"Unreel develops your digital media on professional photo studio reel. It's positively negative, unreal reel."
			}],
			"blueprint": [{
				"title":"Blueprint",
				"thumb":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/p0svd/K3BKfwBnr0XKZZBStZAZ/youtube/thumb.jpg",
				"url":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/p0svd/K3BKfwBnr0XKZZBStZAZ/youtube/movie.mp4",
				"id":18,
				"description":"Blueprint lets you architect a story with your photos and videos. Let's go back to the drawing board!"
			}],
			"comics": [{
				"title":"Comics",
				"thumb":"http://static.stupeflix.com/studio/template/marvel/images/comics-thumbnail-youtube.jpg",
				"url":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/E7P0M/T1HAdRs0vM8mbyWeCENC/youtube/movie.mp4",
				"id":26,
				"description":"With Comics, tell an epic story where you and your friends are the super heros!"
			}],
			"party": [{
				"title":"Party",
				"thumb":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/hmnXN/rWNIhexMCvPHj9pLsk9I/youtube/thumb.jpg",
				"url":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/hmnXN/rWNIhexMCvPHj9pLsk9I/youtube/movie.mp4",
				"id":27,
				"description":"It's party time! An electric theme for peppy videos."
			}],
			"1901": [{
				"title":"1901",
				"thumb":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/Gl7B7/wXfDCjaCmKVL31MmteJP/youtube/thumb.jpg",
				"url":"http://stupeflix-1.0.s3.amazonaws.com/9zvzeLiZ6kw2VqEM2bVV/Gl7B7/wXfDCjaCmKVL31MmteJP/youtube/movie.mp4",
				"id":25,
				"description":"Make your own silent movies! Win oscars!"
			}]
			
	};
	
	var current_id = themes[selected][0].id;
	var current_title = themes[selected][0].title;
	var current_video = themes[selected][0].url;
	var current_desc = themes[selected][0].description;
	var current_thumb = themes[selected][0].thumb;
	
	url = url.replace("ID",current_id);
	
	TINY.box.show({
	 	html:'<div class="row"><div class="span4 columns"><div class="flowplayer is-splash" data-rtmp="rtmp://s3b78u0kbtx79q.cloudfront.net/cfx/st/"><video width="380" height="264" preload="none" poster="'+current_thumb+'"><source type="video/flash" src="mp4:'+current_video+'"/><source type="video/mp4" src="'+current_video+'"/></video></div></div><div class="span3"><h4>'+current_title+'</h4><p>'+current_desc+'</p><a href="'+url+'" class="btn btn-primary btn-large">Make a video</a></div></div>',
	 	//html:'<div class="row"><div class="span4 columns"><video id="'+current_id+'" class="video-js vjs-default-skin" controls preload="auto" width="380" height="264" poster="'+current_thumb+'" data-setup="{}"><source src="'+current_video+'"  type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'></video></div><div class="span2"><h4>'+current_title+'</h4><p>'+current_desc+'</p><a href="'+url+'" class="btn btn-primary btn-large">Make a Video</a></div></div>',
	 	animate:true,
	 	width:600,
	 	height:264,
	 	close:true
	 });
	 
	});
	
    $("#addemail").click(function(e){
    	//var textarea = document.getElementById('emails');
    	//var lines = textarea.value.match(/\n/g).length + 1
    	//if(lines>=100){
    	//	alert('You have reached your limit of '+lines+' contacts in the inviter.');
        //	return false;
    	//}
    	var email = $('#email_add').val();
        $.ajax({
        	type: "POST",
        	url: "process.php",
        	data: 'email='+email+'&action=checkemail',
        	dataType: "json",
        	success: function(data) {
        	 if(data.success) {
        	 	var content = $('#emails').val();
        	 	var split = content.split("\\n");
        	 	var phone = $('#phone_add').val();
        	 	var addname = $('#name_add').val();
        	 	if(split != ''){
        	 	 for(var one in split){    	 	 	
        	 		var checkemail = split[one];
        	 		checkemail = $.trim(checkemail.toLowerCase());
        	 		checkemail = checkemail.split(" - ");
        	 		addemail = $.trim(email.toLowerCase());
        	 		if(checkemail[1] == addemail || checkemail[2] == addemail){
        	 			alert('This email address "'+email+'" already exists in your invites list.');
        	 			return false;
        	 		}
        	 	 }
        	 	}
        	 	$('#emails').val( $('#emails').val() + addname+' - '+phone+' - '+email+"\n");
    			$('#email_add').val('');
    			$('#name_add').val('');
    			$('#phone_add').val('');
    		 } else {
    		 	alert('Please use another email address this one is either already been used or is invalid.');
    		 }
    		}
    	});
    });
    $(".redeem").click(function(e){
    	e.preventDefault();
    	if ($(this).hasClass("disabled")) return false;
    	$(this).addClass('disabled');
    	var reward = $(this).attr('id');
    	$.ajax({
        	type: "POST",
        	url: "process.php",
        	data: 'action=redeem&reward='+reward,
        	dataType: "json",
        	success: function(data) {
        	 if(data.success) {
        	 	document.location.href='thankyou.php'
        	 } else if(data.session) {
        	 	alert('Your session expired. Please login again.');
        	 	document.location.href='index.php'
    		 } else {
    		 	alert('Please use another email address this one is either already been used or is invalid.');
    		 }
    		}
    	});
    });
    
    $("#sendVideoEmail").submit(function(e){
    	e.preventDefault();
    	dataString = $("#sendVideoEmail").serialize();
    	$.ajax({
        	type: "POST",
        	url: "process.php",
        	data: dataString,
        	dataType: "json",
        	success: function(data) {
        		
    		}
    	});
    });
    
    $("#video_maker").submit(function(e){
    	e.preventDefault();
    	$('.video_status').fadeIn();
    	dataString = $("#video_maker").serialize();
    	uid = $('#video_user').val();
    	resource = $('#video_resource').val();
    	$.ajax({
        	type: "POST",
        	url: "process.php",
        	data: dataString,
        	dataType: "json",
        	success: function(data) {
        		$('.video_status .video_text').html(data);
        		setTimeout(function(){
        	 		checkStatus(uid,resource);
        	 	}, 5000)
    		}
    	});
    	
    });
    
    function checkStatus(u,r){
		$.get('process.php',{ user: u, resource: r, action: "videostatus" }, function(data){
			if(data=='success') {
				$('.video_status').hide();
				return false;
			} else {
				$('.video_status .video_text').html(data);
				checkStatus(uid,resource);
			}
    	});		
    }
    
    $("#inviteForm").submit(function(e){
    	e.preventDefault();
    	if( $('#emails').val()=='' ) {
    		alert('Please add emails to your invite list before hitting submit.');
    		return false;
    	} else {
    		this.submit();
    	}
    });
    $("#purchaseForm").submit(function(e){
    	e.preventDefault();
    	if( $('#first_name').val()=='' ) {
    		alert('Please fill out your billing first name.');
    		$('#first_name').addClass('form-error');
    		$(".wrapper").goTo();
			return false;
		} else if( $('#last_name').val()=='' ) {
    		alert('Please fill out your billing last name.');
    		$('#last_name').addClass('form-error');
    		$(".wrapper").goTo();
			return false;
		} else if( $('#address1').val()=='' ) {
    		alert('Please fill out your billing address.');
    		$('#address1').addClass('form-error');
    		$(".wrapper").goTo();
			return false; 
		} else if( $('#city').val()=='' ) {
    		alert('Please fill out your billing town/city.');
    		$('#city').addClass('form-error');
    		$(".wrapper").goTo();
			return false;
		} else if( $('#postal_code').val()=='' ) {
    		alert('Please fill out your billing postal code.');
    		$('#postal_code').addClass('form-error');
    		$(".wrapper").goTo();
			return false; 
		} else if( $('#card_num').val()=='' ) {
    		alert('Please fill out your billing credit card number.');
    		$('#card_num').addClass('form-error');
    		$(".wrapper").goTo();
			return false;
		} else if( $('#card_name').val()=='' ) {
    		alert('Please fill out your billing credit card name.');
    		$('#card_name').addClass('form-error');
    		$(".wrapper").goTo();
			return false;
		} else if( $('#cvv2').val()=='' ) {
    		alert('Please fill out your billing cvv2.');
    		$('#cvv2').addClass('form-error');
    		$(".wrapper").goTo();
			return false; 	
    	} else {
    		$('#ajax-loading').fadeIn();
    		dataString = $("#purchaseForm").serialize();
    		$.ajax({
        	type: "POST",
        	url: "process.php",
        	data: dataString,
        	dataType: "json",
        	success: function(data) {
        		if(data.success){
        	 		$('#ajax-loading').fadeOut();
        	 		document.location.href='success.php?msg='+data.success;
        	 	} else {
        	 		$('#ajax-loading').fadeOut();
        			alert(data.error);
        	 	}
    		},
    		error: function(data) {
        		$('#ajax-loading').fadeOut();
        		alert(data.error);
        	}
    		});
    	}
    });
});
</script>
    </body>
</html>