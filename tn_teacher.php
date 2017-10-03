<?php
ob_start();
session_start();
/* Plugin Name: Tech Ninja Teachers
Plugin URI: http://blubirdinteractive.com/
Description: A plugin to allow users to find and filter teachers using multiple criteria.
Author: author
Version: 1.0
Author URI: http://www.blubirdinteractive.com/
*/
// All configurations and definations
define('TNPLUGINURL',plugin_dir_url( __FILE__ ));
define('TNPLUGINPATH',plugin_dir_path( __FILE__ ));
include TNPLUGINPATH.'inc/config.php';

// Additional functions and configurations
include TNPLUGINPATH.'inc/ajax_functions.php';
include TNPLUGINPATH.'class/db.class.php';
include TNPLUGINPATH.'class/social.class.php';
include TNPLUGINPATH.'class/tag.class.php';
// include TNPLUGINPATH.'class/tag_extended.class.php';
include TNPLUGINPATH.'class/image.class.php';
include TNPLUGINPATH.'class/template.class.php';
include TNPLUGINPATH.'class/teachers.class.php';
include TNPLUGINPATH.'class/address.class.php';
include TNPLUGINPATH.'class/contactsync.class.php';
include TNPLUGINPATH.'class/api_operations.class.php';
include TNPLUGINPATH.'class/activate_deactivate.class.php';
include TNPLUGINPATH.'class/user_interface_common.class.php';
include TNPLUGINPATH.'class/user_interface_frontend.class.php';
include TNPLUGINPATH.'class/user_interface_backend.class.php';
include TNPLUGINPATH.'class/campaign.class.php';
include TNPLUGINPATH.'assets/vendor/autoload.php';
include TNPLUGINPATH.'class/infusion.class.php';
include TNPLUGINPATH.'assets/setcron.php';
// include TNPLUGINPATH.'assets/reftoken.php';
// ========================================================================

// TEST SECTION

// ========================================================================

// $data = new tn_db;
// $activeDeactive = new tn_activate_deactivate;
// $activeDeactive->create_contact_tag_table();
// $activeDeactive->create_settings_table();
// $tn_pages = unserialize (TNPAGES);
// include TNPLUGINPATH.'inc/add_pages.php';
// include TNPLUGINPATH.'inc/remove_pages.php';
// ========================================================================

// TEST SECTION END

// ========================================================================
add_action('admin_menu', 'run_bb_installer');
function run_bb_installer(){
    add_menu_page('TN Teacher', 'Teachers', 'manage_options', 'tn-teachers', 'tn_run_plugin','',100);
    add_submenu_page('tn-teachers', 'All Teachers', 'All Teachers', 'manage_options', 'tn-teachers' );
    add_submenu_page('tn-teachers', 'Settings', 'Settings', 'manage_options', 'tn-settings', 'tn_setting' );
    add_submenu_page('tn-teachers', 'Add Contact', 'Add Contact', 'manage_options', 'tn-add_new', 'tn_addNew' );
}

// Run the plugin
function tn_run_plugin(){ include 'assets/index.php'; }

// settings menu
function tn_setting(){ include 'assets/settings.php'; }

// Add new contact
function tn_addNew(){ include 'assets/add_new.php'; }

// Includes styles and scripts
// include TNPLUGINPATH.'inc/styles_and_scripts.php';
add_action( 'admin_enqueue_scripts', 'safely_add_stylesheet_to_admin' );
add_action( 'init', 'safely_add_stylesheet_to_admin' );
function safely_add_stylesheet_to_admin() {

    // wp_enqueue_script( 'jquery-main', 'http://code.jquery.com/jquery-latest.min.js' );
    wp_enqueue_style( 'bootstrap-css', plugins_url('assets/css/bootstrap.min.css', __FILE__) );
    if ( !is_admin() ) {
        wp_enqueue_style( 'tn-googleapis', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400' );
        wp_enqueue_style( 'tn-googleapis', 'https://fonts.googleapis.com/css?family=Roboto:400,500' );
        wp_enqueue_style( 'tn-googleapis', plugins_url('https://fonts.googleapis.com/css?family=Montserrat:100,200,400,600', __FILE__) );
        wp_enqueue_style( 'tn-googleapis', plugins_url('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600', __FILE__) );
        wp_enqueue_style( 'tn-googleapis', 'https://fonts.googleapis.com/css?family=Kaushan+Script' );
        wp_enqueue_style( 'tn-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
        wp_enqueue_style( 'tn-common', plugins_url('assets/css/common-styles.css', __FILE__) );
        wp_enqueue_style( 'tn-style', plugins_url('assets/css/style.css', __FILE__) );
        wp_enqueue_style( 'tn-mediaquery', plugins_url('assets/css/mediaquery.css', __FILE__) );
    } else {
        wp_enqueue_style( 'tn-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
        wp_enqueue_style( 'tn-main', plugins_url('assets/css/main.css', __FILE__) );
    }
    if ( is_admin() ) { wp_enqueue_script( 'loaderjs', 'https://cdn.jsdelivr.net/jquery.loadingoverlay/latest/loadingoverlay.min.js' ); }
    if ( is_admin() ) { wp_enqueue_script( 'loaderjspro', 'https://cdn.jsdelivr.net/jquery.loadingoverlay/latest/loadingoverlay_progress.min.js' ); }
    if ( !is_admin() ) { wp_enqueue_script( 'bootstrap-css', plugins_url('assets/js/jquery.min.js', __FILE__) ); }
    wp_enqueue_script( 'tn-bootstrap-js', plugins_url('assets/js/bootstrap.min.js', __FILE__) );
    if ( !is_admin() ) { wp_enqueue_script( 'bootstrap-css', plugins_url('assets/js/custom.js', __FILE__) ); }
    else { wp_enqueue_script( 'tn-main', plugins_url('assets/js/main.js', __FILE__) ); }
    wp_enqueue_script( 'tn-common', plugins_url('assets/js/common.js', __FILE__) );
    // Media uploader
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
        wp_enqueue_script( 'tn-meia', plugins_url('assets/js/wp_media_uploader.js', __FILE__) );
    }
}

// Install and uninstall hooks
function tn_plugin_activate() {
    $activeDeactive = new tn_activate_deactivate;
    $activeDeactive->createTables();
    $tn_pages = unserialize (TNPAGES);
    include TNPLUGINPATH.'inc/add_pages.php';
}
function tn_on_deactivation(){
    $tn_pages = unserialize (TNPAGES);
    include TNPLUGINPATH.'inc/remove_pages.php';
    $activeDeactive = new tn_activate_deactivate;
    $activeDeactive->deleteTables();
}
function tn_on_uninstall(){}
register_activation_hook( __FILE__, 'tn_plugin_activate' );
register_deactivation_hook(__FILE__, 'tn_on_deactivation');
register_uninstall_hook(__FILE__, 'tn_on_uninstall');
function admin_pages_redirect() {
    $current_url = add_query_arg(NULL, NULL);
    $params = explode('?', $current_url);
    $parts = explode('code=', $current_url);
    if(is_array($parts)&& strlen($parts[1]) == 24){ 
        wp_redirect($current_url.'&page=tn-settings');
        exit;
    }
}
add_action('admin_init', 'admin_pages_redirect');
add_action( 'admin_footer', 'contact_sync_javascript' ); 
function contact_sync_javascript() { ?>
    <script type="text/javascript" >
    jQuery(document).ready(function($) {
        $("#sync_now").on("click",function(){ // When btn is pressed.
            jQuery.LoadingOverlay("show");
            var ajaxurl = '<?php echo admin_url( "admin-ajax.php" );?>';
            jQuery.ajax({
                url:ajaxurl,
                type:'POST',
                data:{'action': 'contact_sync'},
                success:function(response){
                    console.log(response);
                    jQuery('#sync_result').html('Contact Sync Successfull.');
                    jQuery('#sync_result').delay(3000).fadeOut();
                    jQuery.LoadingOverlay("hide");
                    ajax_address_updata(); // Generate geolocation for each contacts
                },
                error:function (error) {
                    console.log(error);
                    jQuery.LoadingOverlay("hide");
                }
            });
            return false;
        });
    });
    </script> <?php
}
add_action( 'wp_ajax_nopriv_contact_sync', 'contacts_sync' );
add_action( 'wp_ajax_contact_sync', 'contacts_sync' );
function token_unset_when_login() {
    if(isset($_SESSION['token'])){
        unset($_SESSION['token']);
        // print_r($_SESSION);
    }
    // print_r($_SESSION);
}
add_action('wp_login', 'token_unset_when_login');

function add_address_ajax_call() { ?>
    <script type="text/javascript" >
        function ajax_address_updata(){
            var ajaxurl = '<?php echo admin_url( "admin-ajax.php" );?>';
            jQuery.ajax({
                url:ajaxurl,
                type:'POST',
                data:{action: 'update_lat_lng'},
                success:function(response){
                    // console.log(response);
                    if ( response == 200 ) { ajax_address_updata(); }
                },
                error:function (error) {
                    console.log(error);
                }
            });
        }
        jQuery(document).ready(function($) {
            ajax_address_updata();
        });
    </script> <?php
}
add_action( 'in_admin_footer', 'add_address_ajax_call' );
ob_flush();
