<?php 

add_action( "wp_ajax_nopriv_update_lat_lng", "update_lat_lng" );
add_action( "wp_ajax_update_lat_lng", "update_lat_lng" );

add_action( "wp_ajax_nopriv_selectStates", "selectStates" );
add_action( "wp_ajax_selectStates", "selectStates" );

add_action( "wp_ajax_nopriv_searchTeachers", "searchTeachers" );
add_action( "wp_ajax_searchTeachers", "searchTeachers" );

add_action( "wp_ajax_nopriv_load_sequences", "load_sequences" );
add_action( "wp_ajax_load_sequences", "load_sequences" );

add_action( "wp_ajax_nopriv_show_details", "show_details" );
add_action( "wp_ajax_show_details", "show_details" );

add_action( "wp_ajax_nopriv_admin_approval_status", "admin_approval_status" );
add_action( "wp_ajax_admin_approval_status", "admin_approval_status" );

add_action( "wp_ajax_nopriv_admin_autolog_status", "admin_autolog_status" );
add_action( "wp_ajax_admin_autolog_status", "admin_autolog_status" );

add_action( "wp_ajax_nopriv_change_status", "change_status" );
add_action( "wp_ajax_change_status", "change_status" );

add_action( "wp_ajax_nopriv_fileToUpload", "fileToUpload" );
add_action( "wp_ajax_fileToUpload", "fileToUpload" );

function fileToUpload(){
    $image = new tn_image;
    // global $wpdb;
    $file = isset($_POST['file']) && !empty(trim($_POST['file'])) ? trim($_POST['file']) : 'false';
    $file =  stripslashes($file);
    $file =  json_decode($file);
    echo $image->upload_image_via_ajax($file);
    // echo "file : $file";
    wp_die();
}

function change_status(){
    // global $wpdb;
    // $userID = isset($_POST['userID']) && !empty(trim($_POST['userID'])) ? trim($_POST['userID']) : 'false';
    // echo 'userID';
    wp_die();
}

function update_lat_lng(){
    global $wpdb;
    $address = new tn_address;
    $status = $address->update_address_lat_lng();
    echo $status;
    wp_die();
}
function admin_autolog_status(){
    global $wpdb;
    $obj_auto = new tn_campaign;

    $status = isset($_POST['status']) && !empty(trim($_POST['status'])) ? trim($_POST['status']) : 'false';
    $camStat = $obj_auto->is_exists('autolog_status');
    // echo $camStat.'-'.$status;
    if($camStat){
        $auto_stat_up = $obj_auto->update( $status, $camStat );
        if(!$auto_stat_up){$auto_status_error++;echo "No ";}else{}
    }else{
        $auto_state_save = $obj_auto->save( $status, 'autolog_status' );
        if(!$auto_state_save){$auto_status_error++;}else{}
    }
    if($auto_status_error==0){
        echo "Status changed.";
    }else{
        echo "Status not changed. Please try again.";
    }
    wp_die();
}

function admin_approval_status(){
    global $wpdb;
    $data = '';
    $obj_cam    = new tn_campaign;
    $status = isset($_POST['status']) && !empty(trim($_POST['status'])) ? trim($_POST['status']) : 'false';
    $theID = $obj_cam->is_exists('admin_approval_status');
    if ( $theID ) {
        $retVal = $obj_cam->update($status, $theID);
    }else{
        $retVal = $obj_cam->save($status, 'admin_approval_status');
    }
    if ($retVal) { echo "Status changed"; }
    else { echo "Could not changed. Please try again."; }
    wp_die();
}

function show_details(){
    global $wpdb;
    $data = '';
    $UI = new tn_ui_frontend;
    $teachers = new tn_teachers;
    $userID = isset($_POST['userID']) && !empty(trim($_POST['userID'])) ? trim($_POST['userID']) : false;
    $theTeacher = $teachers->teachersRow($userID);
    // echo json_encode($theTeacher);
    echo $UI->teachers_details($theTeacher[0]);
    wp_die();
}
function selectStates(){
    global $wpdb;
    $data = '';
    $teachers = new tn_teachers;
    $country = isset($_POST['country']) && !empty(trim($_POST['country'])) ? trim($_POST['country']) : false;
    $rows = $teachers->getStates($country);
    if ( $rows ) {
        $data .= '<option value="">Select State</option>';
        foreach ($rows as $row) {
            if (!empty(trim($row->region))) $data .= '<option value="'.$row->region.'">'.$row->region.'</option>';
        }
        echo $data;
    } else { echo "Error!"; }
    wp_die();
}

function searchTeachers(){
    global $wpdb;
    $data = '';
    $teachers = new tn_teachers;

    $country    = isset($_POST['country']) && !empty(trim($_POST['country'])) ? trim($_POST['country']) : false;
    $state      = isset($_POST['state']) && !empty(trim($_POST['state'])) ? trim($_POST['state']) : false;
    $zip        = isset($_POST['zip']) && !empty(trim($_POST['zip'])) ? trim($_POST['zip']) : false;
    
    if ( !empty($zip) ) { 
        $values = array('country' => $country, 'zip' => $zip);
        $data = $teachers->frontend_all_teachers($values);
    }
    elseif ( !empty($country) || !empty($state) ) { 
        $values = array('country' => $country, 'state' => $state );
        $data = $teachers->frontend_all_teachers($values); 
    }
    else { $data = $teachers->frontend_all_teachers(); }
    echo json_encode($data);
    // echo json_encode($values);
    wp_die();
}

function load_sequences(){
    $campId = $_POST['campId'];
    $db         = new tn_db;
    $sequrest   = new RestApi;
    $i_token    = $db->select_token();
    $sequrest->uri = "https://api.infusionsoft.com/crm/rest/v1/campaigns/".$campId."?optional_properties=sequences&access_token=".$i_token['access_t'];
    $allCampaigns = $sequrest->rest_get_method();
    $resultseq = json_decode($allCampaigns, true);
    $html .= "<option value='' selected='selected' style='display:none;'>Select One</option>";
    foreach ($resultseq['sequences'] as $key => $value) {
        $html .= "<option value='".$value['id']."'>".$value['name']."</option>";
    }
    echo json_encode($html);
    wp_die();
}
