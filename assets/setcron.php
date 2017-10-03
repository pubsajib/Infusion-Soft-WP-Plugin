<?php
add_filter( 'cron_schedules', 'request_access_cron_intervals' );
function request_access_cron_intervals( $schedules ) {

   $schedules['1minute'] = array( // Provide the programmatic name to be used in code
      'interval' => 60, // Intervals are listed in seconds
      'display' => __('Every 1 minutes') // Easy to read display name
   );
   return $schedules; // Do not forget to give back the list of schedules!
}

add_filter( 'cron_schedules', 'contact_sybc_cron_intervals' );
function contact_sybc_cron_intervals( $schedules ) {

   $schedules['5minutes'] = array( 
      'interval' => 300, 
      'display' => __('Every 5 minutes') 
   );
   return $schedules;
}

// CRON JOB FOR REFRESH ACCESS TOKEN.
add_action( 'access_token_refreshing', 'request_access_token' );
function request_access_token() {
	$db         	= new tn_db;
    $sync       	= new RestApi;
    $i_token    	= $db->select_token();
    $client 		= $db->select_data();
	$client_id 		= $client[0]->client_id;
	$client_secret 	= $client[0]->client_secret;

	// Debug purpose
	echo "Refresh Token retrieve Successful:- ".$i_token['refresh_t'].'<br>';

	// Setup the Fields we are going to post.
	$headers = array(
	    'Authorization: Basic ' . base64_encode( $client_id . ':' . $client_secret ),
	    'Content-Type: application/x-www-form-urlencoded'
	);

	$params = array(
	    'grant_type' 	=> 'refresh_token',
	    'refresh_token' => $i_token['refresh_t']
	);

	// Setup cURL so that we can post the Authorization information.
	$ch = curl_init();   

	curl_setopt($ch, CURLOPT_URL,         "https://api.infusionsoft.com/token");
	curl_setopt($ch, CURLOPT_HTTPHEADER,     $headers);
	curl_setopt($ch, CURLOPT_POST,           count($params));
	curl_setopt($ch, CURLOPT_POSTFIELDS,     http_build_query($params));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Execute cURL and get the result back.
	$result = curl_exec($ch);
	// Close the cURL connection.
	curl_close($ch);
	// Process return data
	$response = json_decode($result,true); 
	// Debug purpose
	echo "Access Token request Successful:<br>"; 
	// echo "<pre>"; print_r($response); echo "</pre>";

	$acc_token 		= $response['access_token'];
	$ref_token 		= $response['refresh_token'];
	$appid 			= $response['scope'];
	$end_of_life 	= time() + 86400;

	$token = array($acc_token, $ref_token,$end_of_life, $appid);
	if ( $db->insert_token($token) ) {
		echo "New access token inserted.";
	}
}
add_action( 'wp', 'register_access_token_refresh');
function register_access_token_refresh() {
	if( !wp_next_scheduled( 'access_token_refreshing' ) ) {
		wp_schedule_event( time(), 'daily', 'access_token_refreshing' );
	}
}

// CRON JOB FOR Synchronizing Contacts.
// add_action( 'contact_sync_from_crm_end', 'contacts_sync' );
function contacts_sync(){
	$db 	= new tn_db;
	// parameters ( CRM code,  $mustTagRequired)
	$db->insert_CRM_data_into_DB_from_settings_page('', TNISMUSTTAG);
}
function contacts_sync_old(){
	$sync 		= new RestApi;
	$ins_con 	= new tn_db;
	$objInf 	= new tn_infusion;
	$conSocial  = new tn_social;
	$conTag     = new tn_tag;
	$address 	= new tn_address;
	$teacher 	= new tn_teachers;
	$i_token    = $ins_con->select_token();
	$contacts 	= $objInf->getContactsbyTags();
	$insert_contact_error=0;
	$insert_contact_success=0;
	$target = unserialize (TNTAGS);

	$ins_con->truncate_table();
	$address->truncate();
	$conSocial->truncate();
	$conTag->truncate();

	$allAddress = array();
	foreach ($contacts as $key => $value) {
		// echo $insert_contact_success;
		$email_adds = [];
		if(!empty($value['Email'])){
			$email_adds[] = $value['Email'];
		}
		if(!empty($value['EmailAddress2'])){
			$email_adds[] = $value['EmailAddress2'];
		}
		$email = json_encode($email_adds);
    	$addr = '';
    	$addrArray = [];
    	if (!empty( trim($value_address['field']) )) {
    		$addrArray['field'] = $value_address['field'];
    	}
    	if (!empty( trim($value['StreetAddress1']) )) {
    		$addrArray['line1'] = $value['StreetAddress1'];
    	}
    	if (!empty( trim($value['StreetAddress2']) )) {
    		$addr .= trim($value['StreetAddress2']).' ';
    		$addrArray['line2'] = $value['StreetAddress2'];
    	}
    	if (!empty( trim($value['City']) )) {
    		$addr .= trim($value['City']).' ';
    		$addrArray['locality'] = $value['City'];
    	}
    	if (!empty( trim($value['State']) )) {
    		$addr .= trim($value['State']).' ';
    		$addrArray['region'] = $value['State'];
    	}
    	if (!empty( trim($value['PostalCode']) )) {
    		$addr .= trim($value['PostalCode']).' ';
    		$addrArray['postal_code'] = $value['PostalCode'];
    	}
    	if (!empty( trim($value['Country']) )) {
    		$addr .= trim($value['Country']).' ';
    		$addrArray['country_code'] = $value['Country'];
    	}
    	// get geolocation
    	// if (!empty(trim($value['PostalCode']))) {
    	// 	$latLng = $ins_con->getLatLng($value['PostalCode']);
		   //  $addrArray['lat'] = $latLng['lat']; // generating lat long on fly.
		   //  $addrArray['lng'] = $latLng['lng'];
    	// }
    	$addrArray['lat'] = '';
		$addrArray['lng'] = '';
	    	
    	
    	$addrArray['contact_id'] = (int) $value['Id'];	    

	    $teacherData = array(
	    	'contact_id' 	=> $value['Id'],
	    	'username' 		=> !empty($value['Username'])?$value['Username']:'',
	    	'password' 		=> !empty($value['Password'])?$value['Password']:'',
	    	'given_name' 	=> !empty($value['FirstName'])?$value['FirstName']:'',
	    	'middle_name' 	=> '',
	    	'family_name' 	=> !empty($value['LastName'])?$value['LastName']:'',
	    	'phone' 		=> !empty($value['Phone1'])?$value['Phone1']:'',
	    	'website' 		=> !empty($value['Website'])?$value['Website']:'',
	    	'job_title'		=> !empty($value['JobTitle'])?$value['JobTitle']:'',
	    	'_YogaStudio'	=> !empty($value['_YogaStudio'])?$value['_YogaStudio']:'',
	    	'email' 		=> $email,
	    	'updated_by'	=> 'admin',
	    	'updated_at'	=> current_time('timestamp')
	    );	
	    if($teacher->insert($teacherData)){ 
	    	$tagIds = explode(',', $value['Groups']);
			$resulttag = array_intersect($tagIds, $target);
			foreach ($resulttag as $key => $tagid) {
				$conTag->insert_tag_contact($value['Id'],$tagid);
			}
	    	$insert_contact_success++; 
	    }else{ 
	    	$insert_contact_error++; 
	    }
	    array_push($allAddress, $addrArray);
	}

	if($insert_contact_error>0){
    	echo "Teachers Insertion not succrssfull.";
    	return false;
    }else{
    	echo "Teachers Insertion succrssfull.";
    }
    
    foreach($allAddress as $a_address){
		if(isset($a_address['postal_code']) && !isset($a_address['line1']) && !isset($a_address['line2']) && !isset($a_address['locality']) && !isset($a_address['region']) && !isset($a_address['country_code'])){
			// Only zip code found. Ignor updating
			$a_address['lat'] = '';
			$a_address['lng'] = '';
		}
		else if(isset($a_address['line1']) && !isset($a_address['postal_code']) && !isset($a_address['line2']) && !isset($a_address['locality']) && !isset($a_address['region']) && !isset($a_address['country_code'])){
			// Only street 1 found. Ignor updating
			$a_address['lat'] = '';
			$a_address['lng'] = '';
		}
		else if(!isset($a_address['line1']) && !isset($a_address['postal_code']) && isset($a_address['line2']) && !isset($a_address['locality']) && !isset($a_address['region']) && !isset($a_address['country_code'])){
			// Only street 2 found. Ignor updating
			$a_address['lat'] = '';
			$a_address['lng'] = '';
		}
		else if(!isset($a_address['line1']) && !isset($a_address['postal_code']) && !isset($a_address['line2']) && isset($a_address['locality']) && !isset($a_address['region']) && !isset($a_address['country_code'])){
			// Only city found. Ignor updating
			$a_address['lat'] = '';
			$a_address['lng'] = '';
		}
		else if(!isset($a_address['line1']) && !isset($a_address['postal_code']) && !isset($a_address['line2']) && !isset($a_address['locality']) && isset($a_address['region']) && !isset($a_address['country_code'])){
			// Only region found. Ignor updating
			$a_address['lat'] = '';
			$a_address['lng'] = '';
		}
		else {
			$addr = '';
	    	if (isset($a_address['line1'])) {
	    		$addr .= trim($a_address['line1']).' ';
	    	}
	    	if (isset($a_address['line2'])) {
	    		$addr .= trim($a_address['line2']).' ';
	    	}
	    	if (isset($a_address['locality'])) {
	    		$addr .= trim($a_address['locality']).' ';
	    	}
	    	if (isset($a_address['region'])) {
	    		$addr .= trim($a_address['region']).' ';
	    	}
	    	if (isset($a_address['postal_code'])) {
	    		$addr .= trim($a_address['postal_code']).' ';
	    	}
	    	if (isset($a_address['country_code'])) {
	    		$addr .= trim($a_address['country_code']).' ';
	    	}	
	    	
	    	/*Get latitude & longitude and then update.*/
    		if (!empty(trim($addr))) {
    		    $latLng = $ins_con->getLatLng($addr);
    		    $country = isset($a_address['country_code']) && !empty(trim($a_address['country_code'])) ? trim($a_address['country_code']) : false;
    		    if(!empty($latLng)){
    		       $a_address['lat'] = $latLng['lat'];
			       $a_address['lng'] = $latLng['lng'];
    		    } elseif ($country) { // If full address fail then get country location
    		    	$latLng = $ins_con->getLatLng($country);
    		    	$a_address['lat'] = $latLng['lat'];
			       	$a_address['lng'] = $latLng['lng'];
		    	}else{
    		        $a_address['lat'] = '';
					$a_address['lng'] = '';
    		    }
	    	}	
		}
		$address_id = $address->insert($a_address);		    	
	}

    $socials = $objInf->getallSocials();
    foreach ($socials as $key => $social) {
    	$resocial[$social['ContactId']][$social['AccountType']] = $social;
    }
    
    foreach ($resocial as $key2 => $val) {
    	foreach ($val as $key => $value) {
    		//echo "<pre>"; print_r($value); echo "</pre>";
    		if($key =='Facebook' || $key =='Twitter' || $key =='LinkedIn'){
	    		switch ($key) {
		    		case 'Facebook':
		    			$socId = 1;
		    			break;

		    		case 'Twitter':
		    			$socId = 2;
		    			break;
		    		
		    		case 'LinkedIn':
		    			$socId = 3;
		    			break;

		    		default:
		    			$socId = 1;
		    			break;
		    	}
		    	//echo $value['ContactId'].'--'. $value['AccountName'];
		    	$soc_val = $conSocial->insert_contact_social($value['ContactId'],$socId,$value['AccountName'], $value['Id']);
		    	if($soc_val){
		    		//echo "inserted.";
		    	}else{
		    		echo "Not inserted.";
		    	}
	    	}
    	}
    }
    return true;
}

// add_action( 'wp', 'register_contact_sync');
// function register_contact_sync() {
// 	if( !wp_next_scheduled( 'contact_sync_from_crm_end' ) ) {
// 		wp_schedule_event( time(), 'twicedaily', 'contact_sync_from_crm_end' );
// 	}
// }