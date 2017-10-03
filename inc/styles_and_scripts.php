<?php 
// styles and scripts
add_action( 'admin_enqueue_scripts', 'safely_add_stylesheet_to_admin' );
add_action( 'init', 'safely_add_stylesheet_to_admin' );
function safely_add_stylesheet_to_admin() {

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