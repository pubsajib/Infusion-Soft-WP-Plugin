<?php session_start();
// print_r($_SESSION);
$db             = new tn_db;
$objInf         = new tn_infusion;
$obj_cam        = new tn_campaign;

// $_SESSION['contactID'] = isset($_GET['id']) && !empty($_GET['id']) ? (int) $_GET['id'] : false;
// $url = esc_url( site_url('/login-teacher/?id='.$_SESSION['contactID']) );
$url = esc_url( site_url('/login-teacher/') );
if ( isset($_GET['logout'])) {
    unset($_SESSION['teacher_loged_in']); 
	unset($_SESSION['contactID']); 
	wp_redirect($url);
}

if ( isset($_POST['teacherLoginBtn']) ) {
    $usermail = isset($_POST['inputEmail']) && !empty(trim($_POST['inputEmail'])) ? trim($_POST['inputEmail']) : '';
    $password = isset($_POST['inputPassword']) && !empty(trim($_POST['inputPassword'])) ? trim($_POST['inputPassword']) : '';
    $email = '';
    // print_r($_POST);
    $result = $objInf->authenticate($usermail , $password);
    // echo $result;
    if($result){
    	if ( !session_id() ) { session_start(); }
            $_SESSION['teacher_loged_in'] = true;
            $_SESSION['contactID'] = $result;
            // if ( !isset($_SESSION['contactID']) || $_SESSION['contactID'] != false ) {}
            // echo "<pre>"; print_r($_SESSION); echo "</pre>";
    }else{
        $_SESSION['teacher_loged_in'] = false;
        echo "Logged in not Successfull.";
    }
}

if ( ( !empty($_GET['tId']) && !empty($_GET['autologin']) ) && ( !empty($_GET['tEmail1']) || !empty($_GET['tEmail2']) ) ){
    $auto_stat = 0;   
    if($_GET['autologin']==true){
        $autolog = $obj_cam->is_exists('autolog_status');       
        if($autolog){ $auto_stat = $obj_cam->select($autolog); }
        if($_GET['autologin']==true && $auto_stat=='true'){
            global $wp_query;
            $usermail1 = isset($_GET['tEmail1']) && !empty(trim($_GET['tEmail1'])) ? trim($_GET['tEmail1']) : '';
            $usermail2 = isset($_GET['tEmail2']) && !empty(trim($_GET['tEmail2'])) ? trim($_GET['tEmail2']) : '';
            $id = isset($_GET['tId']) && !empty(trim($_GET['tId'])) ? trim($_GET['tId']) : '';
            // echo  $id .'-'. $usermail2.'-'. $usermail1;
            $result = $objInf->authenticate_autologin( $usermail1, $usermail2, $id );
            if($result){
                if ( !session_id() ) { session_start(); }
                $_SESSION['teacher_loged_in'] = true;
                $_SESSION['contactID'] = $result;
                // if ( !isset($_SESSION['contactID']) || $_SESSION['contactID'] != false ) {}
            }else{
                $_SESSION['teacher_loged_in'] = false;
                echo "Logged in not Successfull.";
            }
        }else{
            $_SESSION['teacher_loged_in'] = false;
            echo "Auto login is not permitted.";
        }
        // print_r($_SESSION);
    }
}
if ( $_SESSION['teacher_loged_in'] ) { 
	get_header();
    echo '<div class="fusion-builder-preview-image bannerImage"><img src="'.TNPLUGINURL.'assets/images/header-bg.jpg"></div>';
	include 'edit_profile.php'; 
    echo '<script>$(".tooltipShow").tooltip();</script>';
	get_footer();
}
else {
	get_header();
    echo '<div class="fusion-builder-preview-image bannerImage"><img src="'.TNPLUGINURL.'assets/images/header-bg.jpg"></div>';
	include 'login_form.php';
	get_footer();
}