<?php

/**
 * class Login
 * 
 * creates db connection, logs in the user, creates session
 * 
 * @author Panique <panique@web.de>
 * @version 1.0
 * @package SimplePHPLogin
 */

class Login {

    protected   $db                     = null;                     // database connection
    private     $logged_in              = false;                    // status of login    
    public      $errors                 = array();                  // collection of error messages
    public      $messages               = array();                  // collection of success / neutral messages
    
    private		$first_name				= "";
    private		$last_name				= "";
    private		$user_id				= "";						// user's id
    private     $user_name              = "";                       // user's name
    private     $user_email             = "";                       // user's email
    private     $user_password          = "";                       // user's password (what comes from POST)
    private     $user_password_hashed   = "";                       // user's hashed and saltes password
    private     $user_salt              = "";                       // user's personal salt
    
    
    public function __construct($me=NULL) {
        
        include_once("config/db.php");                  // include database constants        
        
        if ($this->checkDatabase()) {                   // check for database connection
            
            session_start();                            // create session

            if ($this->logout()) {                      // checking for logout, performing login            
                // do nothing, you are logged out now   // this if construction just exists to prevent unnecessary method calls
            } elseif ($this->loginWithSessionData()) {
                $this->logged_in = true;
            } elseif ($this->loginWithPostData()) {
                $this->logged_in = true;
            } elseif (!empty($me) && $this->loginWithFacebook($me)) {
                $this->logged_in = true;
            }        

            $this->registerNewUser();                   // check for registration data            
        } else {
            $this->errors[] = "No MySQL connection.";
        }        
    }
    
    public function set($name,$val){
    	session_start();
    	$_SESSION[trim($name)] = trim($val);
    }
    
    
    private function checkDatabase() {
        
        if (!$this->db) {                                                       // does db connection exist ?
            $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);         // create db connection     
            return (!$this->db->connect_errno ? true : false);                  // if no connect errors return true else false
        }
        
    }
    

    private function loginWithSessionData() {
        
        if (!empty($_SESSION['user_name']) && ($_SESSION['user_logged_in']==1)) {
            return true;
        } else {
            return false;
        }
        
    }
    
    public function generateSalt($max = 15) {
        $characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*?";
        $i = 0;
        $salt = "";
        while ($i < $max) {
            $salt .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
            $i++;
        }
        return $salt;
	}
	
	
    private function loginWithFacebook($me) {
        
        if(!empty($me['email']) && !empty($me['token'])) {
           
            $this->user_name = $this->db->real_escape_string($me['email']);            
            $checklogin = $this->db->query("SELECT user_id, user_name, user_salt, user_code FROM users WHERE user_name = '".$this->user_name."';");
            if($checklogin->num_rows == 1) {
                $result_row = $checklogin->fetch_object();                

                $_SESSION['user_name'] = $result_row->user_name;
                $_SESSION['user_logged_in'] = 1;
                $_SESSION['code'] = $result_row->user_code;
                $_SESSION['access_token'] = $me['token'];
                $ipaddress = $this->getIPAddr();
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $updateip = $this->db->query("UPDATE users SET fb_id = '".$me['id']."', ipaddress = '".$ipaddress."', user_agent = '".$user_agent."' WHERE user_name = '".$result_row->user_name."';");             
                return true;                                
            } else {                
                $this->errors[] = "You do not have an active account. please register first.";
                return false;
            }
        } else {
            $this->errors[] = "Login not authenticated. Please login again.";
            return false;
        }      
        
    }
	

    private function loginWithPostData() {
        
        if($_POST["action"]=='login' && !empty($_POST['user_name']) && !empty($_POST['user_password'])) {
           
            $this->user_name = $this->db->real_escape_string($_POST['user_name']);            
            $checklogin = $this->db->query("SELECT user_id, user_name, user_salt, user_password, user_code FROM users WHERE user_name = '".$this->user_name."';");
            if($checklogin->num_rows == 1) {
                $result_row = $checklogin->fetch_object();                
                if (hash("sha256", $_POST["user_password"].$result_row->user_salt) == $result_row->user_password) {
                    $_SESSION['user_name'] = $result_row->user_name;
                    $_SESSION['user_logged_in'] = 1;
                    $_SESSION['code'] = $result_row->user_code;
                     $ipaddress = $this->getIPAddr();
                     $updateip = $this->db->query("UPDATE users SET ipaddress = '".$ipaddress."', user_agent = '".$user_agent."' WHERE user_name = '".$result_row->user_name."';");             
                    return true;                    
                } else {
                    $this->errors[] = "Password was wrong.";
                    return false;                    
                }                
            } else {                
                $this->errors[] = "This user does not exist.";
                return false;
            }
        } elseif (isset($_POST["login"]) && !empty($_POST['user_name']) && empty($_POST['user_password'])) {
            $this->errors[] = "Password field was empty.";
        }      
        
    }
    
    public function getUser() {
    
    	if($this->isLoggedIn()){
    		$qry = "SELECT user_id,first_name,last_name,user_code,ipaddress FROM users WHERE user_name = '".$_SESSION['user_name']."';";
    		$checklogin = $this->db->query($qry);
    		$result_row = $checklogin->fetch_object(); 
    		return $result_row;
    	}
    	return false;
    	
    }
    
    public function shortUrl($url) {
    
    	$postdata = '{"longUrl":"'.$url.'"}';
   		$opts = array('http' =>
    		array(
        		'method'  => 'POST',
        		'header'  => 'Content-type: application/json',
        		'content' => $postdata
    		)
		);
		$context  = stream_context_create($opts);
		$result = file_get_contents('https://www.googleapis.com/urlshortener/v1/url', false, $context);
		return json_decode($result,true);
		
    }
    
    public function deshortUrl($url) {
    
    	$postdata = '{"shortUrl":"'.$url.'"}';
   		$opts = array('http' =>
    		array(
        		'method'  => 'POST',
        		'header'  => 'Content-type: application/json',
        		'content' => $postdata
    		)
		);
		$context  = stream_context_create($opts);
		$result = file_get_contents('https://www.googleapis.com/urlshortener/v1/url', false, $context);
		return json_decode($result,true);
		
    }
    
    
    public function logout($sdk=false) {  
        
        if (isset($_GET["logout"]) && $_GET["logout"]=="1") {
            $_SESSION = array();
            if($me){
            	$sdk->destroySession();
            }
            session_destroy();
            return true;
        }  
              
    }
    
    
    public function isLoggedIn() {
        
        return $this->logged_in;
        
    }
    
    public function checkLogin(){
    
    	if(!$this->isLoggedIn()){
    		header('WWW-Authenticate: Basic realm="Restricted Area"');
   			header('HTTP/1.0 401 Unauthorized');
   			return false;
    	}
    	return true;
    	
    }
    
    
    public function checkForRegisterPage() {
        
        if (isset($_POST["action"]) && $_POST["action"]=="register") {        
            return true;        
        } else {       
            return false;
        }
        
    }
    
    function getIPAddr(){
    
    	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
      		$ip=$_SERVER['HTTP_CLIENT_IP'];
    	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    	}else{
      		$ip=$_SERVER['REMOTE_ADDR'];
    	}
    	return $ip;
    	
	}

    private function registerNewUser() {

        if($_POST["action"]=='register' && !empty($_POST['customer_username']) && !empty($_POST['customer_password'])) {

				$this->first_name = $this->db->real_escape_string($_POST['first_name']);
                $this->last_name = $this->db->real_escape_string($_POST['last_name']);
                $this->user_name = $this->db->real_escape_string($_POST['customer_username']);
                $this->user_password = $this->db->real_escape_string($_POST['customer_password']);
                $user_code = $this->db->real_escape_string($_POST['code']);
                $credit_id = $this->db->real_escape_string($_POST['credit_id']);
                
                // generate 64 char long random string "salt", a string to "encrypt" the password hash
                // this is a basic salt, you might replace this with a more advanced function
                // @see http://en.wikipedia.org/wiki/Salt_(cryptography)
                $this->user_salt = $this->generateSalt(64);
                // double md5 hash the plain password + salt
                //$user_password_hashed = md5(md5($_POST['user_password'].$user_salt));
                
                // hash the combined string of password+salt via the sha256 algorithm, result is a 64 char string                 
                $this->user_password_hashed = hash("sha256", $this->user_password.$this->user_salt);
                

                $query_check_user_name = $this->db->query("SELECT * FROM users WHERE user_name = '".$this->user_name."'");

                if($query_check_user_name->num_rows == 1)
                {
                    $this->errors[] = "Sorry, that user_name is taken. Please go back and try again.";
                }
                else
                {
                	$ipaddress = $this->getIPAddr();
                	$qry = "INSERT INTO users (first_name, last_name, user_name, user_salt, user_password, user_code, ipaddress, credit_id) VALUES('".$this->first_name."', '".$this->last_name."','".$this->user_name."', '".$this->user_salt."', '".$this->user_password_hashed."', '".$user_code."','".$ipaddress."','".$credit_id."')";
                    $query_new_user_insert = $this->db->query($qry);
                    if($query_new_user_insert)
                    {
                    	$_SESSION['user_name'] = $this->user_name;
                    	$_SESSION['user_logged_in'] = 1;
                        $this->messages[] = "Your account was successfully created. Please <a href='index.php'>click here to login</a>.";
                    }
                    else
                    {
                        $this->errors[] = "Sorry, your registration failed. Please go back and try again.";
                    }
                }
        }
    }
    
    function __destruct() {
    	$this->db->close();
    }


}
?>