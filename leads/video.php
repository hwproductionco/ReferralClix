<?php
include('config/db.php');

$cname = $_GET['cname'];
$video_url = $_GET['vid_url'];
$video_name = $_GET['vid_name'];
$thumb_url = $_GET['turl'];
$uid = $_GET['uid'];
$referrer = $_GET['referrer'];
$email = $_GET['email'];
$phone = $_GET['phone'];

$msg = $_GET['msg'];
if(empty($msg)) $msg = 'Watch my travel video and unlock an insanely discounted vacation at the same resort I just went to!';
//if(!empty($cname)) $name = explode(" ",$cname);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml="lang" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=$video_name;?></title>
<meta property="og:title" content="<?=$video_name;?>" />
<meta property="og:description" content="<?=$msg;?>" />
<meta property="og:image" content="<?=$thumb_url;?>" />
<style type="text/css">
html {
    -webkit-text-size-adjust: none;
}
.field {
padding: 6px;
border: 1px solid #CCC;
width: 200px;	
}
.btn-success {
    background-color: #5BB75B;
    background-image: -moz-linear-gradient(center top , #5FBF00, #3F7F00);
    background-repeat: repeat-x;
    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
    color: #FFFFFF;
}
.btn {
    border-radius: 2px 2px 2px 2px;
}
.btn-success {
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
}
.btn {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    border-image: none;
    border-style: solid;
    border-width: 1px;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    line-height: 20px;
    margin-bottom: 0;
    padding: 4px 12px;
    text-align: center;
    vertical-align: middle;
}
input, button, select, textarea {
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
}
label, input, button, select, textarea {
    font-weight: normal;
}
label {
font-family: Arial, Helvetica, sans-serif;
font-size: 11px;	
}
</style>
<link rel="stylesheet" href="http://static.stupeflix.com/play/1.2/style-min.css" type="text/css" charset="utf-8"/>
</head>
<body marginwidth="0" marginheight="0" bgcolor="#E3E3E3" leftmargin="0" offset="0" topmargin="0">
   <br /><br />
   <center>
   <div style="width: 700px; font-family: Tahoma, Geneva, sans-serif; font-size: 12px; text-align: left;">
   <h2 style="margin: 0px; font-size: 23px; font-weight: 400; color: #2291BB"><?=$video_name;?></h2>
   at <?=RESORT_NAME;?><br /><br />
       <div style="width: 700px; background: #ffffff; overflow: hidden; margin-bottom: 80px; border: 1px solid #CCC">
           <div style="width: 700px; height:410px; background: #000000">
           	<div class="sxmovie" style="width:100%; height:360px;"><!--
  <effect type="none">
    <video filename="<?=$video_url;?>"/>
  </effect>
--></div>
           </div>
           <div style="width: 650px; padding:25px; overflow: hidden;">
           	<h2 style="margin: 0px; font-size: 18px; font-weight: 300; color: #333; margin-bottom: 10px;">I'd like more info!</h2>
            
            <form>
       	    <table border="0" width="100%" cellpadding="0"  cellspacing="0">
                	<tr>
                    	<td width="38%" height="66" valign="top">
                        <label style="font-family: Arial, Helvetica, sans-serif; font-size: 11px;">First Name</label><br />
                        <input type="text" class="field" value="<?=$name[0];?>" />
                        </td>
                        <td width="62%" valign="top">
                        <label style="font-family: Arial, Helvetica, sans-serif; font-size: 11px;">Last Name</label><br />
                        <input type="text" class="field" value="<?=$name[1];?>" />
                        </td>
                    </tr>
                    <tr>
                    	<td width="38%">
                        <label style="font-family: Arial, Helvetica, sans-serif; font-size: 11px;">E-Mail</label><br />
                        <input type="text" class="field" value="<?=$email;?>" />
                        </td>
                        <td width="62%">
                        <label style="font-family: Arial, Helvetica, sans-serif; font-size: 11px;">Phone Number</label><br />
                        <input type="text" class="field" value="<?=$phone;?>" />
                        </td>
                  </tr>
                </table>
                <button class="btn btn-success" type="submit" style="margin-top: 10px;">Submit</button>
            </form>
            <br />
            <hr style="border: 1px solid #CCC" />
            <br />
            <h2 style="margin: 0px; font-size: 19px; font-weight: 800; color: #333; margin-bottom: 10px;">Vacation Deal: <span style="color: #1A650C">$249</span></h2>
            <img src="http://www.sfx-resorts.com/emails/images/resort2.jpg" style="border: 1px solid #999; float: left;">
            <div style="width: 225px; float: right;">
            <strong style="font-size: 14px; margin-bottom: 15px;">7-Day Stay in Mexico</strong><br /><br />
            <span style="line-height: 20px;">&bull; Multiple location offered in Mexico<br />
            &bull; Luxury resort and rooms<br />
            &bull; Beautiful pools<br /></span>
            <button class="btn btn-success" type="submit" style="margin-top: 10px;">Book Now</button>
            </div>
            
            <div style="clear: both"></div>
            <br />
            <hr style="border: 1px solid #CCC" />
            <br />
            
            <div style="width: 280px; float: left;">
            <h2 style="margin: 0px; font-size: 19px; font-weight: 800; color: #333; margin-bottom: 10px;">About</h2>
            <span style="line-height: 17px;">A visit to The Grand Mayan Los Cabos luxury resort is truly an intimate escape. Based upon the concept of a boutique hotel, everything about The Grand Mayan Los Cabos is unique, luxurious, and designed to provide the most romantic getaway possible. Located in the beautiful San Jose del Cabo region, The Grand Mayan Los Cabos luxury resort is surrounded by beautiful Pacific views and is lulled by lush, tropical breezes.</span>
            </div>
            <img src="http://www.sfx-resorts.com/emails/images/resort.jpg" style="border: 1px solid #999; float: right;">
            
           </div>
       </div>
   </div>
   <span style="color: #403f3f; font-size: 12px; font-weight: 400; font-family: Tahoma, Arial, sans-serif;">&copy;2012-2013 <?=VENDOR_NAME;?>. All Rights Reserved.</span><br /><a title="Real Time Web Analytics" href="http://clicky.com/100589920"><img alt="Real Time Web Analytics" src="//static.getclicky.com/media/links/badge.gif" border="0" /></a>
<script src="//static.getclicky.com/js" type="text/javascript"></script>
<script type="text/javascript">try{ clicky.init(100589920); }catch(e){}</script>
<noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/100589920ns.gif" /></p></noscript><br /><br /><br />
   </center>
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
 	</script>
</body>
</html>