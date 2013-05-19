<?php
/*
/-----------------------------------------------\
|												|
|	Destination Lifestyles API Class			|
|												|
\-----------------------------------------------/
*/

class DestinationLifestyles {
	
	private $_api_user = 'stickyref';
	private $_api_key = 'T69HUrUSwe';
	private $_url = 'https://webservices.sfx-resorts.com/dl/activate.asmx/getCode';
	private static $instance;
	
  	function __construct(){}

  	public static function getInstance() {
    	if(!self::$instance) {
      		self::$instance = new self();
    	}
    	return self::$instance;
  	}
  	
  	private function sendData($postvars,$array=false){
  		$postvars= http_build_query($postvars);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_url ); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars ); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$result = curl_exec($ch);
		curl_close ($ch);
		$c = json_encode((array) simplexml_load_string($result));
		if($array) $c = json_decode($c,true);
		return $c;
	}
	
	public function getKey(){
		$params = array(
			'username'=>$this->_api_user,
			'authcode'=>$this->_api_key
		);
		$result = $this->sendData($params,true);
		return $result;
	}

}
?>