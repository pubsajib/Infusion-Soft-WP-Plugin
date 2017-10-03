<?php 
$page_content = '&nbsp;';

require( ABSPATH . WPINC . '/pluggable.php' );
require( ABSPATH . WPINC . '/pluggable-deprecated.php' );
$GLOBALS['wp_rewrite'] = new WP_Rewrite();
foreach ($tn_pages as $spage) {
  $page_title = $spage.' Teacher';
  $page_check = get_page_by_title($page_title);
  $post = array(
    'post_content' => $page_content,
    'post_content' => $page_content,
    'post_status' => 'publish',
    'post_title' => $page_title,
    'post_type' => 'page',
  );  

  // Insert the post into the database
  if (!$page_check) { 
    $id = wp_insert_post( $post);
    if ($id) { //update_post_meta( $id, '_wp_page_template', 'login-teacher.php' );
    add_post_meta( $id, '_wp_page_template', strtolower($spage).'-teacher.php' ); }
  }
}

