<?php 
/**
* API SDK
*/
class tn_infusion extends tn_db {

	public $infusionsoft;
	public $token_id;
	
	function __construct(){
		parent::__construct();
		$re_url 		= $this->currentPageUrl();
		$client       	= $this->select_data();
	    $client_id      = $client[0]->client_id;
	    $client_secret  = $client[0]->client_secret;
		$this->infusionsoft = new \Infusionsoft\Infusionsoft(array(
	        'clientId'     => $client_id,
	        'clientSecret' => $client_secret,
	        'redirectUri'  => $re_url
	    ));
	    $this->tokenHack();
	}

	public function currentPageUrl(){
		$isSecure = false;
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		    $isSecure = true;
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
		    $isSecure = true;
		}
		$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
		return admin_url( 'admin.php?page=tn-settings', $REQUEST_PROTOCOL );
	}

	public function tokenHack(){
	
		$i_token 	= $this->select_token();
		// print_r($i_token);
		if(isset($i_token) && !empty($i_token)){
			$extraInfo = array(
	            'token_type' => 'bearer',
	            'scope' => $i_token['scope']
	        );
		        
			$token = new \Infusionsoft\Token();
	        $token->accessToken = trim($i_token['access_t']);
	        $token->refreshToken = trim($i_token['refresh_t']);
	        $token->endOfLife = $i_token['end_of_life'];
	        $token->extraInfo = $extraInfo;
	        // print_r($token);
	        $this->infusionsoft->setToken($token);

	        if($this->infusionsoft->isTokenExpired()){
	        	echo "expired";
	        	$this->infusionsoft->refreshAccessToken();
	   			$generated_token = $this->infusionsoft->getToken();	
	   			if(is_object ( $generated_token )){
	   				$insert_array[0] = $generated_token->accessToken;
	   				$insert_array[1] = $generated_token->refreshToken;
	   				$insert_array[2] = $generated_token->endOfLife;
	   				$insert_array[3] = $generated_token->extraInfo['scope'];
	   				$this->insert_token($insert_array);
	   			}
	        }
		}
		// else{
		// 	$url = $this->infusionsoft->getAuthorizationUrl();
		// 	echo '<a class="btn btn-primary" href="' . $url . '">Click here to authorize</a>';
		// }	
	}

	public function firstTokenInsert($code){
	    if (isset( $code ) && !empty($code)) {
			$token_array = $this->infusionsoft->requestAccessToken( $code );
			// echo "<pre>"; print_r($retVal); echo "</pre>"; exit();
			if(is_object ( $token_array )){
   				$insert_array[0] = $token_array->accessToken;
   				$insert_array[1] = $token_array->refreshToken;
   				$insert_array[2] = $token_array->endOfLife;
   				$insert_array[3] = $token_array->extraInfo['scope'];
   				if ($this->insert_token($insert_array)) {
   					return true;
   				}else{
   					return false;
   				}
   			}
		}else{
			return false;
		}
	}

	public function getAuthencation(){
		$url = $this->infusionsoft->getAuthorizationUrl();
		echo '<a class="btn btn-primary" href="' . $url . '">Click here to authorize</a>';
	}

	public function getCurrentToken(){
	    $i_token    = $this->select_token();
	    if($i_token==false){
	        echo "0 results. Check database.";
	    }else{
	        $extraInfo = array(
	            'token_type' => 'bearer',
	            'scope' => $i_token['scope']
	        );
	        $token = new \Infusionsoft\Token();
	        $token->accessToken = trim($i_token['access_t']);
	        $token->refreshToken = trim($i_token['refresh_t']);
	        $token->endOfLife = $i_token['end_of_life'];
	        $token->extraInfo = $extraInfo;

	        $this->token_id = $i_token['id'];

	        $this->infusionsoft->setToken($token);
	        if($this->infusionsoft->isTokenExpired()){
	            $this->generateToken();
	        }else{
	            $_SESSION['token'] = serialize($token);
	        }
	    }
	}

	public function generateToken(){
	    $this->infusionsoft->refreshAccessToken();
	    $generated_token = $this->infusionsoft->getToken();
	    $_SESSION['token'] = serialize($generated_token);
	    $end_of_life = time() + 86400;
	    $bool_res = $this->update_token($this->token_id,$generated_token->accessToken,$generated_token->refreshToken,$end_of_life);
	    // print_r($bool_res);
	    if($bool_res){
	        // echo "Refresh token updated";
	    }else{
	        echo "Refresh token not updated";
	    }
	    // echo $_SESSION['token'];
	}

	public function return_job_and_studio($id,$returnFields){
	    $table = 'Contact';
        $limit = 1;
        $page = 0;
        $fieldName = 'Id';
        $fieldValue = $id;
        $dataJY = $this->infusionsoft->data()->findByField($table, $limit, $page, $fieldName, $fieldValue, $returnFields);
        return $dataJY;
	}

	public function return_social_byID($id){
	        $table = 'SocialAccount';
	        $limit = 1000;
	        $page = 0;
	        $contactId = $id;
	        $queryData = array('ContactId' => $contactId);
	        $selectedFields = array('Id','ContactId','AccountName','AccountType','DateCreated','LastUpdated');
	        $orderBy = 'Id';
	        $ascending = true;
	        $test = $this->infusionsoft->data()->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);
	        foreach($test as $val){
	            $result[$val['AccountType']]['id'] = $val['Id'];
	            $result[$val['AccountType']]['url'] = $val['AccountName'];
	        }
	        return $result;
	}

	public function authenticate($usermail = '', $password = ''){
	    $selectedFields = array('FirstName','LastName','Email','Username','Password','Id');
	    if($password == ''){return false;}
	    if($usermail == ''){return false;}
	    // echo $usermail. "=" . $password;
	    $is_email = $this->isValidEmail($usermail);	
	    if($is_email){
	    	$email = $usermail;
	    }else{
	    	$username = $usermail;
	    }
	    $table = 'Contact';
        $limit = 1;
        $page = 0;
        $returnFields = $selectedFields;
	    if($username != ''){
	        $fieldName = 'Username';
	        $fieldValue = $username;
	        $data = $this->infusionsoft->data()->findByField($table, $limit, $page, $fieldName, $fieldValue, $returnFields);
	        // print_r($data);
	    }
	    if( $email != '' && empty($data) ){
	        $fieldName = 'Email';
	        $fieldValue = $email;
	        $data = $this->infusionsoft->data()->findByField($table, $limit, $page, $fieldName, $fieldValue, $returnFields);
	        if(!isset($data) && empty($data)){
	        	$fieldName = 'EmailAddress2';
		        $fieldValue = $email;
		        $data = $this->infusionsoft->data()->findByField($table, $limit, $page, $fieldName, $fieldValue, $returnFields);
	        }
	    }
	    if(isset($data) && !empty($data)){
            if($data[0]['Password'] == $password){
                return $data[0]['Id'];
            }else{
                return false;
            }
        }
	    return false;
	}

	public function isValidEmail($email) {
	    return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);
	}

	public function authenticate_autologin($usermail1 = '', $usermail2 = '', $id = ''){ 
	    $selectedFields = array('FirstName','LastName','Email','EmailAddress2','Username','Password','Id');
	    if($id == ''){return false;}
	    // if($usermail == ''){return false;}
	    if($usermail1 != '' || $usermail2 != ''){
	        $table = 'Contact';
	        $limit = 1;
	        $page = 0;
	        $fieldName = 'Id';
	        $fieldValue = $id;
	        $returnFields = $selectedFields;
	        $data = $this->infusionsoft->data()->findByField($table, $limit, $page, $fieldName, $fieldValue, $returnFields);
	        if(isset($data) && !empty($data)){
	        	// print_r($data);
	            if(($data[0]['Email'] == $usermail1) || ($data[0]['EmailAddress2'] == $usermail2))
	            {
	                return $data[0]['Id'];
	            }else{
	                return false;
	            }
	        }
	    }
	    return false;
	}

	public function getContactById($id){
		$table = 'Contact';
        $limit = 1;
        $page = 0;
        $fieldName = 'Id';
        $fieldValue = $id;
        try {
        	$selectedFields = array('Id','Groups','FirstName','LastName','Email','Username','Password','EmailAddress2','JobTitle','Phone1','Website','City','Country','PostalCode','State','StreetAddress1','StreetAddress2','_YogaStudio');
        	$returnFields = $selectedFields;
        	$data = $this->infusionsoft->data()->findByField($table, $limit, $page, $fieldName, $fieldValue, $returnFields);
        } catch (Exception $e) {
        	$selectedFields = array( 'Id','Groups','FirstName','LastName','Email','Username','Password','EmailAddress2','JobTitle','Phone1','Website','City','Country','PostalCode','State','StreetAddress1','StreetAddress2' );
        	$returnFields = $selectedFields;
        	$data = $this->infusionsoft->data()->findByField($table, $limit, $page, $fieldName, $fieldValue, $returnFields);
        	// echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        if( isset($data) && !empty($data) ){ return $data[0]; }
        else{ return false; }
	}

	public function contactSocial($contactId){
	    $table = 'SocialAccount';
	    $limit = 1000;
	    $page = 0;
	    $queryData = array('ContactId' => $contactId);
	    $selectedFields = array('Id','ContactId','AccountName','AccountType','DateCreated','LastUpdated');
	    $orderBy = 'Id';
	    $ascending = true;

	    $result = array();
	    $date = date('Y-m-d H:i:s');
	    $contact_socials = $this->infusionsoft->data()->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);

	    // echo "<pre>";print_r($contact_socials);	 echo "</pre>";   
	    foreach($contact_socials as $val){
	        $result[$val['Id']] = array(
	        	'social_id'	=> $val['Id'],
	        	'cont_id'	=> $val['ContactId'],
	        	'acc_type'	=> $val['AccountType'],
	        	'acc_name'	=> $val['AccountName']
	        );
	    }
	    // echo "<pre>";print_r($result);	 echo "</pre>";   
	    $objSoc = new tn_social();
	    foreach($result as $key => $val){
	        $existing_social = $objSoc->select_social_type($val['acc_type']);
	        // echo "<pre>"; print_r($existing_social);echo "</pre>"; 
			if (count($existing_social) > 0) {
	            // output data of each row
	            foreach ($existing_social as $key => $value) {
	            	$social_id = $value['social_id'];
	            	$existing_contact_social = $objSoc->existing_contact_social($contactId,$social_id);
	            	// echo "<pre>"; print_r($existing_contact_social);echo "</pre>"; 
	            	if(count($existing_contact_social)>0){
	            		foreach ($existing_contact_social as $keyS => $value_social) {
	            			$contact_social_id = $value_social['contact_social_id'];
	            			$update_social = $objSoc->update_contact_social($val['acc_name'],$contact_social_id);
	            			if($update_social){}else{echo "Social Not Update.";}
	            		}
	            	}else{
	            		$contact_social = $objSoc->insert_contact_social($contactId,$social_id,$val['acc_name'], $crm_social_id);
	            		if($contact_social){}else{echo "Social Not Inserted.";}
	            		// echo $social_id.'Add:-'.$key.'-'.$contact_social;
	            	}
	            }
	        }
	    }
	}

	public function getTags(){

	    $table = 'ContactGroup';
	    $limit = 1000;
	    $page = 0;
	    $queryData = array( 'Id' => '%' );
	    $selectedFields = array('Id','GroupName','GroupDescription','GroupCategoryId');
	    $orderBy = 'Id';
	    $ascending = true;
	    $date = date('Y-m-d H:i:s');
	    
	    $tags = $this->infusionsoft->data()->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);
	    $updated = 0;
	    $inserted = 0;
	    $obj_tag = new tn_tag;
	    // print_r($tags);
	    foreach($tags as $val){

	        $id = $val['Id'];
	        $CategoryId = $val['GroupCategoryId'];
	        $tagName = $val['GroupName'];

	        $existing = $obj_tag->select_tagbyId($id);
	        // echo "<pre>"; print_r($existing); echo "</pre>";

	        if (count($existing) > 0) {
	            // output data of each row
	            foreach ($existing as $key => $value) {
	            	$obj_tag->update_contact_tag($id,$tagName);
	            	$updated++;
	            }
	        } else {
	        	$obj_tag->insert_contact_tag($id,$CategoryId,$tagName);
	            $inserted++;
	        }
	    }
	    $message = $updated.' Updated<br/>'.$inserted.' Inserted';
	    echo $message;
	}

	public function getCategory(){

	    $table = 'ContactGroupCategory';
	    $limit = 1000;
	    $page = 0;
	    $queryData = array('Id' => '%');
	    $selectedFields = array('Id','CategoryName', 'CategoryDescription');
	    $orderBy = 'Id';
	    $ascending = true;

	    $date = date('Y-m-d H:i:s');
	    $categories = $this->infusionsoft->data()->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);
	    $updated = 0;
	    $inserted = 0;
	    $objCat = new tn_tag;  // print_r($categories);
	    foreach($categories as $val){
	        $id = $val['Id'];
	        $CategoryName = $val['CategoryName'];

	        $existing = $objCat->select_categorybyId($id);

	        if (count($existing) > 0) {
	            // output data of each row
	            foreach ($existing as $key => $value) {
	            	echo $CategoryName."Update";
	            	$existing = $objCat->update_tag_category($id,$CategoryName);
	            	$updated++;
	            }
	        } else {
	        	echo $CategoryName;
	        	$existing = $objCat->insert_tag_category($id,$CategoryName);
	            $inserted++;
	        }
	    }
	    // $message = $updated.' Updated<br/>'.$inserted.' Inserted';
	    // echo $message;
	}

	public function contactTags($contactId){
	    $selectedFields = array('Groups');
	    try{
			$contact_groups = $this->infusionsoft->contacts()->load($contactId, $selectedFields);
			// print_r($contact_groups);
	    }catch(Exception $ex){
			echo "";
	    }
	    if(is_array($contact_groups)){
	    	// print_r($contact_groups);
		    $groups = explode(',', $contact_groups['Groups']);
		    $date = date('Y-m-d H:i:s');
		    $updated = 0;
		    $inserted = 0;
		    $contact_tag_updated = 0;
		    $contact_tag_inserted = 0;
		    $objCon = new tn_tag;
		    foreach($groups as $val){
		        $table = 'ContactGroup';
		        $limit = 1000;
		        $page = 0;
		        $queryData = array('Id' => $val);
		        $selectedFields = array('Id','GroupName','GroupDescription','GroupCategoryId');
		        $orderBy = 'Id';
		        $ascending = true;
		        $tag_data = $this->infusionsoft->data()->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);
		        foreach($tag_data as $v){
		            $tagId = $v['Id'];
		            $GroupName = $v['GroupName'];
		            $categoryId = $v['GroupCategoryId'];

		            $existing = $objCon->select_contactbyId($tagId);
		            // echo "<pre>";print_r($existing); echo "</pre>";
		            if (count($existing)> 0) {
		            	foreach ($existing as $key => $value) {
		            		$contact_tags_id = $existing[0]['contact_tags_id'];
		            		$updateCon = $objCon->update_tag_contact($contactId,$tagId,$contact_tags_id);
		            		if($updateCon){echo "";}else{echo "Not updated.";}
		            	}
		            } else {
		            	$updateCon = $objCon->insert_tag_contact($contactId,$tagId);
		                $contact_tag_inserted++;
		            }
		        }
		    }
	    }else{
	    	echo "No tag.";
	    }
	}

	public function getContactTags($contact_id){
	    $selectedFields = array('Groups');
	    $test = $this->infusionsoft->contacts()->load($contact_id, $selectedFields);
	    $groups = explode(',', $test['Groups']);
	    $return = array();
	    foreach($groups as $val){

	        $table = 'ContactGroup';
	        $limit = 1000;
	        $page = 0;
	        $queryData = array('Id' => $val);
	        $selectedFields = array('Id','GroupName','GroupDescription','GroupCategoryId');
	        $orderBy = 'Id';
	        $ascending = true;

	        $tag_data = $this->infusionsoft->data()->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);
	        foreach($tag_data as $v){
	            $tag_name = $v['GroupName'];
	            $tags_id = $v['Id'];
	            $category_id = $v['GroupCategoryId'];

	            $table = 'ContactGroupCategory';
	            $limit = 1000;
	            $page = 0;
	            $queryData = array('Id' => $category_id);
	            $selectedFields = array('Id','CategoryName');
	            $orderBy = 'Id';
	            $ascending = true;

	            $date = date('Y-m-d H:i:s');
	            $testData = $this->infusionsoft->data()->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);

	            $category_name = $testData[0]['CategoryName'];
	            $return[$tags_id]['tag_name'] = $tag_name;
	            $return[$tags_id]['category_name'] = $category_name;
	        }
	    }
	    return $return;
	}

	public function updateContact($id,$data){
    	if (is_object($data)) {
    		if(isset($data->username)){
		        $contactData['Username']=$data->username;
		    }
		    if(isset($data->job_title)){
		        $contactData['JobTitle']=$data->job_title;
		    }
		    if(isset($data->given_name)){
		        $contactData['FirstName']=$data->given_name;
		    }
		    if(isset($data->family_name)){
		        $contactData['LastName']=$data->family_name;
		    }
		    if(isset($data->email[0])){
		        $contactData['Email']=$data->email[0];
		    }
		    if(isset($data->email[1])){
		        $contactData['EmailAddress2']=$data->email[1];
		    }
		    if(isset($data->password)){
		        $contactData['Password']=$data->password;
		    }
		    if(isset($data->website)){
		        $contactData['Website']=$data->website;
		    }
		    if(isset($data->phone)){
		        $contactData['Phone1']=$data->phone;
		    }
		    if(isset($data->_YogaStudio)){
		        $contactData['_YogaStudio']=$data->_YogaStudio;
		    }
    	} else {
    		if(isset($data['username'])){
		        $contactData['Username']=$data['username'];
		    }
		    if(isset($data['job_title'])){
		        $contactData['JobTitle']=$data['job_title'];
		    }
		    if(isset($data['given_name'])){
		        $contactData['FirstName']=$data['given_name'];
		    }
		    if(isset($data['family_name'])){
		        $contactData['LastName']=$data['family_name'];
		    }
		    if(isset($data['email'][0])){
		        $contactData['Email']=$data['email'][0];
		    }
		    if(isset($data['email'][1])){
		        $contactData['EmailAddress2']=$data['email'][1];
		    }
		    if(isset($data['password'])){
		        $contactData['Password']=$data['password'];
		    }
		    if(isset($data['website'])){
		        $contactData['Website']=$data['website'];
		    }
		    if(isset($data['phone'])){
		        $contactData['Phone1']=$data['phone'];
		    }
		    if(isset($data['_YogaStudio'])){
		        $contactData['_YogaStudio']=$data['_YogaStudio'];
		    }
    	}
    	if (!empty($contactData)) {
		    return $this->infusionsoft->contacts()->update($id, $contactData);
    	} else { return true; }
	}

	public function updateSocial( $social_id,$social_data,$crm_social_id,$cId ){
		$crm_social_id = json_decode($crm_social_id, true);
		foreach ($crm_social_id as $key => $value) {
		    if (empty($value)) {
		       unset($crm_social_id[$key]);
		    }
		}
	    $table = 'SocialAccount';
	    if( empty($crm_social_id) ){
	    	foreach($social_data as $key=>$value){
    			switch ($key) {
    				case '1':
    					$type = 'Facebook';
    					break;
    				case '2':
    					$type = 'Twitter';
    					break;
    				case '3':
    					$type = 'LinkedIn';
    					break;
    				default:
    					# code...
    					break;
    			}
		    	$values = array(
			        'AccountName'=>$value,
			        'AccountType'=>$type,
			        'ContactId'=>$cId
			    );
			    $return[] = $this->infusionsoft->data()->add($table, $values);
			}
	    }else{
	    	foreach($social_data as $key=>$value){
		        $crm_id = $crm_social_id[$key];
		        $accountName = $value;
		        $values = array(
			        'AccountName'=>$accountName,
			    );
		        try {
		        	$return[] = $this->infusionsoft->data()->update($table, $crm_id, $values);
		        } catch (Exception $e) {
		        	//echo "Not Updated.";
		        }
		    }
	    }
	    return $return;
	}

	public function updateTag($id,$data){

	    if(isset($data->username)){
	        $tagData['Username']=$data->username;
	    }
	    if(isset($data->job_title)){
	        $tagData['JobTitle']=$data->job_title;
	    }
	    if(isset($data->given_name)){
	        $tagData['FirstName']=$data->given_name;
	    }

	    // echo "<pre>"; print_r($contactData); echo "</pre>";
	    // exit();
	    $return = $this->infusionsoft->contacts()->update($id, $tagData);
	    return $return;
	}

	public function updateAddress($id,$data){
		if (is_object($data)) {
		    if(isset($data->line1)){
		        $addressData['StreetAddress1']=$data->line1;
		    }
		    if(isset($data->line2)){
		        $addressData['StreetAddress2']=$data->line2;
		    }
		    if(isset($data->locality)){
		        $addressData['State']=$data->locality;
		    }
		    if(isset($data->region)){
		        $addressData['City']=$data->region;
		    }
		    if(isset($data->postal_code)){
		        $addressData['PostalCode']=$data->postal_code;
		    }
		    if(isset($data->country_code)){
		        $addressData['Country']=$data->country_code;
		    }
		} else{
			if(isset($data['line1'])){
		        $addressData['StreetAddress1']=$data['line1'];
		    }
		    if(isset($data['line2'])){
		        $addressData['StreetAddress2']=$data['line2'];
		    }
		    if(isset($data['locality'])){
		        $addressData['State']=$data['locality'];
		    }
		    if(isset($data['region'])){
		        $addressData['City']=$data['region'];
		    }
		    if(isset($data['postal_code'])){
		        $addressData['PostalCode']=$data['postal_code'];
		    }
		    if(isset($data['country_code'])){
		        $addressData['Country']=$data['country_code'];
		    }
		}

	    // echo "<pre>"; print_r($addressData); echo "</pre>";
	    $return = $this->infusionsoft->contacts()->update($id, $addressData);
	    return $return;
	}
	/** 
    * Fetch contacts form CRM by tags 
    * Check is there any mandatory tags or not 
    * @param (boolean) true, false
    * @return (array) contacts
    **/
	public function getContactsbyTags($isMandatoryTag=false){
		$table = 'Contact';
		$limit = 1000;
		$page = 0;
		$fieldName = 'Groups';
		$fieldValues = unserialize (TNTAGS);
		if ( !$isMandatoryTag ) {
			$fieldValues = array_flip($fieldValues);
			if (isset($fieldValues[TNMANDATORYTAGS])) { unset($fieldValues[TNMANDATORYTAGS]); }
			$fieldValues = array_flip($fieldValues);
		}
		// echo "<pre>"; print_r($fieldValues); echo "</pre>";
		try {
			$returnFields = array('Id','Groups','FirstName','LastName','Email','Username','Password','EmailAddress2','JobTitle','Phone1','Website','City','Country','PostalCode','State','StreetAddress1','StreetAddress2','_YogaStudio');
			foreach( $fieldValues as $fieldValue ){
				$rawSocialContacts = $this->infusionsoft->data()->findByField($table, $limit, $page, $fieldName, $fieldValue, $returnFields);
				foreach($rawSocialContacts as $socialContact){
					$finalSocialContacts[$socialContact['Id']] = $socialContact;

					// Adding must tag conditions
					// $tags = explode(',', $socialContact['Groups']);
					// if (
					// 	in_array(1148, $tags) && (
					// 		in_array(423, $tags) ||
					// 		in_array(145, $tags) ||
					// 		in_array(303, $tags) ||
					// 		in_array(143, $tags) ||
					// 		in_array(509, $tags) ||
					// 		in_array(305, $tags) 
					// 	)
					// ) { $finalSocialContacts[$socialContact['Id']] = $socialContact; }
				}
			}
		} catch (Exception $e) {
			$returnFields = array('Id','Groups','FirstName','LastName','Email','Username','Password','EmailAddress2','JobTitle','Phone1','Website','City','Country','PostalCode','State','StreetAddress1','StreetAddress2');
			foreach( $fieldValues as $fieldValue ){
				$rawSocialContacts = $this->infusionsoft->data()->findByField($table, $limit, $page, $fieldName, $fieldValue, $returnFields);
				foreach($rawSocialContacts as $socialContact){
					$finalSocialContacts[$socialContact['Id']] = $socialContact;
				}
			}
		} 
		return $finalSocialContacts;
	}

	public function getallSocials(){
	    $table = 'SocialAccount';
	    $limit = 1000;
	    $page = 0;
	    $queryData = array('Id' => '%','AccountType'=>'~<>~null');
	    $selectedFields = array('Id','ContactId','AccountName','AccountType');
	    $orderBy = 'Id';
	    $ascending = true;

	    $socials = $this->infusionsoft->data()->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);
	    return $socials;
	}

	public function addContactTosequence( $sequenceStepId ){
		$teachers = new tn_teachers;
		$contactIds = $teachers->getContactIds();
		try {
			$data = $this->infusionsoft->contacts()->rescheduleCampaignStep($contactIds, $sequenceStepId);
		} catch (Exception $e) {
			echo "Contacts addition to sequence not successfull. Try again.".$e->getMessage();
		}
		if($data){ return $data; } else{ return false; }
	}

	public function updateTagsbyCId($contactId, $tags = array(1148)){
		$return = 0;
		if(count($tags) > 0 && !empty($contactId)) {
			foreach ($tags as $tag) {
				$return = $this->infusionsoft->contacts()->addToGroup($contactId, $tag);
			}
		}
		return $return;
	}
	/**
	* Check whether its a valid contact or not for updating or inserting 
	* Based on tag IDs ( Mandatory and Optionals )
	**
	* @param $contact ( contactID or contact )
	* @param (boolean) $isTagBased 
	* @param (boolean) $mandatoryTag 
	**
	* @return contact informations if all tags set or false 
	**/
	public function is_valid_contact($contact, $isTagBased=false, $isMandatoryTag=TNISMUSTTAG){
		$errors = 0;
		if ($isTagBased) { $contactData = $contact; }
		else{ $contactData = $this->getContactById($contact); }
		$CRMTags = explode(',', $contactData['Groups']);

		// Check the mandatory tag
		if ($isMandatoryTag && !empty(TNMANDATORYTAGS) && in_array(TNMANDATORYTAGS, $CRMTags)) { 
			$CRMTags = array_flip($CRMTags);
			unset($CRMTags[TNMANDATORYTAGS]);
			$CRMTags = array_flip($CRMTags); 
		} else if ($isMandatoryTag) { $errors++; }

		// Check optional tags
		// if ( !empty(TNOPTIONALTAGS)) {
		// 	$optionalTags = explode(',', TNOPTIONALTAGS);
		// 	$commonTags = array_intersect($optionalTags, $CRMTags);
		// 	if (empty($commonTags)) { $errors++; }
		// }

		// return the required data
		// if ( $errors < 1 ) { return $contactData; }
		if ( $errors < 1 ) { return true; }
		else { return false; }
	}
}