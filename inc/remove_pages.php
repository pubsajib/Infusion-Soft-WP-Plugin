<?php 
require( ABSPATH . WPINC . '/pluggable.php' );
require( ABSPATH . WPINC . '/pluggable-deprecated.php' );
$GLOBALS['wp_rewrite'] = new WP_Rewrite();

foreach ($tn_pages as $spage) {
	$thePage = get_page_by_title($spage. ' Teacher');
	$pageID = $thePage->ID;
	// wp_delete_post($pageID, ture);
	if ($pageID) { wp_delete_post($pageID, true); }
}