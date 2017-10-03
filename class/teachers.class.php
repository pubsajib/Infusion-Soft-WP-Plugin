<?php 
/**
* Teachers class
*/
class tn_teachers extends tn_db{
	private $backend = false;
	private $UI;
	function __construct(){
		parent::__construct();
		$this->FUI = new tn_ui_frontend;
		$this->BUI = new tn_ui_backend;
	}
	public function update($array, $ID){
		return $this->db->update( $this->teacherTable, $array, array( 'contact_id' => $ID ) );
	}
	public function insert($array){
		$this->insertArray = $array;
		$inserted = $this->db->insert($this->teacherTable, $this->insertArray);
        if ($inserted){ return true; }
        else{ return false; }
	}
	public function delete_teacher($ID){
		return $this->db->delete( $this->teacherTable, array( 'contact_id' => $ID ) );
	}
	public function is_exists($ID){
		$tableName = $this->teacherTable;
        $sql = "SELECT * FROM `$tableName` WHERE `contact_id`=".$ID." LIMIT 1;";
        $rows = $this->db->get_results($sql);
        if (!empty($rows)) return true;
        else return false;
	}
	public function update_teacher($ID, $array){
		$tableName =$this->teacherTable;
		if ( $this->check_valid_data($array['given_name']) ) $this->insertArray['given_name'] = $this->check_valid_data($array['given_name']);
		if ( $this->check_valid_data($array['family_name']) ) $this->insertArray['family_name'] = $this->check_valid_data($array['family_name']);
		if ( $this->check_valid_data($array['email']) ) $this->insertArray['email'] = $this->check_valid_data($array['email']);
		if ( $this->check_valid_data($array['phone']) ) $this->insertArray['phone'] = $this->check_valid_data($array['phone']);
		if ( $this->check_valid_data($array['website']) ) $this->insertArray['website'] = $this->check_valid_data($array['website']);
		if ( $this->check_valid_data($array['tags']) ) $this->insertArray['tags'] = $this->check_valid_data($array['tags']);
		if ( $this->check_valid_data($array['special_tags']) ) $this->insertArray['special_tags'] = $this->check_valid_data($array['special_tags']);

		return $this->db->update( $this->teacherTable, $this->insertArray, array( 'contact_id' => $ID ) );
	}
	public function get_config(){
		$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
		include($path.'wp-load.php');
	}
	public function get_columns(){
		$tableName = $this->teacherTable;
		$existing_columns = $this->db->get_col("DESC {$tableName}", 0);
    	return $sql = implode( ', ', $existing_columns );
	}
	// get address
	public function teachers_corrosponding_address($teachers){
		$addressTable = $this->addressTable;
        if ($teachers) {
        	foreach ($teachers as $teacher) {
		        $sql = "SELECT * FROM `$addressTable` WHERE `contact_id`=".$teacher->contact_id." ORDER BY `id` ASC;";
		        $adds = $this->db->get_results($sql);
		        $teacher->adds = $adds;
        	}
        }
        return $teachers;
	}
	// get image or avatar
	public function teachers_corrosponding_avatar($teachers){
		$tableName = $this->imageTable;
        if ($teachers) {
        	foreach ($teachers as $teacher) {
		        $sql = "SELECT * FROM `$tableName` WHERE `contact_id`=".$teacher->contact_id." ORDER BY `id` ASC;";
		        $avatar = $this->db->get_results($sql);
		        $avatar = $avatar[0]->url ? $avatar[0]->url : '';
		        $teacher->avatar = $avatar;
        	}
        }
        return $teachers;
	}
	// get socials
	public function teachers_corrosponding_socials($teachers){
        if ($teachers) {
        	foreach ($teachers as $teacher) {
		        $sql = "";
		        $sql .= "SELECT * FROM `".$this->contactSocialTable."` AS cs ";
		        $sql .= "JOIN `".$this->socialTypeTable."` AS st ON cs.`social_id`=st.`social_id` ";
		        $sql .= "WHERE `contact_id`=".$teacher->contact_id.";";
		        $socials = $this->db->get_results($sql);
		        // $socials[] = $socials;
        	}
		    $teacher->social = $socials;
        }
        return $teachers;
	}
	// get corresponding tags
	public function teachers_corrosponding_tags($teachers){
        if ($teachers) {
        	foreach ($teachers as $teacher) {
        		// SELECT * FROM `wp_tn_contact_tags` AS ct JOIN `wp_tn_tags` AS t ON ct.`tag_id` = t.`tag_id` WHERE ct.`contact_id`=1250 ORDER BY ct.`tag_id` ASC
		        $sql = "";
		        $sql .= "SELECT * FROM `".$this->contactTagTable."` AS ct ";
		        $sql .= "JOIN ".$this->tagTable." AS t ON ct.tag_id = t.tag_id ";
		        $sql .= "JOIN ".$this->tagCatTable." AS c ON t.category_id = c.category_id ";
		        $sql .= "WHERE `contact_id`=".$teacher->contact_id." ORDER BY ct.`tag_id` ASC;";
		        $tags = $this->db->get_results($sql);
        	}
		    $teacher->tags = $tags;
        }
        return $teachers;
	}
	public function allteachersRows(){
		$teacherTable = $this->teacherTable;
        // $sql = "SELECT * FROM `$teacherTable` ORDER BY `id` ASC LIMIT 1;";
        $sql = "SELECT COUNT(*) as total FROM `$teacherTable`;";
        $count = $this->db->get_results($sql, ARRAY_A);
        return $count;
	}
	public function getContactIds(){
		$teacherTable = $this->teacherTable;
        $sql = "SELECT `contact_id` FROM `$teacherTable` ORDER BY `id` ASC LIMIT 1;";
        $conId= $this->db->get_results($sql, ARRAY_A);
        return $conId;
	}
	public function lastsyncdate(){
		$teacherTable = $this->teacherTable;
        $sql = "SELECT `created_at` FROM `$teacherTable` ORDER BY `id` ASC LIMIT 1;";
        $createdate = $this->db->get_results($sql, ARRAY_A);
        return $createdate;
	}
	// select teachers form db
	public function teachersRows($type='back_end'){
		$teacherTable = $this->teacherTable;
		if ($type == 'back_end') {
        	$sql = "SELECT * FROM `$teacherTable` ORDER BY `id` ASC;";
		}else{
			$allowedTeachers = $this->allowed_teachers();
	        $sql = "SELECT * FROM `$teacherTable` WHERE `contact_id` IN (".$allowedTeachers.") ORDER BY `id` ASC;";
		}
        $teachers = $this->db->get_results($sql);
        $teachers = $this->teachers_corrosponding_address($teachers); // get address
        $teachers = $this->teachers_corrosponding_avatar($teachers); // get image or avatar
        return $teachers;
	}
	// select teachers form db
	public function teachersRow($ID){
		$teacherTable = $this->teacherTable;
        // $sql = "SELECT * FROM `$teacherTable` ORDER BY `id` ASC LIMIT 1;";
        $sql = "SELECT * FROM `$teacherTable` WHERE `contact_id`=$ID ORDER BY `id` ASC LIMIT 1;";
        $teachers = $this->db->get_results($sql);
        $teachers = $this->teachers_corrosponding_address($teachers); // get address
        $teachers = $this->teachers_corrosponding_avatar($teachers); // get image or avatar
        $teachers = $this->teachers_corrosponding_socials($teachers); // get socials
        $teachers = $this->teachers_corrosponding_tags($teachers); // get tags
        return $teachers;
	}
	// search teachers form db
	public function searchTeachers_20170824($values=''){
		$sort = "";
		$teacherTable = $this->teacherTable;
		$addressTable = $this->addressTable;
		if ( $values != '' ) {
			// echo "<pre>"; print_r($values); echo "</pre>"; die('check value');
			if (isset($values['state'])) { // Search using country and state
				if ( !empty(trim($values['country'])) && !empty(trim($values['state'])) ) {
					$where = "`country_code`='". trim($values['country']) ."' AND `region`='".trim($values['state'])."'";
				} elseif ( !empty(trim($values['country'])) ) {
					$where = "`country_code`='". trim($values['country']) ."'";
				} elseif ( !empty(trim($values['state'])) ) {
					$where = "`region`='". trim($values['state']) ."'";
				} else {
					$where = "";
				}

				if (!empty($where) ) { 
					$counter = 1;
					$sql = "SELECT DISTINCT(`contact_id`) FROM `$addressTable` WHERE ".$where.";"; 
					$IDs = $this->db->get_results($sql);
					if ( $IDs ) {
						$where = "";
						foreach ( $IDs as $ID ) {
							if ( $counter > 1) { $where .= "OR "; }
							$where .= "`contact_id` = ".$ID->contact_id." ";
							$counter++;
						}
					}
					$sql = "SELECT * FROM `$teacherTable` WHERE ".$where.";"; 
				}
				else { return false; }
		        $teachers = $this->db->get_results($sql);
		        if ($teachers) { 
		        	$teachers = $this->teachers_corrosponding_address($teachers); 
		        	$teachers = $this->teachers_corrosponding_avatar($teachers); 
		        	return $teachers;
		        }
		        else { return false; }
			} else {
				// echo "<pre>"; print_r($values); echo "</pre>"; die('zip');
				if ( !empty($values) ) { // Search using country and zip
					$counter = 1;
					$db_address = $this->get_latlong_by_zip($values['country'],$values['zip']);
					if (!empty($db_address)){
						$latitude = $db_address[0]->lat;
						$longitude = $db_address[0]->lng;
						// echo "<pre>"; print_r($db_address); echo "</pre>"; die('get zip form db');
					}
					else{
						$address = $values['country']." ".$values['zip'];
						// echo "<pre>"; print_r($address); echo "</pre>";
						$latLng = $this->getLatLng($address);
						if(!empty($latLng)){
							$latitude = $latLng['lat'];
							$longitude = $latLng['lng'];
							// echo "<pre>"; print_r($latLng); echo "</pre>"; die('Geolocation');
						}
						else{
							return false;
						}
					}
					// Get nearby location within defined area
					$geoLoc = $this->tn_get_nearby($latitude, $longitude,TNMAPRADIUS);				
					// echo "<pre>"; print_r($geoLoc); echo "</pre>"; die('nearby geoLoc');

					$sql = "SELECT DISTINCT(`contact_id`) FROM `$addressTable` WHERE 1=1 ";
        			$sql .= " AND ( `lat` <= ".$geoLoc['max_lat']." AND `lat` >= ".$geoLoc['min_lat']." ) AND (`lng` <= ".$geoLoc['max_lng']." AND `lng` >= ".$geoLoc['min_lng']." )";

					$IDs = $this->db->get_results($sql);
					if ( $IDs ) {
						$where = "";
						foreach ( $IDs as $ID ) {
							if ( $counter > 1) { $where .= "OR "; }
							$where .= "`contact_id` = ".$ID->contact_id." ";
							$counter++;
						}
					}
					$sql = "SELECT * FROM `$teacherTable` WHERE ".$where.";"; 
					
					$teachers = $this->db->get_results($sql);
			        if ($teachers) { 
			        	$teachers = $this->teachers_corrosponding_address($teachers); 
			        	$teachers = $this->teachers_corrosponding_avatar($teachers); 
			        	return $teachers;
			        }
		        	else { return false; }
				} else { return false; }
			}
		}
	}
	public function searchTeachers($values=''){
		$sort = "";
		$teacherTable = $this->teacherTable;
		$addressTable = $this->addressTable;
		$allowedTeachers = $this->allowed_teachers();
		if ( $values != '' ) {
			// echo "<pre>"; print_r($values); echo "</pre>"; die('check value');
			if (isset($values['state'])) { // Search using country and state
				if ( !empty(trim($values['country'])) && !empty(trim($values['state'])) ) {
					$where = "`country_code`='". trim($values['country']) ."' AND `region`='".trim($values['state'])."'";
				} elseif ( !empty(trim($values['country'])) ) {
					$where = "`country_code`='". trim($values['country']) ."'";
				} elseif ( !empty(trim($values['state'])) ) {
					$where = "`region`='". trim($values['state']) ."'";
				} else {
					$where = "";
				}

				if (!empty($where) ) { 
					$counter = 1;
					$where .= " AND `contact_id` IN (".$allowedTeachers." )";
					$sql = "SELECT DISTINCT(`contact_id`) FROM `$addressTable` WHERE ".$where.";"; // die($sql);
					$IDs = $this->db->get_results($sql);
					if ( $IDs ) {
						$where = "";
						foreach ( $IDs as $ID ) {
							if ( $counter > 1) { $where .= "OR "; }
							$where .= "`contact_id` = ".$ID->contact_id." ";
							$counter++;
						}
					}
					$sql = "SELECT * FROM `$teacherTable` WHERE ".$where.";"; 
				}
				else { return false; }
		        $teachers = $this->db->get_results($sql);
		        if ($teachers) { 
		        	$teachers = $this->teachers_corrosponding_address($teachers); 
		        	$teachers = $this->teachers_corrosponding_avatar($teachers); 
		        	return $teachers;
		        }
		        else { return false; }
			} else {
				if ( !empty($values) ) { // Search using country and zip
					$counter = 1;
					$db_address = $this->get_latlong_by_zip($values['country'],$values['zip']);
					if (!empty($db_address)){
						$latitude = $db_address[0]->lat;
						$longitude = $db_address[0]->lng;
					}
					else{
						$address = $values['country']." ".$values['zip'];
						// echo "<pre>"; print_r($address); echo "</pre>";
						$latLng = $this->getLatLng($address);
						if(!empty($latLng)){ $latitude = $latLng['lat']; $longitude = $latLng['lng']; }
						else{ return false; }
					}
					// Get nearby location within defined area
					$geoLoc = $this->tn_get_nearby($latitude, $longitude,TNMAPRADIUS);				
					// echo "<pre>"; print_r($geoLoc); echo "</pre>"; die('nearby geoLoc');

					$sql = "SELECT DISTINCT(`contact_id`) FROM `$addressTable` WHERE 1=1 ";
        			$sql .= " AND ( `lat` <= ".$geoLoc['max_lat']." AND `lat` >= ".$geoLoc['min_lat']." ) AND (`lng` <= ".$geoLoc['max_lng']." AND `lng` >= ".$geoLoc['min_lng']." )";
        			$sql .= " AND `contact_id` IN (".$allowedTeachers." )"; //die($sql); 
					$IDs = $this->db->get_results($sql);
					if ( $IDs ) {
						$where = "";
						foreach ( $IDs as $ID ) {
							if ( $counter > 1) { $where .= "OR "; }
							$where .= "`contact_id` = ".$ID->contact_id." ";
							$counter++;
						}
					}
					$sql = "SELECT * FROM `$teacherTable` WHERE ".$where.";"; 
					
					$teachers = $this->db->get_results($sql);
			        if ($teachers) { 
			        	$teachers = $this->teachers_corrosponding_address($teachers); 
			        	$teachers = $this->teachers_corrosponding_avatar($teachers); 
			        	return $teachers;
			        }
		        	else { return false; }
				} else { return false; }
			}
		}
	}
	// Teachers list as tabular formate
	public function get_teachers_list_table(){
		$html  		= '';
		$email  	= '';
        $rows 		= $this->teachersRows('back_end');
        $AddNewUrl 	= $this->backend_url('tn-add_new');
        // $AddNewUrl = $this->backend_url('tn-add_new');
        if ($rows) {
        	$counter 	= 1;
        	$total 		= count($rows);
        	$html 		.= $this->BUI->get_teachers_list_table_open($total);
        	foreach ($rows as $row) {
        		$profileUrl = $AddNewUrl.'&id='.$row->contact_id;
        		$html .= '<tr>';
        		$html .= '<td class="text-center">'. $counter .'</td>';
        		$html .= '<td><a href="'.$profileUrl.'">'.$row->given_name.' '. $row->family_name.'</a></td>';
        		$emails = json_decode($row->email);
        		if (!empty($emails)) {
        			foreach ($emails as $value) {
        				if ($value) { $email = $value .'<br> '; }
        			}
        			$email = rtrim($email, '<br>');
        		}
        		$html .= '<td>'. $email. '</td>';
        		$html .= '<td>'. $row->phone .'</td>';
        		$html .= '<td>'. $row->adds[0]->country_code .'</td>';
        		$html .= '<td>'. $row->adds[0]->region .'</td>';
        		$html .= '<td>'. $row->adds[0]->postal_code .'</td>';
        		// $html .= '<td>'. $row->update_values .'</td>';
        		if ($row->update_values) { $html .= '<td>'. $this->BUI->waiting_update_btn($row->contact_id) .'</td>'; }
        		else { $html .= '<td>'. $this->BUI->approved_btn($row->contact_id) .'</td>'; }
        		$html .= '<td>'. $this->BUI->get_teachers_list_table_actions($row->contact_id) .'</td>';
        		$html .= '</tr>';
        		$counter++;
        	}
        	$html .= $this->BUI->get_teachers_list_table_close();
        } else{ return false; }
        echo $html;
	}
	public function frontend_all_teachers($values=''){
		$html = [];
		$element = '';
		$location = '';
		if ( $values == '' ) { $rows = $this->teachersRows('front_end'); }
		else { $rows = $this->searchTeachers($values); }
		if ( $rows ) {
			$counter = 1;
			foreach ( $rows as $row ) {
				$element .= $this->FUI->frontend_single_teacher($row, $counter);
				$location[] = $this->frontend_single_teacher_location($row, $counter);
				$counter++;
			}
			$html['element'] = $element;
			$html['location'] = json_encode($location);
			return $html;
		} 
		else{ return false; }
	}
	public function get_address($data){
		$html = []; 
		$address = $lat = $lng = '';
		if ( !empty($data->adds[0]) ) {
			$address .= !empty(trim($data->adds[0]->line1)) && trim($data->adds[0]->line1) 	!= 'NO' ? trim($data->adds[0]->line1).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->line2)) 	&& trim($data->adds[0]->line2) 	!= 'NO' ? trim($data->adds[0]->line2).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->locality)) 	&& trim($data->adds[0]->locality) 	!= 'NO' ? trim($data->adds[0]->locality).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->region)) 	&& trim($data->adds[0]->region) 	!= 'NO' ? trim($data->adds[0]->region).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->postal_code)) 	&& trim($data->adds[0]->postal_code) 	!= 'NO' ? trim($data->adds[0]->postal_code).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->country_code)) 	&& trim($data->adds[0]->country_code) 	!= 'NO' ? trim($data->adds[0]->country_code).' ' : ' ';
			$lat = !empty(trim($data->adds[0]->lat)) 	&& trim($data->adds[0]->lat) 	!= 'NO' ? trim($data->adds[0]->lat).' ' : '';
			$lng = !empty(trim($data->adds[0]->lng)) 	&& trim($data->adds[0]->lng) 	!= 'NO' ? trim($data->adds[0]->lng).' ' : '';
		}
		$html['adds'] = $address;
		$html['lat'] = $lat;
		$html['lng'] = $lng;
		return $html;
	}
	public function frontend_single_teacher_location($data, $counter=0){
		$html = '';
		if ( !empty($data->adds[0]) ) $adds = $this->get_address($data); 
		$url = site_url('/profile-teacher/?id='.$data->contact_id);
		$btn = '<p class="btnContainer mapProfileBtn text-cernter" style="margin:0;"><a  href="#result-div" data-id='.$data->contact_id.' class="btn btn-primary btn-xs">View Listing</a></p>';
		if (!empty($data->avatar)) {
			$img = '<img src="'.TNPLUGINURL.'uploads/'.$data->avatar.'" alt="Profie Image" style="width:60px;height:60px;border-radius:50%;"><br>';
		} else{ 
			$defaultImg = TNPLUGINURL.'/assets/images/avatar.png';
			$img = '<img src="'.$defaultImg.'" alt="Profie Image" style="width:60px;height:60px;border-radius:50%;"><br>'; 
		}
		$job_title = !empty($data->job_title) && $data->job_title != 'NO' && $data->is_show_title == "YES" ? '<br>'. $data->job_title : '';

		$html[] = "<center>$img<strong> $data->given_name $data->family_name </strong> $job_title <br> ".$adds['adds']."<br><br> ".$btn."</center>";
		$html[] = $adds['lat'];
		$html[] = $adds['lng'];
		$html[] = (int) $counter;
		return $html;
	}
	// get country list from db
    public function getAllCountries(){
    	$data = [];
        $tableName = $this->addressTable;
        $allowedTeachers = $this->allowed_teachers();
        $query = "SELECT DISTINCT(`country_code`) FROM `$tableName` WHERE `contact_id` IN ( ".$allowedTeachers." ) ORDER BY `country_code` ASC"; //die($query);
        $rows = $this->db->get_results($query, OBJECT);
        if ( $rows ) {
        	foreach ($rows as $row) {
        		$data[] = $row->country_code;
        	}
        } else { return false; }
        return $data;
    }
    // get states list from db
    public function getStates($field='USA'){
    	$tableName = $this->addressTable;
    	$allowedTeachers = $this->allowed_teachers();
        $query = "SELECT DISTINCT(`region`) FROM `$tableName` WHERE `country_code`= '".$field."' AND `contact_id` IN ( ".$allowedTeachers." ) ORDER BY `region` ASC";
        return $this->db->get_results($query, OBJECT);
    }
    
    public function getLatLng($adds){
        $url = "https://maps.googleapis.com/maps/api/geocode/json?key=".TNMAPAPIKEY."&address=".urlencode($adds)."&sensor=false";
        $result_string = file_get_contents($url);
        $result = json_decode($result_string, true);
        return $result['results'][0]['geometry']['location'];
    }
    public function getLatLngMsg($adds){
        $url = "https://maps.googleapis.com/maps/api/geocode/json?key=".TNMAPAPIKEY."&address=".urlencode($adds)."&sensor=false";
        $result_string = file_get_contents($url);
        $result = json_decode($result_string, true);
        if (empty($result['results'])) {
        	return $result['error_message'];
        } else return '';
    }
    public function backend_url($pageName = 'tn-teachers'){
		$isSecure = false;
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		    $isSecure = true;
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
		    $isSecure = true;
		}
		$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
		return admin_url( 'admin.php?page='.$pageName, $REQUEST_PROTOCOL );
	}
	private function check_valid_data($data){
		if ( @isset($data) && @!empty(trim($data)) ) { return trim($data); }
		else { return false; }
	}

	public function add_teacher_into_db($array, $cID, $updatedBy='admin'){
        // $array = $this->get_structured_info($jsonData);
        $dataArray = [];
        $dataArray["contact_id"]  	= $cID;
        $dataArray["username"]    	= isset($array['username']) && !empty(trim($array['username'])) ? trim($array['username']) : '';
        $dataArray["password"]    	= isset($array['password']) && !empty(trim($array['password'])) ? trim($array['password']) : '';
        $dataArray["given_name"]  	= isset($array['given_name']) && !empty(trim($array['given_name'])) ? trim($array['given_name']) : '';
        $dataArray["family_name"] 	= isset($array['family_name']) && !empty(trim($array['family_name'])) ? trim($array['family_name']) : '';
        $dataArray["job_title"] 	= isset($array['job_title']) && !empty(trim($array['job_title'])) ? trim($array['job_title']) : '';
        $dataArray["email"]       	= !empty($array['email']) ? json_encode($array['email']) : '';
        $dataArray["phone"] 		= isset($array['phone']) && !empty(trim($array['phone'])) ? trim($array['phone']) : '';
        $dataArray["website"] 		= isset($array['website']) && !empty(trim($array['website'])) ? trim($array['website']) : '';
        $dataArray["_YogaStudio"] 	= isset($array['_YogaStudio']) && !empty(trim($array['_YogaStudio'])) ? trim($array['_YogaStudio']) : '';
        $dataArray["social"]       	= !empty($array['social']) ? json_encode($array['social']) : '';
        $dataArray["tags"]       	= !empty($array['tags']) ? json_encode($array['tags']) : '';
        $dataArray["updated_by"]  	= $updatedBy;
        $dataArray["updated_at"]  	= current_time('mysql');

        $inserted = $this->db->insert($this->teacherTable, $dataArray);
        if ($inserted){ return true; }
        else{ return false; }
    }
    
    public function get_update_field($ID) {
    	$tableName = $this->teacherTable;
        $query = "SELECT `update_values` FROM `$tableName` WHERE `contact_id`=".$ID." LIMIT 1";
        $rows = $this->db->get_results($query, OBJECT);
        if ( $rows ) { 
        	$data = (string) $rows[0]->update_values; 
        	return json_decode($data);
        }
        return false;
    }

    public function is_update_pending($ID) {
    	$tableName = $this->teacherTable;
        $query = "SELECT `update_values` FROM `$tableName` WHERE `contact_id`=".$ID." LIMIT 1";
        $rows = $this->db->get_results($query, OBJECT);
        if ( $rows ) { 
        	return true;
        }
        return false;
    }
    
    public function update_field($rows,$ID){
    	$data = [];      
        if ($rows) {
        	// echo "<pre>"; print_r($rows); echo "</pre>";
        	foreach ($rows as $key => $value ) {
        		if (is_array($value)) {
        			$this->insertArray[$key] = json_encode($value);
        		} else{
        			if (empty($value)) { $value = ''; }
        			@$this->insertArray[$key] = $value;
        		}
        	}
        	if ( 
        		$this->update($this->insertArray, $ID) && 
        		$this->truncate_update_field($ID)
        		){ return true; }
        }
        return false;
    }
    public function direct_update($rows,$ID){
    	$data = [];
        if ($rows) {
        	foreach ($rows as $key => $value ) {
        		if (!empty($value) && is_array($value)) {
        			$this->insertArray[$key] = json_encode($value);
        		}else{
        			if (empty($value)) { $value = ''; }
        			@$this->insertArray[$key] = trim($value);
        		}
        	}
        	if ( $this->update($this->insertArray, $ID) ){ return true; }
        }
        return false;
    }
    public function truncate_update_field($ID){
    	$this->insertArray['update_values'] = NULL;
    	$this->insertArray['update_status'] = 'APPROVED';
    	return $this->update($this->insertArray, $ID);
    }
    public function tn_get_nearby( $lat, $lng, $distance = 50, $unit = 'mi' ) {         
        // radius of earth; @note: the earth is not perfectly spherical, but this is considered the 'mean radius'
        if( $unit == 'km' ) { $radius = 6371.009; }
        elseif ( $unit == 'mi' ) { $radius = 3958.761; }

		$data = $this->get_max_min($lat, $lng, $distance,$radius);
        // latitude boundaries
        $maxLat = $data['maxLat'];
        $minLat = $data['minLat'];

        // longitude boundaries (longitude gets smaller when latitude increases)
        $maxLng = $data['maxLng'];
        $minLng = $data['minLng'];

        $max_min_values = array(
            'max_lat' => $maxLat,
            'min_lat' => $minLat,
            'max_lng' => $maxLng,
            'min_lng' => $minLng
        );
        return $max_min_values;
    }
    
    public function get_max_min($lat, $lng, $distance,$radius){
        // latitude boundaries
        $maxLat = ( float ) $lat + rad2deg( $distance / $radius );
        $minLat = ( float ) $lat - rad2deg( $distance / $radius );

        // longitude boundaries (longitude gets smaller when latitude increases)
        $maxLng = ( float ) $lng + rad2deg( $distance / $radius / cos( deg2rad( ( float ) $lat ) ) );
        $minLng = ( float ) $lng - rad2deg( $distance / $radius / cos( deg2rad( ( float ) $lat ) ) );
        
		$data['maxLat'] = $maxLat;
		$data['minLat'] = $minLat;
		$data['maxLng'] = $maxLng;
		$data['minLng'] = $minLng;
		// echo "<pre>"; print_r($data); echo "</pre>"; die('maxLat&minLat');
		return $data;
    }

    public function get_max_min_old($lat, $lng, $distance,$radius){
        $country_boundary = $this->get_country_boundary($lat, $lng);
        // latitude boundaries
        $maxLat = ( float ) $lat + rad2deg( $distance / $radius );
        $minLat = ( float ) $lat - rad2deg( $distance / $radius );

        // longitude boundaries (longitude gets smaller when latitude increases)
        $maxLng = ( float ) $lng + rad2deg( $distance / $radius / cos( deg2rad( ( float ) $lat ) ) );
        $minLng = ( float ) $lng - rad2deg( $distance / $radius / cos( deg2rad( ( float ) $lat ) ) );
        
        if($maxLng>$country_boundary['lat'] || $maxLng>$country_boundary['lon']){
			echo "<pre>"; print_r($country_boundary); echo "</pre>"; die('Country is smaller');
        	$this->get_max_min($lat, $lng, $distance,$radius-1);
        }
        else{
			$data['maxLat'] = $maxLat;
			$data['minLat'] = $minLat;
			$data['maxLng'] = $maxLng;
			$data['minLng'] = $minLng;
			echo "<pre>"; print_r($boundary); echo "</pre>"; die('maxLat&minLat');
			return $data;
        }
    }
    
    public function get_country_boundary($lat, $long){
		$geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key='.TNMAPAPIKEY.'&latlng='.$lat.','.$long.'&sensor=false');
        $output= json_decode($geocode);
        // echo "<pre>"; print_r($output); echo "</pre>"; die('output');
        for($j=0;$j<count($output->results[0]->address_components);$j++){
            $cn=array($output->results[0]->address_components[$j]->types[0]);
           if(in_array("country", $cn))
           {
            $country= $output->results[0]->address_components[$j]->long_name;
           }
		}
	
		// Get JSON results from this request
		$geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key='.TNMAPAPIKEY.'&address='.urlencode($country).'&sensor=false');
        // echo "<pre>"; print_r($geo); echo "</pre>"; die('geo');
		
		// Convert the JSON to an array
		$geo = json_decode($geo, true);
		
		if ($geo['status'] == 'OK') {
		  // Get Lat & Long
		  $latitude = $geo['results'][0]['geometry']['location']['lat'];
		  $longitude = $geo['results'][0]['geometry']['location']['lng'];
		}
		$boundary['lat'] = $latitude;
		$boundary['lon'] = $longitude;
        // echo "<pre>"; print_r($boundary); echo "</pre>"; die('geo');
		return $boundary;
    }
    public function allowed_teachers($mandatoryTag=TNMANDATORYTAGS, $optionalTags=TNOPTIONALTAGS){
    	$tag = new tn_tag;
    	$IDS = $tag->allowed_teachers($mandatoryTag, $optionalTags);
    	$IDS = !empty($IDS) ? $IDS : '99999999'; // if there is no allowed contact ID then return a unmatched ID
    	return $IDS;
    }
}