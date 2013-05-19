<?php
session_start();

include_once('classes/login.class.php');
include_once('classes/ss.class.php');
$login = new Login();
if (!$login->isLoggedIn()) header("Location: index.php");
$user = $login->getUser();

$user_name = $_SESSION['user_name'];
$post = array('code'=>$user->user_code);
$ss = new StickStreet(SS_API,SS_USER,SS_PASSWORD,SS_ACCOUNT);
$ss->api_url = SS_API_URL;
$info = $ss->getCustomerInfo($post);

$vid_url = urldecode($_GET['video_url']);
$tmb_url = urldecode($_GET['thumb_url']);
$vid_name = urldecode($_GET['video_name']);
$vid_id = urldecode($_GET['video_id']);
$msg = urldecode($_GET['msg']);

$url_orig = BASE_URL.'video.php?uid='.$user_name.'&vid_name='.$vid_name.'&turl='.$tmb_url.'&vid_url='.$vid_url.'&msg='.$msg;

$u = $login->shortUrl($url_orig);
$url=$u['id'];

updateVideo($user->user_id,$vid_name,$vid_url,$vid_id,$tmb_url);

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
		<link rel="stylesheet" href="http://static.stupeflix.com/play/1.2/style-min.css" type="text/css" charset="utf-8"/>
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
                    <ul class="nav nav-tabs nav-stacked">
                        <li<? if($view=="dashboard") echo ' class="active"';?>><a href="bonus.php"><i class="icon icon-home"></i> Dashboard</a></li>
                        <li<? if($view=="invites") echo ' class="active"';?>><a href="bonus.php?view=invites"><i class="icon icon-check"></i> Inviter</a></li>
                        <li<? if($view=="facebook") echo ' class="active"';?>><a href="https://www.facebook.com/resortloyalty/app_207451179363944" target="_blank"><i class="icon icon-thumbs-up"></i> Facebook Inviter</a></li>
                        <li<? if($view=="myinvites") echo ' class="active"';?>><a href="bonus.php?view=myinvites"><i class="icon icon-user"></i> My Invites</a></li>
                        <li<? if($view=="videohome") echo ' class="active"';?>><a href="bonus.php?view=videohome"><i class="icon icon-facetime-video"></i> Video Maker</a></li>
                        <li<? if($view=="myvideos") echo ' class="active"';?>><a href="bonus.php?view=myvideos"><i class="icon icon-play"></i> My Videos</a></li>
                        <li<? if($view=="rewards") echo ' class="active"';?>><a href="bonus.php?view=rewards"><i class="icon icon-certificate"></i> Rewards</a></li>
                        <li<? if($view=="promos") echo ' class="active"';?>><a href="bonus.php?view=promos"><i class="icon icon-tags"></i> Promotions</a></li>
                        <li><a href="process.php?action=logout&logout=1"><i class="icon icon-signout"></i> Log out</a></li>
                    </ul><!--/nav-stacked menu-->
                    <hr>
                    <p class="hidden-phone"><strong>WHAT ARE BONUS POINTS?</strong><br />Bonus Points are our way of saying thank you for spreading the love and sharing your experience with our club! For doing that, you and your friends/family will earn amazing rewards. No gimmicks, No Fuss!</p>
                    <hr class="hidden-phone" />
                    <p class="hidden-phone"><strong>Are you Popular?<br />Do you have lots of Friends?</strong><br />
					Refer a friend that fill's out our site survey and receive a $500 Travel Voucher!…Plus earn instant Bonus Points for referring additional friends and engaging in our Facebook App! You can spend your rewards on anything within our club for things like:</p>
                	<ul class="hidden-phone">
                		<li>Maintenance Fees</li>
                		<li>Dues</li>
                		<li>Upgrades</li>
                		<li>Bookings</li>
                		<li>Specials</li>
                	</ul>
                	
                	
      
                </div><!--/span3 column-->

                <div class="span9 content">
                
                <div class="banner">
                 	<h2>Success</h2>
                 	<p>Your video was created successfully. Share or send it to your friends to earn points.</p>
                 </div>
                 
                 <hr>
                
                <div class="row">
                	<div class="span9 columns"><center><h3><?=$vid_name;?></h3>
                		<div class="sxmovie" style="width:100%; height:420px;"><!--
  						<effect type="none">
    						<video filename="<?=$vid_url;?>"/>
  						</effect>
						--></div><br />
                		<a href="" data-toggle="modal" data-target="#startShare" class="btn btn-large btn-primary"><i class="icon-info-sign"></i>&nbsp;Share Video</a></center>
					</div>
				</div>
                    
                </div><!--/span9-->
            </div><!--/row-->
            
            <hr>
            
            <div id="preparing-file-modal" title="Downloading video…" style="display: none;">We are preparing your video for download, please wait...<div class="ui-progressbar-value ui-corner-left ui-corner-right" style="width: 100%; height:22px; margin-top: 20px;"></div></div>
 
			<div id="error-modal" title="Error" style="display: none;">There was a problem downloading your video, please try again.</div>

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
				<input type="hidden" name="msg" id="share_msg" value="" />
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    				<h3 id="myModalLabel">Share Video with Friends</h3>
    			</div>
    			<div class="modal-body">
    				<center>Your video is available at<br /><a href="<?=$url;?>" target="_blank"><?=$url;?></a></center>
   	 				<a href="#" id="fbshare" class="btn btn-large span4 mt10 offset1" target="_blank"><i class=" icon-facebook"></i>&nbsp;&nbsp;Share to Facebook</a>
					<a href="" id="emailBtn" class="btn btn-large span4 mt10 offset1"><i class="icon-envelope"></i>&nbsp;&nbsp;&nbsp;Share via Email</a>
					
					<a href="<?=$vid_url;?>" class="btn btn-large span4 mt10 download offset1"><i class="icon-download"></i>&nbsp;&nbsp;&nbsp;Download Video</a>
    			</div>
    			<div class="modal-footer">
    				<button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
    			</div>
    			</form>
    		</div>


			<div id="emailVideo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" class="modal hide fade">
				<form action="process.php" method="post" name="sendVideoEmail">
				<input type="hidden" name="msg" id="s_msg" value="" />
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    				<h3 id="myModalLabel">Email Your Video</h3>
    			</div>
    			<div class="modal-body offset1">
    				<input type="hidden" name="action" value="videomailer" />
   	 				<input type="hidden" name="sender" value="<?php echo $info['customer']['first_name'].' '.$info['customer']['last_name'];?>" />
                	<input type="hidden" name="vid_url" value="<?php echo $vid_url;?>" />
                	<input type="hidden" name="thumb" value="<?php echo $tmb_url; ?>" />
   	 				<p>Recipient Name</p>
   	 				<input type="text" name="name" placeholder="(optional)" />
   	 				<p>Recipient Email Address</p>
   	 				<input type="text" name="email" placeholder="email@address.com" />
   	 				<p>Recipient Phone #</p>
   	 				<input type="text" name="phone" placeholder="(optional)" />
    			</div>
    			<div class="modal-footer">
    				<button type="button" class="btn btn-secondary left" id="goback" name="backBtn">Go Back</button>
     				<button type="submit" class="btn btn-primary">Send Email</button>
    			</div>
    			</form>
    		</div>

            <footer>
                <p>&copy; <?=RESORT_NAME;?> 2012</p>
                <a title="Real Time Web Analytics" href="http://clicky.com/100589920"><img alt="Real Time Web Analytics" src="//static.getclicky.com/media/links/badge.gif" border="0" /></a>
<script src="//static.getclicky.com/js" type="text/javascript"></script>
<script type="text/javascript">try{ clicky.init(100589920); }catch(e){}</script>
<noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/100589920ns.gif" /></p></noscript>
            </footer>
            
        </div> <!-- /container wrapper -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/jquery-1.8.0.min.js"><\/script>')</script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/animatedcollapse.js"></script>
        <script src="js/script.js"></script>
        <script src="js/jquery.filedownload.js"></script>
        <script type="text/javascript">
  			document.write(unescape('%3Cscript src="http://static.stupeflix.com/play/1.2/play-min.js" type="text/javascript"%3E%3C/script%3E'));
		</script>
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
		
		$(document).ready(function(){
			$('form#shareForm').submit(function(e){
				e.preventDefault();
				$('#startShare').modal('hide');
				$('#shareOptions').modal('show');
				$('#share_msg').val( $('#message').val() );
				$('#fbshare').attr('href','https://www.facebook.com/sharer.php?u=<?=$url;?>&t=' + $('#message').val());
			});
			$('#emailBtn').click(function(e){
				e.preventDefault();
				$('#shareOptions').modal('hide');
				$('#emailVideo').modal('show');
				$('#s_msg').val( $('#share_msg').val() );
			});
			$('#goback').click(function(e){
				e.preventDefault();
				$('#emailVideo').modal('hide');
				$('#shareOptions').modal('show');
				$('#share_msg').val( $('#s_msg').val() );
			});
		});
		
		$(function() {
    	 $("a.download").click(function (e) {
 		e.preventDefault();
         var $preparingFileModal = $("#preparing-file-modal");
 
         $preparingFileModal.dialog({ modal: true });
 
         $.fileDownload($(this).attr('href'), {
            successCallback: function (url) {
 
                $preparingFileModal.dialog('close');
            },
            failCallback: function (responseHtml, url) {
 
                $preparingFileModal.dialog('close');
                $("#error-modal").dialog({ modal: true });
            }
         });
         return false; //this is critical to stop the click event which will trigger a normal file download!
    	 });
		});
  
 		</script>
    </body>
</html>
