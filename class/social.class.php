<?php 
/**
* Image uploader class
*/
class tn_social extends tn_db {
	//$this->contactSocialTable
	//$this->socialTypeTable
	function __construct() {
		parent::__construct();
	}

    public function user_socials($ID) {
        $sql = "";
        $sql .= "SELECT * FROM `".$this->contactSocialTable."` AS cs ";
        $sql .= "JOIN ".$this->socialTypeTable." AS st ON cs.social_id = st.social_id ";
        $sql .= "WHERE `contact_id`=".$ID." ORDER BY cs.`social_id` ASC;";
        $rows = $this->db->get_results($sql);
        return $rows;
    }

    public function user_socials_html($ID) {
        $value = '';
        $socials = $this->user_socials($ID);
        if ($socials) {
            $value .= '<ul class="social-list pull-right">';
            foreach ($socials as $social) {
                $socialUrl = $social->account_name;
                if (!empty(trim($socialUrl))) {
                    $url = parse_url($socialUrl);
                    if ( !isset($url['scheme']) && empty($url['scheme']) ) { $socialUrl = 'http://'.$socialUrl; }
                    $value .= '<li><a target="_blank" href="'.$socialUrl.'">';
                    $value .= '<img src="'. TNPLUGINURL .'assets/images/social/'.strtolower($social->social_name).'.png">';
                    $value .= '</a></li>';
                }
            }
            $value .= '</ul>';
        }
        return $value;
    }
    public function social_types() {
        $sql = "";
        $sql .= "SELECT * FROM `".$this->socialTypeTable."` ";
        $sql .= "ORDER BY `social_id` ASC;";
        $rows = $this->db->get_results($sql);
        return $rows;
    }
    public function insert_contact_social($contactId,$socId,$acc_name,$crm_social_id){
        $date = date('Y-m-d H:i:s');
        $this->insertArray = array(
            'contact_id'    => $contactId,
            'social_id'     => $socId,
            'social_crm_id' => $crm_social_id,
            'account_name'  => $acc_name,
            'create_date'   => $date,
            'update_date'   => $date
        );
        if ($this->db->insert($this->contactSocialTable, $this->insertArray)) return true;
        else return false;
    }
    public function update_contact_social($val,$id){
        $date = date('Y-m-d H:i:s');
        $data = array(
            'account_name' => $val,
            'update_date' => $date
        );
        return $this->db->update( $this->contactSocialTable, $data, array( 'contact_social_id' => $id ) );
    }

    public function truncate(){
        $tableName = $this->contactSocialTable;
        $delete = $this->db->query("TRUNCATE TABLE $tableName");
        if ($delete){ return true; }
        else{ return false; }
    }
    public function select_social_type($key) {
        $tableName = $this->socialTypeTable;
        $sql = "SELECT * FROM `$tableName` WHERE `social_name`='".$key."';";
        $rows = $this->db->get_results($sql,ARRAY_A);
        return $rows;
    }
    public function existing_contact_social($contactId,$socialId) {
        $tableName = $this->contactSocialTable;
        $sql = "SELECT * FROM `$tableName` WHERE `contact_id`=".$contactId." AND `social_id`=".$socialId.";";
        $rows = $this->db->get_results($sql,ARRAY_A);
        return $rows;
    }

    public function update($data, $ID){
        foreach($data as $key=>$val){
            $social_data['account_name'] = $val;
            $this->db->update( $this->contactSocialTable, $social_data, array( 'contact_id' => $ID,'social_id'=> $val) );
        }
        return true;
    }

    public function update_field($data, $ID){
        foreach($data as $key=>$val){
            if (empty($val)) { $val = ''; }
            if($this->is_exists($ID,$key)){
                // echo "exitst : <pre>"; print_r($data); echo "</pre>";
                $social_data['account_name'] = !empty($val) ? $val : '';
                $social_data['update_date'] = date('Y-m-d h:i');
                $this->db->update( $this->contactSocialTable, $social_data, array( 'contact_id' => $ID,'social_id'=> (int)$key) );
            }else{
                // echo "not exitst : <pre>"; print_r($data); echo "</pre>";
                $social_data['contact_id'] = $ID;
                $social_data['social_id'] = (int)$key;
                $social_data['account_name'] = $val;
                $social_data['create_date'] = date('Y-m-d h:i');
                $this->save($social_data);
            }
        }
        return true;
    }
    public function save($array) {
        $this->insertArray  = $array;
        if ( $this->db->insert($this->contactSocialTable, $this->insertArray) ) return true;
        else return false;
    }
    public function is_social_exists($contactID) {
        $tableName = $this->contactSocialTable;
        $sql = "SELECT * FROM `$tableName` WHERE `contact_id`= $contactID LIMIT 1;";
        $rows = $this->db->get_results($sql);
        if (!empty($rows)) return true;
        else return false;
    }
    public function is_exists($contactID, $socialID) {
        $tableName = $this->contactSocialTable;
        $sql = "SELECT * FROM `$tableName` WHERE `contact_id`=".$contactID." AND `social_id`=".$socialID." LIMIT 1;";
        $rows = $this->db->get_results($sql);
        if (!empty($rows)) return true;
        else return false;
    }
    public function delete_contact_social($contactID, $contactSocialID) {
        // return $this->db->delete( $this->contactSocialTable, array( 'contact_id' => $ID, 'contact_social_id' => $contactSocialID,  ) );
        $sql = "DELETE FROM `".$this->contactSocialTable."` WHERE `contact_social_id` = $contactSocialID AND `contact_id` = $contactID;";
        $rows = $this->db->query($sql);
        return $rows;
    }
}