<?php 
$pags = serialize(array('Login', 'Logout', 'Profile', 'Search'));
$tags = serialize(array(423,145,303,143,509,305,1148));
define('TNPAGES',$pags);
define('TNTAGS',$tags);

// Conditional tags for displaing
define('TNISMUSTTAG', true);
define('TNMANDATORYTAGS', 1148);
define('TNOPTIONALTAGS', '423,145,303,143,509,305');
// define('TNOPTIONALTAGS', '92,110,245,120');

// Initial tables
define('TNIMAGETABLE','tn_image');
define('TNTOKENTABLE','tn_token');
define('TNADDRESSTABLE','tn_address');
define('TNSETTINGTABLE','tn_settings');
define('TNTEACHERSTABLE','tn_teachers');
define('TNCAMPAIGNTABLE','tn_campaign');

// Social tables
define('TNSOCIALTYPETABLE','tn_social_type');
define('TNCONTACTSOCIALTABLE','tn_contact_social');

// Tag tables
define('TNTAGSTABLE','tn_tags');
define('TNTAGCCATSTABLE','tn_categories');
define('TNCONTACTTAGSTABLE','tn_contact_tags');

// Others
define('TNMAPRADIUS',100);
define('TNMAPAPIKEY','AIzaSyA6Dq27cdbmyoGFx3IjTF94Iw-qPfkfwYY');