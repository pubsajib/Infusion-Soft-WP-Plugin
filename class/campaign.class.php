<?php 
/**
* Campaign class
*/
class tn_campaign extends tn_db {
	// $this->campaignTable
	private $tableName;
	function __construct() {
		parent::__construct();
	}

	public function save($value, $key){
		$this->insertArray = array('key' => $key,'value'=>$value);
        if ( $this->db->insert($this->campaignTable, $this->insertArray) ) return true;
        else return false;
	}
	public function update($value,$id){
        // $this->insertArray = array('value' => $value);
        return $this->db->update( $this->campaignTable, array('value' => $value), array( 'id' => $id ) );
    }
	public function truncate(){
        $tableName = $this->campaignTable;
        $delete = $this->db->query("TRUNCATE TABLE $tableName");
        if ($delete){ return true; }
        else{ return false; }
    }
    public function select($ID) {
    	$tableName = $this->campaignTable;
        $sql = "SELECT `value` FROM `$tableName` WHERE `id`=".$ID." LIMIT 1;";
        $rows = $this->db->get_results($sql);
        if(isset($rows[0]->value)){
            return $rows[0]->value;
        }else{
            return false;
        }
        
    }
    public function is_exists($key) {
    	$tableName = $this->campaignTable;
        $sql = "SELECT * FROM `$tableName` WHERE `key`='".$key."' LIMIT 1;";
        $rows = $this->db->get_results($sql);
        // print_r($rows);
        if (!empty($rows)) return $rows[0]->id;
        else return false;
    }
    public function generate_autologin_url(){
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isSecure = true;
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $isSecure = true;
        }
        $REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
        $id      = "~Contact.Id~";
        $umail1  = "~Contact.Email~";
        $umail2  = "~Contact.EmailAddress2~";
        $url = site_url( '/login-teacher?autologin=true&tId='.$id.'&tEmail1='.$umail1.'&tEmail2='.$umail2.'', $REQUEST_PROTOCOL );
        if($url){
            return $url;
        }else{
            return false;
        }
    }
    public function get_value($key){
        $tableName = $this->campaignTable;
        $sql = "SELECT * FROM `$tableName` WHERE `key`='".$key."' LIMIT 1;";
        $rows = $this->db->get_results($sql);
        // print_r($rows);
        if (!empty($rows)) return $rows[0]->value;
        else return null;
    }
}