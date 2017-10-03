<?php 
/**
* Tag class
* $tagTable;
* $tagCatTable;
* $contactTagTable;
*/
class tn_tag extends tn_db {
	function __construct() {
		parent::__construct();
	}

    public function user_tags($ID) {
        $sql = "";
        $sql .= "SELECT * FROM `".$this->contactTagTable."` AS ct ";
        $sql .= "JOIN ".$this->tagTable." AS t ON ct.tag_id = t.tag_id ";
        $sql .= "JOIN ".$this->tagCatTable." AS c ON t.category_id = c.category_id ";
        $sql .= "WHERE `contact_id`=".$ID." ORDER BY ct.`tag_id` ASC;";
        $rows = $this->db->get_results($sql);
        return $rows;
    }
    public function user_contact_tags($ID) {
        $sql = "";
        $sql .= "SELECT `tag_id` FROM `".$this->contactTagTable."` ";
        $sql .= "WHERE `contact_id`=".$ID." ORDER BY `tag_id` ASC;";
        $rows = $this->db->get_results($sql);
        return $rows;
    }
    public function user_tags_html($ID) {
    	$value = '';
    	$tags = $this->user_tags($ID);
    	if ($tags) {
	    	$value .= '<ul class="tag-list">';
	    	foreach ($tags as $tag) { $value .= '<li>'.$tag->tag_name.'</li>'; }
	    	$value .= '</ul>';
    	}
    	if ($value) { return $value; }
    	else { return ''; }
    }

    public function insert_contact_tag($id,$CategoryId,$tagName){
        $date = date('Y-m-d H:i:s');
        $this->insertArray = array(
            'tag_id'            => $id, 
            'category_id'       => $CategoryId,
            'tag_name'          => $tagName, 
            'create_date'       => $date,
            'update_date'       => $date 
        );
        if ($this->db->insert($this->tagTable, $this->insertArray)) return true;
        else return false;
    }

    public function update_contact_tag($id,$tagName){
        $date = date('Y-m-d H:i:s');
        $data = array(
            'tag_name'      => $tagName,
            'update_date'   => $date
        );
        return $this->db->update( $this->tagTable, $data, array( 'tag_id' => $id ) );
    }

    public function select_tagbyId($id) {
        $tableName = $this->tagTable;
        $sql = "SELECT * FROM `$tableName` WHERE `tag_id`=".$id.";";
        $rows = $this->db->get_results($sql,ARRAY_A);
        return $rows;
    }

    public function insert_tag_category($id,$CategoryName){
        $date = date('Y-m-d H:i:s');
        $this->insertArray = array(
            'category_id'       => $id, 
            'category_name'     => $CategoryName, 
            'create_date'       => $date,
            'update_date'       => $date 
        );
        if ($this->db->insert($this->tagCatTable, $this->insertArray)) return true;
        else return false;
    }

    public function update_tag_category($id,$CategoryName){
        $date = date('Y-m-d H:i:s');
        $data = array(
            'category_name'     => $CategoryName,
            'update_date'       => $date
        );
        return $this->db->update( $this->tagCatTable, $data, array( 'category_id' => $id ) );
    }

    public function select_categorybyId($id) {
        $tableName = $this->tagCatTable;
        $sql = "SELECT * FROM `$tableName` WHERE `category_id`=".$id.";";
        $rows = $this->db->get_results($sql,ARRAY_A);
        return $rows;
    }

    public function all_tags() {
        $sql = "";
        $sql .= "SELECT * FROM `".$this->tagTable."` AS t ";
        $sql .= "JOIN ".$this->tagCatTable." AS c ON t.category_id = c.category_id ";
        $sql .= " ORDER BY t.`tag_id` ASC;";
        $rows = $this->db->get_results($sql);
        return $rows;
    }

    public function all_cats() {
        $sql = "";
        $sql .= "SELECT * FROM `".$this->tagCatTable."` ";
        $sql .= " ORDER BY `category_id` ASC;";
        $rows = $this->db->get_results($sql);
        return $rows;
    }

    public function get_tag_list() {
        $array = $cats = [];
        $tags = $this->all_tags();
        if ($tags) {
            foreach ($tags as $tag) {
                $array[trim($tag->category_name)][] = $tag->tag_name;
            }
            return $array;
        } else return false;
    }

    public function insert_tag_contact($contactId,$tagId){
        $date = date('Y-m-d H:i:s');
        $this->insertArray = array(
            'contact_id'        => $contactId, 
            'tag_id'            => $tagId, 
            'create_date'       => $date,
            'update_date'       => $date 
        );
        if ($this->db->insert($this->contactTagTable, $this->insertArray)) return true;
        else return false;
    }

    public function update_tag_contact($contactId,$tagId,$contact_tags_id){
        $date = date('Y-m-d H:i:s');
        $data = array(
            'contact_id'        => $contactId,
            'tag_id'            => $tagId,
            'update_date'       => $date
        );
        return $this->db->update( $this->contactTagTable, $data, array( 'contact_tags_id' => $contact_tags_id ) );
    }

    public function select_contactbyId($tagid) {
        $tableName = $this->contactTagTable;
        $sql = "SELECT * FROM `$tableName` WHERE `tag_id`=".$tagid.";";
        $rows = $this->db->get_results($sql,ARRAY_A);
        return $rows;
    }
    
	public function truncate(){
        $tableName = $this->contactTagTable;
        $delete = $this->db->query("TRUNCATE TABLE $tableName");
        if ($delete){ return true; }
        else{ return false; }
    }

    public function insert($ID, $tagID){
        $this->insertArray = array( 'contact_id' => $ID, 'tag_id' => $tagID );
        if ( $this->db->insert($this->contactTagTable, $this->insertArray) ) return true;
        else return false;
    }

    public function is_exists($ID, $tagID=1148) {
        $tableName = $this->contactTagTable;
        $sql = "SELECT * FROM `$tableName` WHERE `contact_id`=".$ID." AND `tag_id`=".$tagID." LIMIT 1;";
        $rows = $this->db->get_results($sql);
        if (!empty($rows)) return true;
        else return false;
    }

    public function add_the_mandatory_tag($ID, $tagID=1148) {
        if ( !$this->is_exists($ID, $tagID)) {
            if ( $this->insert($ID, $tagID) ) { return true; }
            else { return false; }
        } else { return true; }
    }
    public function allowed_teachers($mandatoryTag, $optionalTags) {
        $IDS = '';
        $mandatory = $optional = [];
        $tableName = $this->contactTagTable;

        // Query for mandatory tag
        $sql = "SELECT `contact_id` FROM `$tableName` WHERE `tag_id`=".$mandatoryTag.";";
        $rows = $this->db->get_results($sql);
        foreach ($rows as $row) { $mandatory[] = $row->contact_id; }

        // Query for mandatory tag
        $sql = "SELECT `contact_id` FROM `$tableName` WHERE `tag_id` IN (".$optionalTags.");";
        $rows = $this->db->get_results($sql);
        foreach ($rows as $row) { $optional[] = $row->contact_id; }
        $cIDS = array_intersect($mandatory,$optional);

        // Make the string of IDs compatible with sql query 
        foreach ($cIDS as $cID) { $IDS .= $cID.', '; }
        return rtrim($IDS, ', ');
    }
    public function has_must_tag($ID) {
        $sql = "";
        $sql .= "SELECT `tag_id` FROM `".$this->contactTagTable."` ";
        $sql .= "WHERE `contact_id`=".$ID." AND `tag_id`=".TNMANDATORYTAGS." ORDER BY `tag_id` ASC;";
        $rows = $this->db->get_results($sql);
        if (!empty($rows)) { return $rows; }
        else { return false; }
    }
    public function add_must_tag($ID) {
        if ( $this->insert($ID, TNMANDATORYTAGS) ) return true;
        else false;
    }
}