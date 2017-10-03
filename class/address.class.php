<?php 
/**
* Address Class
*/
class tn_address extends tn_db{
	function __construct(){
		parent::__construct();
	}
	public function insert($array){
		$this->insertArray = $array;
		$inserted = $this->db->insert($this->addressTable, $this->insertArray);
        if ($inserted){ return $this->db->insert_id; }
        else{ return 0; }
	}
	public function truncate(){
        $tableName = $this->addressTable;
        $delete = $this->db->query("TRUNCATE TABLE $tableName");
        if ($delete){ return true; }
        else{ return false; }
    }
    public function update_by_addressId($data, $ID){
        $this->db->update( $this->addressTable, $data, array( 'id' => $ID) );
        return true;
    }
    
    public function update($data, $ID){
        // echo "<pre>"; print_r($data); echo "</pre>"; return true;
        $this->db->update( $this->addressTable, $data, array( 'contact_id' => $ID) );
        return true;
    }

    public function update_field($data,$ID){
        if ($this->is_exists($ID)) {
            foreach($data as $key=>$value){
                $address_data[$key] = $value;
            }
            $this->db->update( $this->addressTable, $address_data, array( 'contact_id' => $ID) );
        }
        else{
            foreach($data as $key=>$value){
                $address_data[$key] = $value;
            }
            $address_data['field'] = 'BILLING';
            $address_data['contact_id'] = $ID;
            $this->insert($data);
        }
        return true;
    }

    public function is_exists($contactID) {
        $tableName = $this->addressTable;
        $sql = "SELECT * FROM `$tableName` WHERE `contact_id`=".$contactID." LIMIT 1;";
        $rows = $this->db->get_results($sql);
        if (!empty($rows)) return true;
        else return false;
    }

    public function get_field($contactID) {
        $tableName = $this->addressTable;
        $sql = "SELECT * FROM `$tableName` WHERE `contact_id`=".$contactID." LIMIT 1;";
        $rows = $this->db->get_results($sql);
        if (!empty($rows)) return $rows[0];
        else return false;
    }
    
    public function get_country_by_zip($country,$zip){
    	$tableName = $this->addressTable;
        $sql = "SELECT * FROM `$tableName` WHERE `postal_code`=".$zip." AND `country_code` = ".$country." LIMIT 1;";
        $rows = $this->db->get_results($sql);
        if (!empty($rows)) return $rows[0];
        else return false;
    }

    public function update_address_lat_lng() {
        $tableName = $this->addressTable;
        $sql = "SELECT * FROM `$tableName` WHERE `is_lat_lng`=0 LIMIT 2;";
        $rows = $this->db->get_results($sql);

        if (!empty($rows)){
            foreach($rows as $a_address){
                $addr = '';
                $updatedArray = array();
                $address_id = $a_address->id;
                $updatedArray['is_lat_lng'] = 1;

                //if(isset($a_address['postal_code']) && !isset($a_address['line1']) && !isset($a_address['line2']) && !isset($a_address['locality']) && !isset($a_address['region']) && !isset($a_address['country_code'])){
                if($a_address->postal_code!='' && $a_address->line1=='' && $a_address->line2=='' && $a_address->locality=='' && $a_address->region=='' && $a_address->country_code==''){
                    // Only zip code found. search country from address table
                    // No need to update
                }
                //else if(isset($a_address['line1']) && !isset($a_address['postal_code']) && !isset($a_address['line2']) && !isset($a_address['locality']) && !isset($a_address['region']) && !isset($a_address['country_code'])){
                else if($a_address->postal_code=='' && $a_address->line1!='' && $a_address->line2=='' && $a_address->locality=='' && $a_address->region=='' && $a_address->country_code==''){
                    // Only street 1 found. Ignor updating
                    // No need to update
                }
                //else if(!isset($a_address['line1']) && !isset($a_address['postal_code']) && isset($a_address['line2']) && !isset($a_address['locality']) && !isset($a_address['region']) && !isset($a_address['country_code'])){
                else if($a_address->postal_code=='' && $a_address->line1=='' && $a_address->line2!='' && $a_address->locality=='' && $a_address->region=='' && $a_address->country_code==''){
                    // Only street 2 found. Ignor updating
                    // No need to update
                }
                //else if(!isset($a_address['line1']) && !isset($a_address['postal_code']) && !isset($a_address['line2']) && isset($a_address['locality']) && !isset($a_address['region']) && !isset($a_address['country_code'])){
                else if($a_address->postal_code=='' && $a_address->line1=='' && $a_address->line2=='' && $a_address->locality!='' && $a_address->region=='' && $a_address->country_code==''){
                    // Only city found. Ignor updating
                    // No need to update
                }
                //else if(!isset($a_address['line1']) && !isset($a_address['postal_code']) && !isset($a_address['line2']) && !isset($a_address['locality']) && isset($a_address['region']) && !isset($a_address['country_code'])){
                else if($a_address->postal_code=='' && $a_address->line1=='' && $a_address->line2=='' && $a_address->locality=='' && $a_address->region!='' && $a_address->country_code==''){
                    // Only region found. Ignor updating
                    // No need to update
                }
                else {
                    if ($a_address->line1!='') {
                        $addr .= trim($a_address->line1).' ';
                    }
                    if ($a_address->line2!='') {
                        $addr .= trim($a_address->line2).' ';
                    }
                    if ($a_address->locality!='') {
                        $addr .= trim($a_address->locality).' ';
                    }
                    if ($a_address->region!='') {
                        $addr .= trim($a_address->region).' ';
                    }
                    if ($a_address->postal_code!='') {
                        $addr .= trim($a_address->postal_code).' ';
                    }
                    if ($a_address->country_code!='') {
                        $addr .= trim($a_address->country_code).' ';
                    }
                }
                //Get latitude & longitude and then update.
                if (!empty(trim($addr))) {
                    $latLng = $this->getLatLng($addr);
                    $country = isset($a_address->country_code) && !empty(trim($a_address->country_code)) ? trim($a_address->country_code) : false;
                    if(!empty($latLng)){
                        $updatedArray['lat'] = $latLng['lat'];
                        $updatedArray['lng'] = $latLng['lng'];
                    } elseif ($country) { // If full address fail then get country location
                        $latLng = $this->getLatLng($country);
                        $updatedArray['lat'] = $latLng['lat'];
                        $updatedArray['lng'] = $latLng['lng'];
                    }else{
                        $updatedArray['lat'] = '';
                        $updatedArray['lng'] = '';
                    }
                }
                $this->update_by_addressId($updatedArray, $address_id);
                //echo "<pre>"; print_r($a_address); echo "</pre>";
            }
            return '200';
        }
        else{
            return '401';
        }
    }
}
