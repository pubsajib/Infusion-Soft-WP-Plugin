<?php 
/**
* API related DB operations
*/
class tn_api extends tn_db{
	
	function __construct(){
		parent::__construct();
	}
	public function chage_update_status($contactID, $posts){
		$data = [];
		$data = $this->changed_data($contactID, $posts);
		if (!empty($data) && $this->insert_update_data($contactID, json_encode($data)) ) return true;
		else return false;
	}
	public function changed_data($contactID, $posts){
		// echo "<pre>"; print_r($posts); echo "</pre>"; 
		$tagClass 	= new tn_tag;
		$imageClass = new tn_image;
		$data 		= [];
		unset($posts['update_contact_button']);

		$image 			= $this->get_new_field($posts['image'], $posts['h_image']);
		$username 		= $this->get_new_field($posts['username'], $posts['h_username']);
		$password 		= $this->get_new_field($posts['password'], $posts['h_password']);
		$given_name 	= $this->get_new_field($posts['given_name'], $posts['h_given_name']);
		$family_name 	= $this->get_new_field($posts['family_name'], $posts['h_family_name']);
		$job_title 		= $this->get_new_field($posts['job_title'], $posts['h_job_title']);
		$email 			= $this->get_new_field($posts['email'], $posts['h_email']);
		$phone 			= $this->get_new_field($posts['phone'], $posts['h_phone']);
		$website 		= $this->get_new_field($posts['website'], $posts['h_website']);
		$_YogaStudio 	= $this->get_new_field($posts['_YogaStudio'], $posts['h__YogaStudio']);
		$bio 			= $this->get_new_field($posts['bio'], $posts['h_bio']);
		$is_show_title 	= $this->get_new_field($posts['is_show_title'], $posts['h_is_show_title']);

        $social 		= $this->get_new_field($posts['social'], $posts['h_social']);
        if ($social) { $social_crm_id = json_encode($posts['social_crm_id']); }
        $tag 			= $this->get_new_field($posts['tag'], $posts['h_tag']);

        // Address form data
		$line1 			= $this->get_new_field($posts['line1'], $posts['h_line1']);
		$line2 			= $this->get_new_field($posts['line2'], $posts['h_line2']);
		$locality 		= $this->get_new_field($posts['locality'], $posts['h_locality']);
		$region 		= $this->get_new_field($posts['region'], $posts['h_region']);
		$postal_code 	= $this->get_new_field($posts['postal_code'], $posts['h_postal_code']);
		$country_code 	= $this->get_new_field($posts['country_code'], $posts['h_country_code']);


		if ( !is_null($image) ) 		{ $data['image'] 		= $image; }
		if ( $_FILES['fileToUpload']['name'] != '' ) { $data['image'] = $imageClass->upload_image($post,$contactID); }
		if ( !is_null($username) ) 		{ $data['username'] 	= $username; }
		if ( !is_null($password) ) 		{ $data['password'] 	= $password; }
		if ( !is_null($given_name) ) 	{ $data['given_name'] 	= $given_name; }
		if ( !is_null($family_name) ) 	{ $data['family_name'] 	= $family_name; }
		if ( !is_null($job_title) ) 	{ $data['job_title'] 	= $job_title; }
		if ( !is_null($email) ) 		{ $data['email'] 		= $email; }
		if ( !is_null($phone) ) 		{ $data['phone'] 		= $phone; }
		if ( !is_null($website) ) 		{ $data['website'] 		= $website; }
		if ( !is_null($_YogaStudio) ) 	{ $data['_YogaStudio'] 	= $_YogaStudio; }
		if ( !is_null($bio) ) 			{ $data['bio'] 			= $bio; }
		if ( !is_null($is_show_title) ) { $data['is_show_title']= $is_show_title; }
		if ( !is_null($social) ) 		{ $data['social_id'] 	= $posts['h_social_id']; $data['social'] = $social; }
		if ( !is_null($social_crm_id) ) { $data['social_crm_id']= $social_crm_id; }
		if ( !is_null($tag) ) 			{ $data['tag'] 			= $tag; }

		// Address form data
		if ( !is_null($line1) ) 		{ $data['address']['line1'] 		= $line1; }
		if ( !is_null($line2) ) 		{ $data['address']['line2'] 		= $line2; }
		if ( !is_null($locality) ) 		{ $data['address']['locality'] 		= $locality; }
		if ( !is_null($region) ) 		{ $data['address']['region'] 		= $region; }
		if ( !is_null($postal_code) ) 	{ $data['address']['postal_code'] 	= $postal_code; }
		if ( !is_null($country_code) ) 	{ $data['address']['country_code'] 	= $country_code; }

		// echo "<pre>"; print_r($data); echo "</pre>"; exit(); die();
		return $data;
	}
	public function insert_update_data($ID, $values=''){
		$tableName = $this->teacherTable;
		$result = $this->db->update( 
			$tableName, 
			array( 'update_status'=>'PENDING', 'update_values' => $values ),
			array( 'contact_id' => $ID ), 
			array( '%s', ),
			array( '%d' ) 
		);
		return $result;
	}
	public function get_new_field($value1, $value2){
		if (is_array($value1)) {
			// if ( array_diff($value1, $value2) ) { return $value1; }
			if ( $value1 != $value2 ) { return $value1; }
			else { return null;}
			// return $value1;
		}
		elseif ( @trim($value1) != @trim($value2) ) { return $value1; }
		else { return null;}
	}
}