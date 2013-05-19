<?php
include('config/db.php');

$url = urldecode($_GET['url']);
$uname = $_GET['uname'];
$cname = $_GET['cname'];
$phone = $_GET['phone'];
$email = $_GET['email'];
$uid = $_GET['uid'];
if(!empty($cname)) $name = explode(" ",$cname);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml="lang" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=$cname;?> thinks you deserve a vacation</title>
<meta property="og:url" content="<?=BASE_URL;?>facebook.php?uid=<?=$uid;?>&email=<?=$email;?>&cname=<?=$cname;?>&phone=<?=$phone;?>&url=<?=urlencode($url);?>" />
<meta property="og:title" content="<?=$name[0];?> thinks you deserve a vacation" />
<meta property="og:description" content="Your friend <?=$name[0];?> shared this because we both think that you could use a vacation! We want to share our excitement for a new travel company that saves you and your family thousands of dollars on accommodations around the world as well as provides discounts on things you use every day on vacation and at home." />
<?php if($thumb): ?><meta property="og:image" content="<?=$thumb;?>" /><?php else: ?><meta property="og:image" content="http://www.sfx-resorts.com/emails/images/photos2.png" /><?php endif; ?>
</head>
		<body marginwidth="0" marginheight="0" bgcolor="#E3E3E3" leftmargin="0" offset="0" topmargin="0">
    		<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%" style="background: #E3E3E3">
      		<tr>
    			<td style="font-size: 12px;">
               	<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">
                <tbody>
                <tr>
                <td valign="top" align="center">
                <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                <tr>
                <td valign="bottom" height="50" align="center" colspan="3" style="color: #999999; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
                Visit Website <a href="http://www.ownerreferrals.com/" title="" target="_blank" style="color: #000000;">http://www.ownerreferrals.com/
                            </a>
                </td>
                </tr>
                <tr>
                <td valign="bottom" height="60" align="center" colspan="3">
                <a href="http://www.ownerreferrals.com/" title="" target="_blank" style="text-decoration: none; font-family: Tahoma, Arial, sans-serif; font-weight: bold; font-size: 35px; color: #666666">Demo Resort</a></td>
                </tr>
                <tr>
                <td valign="bottom" height="46" align="center" colspan="3"> </td>
                </tr>
                <tr>
                <td width="353" valign="middle" height="220" align="left">
                <img src="http://www.sfx-resorts.com/emails/images/photos2.png">
                </td>
                
                <td width="30" valign="middle" align="left"> </td>
                <td width="217" align="left" valign="middle">
                <span style="color: #484848; font-size: 23px; font-weight: 800; font-family: Tahoma, Arial, sans-serif;"><?=$cname;?></span>
                <span style="color: #484848; font-size: 23px; font-weight: 300; font-family: Tahoma, Arial, sans-serif;"><br />
                Thinks You Deserve a Beautiful Vacation</span>
                <span style="margin: 0; padding-top: 10px; color: #484848; font-size: 15px; font-weight: 400; font-family: Tahoma, Arial, sans-serif;"> <br />
                <br />
                7-Day stay at a luxury resort in various locations in Mexico. <br /><br />
                
                <table cellpadding="0" cellspacing="0" border="0" width="217" bgcolor="#F2F2F2" align="center" style="text-align:center;">
                <tr>
                  <td style="padding-top: 10px; padding-bottom: 10px; font-family:  Tahoma, Arial, sans-serif;">
                <span style="font-family:Tahoma, Arial, sans-serif;font-size:30px;color:#313131;font-weight:900;text-decoration:none;">$186</span><br />
                <span style="font-size: 12px;">Original Price: <span style="text-decoration: line-through;">$799</span></span><br />
				<span style="color: #C00000;">76% SAVINGS</span>
                </td></tr>
                </table>
                <center>              
            	<a href="<?=$url;?>">
                <img alt="" border="0" src="http://www.sfx-resorts.com/emails/images/buy_button.png">
                </a></center>
                
                </span></td>
                </tr>
                <tr>
                <td valign="bottom" height="27" colspan="3">&nbsp;</td>
                </tr>
                </tbody>
                
                </table>
                
                <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f1f1f1">
                <tbody>
                <tr>
                <td valign="top" style="background-color: #f1f1f1; background-image: url(http://www.sfx-resorts.com/emails/images/bg_subscribe.png); background-repeat: repeat-x;">
                <div>
                <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                <tr>
                <td valign="bottom" width="600" colspan="3" style="font-family: Tahoma, Geneva, sans-serif; font-size: 12px; padding-top: 25px; padding-bottom: 30px;"><span style="font-size: 15px; font-weight: bold;"> Hello,</span><br />
                 Your friend <?=$cname;?> shared this because we both think that you could use a vacation! We want to share our excitement for a new travel company that saves you and your family thousands of dollars on accommodations around the world as well as provides discounts on things you use every day on vacation and at home. <br />
                <br />
                <br /></td>
                </tr>
                <tr>
                <td width="600" align="center" valign="top">
                <img style="display: inline-block;" alt="" src="http://www.sfx-resorts.com/emails/images/photos3.png">
                </td>
                </tr>
                </tbody>
                </table>
                </div>
                </td>
                </tr>
                <tr>
                <td>
                <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                <tr>
                <td width="600" align="center" valign="bottom">&nbsp;</td>
                </tr>
                <tr>
                <td valign="middle" align="center">&nbsp;</td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table>
                <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#000000" align="center" style="border-top: 5px solid #101010; ">
                <tbody>
                <tr>
                <td valign="top" align="center">
                <table width="620" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                <tr>
                <td valign="top" align="center">
                <table width="620" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                <tr>
                  <td width="155" height="69" align="center" valign="bottom">
                    <a href="#">
                      <img style="display: inline-block;" alt="" src="http://www.sfx-resorts.com/emails/images/twitter_icon.png">
                      </a>
                    </td>
                  <td width="155" height="69" align="center" valign="bottom">
                    <a href="#">
                      <img style="display: inline-block;" alt="" src="http://www.sfx-resorts.com/emails/images/facebook_icon.png">
                      </a>
                    </td>
                  <td height="69" colspan="3" align="center" valign="bottom">
                    <a href="#">
                      <img style="display: inline-block;" alt="" src="http://www.sfx-resorts.com/emails/images/linkedin_icon.png">
                      </a>                  </td>
                  <td width="155" height="69" align="center" valign="bottom">
                    <a href="#">
                      <img style="display: inline-block;" alt="" src="http://www.sfx-resorts.com/emails/images/rss_icon.png">
                      </a>
                  </td>
                </tr>
                <tr>
                  <td valign="bottom" height="15" align="left" colspan="6">&nbsp;                  </td>
                  </tr>
                <tr>
                <td valign="middle" height="90" align="center" style="color: #403f3f; font-size: 12px; font-weight: 400; font-family: Tahoma, Arial, sans-serif;" colspan="6">&copy;2012-2013 <?=VENDOR_NAME;?>. All Rights Reserved.<br /><a title="Real Time Web Analytics" href="http://clicky.com/100589920"><img alt="Real Time Web Analytics" src="//static.getclicky.com/media/links/badge.gif" border="0" /></a>
<script src="//static.getclicky.com/js" type="text/javascript"></script>
<script type="text/javascript">try{ clicky.init(100589920); }catch(e){}</script>
<noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/100589920ns.gif" /></p></noscript></td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table></td>
  			</tr>
		</table>
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