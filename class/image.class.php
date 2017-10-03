<?php 
/**
* Image uploader class
*/
class tn_image extends tn_db {
	// $this->imageTable
	private $tableName;
	function __construct() {
		parent::__construct();
	}
	public function uploadWP($values='',$type='edit', $updatedVal=''){
		$value = '';
		$image = '';
		$mode = ''; if ($type == 'readonly') { $mode = 'disabled'; }
		$border = isset($updatedVal->avatar) ? ' style="border:1px solid red;"' : '';
		$value .= '<div class="clearfix"></div>';
		$value .= '<style>.image_preview{max-width: 100px;height: auto;}</style>';
		$url = $this->select($values);
		if ($url) $image = '<img src="'.$url.'" class="img-responsive" style="margin-top:10px;" alt="avatar">';
		if ( $type == 'edit' ) {
			$value .= '<input id="h_image-url" type="hidden" name="h_image" value="'.@$url.'"/>';
			$value .= '<input id="image-url" class="col-sm-8" type="text" name="image" value="'.@$url.'" readonly'.$border.' />';
			// $value .= '<input id="upload-button" type="button" class="btn btn-sm btn-primary" style="margin-left:20px;" value="Upload Image" '.$mode.'/>';
			$value .='<input type="file" name="fileToUpload" id="fileToUpload" class="btn btn-sm btn-primary" style="margin-left:20px;" '.$mode.'>';
		} else{
			$value .= '<input class="col-sm-12 form-control" type="text" name="imageNotEditable" value="'.@$url.'" readonly'.$border.' />';
		}
		$value .= '<div class="clearfix"></div>';
		$value .= '<div class="previewWrapper">';
		$value .= '<div class="image_preview">'. $image .'</div>';
		if ( $type == 'edit' ) { $value .= '<div class="image_remove">X</div>'; }
		$value .= '</div>';
		$value .= '<div class="clearfix"></div>';
		return $value;
	}
	public function upload($values='',$type='edit', $updatedVal=''){
		// echo "<pre>"; print_r($updatedVal); echo "</pre>";
		$value = '';
		$image = '';
		$mode = ''; if ($type == 'readonly') { $mode = 'disabled'; }
		$value .= '<div class="clearfix"></div>';
		$value .= '<style>.image_preview{max-width: 100px;height: auto;}</style>';
		$border = '';
		if ( isset($updatedVal->image) ) {
			$border = ' style="border:1px solid red;"';
			$url = TNPLUGINURL.'uploads/'.$updatedVal->image;
		} else {
			$url = $this->select($values);
		}
		if ($url) $image = '<img src="'.$url.'" class="img-responsive" style="margin-top:10px;" alt="avatar">';
		if ( $type == 'edit' ) {
			$value .= '<input id="h_image-url" type="hidden" name="h_image" value="'.@$url.'"/>';
			$value .= '<input id="image-url" class="col-sm-8" type="text" name="image" value="'.@$url.'" readonly'.$border.' />';
			$value .='<input type="file" name="fileToUpload" id="fileToUpload" class="custom-file-input" style="margin-left:20px;" '.$mode.'>';
			// $value .= '<input id="upload-button" type="button" class="btn btn-sm btn-primary" style="margin-left:20px;" value="Upload Image" '.$mode.'/>';
		} else{
			$value .= '<input class="col-sm-12 form-control" type="text" name="imageNotEditable" value="'.@$url.'" readonly'.$border.' />';
		}
		$value .= '<div class="clearfix"></div>';
		$value .= '<div class="previewWrapper">';
		$value .= '<div class="image_preview">'. $image .'</div>';
		if ( $type == 'edit' ) { $value .= '<div class="image_remove">X</div>'; }
		$value .= '</div>';
		$value .= '<div class="clearfix"></div>';
		return $value;
	}
	public function save($data, $ID){
		$this->insertArray = array('url' => $data, 'contact_id' => $ID );
        if ( $this->db->insert($this->imageTable, $this->insertArray) ) return true;
        else return false;
	}
	public function store($data, $ID){
		$this->insertArray = array('url' => $data, 'contact_id' => $ID );
        if ( $this->db->insert($this->imageTable, $this->insertArray) ) return true;
        else return false;
	}
	public function update($data, $ID){
        return $this->db->update( $this->imageTable, $data, array( 'contact_id' => $ID ) );
	}
	public function delete($ID){
		return $this->db->delete( $this->imageTable, array( 'contact_id' => $ID ) );
	}
	public function truncate(){
        $tableName = $this->imageTable;
        $delete = $this->db->query("TRUNCATE TABLE $tableName");
        if ($delete){ return true; }
        else{ return false; }
    }
    public function select($ID) {
    	$tableName = $this->imageTable;
        $sql = "SELECT `url` FROM `$tableName` WHERE `contact_id`=".$ID." LIMIT 1;";
        $rows = $this->db->get_results($sql);
        if ($rows[0]->url) {return TNPLUGINURL.'uploads/'.$rows[0]->url; }
        else return false;
    }
    public function is_exists($ID) {
    	$tableName = $this->imageTable;
        $sql = "SELECT * FROM `$tableName` WHERE `contact_id`=".$ID." LIMIT 1;";
        $rows = $this->db->get_results($sql);
        if (!empty($rows)) return true;
        else return false;
    }

    public function upload_image($post='', $contactID){
	    $target_dir 	= TNPLUGINPATH."uploads/";
	    $imageName 		= $_FILES["fileToUpload"]["name"];
	    $target_file 	= $target_dir . basename($imageName);
	    $uploadOk 		= 1;
	    $imageFileType 	= pathinfo($target_file,PATHINFO_EXTENSION);

	    // Check if image file is a actual image or fake image
	    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	    if($check !== false) {
	        // echo "File is an image - " . $check["mime"] . ".";
	        $uploadOk = 1;
	    } else {
	        echo "File is not an image.";
	        $uploadOk = 0;
	    }
	    // Check if file already exists
	    if (file_exists($target_file)) {
	    	$imageName = time().'_'.$imageName;
	    	$target_file 	= $target_dir . basename($imageName);
	        // echo "Sorry, file already exists.";
	        $uploadOk = 1;
	    }
	    // Check file size
	    if ($_FILES["fileToUpload"]["size"] > 500000) {
	        echo "Sorry, your file is too large.";
	        $uploadOk = 0;
	    }
	    // Allow certain file formats
	    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	    && $imageFileType != "gif" ) {
	        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
	        $uploadOk = 0;
	    }
	    // Check if $uploadOk is set to 0 by an error
	    if ($uploadOk == 0) {
	        echo "Sorry, your file was not uploaded.";
	    // if everything is ok, try to upload file
	    } else {
	        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        	// var_dump($this->is_exists($contactID));
				return $imageName;
	        } else {
	        	return false;
	            // echo "Sorry, there was an error uploading your file.";
	        }
	    }
	    return false;
	}

    public function upload_image_via_ajax($post=''){
	    $target_dir 	= TNPLUGINPATH."uploads/";
	    $contactID 		= $post[0];
	    $imageName 		= $post[1];
	    $target_file 	= $target_dir . basename($imageName);
	    $uploadOk 		= 1;
	    echo "string".$post[3];
	    $fileType       = explode('/', $post[3]);
	    $imageFileType 	= $fileType[1];

	    // Check if image file is a actual image or fake image
	    $check = $fileType[0] == 'image' ? true : false;
	    if($check !== false) {
	        // echo "File is an image - " . $check["mime"] . ".";
	        $uploadOk = 1;
	    } else {
	        echo "File is not an image.";
	        $uploadOk = 0;
	    }
	    // Check if file already exists
	    if (file_exists($target_file)) {
	    	$imageName = time().'_'.$imageName;
	    	$target_file 	= $target_dir . basename($imageName);
	        // echo "Sorry, file already exists.";
	        $uploadOk = 1;
	    }
	    // Check file size
	    if ($post[2] > 500000) {
	        echo "Sorry, your file is too large.";
	        $uploadOk = 0;
	    }
	    // Allow certain file formats
	    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	    && $imageFileType != "gif" ) {
	        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
	        $uploadOk = 0;
	    }
	    // Check if $uploadOk is set to 0 by an error
	    if ($uploadOk == 0) {
	        echo "Sorry, your file was not uploaded.";
	    // if everything is ok, try to upload file
	    } else {
	        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        	// var_dump($this->is_exists($contactID));
				if ($this->is_exists($contactID)) { $imgUpdate = $this->update(array('url' => $imageName), $contactID); }
				else{ $imgUpdate = $this->store($imageName, $contactID); }
				if ( $imgUpdate ) { return true; }
				// return $imageName;
	        } else {
	        	return false;
	            // echo "Sorry, there was an error uploading your file.";
	        }
	    }
	    return false;
	}
}