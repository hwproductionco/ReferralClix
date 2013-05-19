<?php

require_once('wufoo/WufooApiWrapper.php');

/**
 * All available methods and an example of how to call them.
 *
 * @package default
 * @author Timothy S Sabat
 */
class WufooApi {
	
	private $apiKey;
	private $subdomain;
	
	public function __construct($apiKey, $subdomain, $domain = 'wufoo.com') {
		$this->apiKey = $apiKey;
		$this->subdomain = $subdomain;
		$this->domain = $domain;
	}
	
	public function getForms($identifier = null) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getForms($identifier); 
	}
	
	public function getFields($identifier) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getFields($identifier); 
	}
	
	public function getEntries($identifier) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getEntries($identifier); 
	}
	
	public function getEntryCount($identifier) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getEntryCount($identifier);
	}
	
	public function getUsers() {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getUsers(); 
	}
	
	public function getReports($identifier = null) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getReports($identifier); 
	}
	
	public function getWidgets($identifier) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getWidgets($identifier); 
	}
	
	public function getReportFields($identifier) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getReportFields($identifier); 
	}
	
	public function getReportEntries($identifier) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getReportEntries($identifier); 
	}
	
	public function getReportEntryCount($identifier) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getReportEntryCount($identifier);
	}
	
	public function getComments($identifier) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->getComments($identifier);
	}
	
	public function entryPost($identifier, $postArray = '') {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		$postArray = array(
					new WufooSubmitField('Field2', $_POST['first_name']), 
					new WufooSubmitField('Field3', $_POST['last_name']),
					new WufooSubmitField('Field10', $_POST['email']),
					new WufooSubmitField('Field11', str_replace(array("(",")"," ","-"),"",$_POST['phone']) ),
					new WufooSubmitField('Field14', $_POST['street1']),
					new WufooSubmitField('Field15', $_POST['street2']),
					new WufooSubmitField('Field16', $_POST['city']),
					new WufooSubmitField('Field17', $_POST['state']),
					new WufooSubmitField('Field18', $_POST['postal_code']),
					new WufooSubmitField('Field19', $_POST['country']),
					new WufooSubmitField('Field130', $_POST['custom_field_3']),
					new WufooSubmitField('Field131', date('Ymd',strtotime($_POST['custom_date'])) ),
					new WufooSubmitField('Field127', $_POST['custom_field_5']),
					new WufooSubmitField('Field129', $_POST['custom_field_6']),
					new WufooSubmitField('Field133', $_POST['custom_field_7']),
					new WufooSubmitField('Field135', $_POST['custom_field_4'])
					);
		return $wrapper->entryPost($identifier, $postArray);	
	}
	
	public function webHookPut($identifier, $urlToAdd, $handshakeKey, $metadata = false) {
		$wrapper = new WufooApiWrapper($this->apiKey, $this->subdomain, $this->domain);
		return $wrapper->webHookPut($identifier, $urlToAdd, $handshakeKey, $metadata = false);
	}

}

?>