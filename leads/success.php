<?php
include_once('classes/ss.class.php');
include_once('classes/login.class.php');
$login = new Login();
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
					<p><i class="icon icon-user"></i> Welcome to <?=RESORT_NAME;?> rewards. <a href="https://www.facebook.com/resortloyalty/" title="Like us on Facebook" class="pull-right"><img src="assets/fb.png" alt="Facebook" /></a></p>
				</div>
			</div>
        </div>

        <div class="container wrapper">
            <div class="row">

                <div class="span12 content">
                
                    <div class="banner">
                        <h2>Congratulations</h2>
                        <p><?=$_GET['msg'];?></p>
                    </div>

					<div class="well">
                    	<p>You will be receiving an email receipt soon with your purchase details. You can use your points immediately. <br><br>Thank You,<br><?=RESORT_NAME;?></p>
    
    <hr>
<center><a href="bonus.php">Go back to bonus rewards</a></center>
                    </div>
                    
                </div><!--/span9-->
            </div><!--/row-->
            
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
    </body>
</html>
