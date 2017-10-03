<?php 
/**
* Common Database class
*/
class tn_db{
    
    protected   $imageTable;
    protected   $settingTable;
    protected   $tokenTable;
    protected   $teacherTable;
    protected   $addressTable;
    protected   $campaignTable;

    protected   $socialTypeTable;
    protected   $contactSocialTable;

    protected   $tagTable;
    protected   $tagCatTable;
    protected   $contactTagTable;

    protected   $db;
    protected   $charset;
    private     $insertArray=[];
    private     $tags;
    private     $retval;
    private     $preparedArray;

    function __construct(){
        global $wpdb;
        $this->db           = $wpdb;
        $this->charset      = $this->db->get_charset_collate();
        $this->imageTable   = $this->db->prefix.TNIMAGETABLE;
        $this->settingTable = $this->db->prefix.TNSETTINGTABLE;
        $this->tokenTable   = $this->db->prefix.TNTOKENTABLE;
        $this->teacherTable = $this->db->prefix.TNTEACHERSTABLE;
        $this->addressTable = $this->db->prefix.TNADDRESSTABLE;
        $this->campaignTable = $this->db->prefix.TNCAMPAIGNTABLE;

        $this->socialTypeTable = $this->db->prefix.TNSOCIALTYPETABLE;
        $this->contactSocialTable = $this->db->prefix.TNCONTACTSOCIALTABLE;

        $this->tagTable = $this->db->prefix.TNTAGSTABLE;
        $this->tagCatTable = $this->db->prefix.TNTAGCCATSTABLE;
        $this->contactTagTable = $this->db->prefix.TNCONTACTTAGSTABLE;
    }

    public function insert_teacher($dataArray){
        // $this->insertArray = $dataArray;
        $this->insertArray = array(
                    'given_name'     => 'name',
                    'middle_name'   => 'min-nae',
                    'family_name'   => 'family name'
                    );

        $inserted = $this->db->insert($this->teacherTable, $this->insertArray);
        if ($inserted){ return true; }
        else{ return false; }
    }

    public function insert_data($dataArray){
        $this->insertArray = array(
            "client_id"    => $dataArray[0],
            "client_secret"=> $dataArray[1]
        );
        $inserted = $this->db->insert($this->settingTable, $this->insertArray);
        if ($inserted){ return true; }
        else{ return false; }
    }

    public function add_teacher_into_db_backup($jsonData) {
        // $this-> = $this->get_structured_info($jsonData);

        // $this->preparedArray["contact_id"]  = isset($this->['id']) && !empty(trim($this->['id'])) ? trim($this->['id']) : '';
        // $this->preparedArray["given_name"]  = isset($this->['given_name']) && !empty(trim($this->['given_name'])) ? trim($this->['given_name']) : '';
        // $this->preparedArray["middle_name"] = isset($this->['middle_name']) && !empty(trim($this->['middle_name'])) ? trim($this->['middle_name']) : '';
        // $this->preparedArray["family_name"] = isset($this->['family_name']) && !empty(trim($this->['family_name'])) ? trim($this->['family_name']) : '';
        // $this->preparedArray["phone"]       = isset($this->['phone'][0]) && !empty(trim($this->['phone'][0])) ? trim($this->['phone'][0]) : '';
        // $this->preparedArray["email"]       = isset($this->['email'][0]) && !empty(trim($this->['email'][0])) ? trim($this->['email'][0]) : '';
        // $this->preparedArray["website"]     = isset($this->['website']) && !empty(trim($this->['website'])) ? trim($this->['website']) : '';
        // $this->preparedArray["tags"]        = !empty($this->['tags']) ? serialize($this->['tags']) : '';
        // $this->preparedArray["social"]      = !empty($this->['social']) ? serialize($this->['social']) : '';

        $address = '';
        $address .= !empty($this->preparedArray["add_street"]) ? $this->preparedArray["add_street"] : '';
        $address .= !empty($this->preparedArray["add_city"]) ? $this->preparedArray["add_city"] : '';
        $address .= !empty($this->preparedArray["add_state"]) ? $this->preparedArray["add_state"] : '';
        $address .= !empty($this->preparedArray["add_zip"]) ? $this->preparedArray["add_zip"] : '';
        $address .= !empty($this->preparedArray["add_country"]) ? $this->preparedArray["add_country"] : '';

        if ( !empty(trim($address)) ) {
            $LatLng = $this->getLatLng($address);
            $lat = $LatLng['lat'];
            $lng = $LatLng['lng'];
            $this->preparedArray["lat"]         = $lat;
            $this->preparedArray["lng"]         = $lng;
        } else {
            $this->preparedArray["lat"]         = '';
            $this->preparedArray["lng"]         = '';
        }
        $this->preparedArray["updated_by"]  = 'admin';
        $this->preparedArray["updated_at"]  = current_time('mysql');

        if ( $this->db->insert($this->teacherTable, $this->preparedArray) ){ return true; }
        else{ return false; }
    }

    public function insert_token($dataArray){
        $this->insertArray = array(
            "access_token"      => $dataArray[0],
            "refresh_token"     => $dataArray[1],
            "end_of_life"       => $dataArray[2],
            "scope"             => $dataArray[3]
        );
        $inserted = $this->db->insert($this->tokenTable, $this->insertArray);
        if ($inserted){ return true; }
        else{ return false; }
    }
    public function truncate_table(){
        $tableName = $this->teacherTable;
        $delete = $this->db->query("TRUNCATE TABLE $tableName");
        if ($delete){ return true; }
        else{ return false; }
    }

    public function select_data(){
        $tableName = $this->settingTable;
        $sql = "SELECT * FROM `$tableName` ORDER BY id DESC LIMIT 1;";
        return $this->db->get_results($sql);
    }

    public function select_token(){
        $data = '';
        $tableName = $this->tokenTable;
        $sql = "SELECT * FROM `$tableName` ORDER BY id DESC LIMIT 1;";
        $results = $this->db->get_results($sql);
        if ($results) {
            foreach ($results as $result) {
                $data['access_t'] = $result->access_token;
                $data['refresh_t'] = $result->refresh_token;
                $data['id'] = $result->id;
                $data['scope'] = $result->scope;
                $data['end_of_life'] = $result->end_of_life;
            }
        }
        if ( $data ) return $data;
        else return false;
    }

    public function update_token($id,$access,$refresh,$endoflife){
        $data = array(
            'access_token' => $access,
            'refresh_token' => $refresh,
            'end_of_life'     =>$endoflife
        );
        return $this->db->update( $this->tokenTable, $data, array( 'id' => $id ) );
    }

    public function getLatLng($addr){
        $url = "https://maps.googleapis.com/maps/api/geocode/json?key=".TNMAPAPIKEY."&address=".urlencode($addr)."&sensor=false";
        $result_string = file_get_contents($url);
        $result = json_decode($result_string, true);
        return $result['results'][0]['geometry']['location'];
    }

    
    public function formated_address($values, $separator=' ', $type='') {
        $data = '';
        if ( $type == 'profile' ) {
            $data .= $values->line1.' '.$values->line2.$separator;
            $data .= $values->locality.' '.$values->region.$separator;
            $data .= $values->country_code;
            if ( !empty(trim($values->postal_code)) ) {
                $data .= ' - '.$values->postal_code;
            }
        } else {
            foreach ($values as $key => $value ) {
                if ( !empty(trim($value)) && $key != 'field' ) {
                    $data .= $value. $separator;
                }
            }
        }
        return $data;
    }
    
    public function get_structured_info($json = ''){
        $data = [];
        if (!empty($json)) {
            $json = json_decode($json);
            if ($json) {
                // Phone
                if ( !empty( $json->id ) ) {
                    $data['id'] = $json->id;
                }
                if ( !empty( $json->phone_numbers ) ) {
                    foreach ( $json->phone_numbers as $phone ) {
                        $data['phone'][] = $phone->number;
                    }
                }
                // Email
                if ( !empty( $json->email_addresses ) ) {
                    foreach ( $json->email_addresses as $email ) {
                        $data['email'][] = $email->email;
                    }
                }

                $data['tags'] = $json->tag_ids;
                $data['adds'] = $json->addresses;
                $data['given_name'] = $json->given_name;
                $data['middle_name'] = $json->middle_name;
                $data['family_name'] = $json->family_name;
                return $data;
            }
        } else { return false; }
    }
    
    public function get_structured_tags($json = '', $type=''){
        $this->tags = [];
        $html = '';
        $data = [];
        $all_cats = [];
        if (!empty($json)) {
            $json = json_decode($json);
            if ($json) {
                foreach ($json->tags as $values) {
                    $cat        = $values->tag->category;
                    $tagID      = $values->tag->id;
                    $tagName    = $values->tag->name;
                    $this->tags[$cat][$tagID] = $tagName;
                }
            }
        }

        if (!empty( $this->tags ) ) { return $this->tags; }
        else { return false; }
    }
    public function update_contact($post, $contactID, $userType='admin'){
        $teacher    = new tn_teachers;
        $obj_wel    = new tn_campaign;
        $image      = new tn_image;
        $obj_inf    = new tn_infusion;
        $api        = new tn_api;
        $tag        = new tn_tag;
        // echo "<pre>"; print_r($_FILES); echo "</pre>";
        // echo "<pre>"; print_r($post); echo "</pre>"; die();
        // Edit Contact
        if ( $obj_wel->get_value('admin_approval_status') == 'true' && $userType != 'admin' ){
            // Waiting for admin approval
            if ( $api->chage_update_status($contactID, $post) ) { $this->show_message('Your update is waiting for admin approval.'); }
            else { $this->show_message('Nothing to update.', 'warning'); }
        }else{
            // Direct Update
            $data = $api->changed_data($contactID, $post);
            // echo "Data : <pre>"; print_r($data); echo "</pre>";
            
            if (isset($data['social'])) {
                // echo "<br><br><br> string string string string string string ";
                $socout = $obj_inf->updateSocial($data['social_id'], $data['social'], $data['social_crm_id'], $contactID);
                //if (count($socout)==0) { $errors['social-crm'] = 'Social CRM is not updated'; }
                $social = new tn_social;
                if ($social->is_social_exists($contactID)) {
                    // echo "First : <pre>"; print_r($data['social']); echo "</pre>";
                    // Update socials on db
                    if ( !$social->update_field($data['social'], $contactID) ){ $errors['social-db'] = 'Social DB update error'; }
                } else {
                    // Insert socials into db
                    $counter = 0;
                    // echo "else : <pre>"; print_r($data['social']); echo "</pre>";
                    foreach ($data['social'] as $key => $value) {
                        if (empty($value)) { $value = ''; }
                        $social_db_insert_array = array(
                            'contact_id'    => $contactID, 
                            'social_id'     => $key, 
                            'social_crm_id' => null, 
                            'account_name'  => $value
                            );
                        // echo "<pre>"; print_r($social_db_insert_array); echo "</pre>";
                        if ( !$social->save($social_db_insert_array) ) { $errors['social_db_insert'][] = 'Insertion error for ID : '.$ID; }
                        $counter++;
                    }
                }
                unset($data['social']);
                unset($data['social_id']);
                unset($data['social_crm_id']);
            } 

            // Insert or update image
            if ( isset($data['image']) ) {
                if ($image->is_exists($contactID)) { $imgUpdate = $image->update(array('url' => $data['image']), $contactID); }
                else { $imgUpdate = $image->store($data['image'], $contactID); }
                if ( !$imgUpdate ) { $errors['image'] = "Image could not updated.";}
                unset($data['image']);
            }

            // Upload custom image 
            if ( $_FILES['fileToUpload']['name'] != '' ) {
                $imageName = $image->upload_image($post,$contactID);
                if ($this->is_exists($contactID)) { $imgUpdate = $this->update(array('url' => $imageName), $contactID); }
                else{ $imgUpdate = $this->store($imageName, $contactID); }
                if ( !$imgUpdate ) { $errors['image'] = "Image could not updated."; }
            }

            // Remove bio form main data array
            if (isset($data) && !empty($data)) { 
                if (!$obj_inf->updateContact($contactID,$data)) { $errors['contact-CRM'] = "Infusion update error."; }
                if ( $teacher->update_field($data, $contactID) ) { $errors['contact-DB'] = "Teachers update error."; }
            }

            // Address fields
            if ( isset($data['address']) ) {
                if ( !$obj_inf->updateAddress($contactID,$data['address']) ) {
                    $errors['addr_update'] = "Infution update error"; 
                } else {
                    $addr = new tn_address;
                    if ( $addr->is_exists($contactID) ) {
                        // Address is already exists
                        if (!$addr->update($data['address'], $contactID)) { $errors['address'] = "Error! updating address"; }
                    } else {
                        // No address found for current user 
                        if (!$addr->insert($data['address'])) { $errors['address'] = "Error! inserting address"; }
                    }
                }

                // Address inserted successfully
                if (empty($errors)) { unset($data['address']); }
            }

            // Print Message
            if ( count($errors) < 1 ){ 
                $obj_inf->updateTagsbyCId($contactID); // Add the mandatory tag on CRM
                // Add the mandatory tag for first update
                if ( !$tag->add_the_mandatory_tag($contactID) ) { exit("The mandatory tag could not be added!!"); }
                $this->show_message('successfully Updated.');
                // sleep(5); header('Location: '.site_url('/login-teacher/'));
            }
            else { 
                $this->show_message('Sorry! There are some errors.', 'error');
                echo "<pre>"; print_r($errors); echo "</pre>";
            }
        }

        $tags = array(118);
        $updatetag = $obj_inf->updateTagsbyCId($contactID, $tags);
        if( ctype_digit($updatetag) ){ echo "Tag Applied Successfully."; }
    }
    public function get_latlong_by_zip($country,$zip){
    	$tableName = $this->addressTable;
        $sql = "SELECT `lat`,`lng` FROM `$tableName` WHERE `postal_code`='".$zip."' AND `country_code` = '".$country."' LIMIT 1;";
        $rows = $this->db->get_results($sql);
        return $rows;
    }

    /**
    * Insert CRM Data into local DB upon add new contact on CRM
    * 
    * @param ContactID
    * 
    * @return true for success
    * @return false for error
    **/
    public function insert_CRM_data_into_DB ($conId) {
        $infusion       = new tn_infusion;
        $conTag         = new tn_tag;
        $address        = new tn_address;

        $is_tag_match = false;
        $ret_val = [];
        $ret_val = $infusion->is_valid_contact( $conId );
        $tagIdsc = explode(',', $ret_val['Groups']);
        $targetc = unserialize (TNTAGS);
        foreach ($tagIdsc as $value) {
            if( in_array($value, $targetc ) ) { $is_tag_match = true; break; }
        }
        // echo $is_tag_match;
        if( $is_tag_match == true ){
            $email_adds = [];
            if(!empty($ret_val['Email'])){ $email_adds[] = $ret_val['Email']; }
            if(!empty($ret_val['EmailAddress2'])){ $email_adds[] = $ret_val['EmailAddress2']; }
            $email = json_encode($email_adds);
            $teacherData = array(
                'contact_id'    => $ret_val['Id'],
                'username'      => !empty($ret_val['Username'])?$ret_val['Username']:'',
                'password'      => !empty($ret_val['Password'])?$ret_val['Password']:'',
                'given_name'    => !empty($ret_val['FirstName'])?$ret_val['FirstName']:'',
                'middle_name'   => '',
                'family_name'   => !empty($ret_val['LastName'])?$ret_val['LastName']:'',
                'phone'         => !empty($ret_val['Phone1'])?$ret_val['Phone1']:'',
                'website'       => !empty($ret_val['Website'])?$ret_val['Website']:'',
                'job_title'     => !empty($ret_val['JobTitle'])?$ret_val['JobTitle']:'',
                '_YogaStudio'   => !empty($ret_val['_YogaStudio'])?$ret_val['_YogaStudio']:'',
                'email'         => $email,
                'updated_by'    => 'admin',
                'updated_at'    => current_time('timestamp')
            );

            if( $this->insert($teacherData) ){
                $tagIds = explode(',', $ret_val['Groups']);
                // $target = unserialize (TNTAGS);
                // $resultTag = array_intersect($tagIds, $target);
                foreach ($tagIds as $key => $tagid) {
                    if ($tagid == TNMANDATORYTAGS) { continue; }
                    $conTag->insert_tag_contact($ret_val['Id'],$tagid);
                }
                $addr = '';
                $addrArray = [];
                $addrArray['contact_id'] = (int) $conId;
                if (!empty( trim($ret_val['StreetAddress1']) )) {
                    $addrArray['line1'] = $ret_val['StreetAddress1'];
                }
                if (!empty( trim($ret_val['StreetAddress2']) )) {
                    $addr .= trim($ret_val['StreetAddress2']).' ';
                    $addrArray['line2'] = $ret_val['StreetAddress2'];
                }
                if (!empty( trim($ret_val['City']) )) {
                    $addr .= trim($ret_val['City']).' ';
                    $addrArray['locality'] = $ret_val['City'];
                }
                if (!empty( trim($ret_val['State']) )) {
                    $addr .= trim($ret_val['State']).' ';
                    $addrArray['region'] = $ret_val['State'];
                }
                if (!empty( trim($ret_val['PostalCode']) )) {
                    $addr .= trim($ret_val['PostalCode']).' ';
                    $addrArray['postal_code'] = $ret_val['PostalCode'];
                }
                if (!empty( trim($ret_val['Country']) )) {
                    $addr .= trim($ret_val['Country']).' ';
                    $addrArray['country_code'] = $ret_val['Country'];
                }
                // get geolocation
                if (!empty(trim($addr))) {
                    $latLng = $this->getLatLng($addr);
                    $country = isset($addrArray['country_code']) && !empty(trim($addrArray['country_code'])) ? trim($addrArray['country_code']) : false;
                    if(!empty($latLng)){
                       $addrArray['lat'] = $latLng['lat'];
                       $addrArray['lng'] = $latLng['lng'];
                    } elseif ($country) {
                        $latLng = $this->getLatLng($country);
                        $addrArray['lat'] = $latLng['lat'];
                        $addrArray['lng'] = $latLng['lng'];
                    }else{
                        $addrArray['lat'] = '';
                        $addrArray['lng'] = '';
                    }
                }
                // echo "<pre>"; print_r( $addrArray ); echo "</pre>";
                $address_id = $address->insert( $addrArray );
                $socials = $infusion->contactSocial( $conId );
            }else{ return false; }
        } else{  return false; }
        return true;
    }

    /**
    * Update CRM Data into local DB upon add new tag on CRM
    * 
    * @param ContactID
    * 
    * @return true for success
    * @return false for error
    **/
    public function update_CRM_data_into_DB ($conId) {
        $infusion       = new tn_infusion;
        $conTag         = new tn_tag;
        $teachers       = new tn_teachers;
        $contactDBTags  = [];
        $errors         = 0;

        $ret_val = $infusion->is_valid_contact( $conId );
        $DBContact = $conTag->user_contact_tags($conId);
        foreach ($DBContact as $value) {
            $contactDBTags[] = $value->tag_id;
        }
        $contactCRMTags = explode(',', $ret_val['Groups']);
        $newTags = array_diff($contactCRMTags, $contactDBTags);
        // echo "contactCRMTags : <pre>"; print_r($contactCRMTags); echo "</pre>";
        // echo "contactDBTags : <pre>"; print_r($contactDBTags); echo "</pre>";
        // echo "newTags : <pre>"; print_r($newTags); echo "</pre>"; exit();

        // Insert tags into local DB
        if ( !empty($newTags) ) {
            foreach ($newTags as $value) {
                if ($value == TNMANDATORYTAGS) { continue; }
                if (!$conTag->insert_tag_contact($conId,$value)) { $errors++; }
            }
        } else{ echo "Nothing to Update"; return ture; }

        if ($errors < 1 ) { echo " Updated successfully."; return true; }
        else { echo " Error! Please try again"; return false; }
    }
    
    /**
    * Insert CRM Data into local DB after successfull connection on settings page
    * 
    * @param ContactID
    * 
    * @return true for success
    * @return false for error
    **/
    public function insert_CRM_data_into_DB_from_settings_page($code='', $mustTagRequired=false){
        $sync       = new RestApi;
        $conSocial  = new tn_social;
        $conTag     = new tn_tag;
        $infusion   = new tn_infusion;
        $address    = new tn_address;
        $teacher    = new tn_teachers;

        $i_token    = $this->select_token();
        if ($code != '') { $retVal = $infusion->firstTokenInsert( $code ); } // insert token
        else { $retVal = 'true'; } // Already have token
        if($retVal == 'true'){
            echo '<script type="text/javascript">jQuery.LoadingOverlay("show"); </script>';
            // echo "Connection successful. Transferring data.";
            $contacts = $infusion->getContactsbyTags(false); 
            $insert_contact_error=0;
            $insert_contact_success=0;
            $target = unserialize (TNTAGS);

            // echo "<pre>"; print_r($contacts); echo "</pre>";
            // echo "total : ".count($contacts); die();
            
            $this->truncate_table();
            $address->truncate();
            $conSocial->truncate();
            $conTag->truncate();

            $allAddress = array();
            if(is_array($contacts)){
                foreach ($contacts as $key => $value) {
                    // Determine if the contact is valid or not
                    // echo "$key == "; var_dump($infusion->is_valid_contact($value, true, $mustTagRequired)); echo "<br>"; continue;
                    if ( !$infusion->is_valid_contact($value, true, $mustTagRequired) ) { continue; }
                    // Test
                    // $tagIds = explode(',', $value['Groups']);
                    // $resulttag=array_intersect($tagIds, $target);
                    // echo "ID : ".$value['Id']; echo "<br>";
                    // echo "target : ";print_r($target); echo "<br>";
                    // echo "tagIds : ";print_r($tagIds); echo "<br>";
                    // echo "resulttag : ";print_r($resulttag); echo "<br>";
                    // echo "<hr>";
                    // continue;
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
                    $addrArray['lat'] = '';
                    $addrArray['lng'] = '';
                        
                    
                    $addrArray['contact_id'] = (int) $value['Id'];      

                    $teacherData = array(
                        'contact_id'    => $value['Id'],
                        'username'      => !empty($value['Username'])?$value['Username']:'',
                        'password'      => !empty($value['Password'])?$value['Password']:'',
                        'given_name'    => !empty($value['FirstName'])?$value['FirstName']:'',
                        'middle_name'   => '',
                        'family_name'   => !empty($value['LastName'])?$value['LastName']:'',
                        'phone'         => !empty($value['Phone1'])?$value['Phone1']:'',
                        'website'       => !empty($value['Website'])?$value['Website']:'',
                        'job_title'     => !empty($value['JobTitle'])?$value['JobTitle']:'',
                        '_YogaStudio'   => !empty($value['_YogaStudio'])?$value['_YogaStudio']:'',
                        'email'         => $email,
                        'updated_by'    => 'admin',
                        'updated_at'    => current_time('timestamp')
                    );  
                    if($teacher->insert($teacherData)){ 
                        $tagIds = explode(',', $value['Groups']);
                        //$resulttag = array_intersect($tagIds, $target);
                        foreach ($tagIds as $key => $tagid) {
                            if ($tagid == TNMANDATORYTAGS) { continue; }
                            $conTag->insert_tag_contact($value['Id'],$tagid);
                        }
                        $insert_contact_success++; 
                    }else{ 
                        $insert_contact_error++; 
                    }
                
                    array_push($allAddress, $addrArray);
                }

                if($insert_contact_error>0){
                    echo "Teachers Insertion not succrssful.";
                }else{
                    echo "Teachers Insertion succrssful.";
                }
            }else{
                echo "No contact found. Please connect again.";
            }
            
            $socials = $infusion->getallSocials();
            // echo "<pre>"; print_r($socials); echo "</pre>";
            foreach ($socials as $key => $social) {
                $resocial[$social['ContactId']][$social['AccountType']] = $social;
            }
            // echo "<pre>"; print_r($resocial); echo "</pre>";
            
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
                            // echo " Social inserted.";
                        }else{
                            $insert_contact_error++;
                            echo "Not inserted.";
                        }
                    }
                }
            }
            
            foreach($allAddress as $a_address){
                $address_id = $address->insert($a_address);
            }
            /*foreach($allAddress as $a_address){
                $addr = '';
                
                if(isset($a_address['postal_code']) && !isset($a_address['line1']) && !isset($a_address['line2']) && !isset($a_address['locality']) && !isset($a_address['region']) && !isset($a_address['country_code'])){
                    // Only zip code found. search country from address table
                    //$country = $address->get_country_by_zip($a_address['postal_code']);
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
                }
                
                //Get latitude & longitude and then update.
                if (!empty(trim($addr))) {
                    $latLng = $this->getLatLng($addr);
                    $country = isset($a_address['country_code']) && !empty(trim($a_address['country_code'])) ? trim($a_address['country_code']) : false;
                    if(!empty($latLng)){
                       $a_address['lat'] = $latLng['lat'];
                       $a_address['lng'] = $latLng['lng'];
                    } elseif ($country) { // If full address fail then get country location
                        $latLng = $this->getLatLng($country);
                        $a_address['lat'] = $latLng['lat'];
                        $a_address['lng'] = $latLng['lng'];
                    }else{
                        $a_address['lat'] = '';
                        $a_address['lng'] = '';
                    }
                }
                $address_id = $address->insert($a_address);             
            }*/

            echo '<script type="text/javascript"> jQuery.LoadingOverlay("hide"); </script>';
        }else{
            echo "Connection Not Successful. Please authorize again.";
        }

        // Return the insertion success status
        if ( $insert_contact_error > 0 ) { return false; }
        else { return true; }
    }
    public function show_message($message, $type='success'){
        $html = '<br>';
        $html .= '<div class="container">';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-8 col-sm-offset-2">';
        $html .= '<div class="alert alert-'.$type.'">';
        $html .= '<strong class="text-capitalized">'.$type.'!</strong> '.$message;
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        echo $html;
    }
}
