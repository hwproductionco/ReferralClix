<?php
// ERROR REPORTING - comment out to disable
error_reporting(E_ERROR);
ini_set('display_errors', '1');

session_start();

/*********************************************************************************/
/*                                                                               */
/*   CONFIG                                                                      */
/*                                                                               */
/*********************************************************************************/

define("BASE_URL","http://www.ownerreferrals.com/"); // URL this is installed at with slash / at the end

define("DB_HOST", "localhost");
define("DB_NAME", "db_referralclix");
define("DB_USER", "admin_clix");
define("DB_PASS", "clix2013!!");

/* EMAIL SETTINGS */
define("RESORT_NAME","Demo Resort");
define("RESORT_EMAIL","mark.witte@destinationlifestyles.com");
define("RESORT_PHONE","555-555-5678");

define("VENDOR_NAME","Resort Rewards");
define("VENDOR_EMAIL","mark.witte@destinationlifestyles.com");
define("VENDOR_PHONE","(123) 456-7890");
define("VENDOR_URL","https://rewards.clienttoolbox.com");

define("NO_REPLY","noreply@destinationlifestyles.com");
/*----------------*/

/* ENABLE OR DISABLE TEST MODE */
define("TEST_MODE",FALSE);

/* SECURITY */
define("ENABLE_IPBLOCK",FALSE);
define("ENABLE_IPRESTRICT",FALSE);


/********************************************************************************/
/*																				*/
/*			PAYPAL PAYFLOW API INFORMATION										*/
/*																				*/
/*																				*/
/********************************************************************************/

define("PAYPAL_TEST","test"); // test or live
define("PAYPAL_USER","russellbenzing");
define("PAYPAL_PASS","resortrewards1");
define("PAYPAL_VENDOR","destinationlifestyles");
define("PAYPAL_PARTNER","PayPal");
define("PAYPAL_CURRENCY","USD"); // 'USD', 'EUR', 'GBP', 'CAD', 'JPY', 'AUD'.


/********************************************************************************/
/*																				*/
/*			STICKYSTREET API INFORMATION										*/
/*																				*/
/*																				*/
/********************************************************************************/

define("SS_API","ac6ac7b07f95f7bcd583ee670ebc76420e2bb6e1");
define("SS_USER","referraldev");
define("SS_PASSWORD","hw2010");
define("SS_ACCOUNT","referraldev");
define("SS_API_URL","https://rewards.clienttoolbox.com/api.php");

define("SIGNUP_BONUS","");

define("LEADS_CAMPAIGN","3964930392664742");
define("USERS_CAMPAIGN","1574401059185043");
define("QUALEADS_CAMPAIGN","4633807623924154");

/********************************************************************************/
/*																				*/
/*			WUFOO API INFORMATION												*/
/*																				*/
/*																				*/
/********************************************************************************/

define("ENABLE_WUFOO",TRUE);
define("WUFOO_API","MW6V-OM8U-357C-8BVK");
define("WUFOO_SUBDOMAIN","resortrewards");
define("WUFOO_REGISTER_FORM","q7x2x3");
define("WUFOO_SURVEY_FORM","z7x3p9");
define("WUFOO_POST_KEY","JN0E3Rcf+qAmqZKImItygNRxuVRVFrXXxQsGaBFNRBU=");
	$field_array = array(
		'Field2'=>'First Name',
		'Field3'=>'Last Name',
		'Field10'=>'Email',
		'Field11'=>'Phone Number',
		'Field14'=>'Address',
		'Field15'=>'Address2',
		'Field16'=>'City',
		'Field17'=>'State',
		'Field18'=>'Zipcode',
		'Field19'=>'Country',
		'Field131'=>'Birthday'
	);
	
/********************************************************************************/
/*																				*/
/*			MERIDIANLINK CREDIT API INFORMATION												*/
/*																				*/
/*																				*/
/********************************************************************************/

/* TEST DATA */
//define("CREDIT_URL","https://demo.mortgagecreditlink.com/inetapi/AU/get_credit_report.aspx");
//define("CREDIT_PARTY_ID","TestSubmittingPartyID");
//define("CREDIT_USER","lifestyle");
//define("CREDIT_PASS","P24252Q8");


/* LIVE DATA */
define("CREDIT_URL","https://ucs.meridianlink.com/inetapi/AU/get_credit_report.aspx");
define("CREDIT_PARTY_ID","DestinationLifestyles02152013");
define("CREDIT_USER","destination");
define("CREDIT_PASS","lifestyles8");

/********************************************************************************/
/*																				*/
/*			MAILCHIMP API INFORMATION											*/
/*																				*/
/*																				*/
/********************************************************************************/

define("ENABLE_CHIMP",FALSE);
define("CHIMP_API","a995e09debd870f972776d3d68ec6a06-us5");
define("CHIMP_LEAD_LIST","6fc45027d");
define("CHIMP_REGISTER_LIST","b7e2ffbdf2");

/********************************************************************************/
/*																				*/
/*			MANDRILL API INFORMATION											*/
/*																				*/
/*																				*/
/********************************************************************************/

define("ENABLE_MANDRILL",TRUE);
define("MANDRILL_API","yfnxFL7j5D_7YdA29ZPm6g");

/********************************************************************************/
/*																				*/
/*			GOOGLE ANALYTICS INFORMATION										*/
/*																				*/
/*																				*/
/********************************************************************************/
define("GOOGLE_ID","UA-37653964-1");
define("GOOGLE_DOMAIN","ownerreferrals.com");
define("GOOGLE_PATH","/");

/********************************************************************************/
/*																				*/
/*			FACEBOOK API INFORMATION											*/
/*																				*/
/*																				*/
/********************************************************************************/
define("ENABLE_FACEBOOK",TRUE);
define("FB_APP_ID","488993584482797");
define("FB_APP_SECRET","81b25d77443deba9a4c5e98eba50b5ff");

/********************************************************************************/
/*																				*/
/*			PITCHPOINT API INFORMATION											*/
/*																				*/
/*																				*/
/********************************************************************************/
define("PITCHPOINT_URL","https://www.usinfosearch.com/resell/Census.php");
define("PITCHPOINT_USER","dls1");
define("PITCHPOINT_PASS","dls2012!");
define("ENABLE_PITCHPOINT_REGISTER",FALSE);
define("ENABLE_PITCHPOINT_SURVEY",FALSE);


/*******IP BLOCKING***************/
//
//   Values can be wildcards 125.125.* or 125.* or
//	 individual ip addresses 125.125.125.125 in the array()
//
/*********************************/
if(!TEST_MODE && ENABLE_IPBLOCK):
$deny = array(""); //76.217.209.254
if (in_array ($_SERVER['REMOTE_ADDR'], $deny)) {
   header('WWW-Authenticate: Basic realm="Restricted Area"');
   header('HTTP/1.0 401 Unauthorized');
   exit();
}
endif;


include_once('config/functions.php');
?>