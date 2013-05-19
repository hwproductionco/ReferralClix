<?php
/*------------------------------------------------*/
//
//		STICKY STREET API CLASS
//		VERSION 1.0
//		Updated: 9/21/12
//
/*------------------------------------------------*/

class StickStreet {

	var $api_url = 'https://api.clienttoolbox.com/api.php';
	var $api_key = '';
	var $api_ver = '1.5';
	var $user_id = '';
	var $account_id = '';
	var $card_num = '';
	var $campaigns = array();  // Holds campaigns pulled from API
	var $customers = array();  // Holds customers pulled from API


	function __construct($api_key,$user_id,$account_id){
		if(empty($api_key)){
      		throw new Exception('API Key is Required');
	    } elseif(empty($user_id)){
	    	throw new Exception('User ID is Required');
	    } elseif(empty($account_id)){
	    	throw new Exception('Account ID is Required');
	    }
		$this->api_key		= $api_key;
		$this->user_id		= $user_id;
		$this->account_id	= $account_id;
	}

	private function sendData($postvars,$array=false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_url ); 
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
	
/*---------------------------*/	
//
//   CUSTOMER FUNCTIONS
//
/*---------------------------*/
	
	// Function: Record / Update Customer Information
	// Create or update a customer account; Returns a unique Account ID. 
	public function newCustomer($action='new',$post, $array=false){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		$fields = array(
			'type'				=> 'record_customer',
			'customer_action'	=> $action,
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id
		);
		if(!empty($post['campaign_id'])) $fields['campaign_id'] = $post['campaign_id'];
		if(!empty($post['card_number'])) $fields['card_number'] = $post['card_number'];
		if(!empty($post['card_number_generate 	'])) $fields['card_number_generate 	'] = $post['card_number_generate 	'];
		if(!empty($post['code'])) $fields['code'] = $post['code'];
		if(!empty($post['first_name'])) $fields['first_name'] = $post['first_name'];
		if(!empty($post['last_name'])) $fields['last_name'] = $post['last_name'];
		if(!empty($post['phone'])) $fields['phone'] = $post['phone'];
		if(!empty($post['email'])) $fields['email'] = $post['email'];
		if(!empty($post['street1'])) $fields['street1'] = $post['street1'];
		if(!empty($post['street2'])) $fields['street2'] = $post['street2'];
		if(!empty($post['city'])) $fields['city'] = $post['city'];
		if(!empty($post['state'])) $fields['state'] = $post['state'];
		if(!empty($post['postal_code'])) $fields['postal_code'] = $post['postal_code'];
		if(!empty($post['country'])) $fields['country'] = $post['country'];
		if(!empty($post['customer_username'])) $fields['customer_username'] = $post['customer_username'];
		if(!empty($post['customer_password'])) $fields['customer_password'] = $post['customer_password'];
		if(!empty($post['customer_PIN'])) $fields['customer_PIN'] = $post['customer_PIN'];
		if(!empty($post['custom_field'])) $fields['custom_field'] = $post['custom_field'];
		if(!empty($post['custom_date'])) $fields['custom_date'] = date("Y-m-d",strtotime($post['custom_date']));
		if(!empty($post['auto_add'])) $fields['auto_add'] = $post['auto_add'];		
		if(!empty($post['send_no_email'])) $fields['send_no_email'] = $post['send_no_email'];
		if(!empty($post['custom_field_1'])) $fields['custom_field_1'] = $post['custom_field_1'];
		if(!empty($post['custom_field_2'])) $fields['custom_field_2'] = $post['custom_field_2'];
		if(!empty($post['custom_field_3'])) $fields['custom_field_3'] = $post['custom_field_3'];
		if(!empty($post['custom_field_4'])) $fields['custom_field_4'] = $post['custom_field_4'];
		if(!empty($post['custom_field_5'])) $fields['custom_field_5'] = $post['custom_field_5'];
		if(!empty($post['custom_field_6'])) $fields['custom_field_6'] = $post['custom_field_6'];
		if(!empty($post['custom_field_7'])) $fields['custom_field_7'] = $post['custom_field_7'];
		if(!empty($post['custom_field_8'])) $fields['custom_field_8'] = $post['custom_field_8'];
		if(!empty($post['custom_field_9'])) $fields['custom_field_9'] = $post['custom_field_9'];
		if(!empty($post['custom_field_10'])) $fields['custom_field_10'] = $post['custom_field_10'];
		if(!empty($post['custom_field_11'])) $fields['custom_field_11'] = $post['custom_field_11'];
		if(!empty($post['custom_field_12'])) $fields['custom_field_12'] = $post['custom_field_12'];
		if(!empty($post['custom_field_13'])) $fields['custom_field_13'] = $post['custom_field_13'];
		if(!empty($post['custom_field_14'])) $fields['custom_field_14'] = $post['custom_field_14'];
		if(!empty($post['custom_field_15'])) $fields['custom_field_15'] = $post['custom_field_15'];
		if(!empty($post['custom_field_16'])) $fields['custom_field_16'] = $post['custom_field_16'];
		if(!empty($post['custom_field_17'])) $fields['custom_field_17'] = $post['custom_field_17'];
		if(!empty($post['custom_field_18'])) $fields['custom_field_18'] = $post['custom_field_18'];
		if(!empty($post['custom_field_19'])) $fields['custom_field_19'] = $post['custom_field_19'];
		if(!empty($post['custom_field_20'])) $fields['custom_field_20'] = $post['custom_field_20'];
		return $this->sendData($fields, $array);
	}
	
	// Function: Generate a New Customer Card Number
	// Creates a random card number of a given length and checks that this number is unique and not in use already in the account. 
	public function genCardNumber($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'type'				=> 'generate_card_number'
		);
		if(!empty($post['how_many_digits'])) $fields['how_many_digits'] = $post['how_many_digits'];
		return $this->sendData($fields);
	}
	
	// Function: Customer - Search
	// Search for a customer within each given field. As opposed to findCustomer(), which finds a customer across all fields.
	public function searchCustomer($post,$boolean=false){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		$fields = array(
			'type'				=> 'customer_search',
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id
		);
		if(!empty($post['exact_match'])) $fields['exact_match'] = $post['exact_match'];
		if(!empty($post['card_number'])) $fields['card_number'] = $post['card_number'];
		if(!empty($post['first_name'])) $fields['first_name'] = $post['first_name'];
		if(!empty($post['last_name'])) $fields['last_name'] = $post['last_name'];
		if(!empty($post['phone'])) $fields['phone'] = $post['phone'];
		if(!empty($post['email'])) $fields['email'] = $post['email'];
		if(!empty($post['city'])) $fields['city'] = $post['city'];
		if(!empty($post['state'])) $fields['state'] = $post['state'];
		if(!empty($post['postal_code'])) $fields['postal_code'] = $post['postal_code'];
		if(!empty($post['custom_field'])) $fields['custom_field'] = $post['custom_field'];
		if(!empty($post['custom_date'])) $fields['custom_date'] = $post['custom_date'];
		if(!empty($post['customer_username'])) $fields['customer_username'] = $post['customer_username'];
		if(!empty($post['customer_password'])) $fields['customer_password'] = $post['customer_password'];
		if(!empty($post['customer_PIN'])) $fields['customer_PIN'] = $post['customer_PIN'];
		if(!empty($post['include_balances'])) $fields['include_balances'] = $post['include_balances'];
		if($boolean) return $this->sendData($fields,true); else return $this->sendData($fields);
	}
	
	// Function: Customer - Find
	// Finds a customer across all fields. As opposed to searchCustomer(), which searches only inside each given fields.
	public function findCustomer($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['find_customer'])){
      		throw new Exception('find_customer field is Required');
	    }
		
		$fields = array(
			'type'				=> 'customer_find',
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'find_customer'		=> $post['find_customer']
		);
		if(!empty($post['include_balances'])) $fields['include_balances'] = $post['include_balances'];		
		return $this->sendData($fields);
	}
	
	// Function: Retrieve Customer Information
	// Retrieves a customer's information and campaigns' balances. 
	public function getCustomerInfo($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		$fields = array(
			'type'				=> 'customer_info',
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id
		);
		if(!empty($post['card_number'])) $fields['card_number'] = $post['card_number'];
		if(!empty($post['code'])) $fields['code'] = $post['code'];
		if(!empty($post['hide_customer_field'])) $fields['hide_customer_field'] = $post['hide_customer_field'];
		return $this->sendData($fields,true);
	}
	
	// Function: Customer Balance and Transaction History
	// Returns a customer's balance and transaction history for a particular campaign. 
	public function getCustomerHistory($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    }
		
		$fields = array(
			'type'				=> 'customer_balance',
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'campaign_id'		=> $post['campaign_id']
		);
		if(!empty($post['card_number'])) $fields['card_number'] = $post['card_number'];
		if(!empty($post['code'])) $fields['code'] = $post['code'];
		if(!empty($post['transactions_number'])) $fields['transactions_number'] = $post['transactions_number'];
		return $this->sendData($fields);
	}
	
	// Function: Customer Password Validation
	// Returns whether a customer's password is valid or not. 
	public function validateCustomer($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['card_number']) && empty($post['code'])){
      		throw new Exception('card_number or code field is Required');
	    }
		
		$fields = array(
			'type'				=> 'validate_customer_password',
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id
		);
		if(!empty($post['card_number'])) $fields['card_number'] = $post['card_number'];
		if(!empty($post['code'])) $fields['code'] = $post['code'];
		if(!empty($post['customer_username'])) $fields['customer_username'] = $post['customer_username'];
		if(!empty($post['customer_password'])) $fields['customer_password'] = $post['customer_password'];
		if(!empty($post['customer_PIN'])) $fields['customer_PIN'] = $post['customer_PIN'];
		if(!empty($post['phone'])) $fields['phone'] = $post['phone'];
		return $this->sendData($fields);
	}
	
	// Function: Pre-Existing Report: Customer Balances
	// Generate a report with all the customer balances for the given campaign.
	public function getBalances($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		$fields = array(
			'type'				=> 'reports',
			'report'			=> 'customers_balances',
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'campaign_id'		=> $post['campaign_id']
		);
		return $this->sendData($fields);
	}

/*---------------------------*/	
//
//    CAMPAIGN FUNCTIONS
//
/*---------------------------*/

	// Function: Create New Campaign
	// Add a new campaign to an existing account and returns the campaign_id of the new campaign
	public function newCampaign($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'action'			=> 'campaign',
			'type'				=> 'campaign_new'
		);
		if(!empty($post['campaign_type'])) $fields['campaign_type'] = $post['campaign_type'];
		if(!empty($post['campaign_name'])) $fields['campaign_name'] = $post['campaign_name'];
		if(!empty($post['points_ratio'])) $fields['points_ratio'] = $post['points_ratio'];
		if(!empty($post['reward_ratio'])) $fields['reward_ratio'] = $post['reward_ratio'];
		if(!empty($post['amount_per_event'])) $fields['amount_per_event'] = $post['amount_per_event'];
		if(!empty($post['coalition_opt_out'])) $fields['coalition_opt_out'] = $post['coalition_opt_out'];
		if(!empty($post['two_tier_opt_out'])) $fields['two_tier_opt_out'] = $post['two_tier_opt_out'];	
		return $this->sendData($fields);
	}
	
	// Function: Create New Campaign Reward
	// Add a new reward to an existing Points or Event-based campaign.
	public function newCampaignReward($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    } elseif(empty($post['reward_level'])){
      		throw new Exception('reward_level field is Required');
	    } elseif(empty($post['reward_description'])){
      		throw new Exception('reward_description field is Required');
	    }	
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'action'			=> 'reward',
			'type'				=> 'campaign_new',
			'campaign_id'		=> $post['campaign_id'],
			'reward_level'		=> $post['reward_level'],
			'reward_description'=> $post['reward_description']
		);
		if(!empty($post['reward_identifier'])) $fields['reward_identifier'] = $post['reward_identifier'];
		return $this->sendData($fields);
	}
	
	// Function: Create New Campaign
	// Add a new campaign to an existing account and returns the campaign_id of the new campaign
	public function updateCampaign($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'action'			=> 'campaign',
			'type'				=> 'campaign_update',
			'campaign_id'		=> $post['campaign_id']
		);
		if(!empty($post['new_campaign_name'])) $fields['new_campaign_name'] = $post['new_campaign_name'];
		if(!empty($post['new_points_ratio'])) $fields['new_points_ratio'] = $post['new_points_ratio'];
		if(!empty($post['new_reward_ratio'])) $fields['new_reward_ratio'] = $post['new_reward_ratio'];
		if(!empty($post['new_amount_per_event'])) $fields['new_amount_per_event'] = $post['new_amount_per_event'];
		if(!empty($post['coalition_opt_out'])) $fields['coalition_opt_out'] = $post['coalition_opt_out'];
		if(!empty($post['two_tier_opt_out'])) $fields['two_tier_opt_out'] = $post['two_tier_opt_out'];	
		return $this->sendData($fields);
	}
	
	// Function: Update Existing Campaign Reward
	// Update an existing campaign reward to an existing Points or Event-based campaign. 
	public function updateCampaignReward($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    } elseif(empty($post['reward_id'])){
      		throw new Exception('reward_id field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'action'			=> 'reward',
			'type'				=> 'campaign_update',
			'campaign_id'		=> $post['campaign_id'],
			'reward_id'			=> $post['reward_id']
		);
		if(!empty($post['new_reward_level'])) $fields['new_reward_level'] = $post['new_reward_level'];
		if(!empty($post['new_reward_description'])) $fields['new_reward_description'] = $post['new_reward_description'];
		if(!empty($post['new_reward_identifier'])) $fields['new_reward_identifier'] = $post['new_reward_identifier'];
		return $this->sendData($fields);
	}
	
	// Function: Campaigns List
	// Returns a list of the campaigns for an account.
	public function listCampaigns($post){
		$api_ver =	$this->api_ver;
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		$fields = array(
			'API'				=> $api_ver,
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'type'				=> 'campaigns_list'
		);
		if(!empty($post['type_restrict'])) $fields['type_restrict'] = $post['type_restrict'];
		if(!empty($post['searchField'])) $fields['searchField'] = $post['searchField'];
		if(!empty($post['searchOper'])) $fields['searchOper'] = $post['searchOper'];
		if(!empty($post['searchValue'])) $fields['searchValue'] = $post['searchValue'];
		if(!empty($post['sortField'])) $fields['sortField'] = $post['sortField'];
		if(!empty($post['sortOrder'])) $fields['sortOrder'] = $post['sortOrder'];
		if(!empty($post['offset'])) $fields['offset'] = $post['offset'];
		if(!empty($post['length'])) $fields['length'] = $post['length'];	
		return $this->sendData($fields);
	}
	
	// Function: Campaign Rewards List
	// Returns a list of the rewards available for a given campaign. 
	public function listCampaignRewards($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'type'				=> 'campaign_rewards',
			'campaign_id'		=> $post['campaign_id']
		);
		return $this->sendData($fields,true);
	}
	
	// Function: Campaign Promotions List
	// Returns a list of the rewards available for a given campaign. 
	public function listCampaignPromotions($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'type'				=> 'campaign_promos',
			'campaign_id'		=> $post['campaign_id']
		);
		return $this->sendData($fields,true);
	}
	
	// Function: Delete a Campaign
	// Removes a campaign from an account, including all customer transactions in it.
	public function deleteCampaign($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'type'				=> 'campaign_delete',
			'action'			=> 'campaign',
			'campaign_id'		=> $post['campaign_id']
		);
		return $this->sendData($fields);
	}
	
	// Function: Delete a Reward
	// Removes a reward from an account's campaign.
	public function deleteCampaignReward($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    } elseif(empty($post['reward_id'])){
      		throw new Exception('reward_id field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'type'				=> 'campaign_delete',
			'action'			=> 'reward',
			'campaign_id'		=> $post['campaign_id'],
			'reward_id'			=> $post['reward_id']
		);
		return $this->sendData($fields);
	}
	
	// Function: Deactivate a Campaign
	// A safer and reversible alternative to deleting a campaign. 
	public function deactivateCampaign($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'type'				=> 'campaign_deactivate',
			'campaign_id'		=> $post['campaign_id']
		);
		return $this->sendData($fields);
	}
	
	// Function: Reactivate a Campaign
	// Makes a campaign that has been previously deactivated active again.
	public function reactivateCampaign($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'type'				=> 'campaign_reactivate',
			'campaign_id'		=> $post['campaign_id']
		);		
		return $this->sendData($fields);
	}
	
/*---------------------------*/	
//
//    TRANSACTION FUNCTIONS
//
/*---------------------------*/

	// Function: Record Transaction
	// Record a transaction for a customer account.
	public function recordTransaction($post,$type,$array=false){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['code'])){
      		throw new Exception('code field is Required');
	    }
		
		$fields = array(
			'type'				=> 'record_activity',
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'code'				=> $post['code']
		);
		
		switch($type){
			case "points":
			
				if(empty($post['campaign_id'])){
      				throw new Exception('campaign_id field is Required');
	    		}
				
				$fields['campaign_id'] = $post['campaign_id'];
				if(!empty($post['amount'])) $fields['amount'] = $post['amount'];
				if(!empty($post['promo_id'])) $fields['promo_id'] = $post['promo_id'];
				if(!empty($post['authorization'])) $fields['authorization'] = $post['authorization'];
				if(!empty($post['send_transaction_email'])) $fields['send_transaction_email'] = $post['send_transaction_email'];
			break;
			case "giftcard":
			
				if(empty($post['campaign_id'])){
      				throw new Exception('campaign_id field is Required');
	    		} elseif(empty($post['amount'])){
      				throw new Exception('amount field is Required');
	    		}
	    		
				$fields['campaign_id'] = $post['campaign_id'];
				$fields['amount'] = $post['amount'];
				if(!empty($post['authorization'])) $fields['authorization'] = $post['authorization'];
				if(!empty($post['send_transaction_email'])) $fields['send_transaction_email'] = $post['send_transaction_email'];
			break;
			case "buyx":
			
				if(empty($post['campaign_id'])){
      				throw new Exception('campaign_id field is Required');
	    		} elseif(empty($post['service_product'])){
      				throw new Exception('service_product field is Required');
	    		}
				
				$fields['campaign_id'] = $post['campaign_id'];
				$fields['service_product'] = $post['service_product'];
				if(!empty($post['buyx_quantity'])) $fields['buyx_quantity'] = $post['buyx_quantity'];
				if(!empty($post['authorization'])) $fields['authorization'] = $post['authorization'];
				if(!empty($post['send_transaction_email'])) $fields['send_transaction_email'] = $post['send_transaction_email'];
			break;
			case "event":
			
				if(empty($post['campaign_id'])){
      				throw new Exception('campaign_id field is Required');
	    		}
			
				$fields['campaign_id'] = $post['campaign_id'];
				if(!empty($post['authorization'])) $fields['authorization'] = $post['authorization'];
				if(!empty($post['send_transaction_email'])) $fields['send_transaction_email'] = $post['send_transaction_email'];
			break;
		}
		return $this->sendData($fields,$array);
	}
	
	// Function: Redemption Transaction
	// Record a redemption transaction for a customer account.
	public function redeemTransaction($post, $type){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['code'])){
      		throw new Exception('code field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'type'				=> 'redeem',
			'code'				=> $post['code']
		);
		switch($type){
			case "points":
			
				if(empty($post['campaign_id'])){
      				throw new Exception('campaign_id field is Required');
	    		}
			
				$fields['campaign_id'] = $post['campaign_id'];
				if(!empty($post['custom_points_redeem'])) $fields['custom_points_redeem'] = $post['custom_points_redeem'];
				if(!empty($post['custom_dollars_redeem'])) $fields['custom_dollars_redeem'] = $post['custom_dollars_redeem'];
				if(!empty($post['reward_to_redeem'])) $fields['reward_to_redeem'] = $post['reward_to_redeem'];
				if(!empty($post['authorization'])) $fields['authorization'] = $post['authorization'];
			break;
			case "giftcard":
			
				if(empty($post['campaign_id'])){
      				throw new Exception('campaign_id field is Required');
	    		} elseif(empty($post['reward_to_redeem'])){
      				throw new Exception('reward_to_redeem field is Required');
	    		}
			
				$fields['campaign_id'] = $post['campaign_id'];
				$fields['reward_to_redeem'] = $post['reward_to_redeem'];
				if(!empty($post['authorization'])) $fields['authorization'] = $post['authorization'];
			break;
			case "buyx":
				
				if(empty($post['campaign_id'])){
      				throw new Exception('campaign_id field is Required');
	    		} elseif(empty($post['reward_to_redeem'])){
      				throw new Exception('reward_to_redeem field is Required');
	    		}
			
				$fields['campaign_id'] = $post['campaign_id'];
				$fields['reward_to_redeem'] = $post['reward_to_redeem'];
				if(!empty($post['authorization'])) $fields['authorization'] = $post['authorization'];
			break;
			case "event":
			
				if(empty($post['campaign_id'])){
      				throw new Exception('campaign_id field is Required');
	    		} elseif(empty($post['reward_to_redeem'])){
      				throw new Exception('reward_to_redeem field is Required');
	    		}
			
				$fields['campaign_id'] = $post['campaign_id'];
				$fields['reward_to_redeem'] = $post['reward_to_redeem'];
				if(!empty($post['authorization'])) $fields['authorization'] = $post['authorization'];
			break;
		}
		return $this->sendData($fields);
	}
	
	// Function: Delete a Transaction
	// Deletes a specific transaction in a customer account.
	public function deleteTransaction($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['campaign_id'])){
      		throw new Exception('campaign_id field is Required');
	    } elseif(empty($post['code'])){
      		throw new Exception('code field is Required');
	    } elseif(empty($post['transaction_id'])){
      		throw new Exception('transaction_id field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'type'				=> 'transaction_delete',
			'campaign_id'		=> $post['campaign_id'],
			'code'				=> $post['code'],
			'transaction_id'	=> $post['transaction_id']
		);		
		return $this->sendData($fields);
	}
	
/*---------------------------*/	
//
//    REPORTING FUNCTIONS
//
/*---------------------------*/	
	
	// Function: Pre-Existing Report: All Customers
	// Generate a report with all the customers who have had transactions between two date ranges in the selected campaigns.
	public function allCustomerReport($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'report'			=> 'customers_all',
			'type'				=> 'reports'
		);
		if(!empty($post['date_start'])) $fields['date_start'] = $post['date_start'];
		if(!empty($post['date_end'])) $fields['date_end'] = $post['date_end'];
		if(!empty($post['selected_campaigns'])) $fields['selected_campaigns'] = $post['selected_campaigns'];
		return $this->sendData($fields);
	}
	
	// Function: Pre-Existing Report: New Customers
	// Generate a report with all the customers who have had their first transaction between two date ranges in the selected campaigns.
	public function newCustomerReport($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['date_start'])){
      		throw new Exception('date_start field is Required');
	    } elseif(empty($post['selected_campaigns'])){
      		throw new Exception('selected_campaigns field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'report'			=> 'customers_new',
			'type'				=> 'reports',
			'date_start'		=> $post['date_start'],
			'selected_campaigns'=> $post['selected_campaigns']
		);
		if(!empty($post['date_end'])) $fields['date_end'] = $post['date_end'];
		return $this->sendData($fields);
	}
	
	// Function: Pre-Existing Report: Frequent Customers
	// Generate a report with all the customers who have had a certain amount of transaction between two date ranges in the selected campaigns.
	public function frequentCustomerReport($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['date_start'])){
      		throw new Exception('date_start field is Required');
	    } elseif(empty($post['selected_campaigns'])){
      		throw new Exception('selected_campaigns field is Required');
	    } elseif(empty($post['frequency'])){
      		throw new Exception('frequency field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'report'			=> 'customers_frequent',
			'type'				=> 'reports',
			'date_start'		=> $post['date_start'],
			'selected_campaigns'=> $post['selected_campaigns'],
			'frequency'			=> $post['frequency']
		);
		if(!empty($post['date_end'])) $fields['date_end'] = $post['date_end'];
		if(!empty($post['include_redeemed'])) $fields['include_redeemed'] = $post['include_redeemed'];
		return $this->sendData($fields);
	}
	
	// Function: Pre-Existing Report: Inactive Advocates
	// Generate a report with all the customers who have had a transaction between two date ranges in the selected campaigns, but haven't ben back for a given amount of days.
	public function InactiveCustomerReport($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['date_start'])){
      		throw new Exception('date_start field is Required');
	    } elseif(empty($post['selected_campaigns'])){
      		throw new Exception('selected_campaigns field is Required');
	    } elseif(empty($post['Inactive_for'])){
      		throw new Exception('Inactive_for field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'report'			=> 'customers_Inactive',
			'type'				=> 'reports',
			'date_start'		=> $post['date_start'],
			'selected_campaigns'=> $post['selected_campaigns'],
			'Inactive_for'		=> $post['Inactive_for']
		);
		if(!empty($post['date_end'])) $fields['date_end'] = $post['date_end'];
		if(!empty($post['include_redeemed'])) $fields['include_redeemed'] = $post['include_redeemed'];
		return $this->sendData($fields);
	}
	
	// Function: Pre-Existing Report: Customers Birthday
	// Generate a report with all the customers who have had a transaction in the selected campaigns and who will celebrate a birthday in the given date range.
	public function customerBirthdayReport($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['date_start'])){
      		throw new Exception('date_start field is Required');
	    } elseif(empty($post['selected_campaigns'])){
      		throw new Exception('selected_campaigns field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'report'			=> 'customers_birthday',
			'type'				=> 'reports',
			'date_start'		=> $post['date_start'],
			'selected_campaigns'=> $post['selected_campaigns']
		);
		if(!empty($post['date_end'])) $fields['date_end'] = $post['date_end'];
		return $this->sendData($fields, true);
	}
	
	// Function: Pre-Existing Report: Custom Date Search
	// Generate a report with all the customers who have had a transaction in the selected campaigns and whose custom_date field includes a date in the given date range.
	public function customDateReport($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['date_start'])){
      		throw new Exception('date_start field is Required');
	    } elseif(empty($post['selected_campaigns'])){
      		throw new Exception('selected_campaigns field is Required');
	    }
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'report'			=> 'customers_custom_date',
			'type'				=> 'reports',
			'date_start'		=> $post['date_start'],
			'selected_campaigns'=> $post['selected_campaigns']
		);
		if(!empty($post['date_end'])) $fields['date_end'] = $post['date_end'];
		return $this->sendData($fields);
	}
	
	// Function: Pre-Existing Report: Search Customers
	// Generate a report with all the customers who have a transaction between two date ranges in the selected campaigns, and whose information contains the search text.
	public function searchCustomerReport($post){
		$account_id = $this->account_id;
		$api_key = $this->api_key;
		$user_id = $this->user_id;
		
		if(empty($post['date_start'])){
      		throw new Exception('date_start field is Required');
	    } elseif(empty($post['selected_campaigns'])){
      		throw new Exception('selected_campaigns field is Required');
	    } elseif(empty($post['search_text'])){
      		throw new Exception('search_text field is Required');
	    }		
		
		$fields = array(
			'user_id'			=> $user_id,
			'user_password'		=> $api_key,
			'account_id'		=> $account_id,
			'report'			=> 'customers_search',
			'type'				=> 'reports',
			'date_start'		=> $post['date_start'],
			'selected_campaigns'=> $post['selected_campaigns'],
			'search_text'		=> $post['search_text']
		);
		if(!empty($post['date_end'])) $fields['date_end'] = $post['date_end'];
		return $this->sendData($fields);
	}

}

?>