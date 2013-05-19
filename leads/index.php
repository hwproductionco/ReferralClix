<?php
include_once('classes/ss.class.php');
include_once('classes/login.class.php');
include_once('classes/facebook/facebook.php');
include_once('classes/class.facebook.php');

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
} else {
	$token = $fb->getApplicationAccessToken();
	$me = $fb->getUserProfileData();
	$me['token'] = $token;
	$login = new Login($me);
    $logoutUrl = $fb->getLogoutUrl();
}
//if(!empty($me) && $fb->isLogged()) header("Location: ".BASE_URL."bonus.php");
endif;
$msg = !empty($_GET['msg']) ? '<div class="alert alert-error">'.html_entity_decode(urldecode($_GET['msg'])).'</div>' : '';
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
        <!--[if lt IE 9]>
            <script src="js/html5-3.6-respond-1.1.0.min.js"></script>
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
					<p><i class="icon icon-user"></i> Welcome to <?=RESORT_NAME;?> rewards. Please login or register. <a href="https://www.facebook.com/resortloyalty/" title="Like us on Facebook" class="pull-right"><img src="assets/fb.png" alt="Facebook" /></a></p>
				</div>
			</div>
        </div>

        <div class="container wrapper">
            <div class="row">
                <br><br>
                <?php echo $msg; ?>
                <div class="span4 offset1 well">
                    <i class="icon icon-user"></i> MEMBER'S LOG IN
                    <hr>
                    <?php
					if (!$login->isLoggedIn()) {
					?>
                    	<form action="<?=BASE_URL;?>process.php" method="post" class="promo-form" id="loginForm">
						<input type="hidden" name="action" value="login" />
                        <label for="Email">Email:</label>
                        <input class="span4" name="user_name" type="text" id="Email" placeholder="Email Address">

                        <label for="Password">Password:</label>
                        <input class="span4" name="user_password" type="password" id="Password">

                        <label class="checkbox">
                            <input type="checkbox"> Keep me logged in
                        </label>
                        <button type="submit" class="btn btn-success">Log in</button><?php if(ENABLE_FACEBOOK): ?> -or- <a href="<?=$loginUrl;?>"><img src="images/fb-login.png" alt="Sign in with Facebook"></a><?php endif; ?>
                    </form>
                    <?php
					} else {
					
					if($fb->isLogged()):
					
					if(!$login->getUser()){
						$name = $me['first_name'].' '.$me['last_name'];
						$x = 'You do not have an account please sign up for an account to the right.';
						$btn = false;
						$logout = $logoutUrl;
					} else {
						$x = 'You are logged in.';
						$user = $login->getUser();
						$name = $user->first_name.' '.$user->last_name;
						$btn = true;
						$logout = BASE_URL.'process.php?action=logout&logout=1';
					}
					?>
						<h5>Welcome <? 
						echo $name;
						?></h5>
						<p><?=$x;?></p>
						<hr>
						<?php echo '<h4>'.$_SESSION['user_name'].'</h4>'; ?>
						<p>if you would like to logout <a href="<?=$logout;?>" title="logout">click here</a></p>
						<?php
						if($btn):
						?>
						<input type="submit" class="btn" value="View My Account" onclick="document.location.href='bonus.php';" />
						<?php
						endif;
						?>
					<?php
					else:
					?>
					
						<h5>Welcome <? 
						$user = $login->getUser();
						echo $user->first_name." ".$user->last_name;
						?></h5>
						<p>You are logged in.</p>
						<hr>
						<?php echo '<h4>'.$_SESSION['user_name'].'</h4>'; ?>
						<p>if you would like to logout <a href="<?=BASE_URL;?>process.php?action=logout&logout=1" title="logout">click here</a></p>
						<input type="submit" class="btn" value="View My Account" onclick="document.location.href='bonus.php';" />
					<?php
					endif;
					}
					?>
                </div><!--/span-->
                <div class="span4 offset1 well">
                	<i class="icon icon-cog"></i> SIGN UP FOR AN ACCOUNT
                	<hr>
                	<a href="<?=BASE_URL;?>register.php" class="btn btn-success btn-large span3">SIGN UP NOW</a>
                </div>
            </div>

            <hr>

            <footer>
                <p>&copy; <?=RESORT_NAME;?> 2012</p>
                <a title="Real Time Web Analytics" href="http://clicky.com/100589920"><img alt="Real Time Web Analytics" src="//static.getclicky.com/media/links/badge.gif" border="0" /></a>
<script src="//static.getclicky.com/js" type="text/javascript"></script>
<script type="text/javascript">try{ clicky.init(100589920); }catch(e){}</script>
<noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/100589920ns.gif" /></p></noscript>
            </footer>
            
        </div> <!-- /container -->

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/jquery-1.8.0.min.js"><\/script>')</script>
        <script src="js/bootstrap.min.js"></script>
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
        <script type="text/javascript">
$(function(){
    $("#loginForm").submit(function(e){
       	e.preventDefault();
 		dataString = $("#loginForm").serialize();
        $.ajax({
        	type: "POST",
        	url: "process.php",
        	data: dataString,
        	dataType: "json",
        	success: function(data) {
 				if(data.login_check == "invalid"){
                	$("#message_ajax").html("<div class='error'>Error: Your login information was incorrect. Please try again.</div>");
            	} else if(data.error) {
                	$("#message_ajax").html("<div class='error'>Error: " + data.error + "</div>");
            	} else if(data.success) {
            		document.location.href=data.success;
            	}
        	},
        	error: function(data) {
        		if(data.email_check == "invalid"){
                	$("#message_ajax").html("<div class='error'>Error: Your email address did not validate. Please try again.</div>");
            	} else if(data.error) {
                	$("#message_ajax").html("<div class='error'>Error: " + data.error + "</div>");
            	} else if(data.success) {
            		document.location.href=data.success;
            	}
        	}
        });            
    });
});
		</script>
    </body>
</html>
