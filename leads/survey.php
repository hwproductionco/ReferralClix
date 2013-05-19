<?php include('config/db.php');
if(!empty($_GET['uid'])) $referral = $_GET['uid']; else $referral = '';
$ref = getUserInfo($referral);
if(!empty($ref)){
	$name = $ref->first_name." ".$ref->last_name;
}
$fb = empty($_GET['fb']) ? '' : $_GET['fb'];

// FIGURE OUT NAME
if(strpos($_GET['cname'],"  ")){
 $cname = explode("  ",$_GET['cname']);
 if(!empty($cname[1])){
	$cl = explode(" ",$cname[1]);
	if(!empty($cl[1])) $clast = $cl[1]; else $clast = $cname[1];
	$cfirst = $cname[0];
 }
}else{
$cname = explode(" ",$_GET['cname']);
$cfirst = $cname[0];
$clast = $cname[1];
}
// end

$phone = $_GET['phone'];
$email = $_GET['email'];
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
    	<div id="fb-root"></div>
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
                <div class="span3">
                
                <?php if($fb==1): ?>
                    
                    <h4>Salutations from <?=VENDOR_NAME;?></h4>                            

<p>Your friend <?=$name;?> asked us to get ahold of you.  We both think that you could use a free vacation!  We want to share our excitement for a new travel company that saves you and your family thousands of dollars on accommodations around the world as well as provides discounts on things you use every day on vacation and at home. </p>

<p><strong>For filling our our short survey you will qualify for a free vacation and receive:</strong></p>

<p><ol style="margin-left:25px;"><li>$500 dollar travel voucher to use at any of our affiliated resorts</li>
<li>1 year complimentary membership into our <a href="http://destinationlifestyles.com/membership.asp" target="_blank">Destination Lifestyles</a> global savings program</li></ol></p>

<p><strong>What is the Destination Lifestyles Global Savings Program:</strong></p>
<p>It's a program that can save you and your family thousands of dollars on the things you do and buy everyday while vacationing and at home.  Use our <a href="http://destinationlifestyles.com/calculator.asp" target="_blank">calculator</a> to calculate your savings. Expect to save up to 50% on over 200,000 discounts including:</p>

<p>- Rental Cars<br>
- Dinging<br>
- Shopping<br>
- Golf<br>
- Wine Club<br>
- Discount Cards<br>
- Activities and Excursion</p>

<p>Lastly, send your friend a huge thank you because he is the one that sent you the hook up, but don’t worry he was also rewarded!</p>

<p>Happy Travels!<br>The Travel Team</p>
				
				<?php else: ?>
				
				<img src="images/survey-vacation.jpg" class="hidden-phone" alt="" />
				
				<?php endif; ?>
                    
                </div><!--/span-->

                <div class="span9 content">
                
                    <div class="banner img3">
                        <h2>Site Survey</h2>
                        <!-- <p>Thank you for taking the time to fill out our site survey! You are now one step closer to
qualifying for a free vacation! We believe in amazing customer service and we will be using this survey to custom taylor an amazing vacation just for you!</p> //--> <p>We are a travel company with many resorts around the world.  We have  excess space to fill and we want to reach new people that would not have known about our resort.  This is a win, win; our resorts get new faces on our beautiful properties and you get to quality for a free vacation, a $500 travel voucher, and a membership that saves your family lots of money.</p>
                    </div>
                    
                    <hr>

                    <div id="message_ajax"></div>
                    <div id="auth-status">
        				<div id="auth-loggedout" style="margin-bottom:10px;">
          					<a href="#" id="auth-loginlink"><img src="images/facebook-connect.png" alt="Connect with Facebook" /></a>
        				</div>
        				<div id="auth-loggedin" style="display:none;margin-bottom:10px;">
          					Hi, <span id="auth-displayname"></span> you are connected with Facebook  (<a href="#" id="auth-logoutlink">disconnect</a>)
      					</div>
      				</div>
                    <div class="well">
                        <form action="<?=BASE_URL;?>process.php" method="post" class="apply-form" name="leadform" id="ajaxForm">
							<input type="hidden" name="action" value="lead" />
							<input type="hidden" name="fb" value="<?=$fb;?>" /> 
							<input type="hidden" name="campaign_id" value="<?php echo QUALEADS_CAMPAIGN; ?>" />
                            <div class="row">
                             <div class="span4">
                            	<label>Your Referrer:</label>
                            	<input type="hidden" name="custom_field_7" value="<?=$referral;?>" /><strong><?=$name;?></strong><br><br>
                             </div>
                             <div class="span4"><span class="text-error">*</span> Required Fields</div>
                            </div>
                            <div class="row">
                             <div class="span4">
                            	<label>Member First Name <span class="text-error">*</span></label>
								<input type="text" class="span4" name="first_name"  value="<?=$cfirst;?>" />
                             </div>
                             <div class="span4">
                             	<label>Member Last Name <span class="text-error">*</span></label>
								<input type="text" class="span4" name="last_name"  value="<?=$clast;?>" />
                             </div>
                            </div>
							<div class="row">
							 <div class="span4">
							 	<label>Address Line 1<span class="text-error">*</span></label>
							 	<input type="text" class="span4" name="street1" />
							 </div>
							 <div class="span4">
							 	<label>Address Line 2 <small>(optional)</small></label>
								<input type="text" class="span4" name="street2" />
							 </div>
							</div>
							<div class="row">
							 <div class="span4">
							 	<label>Country <span class="text-error">*</span></label>
								<select name="country" id="country_select" class="span4"> 
									<option value="" selected="selected">Please Select</option> 
									<option value="United States">United States</option> 
									<option value="United Kingdom">United Kingdom</option> 
									<option value="Afghanistan">Afghanistan</option> 
									<option value="Albania">Albania</option> 
									<option value="Algeria">Algeria</option> 
									<option value="American Samoa">American Samoa</option> 
									<option value="Andorra">Andorra</option> 
									<option value="Angola">Angola</option> 
									<option value="Anguilla">Anguilla</option> 
									<option value="Antarctica">Antarctica</option> 
									<option value="Antigua and Barbuda">Antigua and Barbuda</option> 
									<option value="Argentina">Argentina</option> 
									<option value="Armenia">Armenia</option> 
									<option value="Aruba">Aruba</option> 
									<option value="Australia">Australia</option> 
									<option value="Austria">Austria</option> 
									<option value="Azerbaijan">Azerbaijan</option> 
									<option value="Bahamas">Bahamas</option> 
									<option value="Bahrain">Bahrain</option> 
									<option value="Bangladesh">Bangladesh</option> 
									<option value="Barbados">Barbados</option> 
									<option value="Belarus">Belarus</option> 
									<option value="Belgium">Belgium</option> 
									<option value="Belize">Belize</option> 
									<option value="Benin">Benin</option> 
									<option value="Bermuda">Bermuda</option> 
									<option value="Bhutan">Bhutan</option> 
									<option value="Bolivia">Bolivia</option> 
									<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option> 
									<option value="Botswana">Botswana</option> 
									<option value="Bouvet Island">Bouvet Island</option> 
									<option value="Brazil">Brazil</option> 
									<option value="British Indian Ocean Territory">British Indian Ocean Territory</option> 
									<option value="Brunei Darussalam">Brunei Darussalam</option> 
									<option value="Bulgaria">Bulgaria</option> 
									<option value="Burkina Faso">Burkina Faso</option> 
									<option value="Burundi">Burundi</option> 
									<option value="Cambodia">Cambodia</option> 
									<option value="Cameroon">Cameroon</option> 
									<option value="Canada">Canada</option> 
									<option value="Cape Verde">Cape Verde</option> 
									<option value="Cayman Islands">Cayman Islands</option> 
									<option value="Central African Republic">Central African Republic</option> 
									<option value="Chad">Chad</option> 
									<option value="Chile">Chile</option> 
									<option value="China">China</option> 
									<option value="Christmas Island">Christmas Island</option> 
									<option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option> 
									<option value="Colombia">Colombia</option> 
									<option value="Comoros">Comoros</option> 
									<option value="Congo">Congo</option> 
									<option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option> 
									<option value="Cook Islands">Cook Islands</option> 
									<option value="Costa Rica">Costa Rica</option> 
									<option value="Cote D'ivoire">Cote D'ivoire</option> 
									<option value="Croatia">Croatia</option> 
									<option value="Cuba">Cuba</option> 
									<option value="Cyprus">Cyprus</option> 
									<option value="Czech Republic">Czech Republic</option> 
									<option value="Denmark">Denmark</option> 
									<option value="Djibouti">Djibouti</option> 
									<option value="Dominica">Dominica</option> 
									<option value="Dominican Republic">Dominican Republic</option> 
									<option value="Ecuador">Ecuador</option> 
									<option value="Egypt">Egypt</option> 
									<option value="El Salvador">El Salvador</option> 
									<option value="Equatorial Guinea">Equatorial Guinea</option> 
									<option value="Eritrea">Eritrea</option> 
									<option value="Estonia">Estonia</option> 
									<option value="Ethiopia">Ethiopia</option> 
									<option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option> 
									<option value="Faroe Islands">Faroe Islands</option> 
									<option value="Fiji">Fiji</option> 
									<option value="Finland">Finland</option> 
									<option value="France">France</option> 
									<option value="French Guiana">French Guiana</option> 
									<option value="French Polynesia">French Polynesia</option> 
									<option value="French Southern Territories">French Southern Territories</option> 
									<option value="Gabon">Gabon</option> 
									<option value="Gambia">Gambia</option> 
									<option value="Georgia">Georgia</option> 
									<option value="Germany">Germany</option> 
									<option value="Ghana">Ghana</option> 
									<option value="Gibraltar">Gibraltar</option> 
									<option value="Greece">Greece</option> 
									<option value="Greenland">Greenland</option> 
									<option value="Grenada">Grenada</option> 
									<option value="Guadeloupe">Guadeloupe</option> 
									<option value="Guam">Guam</option> 
									<option value="Guatemala">Guatemala</option> 
									<option value="Guinea">Guinea</option> 
									<option value="Guinea-bissau">Guinea-bissau</option> 
									<option value="Guyana">Guyana</option> 
									<option value="Haiti">Haiti</option> 
									<option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option> 
									<option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option> 
									<option value="Honduras">Honduras</option> 
									<option value="Hong Kong">Hong Kong</option> 
									<option value="Hungary">Hungary</option> 
									<option value="Iceland">Iceland</option> 
									<option value="India">India</option> 
									<option value="Indonesia">Indonesia</option> 
									<option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option> 
									<option value="Iraq">Iraq</option> 
									<option value="Ireland">Ireland</option> 
									<option value="Israel">Israel</option> 
									<option value="Italy">Italy</option> 
									<option value="Jamaica">Jamaica</option> 
									<option value="Japan">Japan</option> 
									<option value="Jordan">Jordan</option> 
									<option value="Kazakhstan">Kazakhstan</option> 
									<option value="Kenya">Kenya</option> 
									<option value="Kiribati">Kiribati</option> 
									<option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option> 
									<option value="Korea, Republic of">Korea, Republic of</option> 
									<option value="Kuwait">Kuwait</option> 
									<option value="Kyrgyzstan">Kyrgyzstan</option> 
									<option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option> 
									<option value="Latvia">Latvia</option> 
									<option value="Lebanon">Lebanon</option> 
									<option value="Lesotho">Lesotho</option> 
									<option value="Liberia">Liberia</option> 
									<option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option> 
									<option value="Liechtenstein">Liechtenstein</option> 
									<option value="Lithuania">Lithuania</option> 
									<option value="Luxembourg">Luxembourg</option> 
									<option value="Macao">Macao</option> 
									<option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option> 
									<option value="Madagascar">Madagascar</option> 
									<option value="Malawi">Malawi</option> 
									<option value="Malaysia">Malaysia</option> 
									<option value="Maldives">Maldives</option> 
									<option value="Mali">Mali</option> 
									<option value="Malta">Malta</option> 
									<option value="Marshall Islands">Marshall Islands</option> 
									<option value="Martinique">Martinique</option> 
									<option value="Mauritania">Mauritania</option> 
									<option value="Mauritius">Mauritius</option> 
									<option value="Mayotte">Mayotte</option> 
									<option value="Mexico">Mexico</option> 
									<option value="Micronesia, Federated States of">Micronesia, Federated States of</option> 
									<option value="Moldova, Republic of">Moldova, Republic of</option> 
									<option value="Monaco">Monaco</option> 
									<option value="Mongolia">Mongolia</option> 
									<option value="Montserrat">Montserrat</option> 
									<option value="Morocco">Morocco</option> 
									<option value="Mozambique">Mozambique</option> 
									<option value="Myanmar">Myanmar</option> 
									<option value="Namibia">Namibia</option> 
									<option value="Nauru">Nauru</option> 
									<option value="Nepal">Nepal</option> 
									<option value="Netherlands">Netherlands</option> 
									<option value="Netherlands Antilles">Netherlands Antilles</option> 
									<option value="New Caledonia">New Caledonia</option> 
									<option value="New Zealand">New Zealand</option> 
									<option value="Nicaragua">Nicaragua</option> 
									<option value="Niger">Niger</option> 
									<option value="Nigeria">Nigeria</option> 
									<option value="Niue">Niue</option> 
									<option value="Norfolk Island">Norfolk Island</option> 
									<option value="Northern Mariana Islands">Northern Mariana Islands</option> 
									<option value="Norway">Norway</option> 
									<option value="Oman">Oman</option> 
									<option value="Pakistan">Pakistan</option> 
									<option value="Palau">Palau</option> 
									<option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option> 
									<option value="Panama">Panama</option> 
									<option value="Papua New Guinea">Papua New Guinea</option> 
									<option value="Paraguay">Paraguay</option> 
									<option value="Peru">Peru</option> 
									<option value="Philippines">Philippines</option> 
									<option value="Pitcairn">Pitcairn</option> 
									<option value="Poland">Poland</option> 
									<option value="Portugal">Portugal</option> 
									<option value="Puerto Rico">Puerto Rico</option> 
									<option value="Qatar">Qatar</option> 
									<option value="Reunion">Reunion</option> 
									<option value="Romania">Romania</option> 
									<option value="Russian Federation">Russian Federation</option> 
									<option value="Rwanda">Rwanda</option> 
									<option value="Saint Helena">Saint Helena</option> 
									<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option> 
									<option value="Saint Lucia">Saint Lucia</option> 
									<option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option> 
									<option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option> 
									<option value="Samoa">Samoa</option> 
									<option value="San Marino">San Marino</option> 
									<option value="Sao Tome and Principe">Sao Tome and Principe</option> 
									<option value="Saudi Arabia">Saudi Arabia</option> 
									<option value="Senegal">Senegal</option> 
									<option value="Serbia and Montenegro">Serbia and Montenegro</option> 
									<option value="Seychelles">Seychelles</option> 
									<option value="Sierra Leone">Sierra Leone</option> 
									<option value="Singapore">Singapore</option> 
									<option value="Slovakia">Slovakia</option> 
									<option value="Slovenia">Slovenia</option> 
									<option value="Solomon Islands">Solomon Islands</option> 
									<option value="Somalia">Somalia</option> 
									<option value="South Africa">South Africa</option> 
									<option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option> 
									<option value="Spain">Spain</option> 
									<option value="Sri Lanka">Sri Lanka</option> 
									<option value="Sudan">Sudan</option> 
									<option value="Suriname">Suriname</option> 
									<option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option> 
									<option value="Swaziland">Swaziland</option> 
									<option value="Sweden">Sweden</option> 
									<option value="Switzerland">Switzerland</option> 
									<option value="Syrian Arab Republic">Syrian Arab Republic</option> 
									<option value="Taiwan, Province of China">Taiwan, Province of China</option> 
									<option value="Tajikistan">Tajikistan</option> 
									<option value="Tanzania, United Republic of">Tanzania, United Republic of</option> 
									<option value="Thailand">Thailand</option> 
									<option value="Timor-leste">Timor-leste</option> 
									<option value="Togo">Togo</option> 
									<option value="Tokelau">Tokelau</option> 
									<option value="Tonga">Tonga</option> 
									<option value="Trinidad and Tobago">Trinidad and Tobago</option> 
									<option value="Tunisia">Tunisia</option> 
									<option value="Turkey">Turkey</option> 
									<option value="Turkmenistan">Turkmenistan</option> 
									<option value="Turks and Caicos Islands">Turks and Caicos Islands</option> 
									<option value="Tuvalu">Tuvalu</option> 
									<option value="Uganda">Uganda</option> 
									<option value="Ukraine">Ukraine</option> 
									<option value="United Arab Emirates">United Arab Emirates</option> 
									<option value="United Kingdom">United Kingdom</option> 
									<option value="United States">United States</option> 
									<option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option> 
									<option value="Uruguay">Uruguay</option> 
									<option value="Uzbekistan">Uzbekistan</option> 
									<option value="Vanuatu">Vanuatu</option> 
									<option value="Venezuela">Venezuela</option> 
									<option value="Viet Nam">Viet Nam</option> 
									<option value="Virgin Islands, British">Virgin Islands, British</option> 
									<option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option> 
									<option value="Wallis and Futuna">Wallis and Futuna</option> 
									<option value="Western Sahara">Western Sahara</option> 
									<option value="Yemen">Yemen</option> 
									<option value="Zambia">Zambia</option> 
									<option value="Zimbabwe">Zimbabwe</option>
								</select>
							 </div>
							 <div class="span4">
							 	<label>Town/City <span class="text-error">*</span></label>
								<input type="text" class="span4" name="city" />
							 </div>
							</div>
							<div class="row">
							 <div class="span4 state_show">
							 	<label>State/Province <span class="text-error">*</span></label>
								<select name="state" class="span4"> 
									<option value="" selected="selected"></option> 
									<option value="AL">Alabama</option> 
									<option value="AK">Alaska</option> 
									<option value="AZ">Arizona</option> 
									<option value="AR">Arkansas</option> 
									<option value="CA">California</option> 
									<option value="CO">Colorado</option> 
									<option value="CT">Connecticut</option> 
									<option value="DE">Delaware</option> 
									<option value="DC">District Of Columbia</option> 
									<option value="FL">Florida</option> 
									<option value="GA">Georgia</option> 
									<option value="HI">Hawaii</option> 
									<option value="ID">Idaho</option> 
									<option value="IL">Illinois</option> 
									<option value="IN">Indiana</option> 
									<option value="IA">Iowa</option> 
									<option value="KS">Kansas</option> 
									<option value="KY">Kentucky</option> 
									<option value="LA">Louisiana</option> 
									<option value="ME">Maine</option> 
									<option value="MD">Maryland</option> 
									<option value="MA">Massachusetts</option> 
									<option value="MI">Michigan</option> 
									<option value="MN">Minnesota</option> 
									<option value="MS">Mississippi</option> 
									<option value="MO">Missouri</option> 
									<option value="MT">Montana</option> 
									<option value="NE">Nebraska</option> 
									<option value="NV">Nevada</option> 
									<option value="NH">New Hampshire</option> 
									<option value="NJ">New Jersey</option> 
									<option value="NM">New Mexico</option> 
									<option value="NY">New York</option> 
									<option value="NC">North Carolina</option> 
									<option value="ND">North Dakota</option> 
									<option value="OH">Ohio</option> 
									<option value="OK">Oklahoma</option> 
									<option value="OR">Oregon</option> 
									<option value="PA">Pennsylvania</option> 
									<option value="RI">Rhode Island</option> 
									<option value="SC">South Carolina</option> 
									<option value="SD">South Dakota</option> 
									<option value="TN">Tennessee</option> 
									<option value="TX">Texas</option> 
									<option value="UT">Utah</option> 
									<option value="VT">Vermont</option> 
									<option value="VA">Virginia</option> 
									<option value="WA">Washington</option> 
									<option value="WV">West Virginia</option> 
									<option value="WI">Wisconsin</option> 
									<option value="WY">Wyoming</option>
								</select>
							 </div>
							 <div class="span4">
							 	<label class="small">Postal Code <span class="text-error">*</span></label>
								<input type="text" name="postal_code" class="span4" value="" />
							 </div>
							</div>
							<div class="row">
							 <div class="span4">
							 	<label>Email <span class="text-error">*</span></label>
								<input type="email" class="span4" name="email" value="<?=$email;?>" />
							 </div>
							 <div class="span4">
							 	<label>Confirm Email <span class="text-error">*</span></label>
								<input type="email" class="span4" name="customer_username" value="<?=$email;?>" />
							 </div>
							</div>
							<div class="row">
							 <div class="span4">
							 	<label>Phone Number <span class="text-error">*</span></label>
								<input type="tel" class="span4" name="phone" id="phone"  value="<?=$phone;?>" />
							 </div>
							 <div class="span4">
							 	<label>Birth date</label>
								<input type="text" id="birthday" class="span4" name="custom_date" />
							 </div>
							</div>
							<div class="row">
							 <div class="span4">
							 	<label>Marital Status</label>
                                <select name="custom_field_3" class="span4">
                                	<option value="">Select One</option>
                                    <option value="Married">Married</option>
                                    <option value="Single">Single</option>
                                </select>
							 </div>
							 <div class="span4">
							 	<label>House Hold Income</label>
							 	<select name="custom_field_6" class="span4">
                                	<option value="">Select One</option>
                                    <option value="$0-$49,999">$0-$49,999</option>
                                    <option value="$50,000-$99,999">$50,000-$99,999</option>
                                    <option value="$100,000-$149,999">$100,000-$149,999</option>
                                    <option value="$150,000-$200,000">$150,000-$200,000</option>
                                    <option value="$200,000">>$200,000</option>
                                </select>
							 </div>
							</div>
							<hr>
							<div class="row">
							 <div class="span4">
							 	<label>Do you travel with children?</label>
                                <input type="radio" name="custom_field_4" value="Yes" /> Yes 
                                <input type="radio" name="custom_field_4" value="No" /> No
							 </div>
							 <div class="span4">
							 	<label>Do you own a timeshare?</label>
							 	<input type="radio" name="custom_field_5" value="Yes" /> Yes 
                                <input type="radio" name="custom_field_5" value="No" /> No
							 </div>
							</div>
                            <hr>
                            <button type="submit" class="btn btn-success btn-large" data-loading-text="Processing...">Submit Survey</button>
                            <button type="reset" class="btn pull-right">Reset Form</button>
                        </form>
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
		<div id="ajax-loading" style="display:none;">Processing…<br><br><img src="assets/ajax-loader.gif" /></div>
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
 		<?php if(ENABLE_WUFOO): ?><script src="js/wufoo.js"></script><?php endif; ?>
        <script src="js/jquery.maskedinput-1.3.min.js" type="text/javascript"></script>
		<script type="text/javascript">
		$(function() {
			$.mask.definitions['~'] = "[+-]";
			$("#birthday").mask("99/99/9999");
			$("#phone").mask("(999) 999-9999");
		});
		</script>
		<script type="text/javascript">
		
      // Load the SDK Asynchronously
      (function(d){
         var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement('script'); js.id = id; js.async = true;
         js.src = "//connect.facebook.net/en_US/all.js";
         ref.parentNode.insertBefore(js, ref);
       }(document));

      // Init the SDK upon load
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '488993584482797', // App ID
          channelUrl : '//'+window.location.hostname+'/channel', // Path to your Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true  // parse XFBML
        });

        // listen for and handle auth.statusChange events
        FB.Event.subscribe('auth.statusChange', function(response) {
          if (response.authResponse) {
            // user has auth'd your app and is logged into Facebook
            FB.api('/me', function(me){
              if (me.name) {
              	console.log(me);
              	var states = new Array();
              	states["Alabama"] = "AL";  
              	states["Alaska"] = "AK";  
              	states["Arizona"] = "AZ";  
              	states["Arkansas"] = "AR";  
              	states["California"] = "CA";  
              	states["Colorado"] = "CO";  
              	states["Connecticut"] = "CT";  
              	states["Delaware"] = "DE";  
              	states["District Of Columbia"] = "DC";  
              	states["Florida"] = "FL";  
              	states["Georgia"] = "GA";  
              	states["Hawaii"] = "HI";  
              	states["Idaho"] = "ID";  
              	states["Illinois"] = "IL";  
              	states["Indiana"] = "IN";  
              	states["Iowa"] = "IA";  
              	states["Kansas"] = "KS";  
              	states["Kentucky"] = "KY";  
              	states["Louisiana"] = "LA";  
              	states["Maine"] = "ME";  
              	states["Maryland"] = "MD";  
              	states["Massachusetts"] = "MA";  
              	states["Michigan"] = "MI";  
              	states["Minnesota"] = "MN";  
              	states["Mississippi"] = "MS";  
              	states["Missouri"] = "MO";  
              	states["Montana"] = "MT";
              	states["Nebraska"] = "NE";
              	states["Nevada"] = "NV";
              	states["New Hampshire"] = "NH";
              	states["New Jersey"] = "NJ";
              	states["New Mexico"] = "NM";
              	states["New York"] = "NY";
              	states["North Carolina"] = "NC";
              	states["North Dakota"] = "ND";
              	states["Ohio"] = "OH";  
              	states["Oklahoma"] = "OK";  
              	states["Oregon"] = "OR";  
              	states["Pennsylvania"] = "PA";  
              	states["Rhode Island"] = "RI";  
              	states["South Carolina"] = "SC";  
              	states["South Dakota"] = "SD";
              	states["Tennessee"] = "TN";  
              	states["Texas"] = "TX";  
              	states["Utah"] = "UT";  
              	states["Vermont"] = "VT";  
              	states["Virginia"] = "VA";  
              	states["Washington"] = "WA";  
              	states["West Virginia"] = "WV";  
              	states["Wisconsin"] = "WI";  
              	states["Wyoming"] = "WY";
              	
              	var full_name = me.name;
              	var name = full_name.split(" ");
              	var location = me.location.name;
              	var local = location.split(", ");
              	
                document.getElementById('auth-displayname').innerHTML = name[0]+' '+name[1];
                document.getElementById('first_name').value = name[0];
                document.getElementById('last_name').value = name[1];
                document.getElementById('email').value = me.email;
                document.getElementById('email_confirm').value = me.email;
                document.getElementById('city').value = local[0];
                document.getElementById('state').value = states[local[1]];
                $('.state_show').show();
                
                if(me.locale=='en_US') document.getElementById('country_select').value = 'United States';
                
              }
            })
            document.getElementById('auth-loggedout').style.display = 'none';
            document.getElementById('auth-loggedin').style.display = 'block';
          } else {
            // user has not auth'd your app, or is not logged into Facebook
            document.getElementById('auth-loggedout').style.display = 'block';
            document.getElementById('auth-loggedin').style.display = 'none';
          }
        });

        // respond to clicks on the login and logout links
        document.getElementById('auth-loginlink').addEventListener('click', function(){
          FB.login(function(response) {}, {scope: 'email,user_birthday,user_location'});
        });
        document.getElementById('auth-logoutlink').addEventListener('click', function(){
          FB.logout();
        }); 
      } 		
		
$(function(){
	$('.state_show').hide();
	$('#country_select').change(function (){
		var str = $(this).val();
		if(str!='' && str=='United States' || str!='' && str=='Canada'){
			$('.state_show').show();
		} else {
			$('.state_show').hide();
		}
	});
	$('.noSubmit').bind('keypress', false);
    $("#ajaxForm").submit(function(e){
       	e.preventDefault();
       	$('#ajax-loading').fadeIn();
 		dataString = $("#ajaxForm").serialize();
        $.ajax({
        	type: "POST",
        	url: "process.php",
        	data: dataString,
        	dataType: "json",
        	success: function(data) {
        		$('#ajax-loading').fadeOut();
 				if(data.email_check == "invalid"){
                	$("#message_ajax").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Error: Your email address did not validate. Please try again.</div>");
                	$(".wrapper").goTo();
                } else if(data.email_check == "existing"){
                	$("#message_ajax").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Error: You have already filled out our survey.</div>");
                	$(".wrapper").goTo();
                } else if(data.pass_check == "invalid"){
                	$("#message_ajax").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Error: Your password did not validate. Please try again.</div>");
            	} else if(data.error) {
                	$("#message_ajax").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Error: " + data.error + "</div>");
                	$(".wrapper").goTo();
            	} else if(data.success) {
                	document.location.href='survey_thanks.php<?php if($fb==1) echo "?fb=1";?>';
            	}
        	},
        	error: function(data) {
        		$('#ajax-loading').fadeOut();
        		if(data.email_check == "invalid"){
                	$("#message_ajax").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Error: Your email address did not validate. Please try again.</div>");
                	$(".wrapper").goTo();
                } else if(data.email_check == "existing"){
                	$("#message_ajax").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Error: You have already filled out our survey.</div>");
                	$(".wrapper").goTo();
                } else if(data.pass_check == "invalid"){
                	$("#message_ajax").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Error: Your password did not validate. Please try again.</div>");
                	$(".wrapper").goTo();
            	} else if(data.error) {
                	$("#message_ajax").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Error: " + data.error + "</div>");
                	$('.wrapper').goTo();
            	}
        	}
        });            
    });
});
</script>
    </body>
</html>
