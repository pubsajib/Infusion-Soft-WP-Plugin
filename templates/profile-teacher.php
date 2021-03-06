<?php session_start();

get_header();

echo '<div class="fusion-builder-preview-image bannerImage"><img src="'.TNPLUGINURL.'assets/images/header-bg.jpg"></div>';

$formType       = 'new';
$db             = new tn_teachers;
$sync           = new RestApi;
$FUI            = new tn_ui_frontend;
$data           = new tn_db;
$objInf         = new tn_infusion;
$conTag         = new tn_tag;
$address        = new tn_address;
$acc_token      = $data->select_token();
$client         = $data->select_data();
$client_id      = $client[0]->client_id;
$client_secret  = $client[0]->client_secret;

if ( isset($_GET['id']) && !empty(trim($_GET['id'])) ) {
    $formType = 'edit';
    $contactID = (int) trim($_GET['id']);

    $returnFields = array('FirstName','LastName','Email','Username','Password','EmailAddress2','JobTitle','Phone1','Website','City','Country','PostalCode','State','StreetAddress1');
    $contact_array = $objInf->return_job_and_studio($contactID,$returnFields);

    $datayoga = array('_YogaStudio');

    try{
        $contact_yoga = $objInf->return_job_and_studio($contactID,$datayoga);
        if($contact_yoga){
            $contact_array[0]['_YogaStudio']=$contact_yoga[0]['_YogaStudio'];
        }else{
            $contact_array[0]['_YogaStudio']='';
        }
    }catch(Exception $ex){}
    $socials = $objInf->return_social_byID($contactID);
    // echo "<pre>"; print_r($socials); echo "</pre>";
    if( is_array($socials) ){
        foreach ($socials as $key => $value) {
            $contact_array[0][$key]= $value;
        } 
    }
    // echo "<pre>"; print_r($contact_array); echo "</pre>";exit();
    $sync->uri = "https://api.infusionsoft.com/crm/rest/v1/contacts/".$contactID."/tags?access_token=".$acc_token['access_t'];
    $contact_tags = $sync->rest_get_method();
}

// Insert or update contact into DB
if ( isset($_GET['webpost']) && $_GET['webpost']=='yes' && !empty(trim($_GET['contactId']))) {
    $contactID = (int) trim( $_GET['contactId'] );
    if (!$db->is_exists($contactID) ) { $db->insert_CRM_data_into_DB($contactID); } // Insert into DB
    else { $db->update_CRM_data_into_DB($contactID); } // Update DB data
}  
echo $FUI->view_profile($contactID, $contact_array[0], $contact_tags);

get_footer(); ?>