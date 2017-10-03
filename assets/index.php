<?php session_start();
$db             = new tn_db;
$sync           = new RestApi;
$image          = new tn_image;
$teachers       = new tn_teachers;
$address        = new tn_address;
$social         = new tn_social;
$tag            = new tn_tag;
$obj_inf        = new tn_infusion;
$i_token        = $db->select_token();
$client         = $db->select_data();
$client_id      = $client[0]->client_id;
$client_secret  = $client[0]->client_secret;

// Updata user statush to published
if ( isset($_GET['published']) && !empty(trim($_GET['published'])) ) {
    if ($tag->add_must_tag($_GET['published'])) { echo "status changed successfully.";}
    else echo "Sorry try again.";
}


// Apply the update
if ( isset($_GET['update_apply']) && !empty(trim($_GET['update_apply'])) ) {
    $errors = [];
    $ID = (int) trim($_GET['update_apply']);
    $fields = $teachers->get_update_field($ID);
    if ($fields) {
        if (isset($fields->address)) { // If comes from adress tab
            if ( !empty( (array) $fields->address) ) {
                if ( !$obj_inf->updateAddress($ID,$fields->address) ) { 
                    $errors['addr_update'] = "Infution update error"; 
                }
            }

            if( count($errors) <= 0 && !empty( $fields->address) ){
                if ( $address->update_field($fields->address, $ID) ){
                    $teachers->truncate_update_field($ID);
                    // Add the mandatory tag for first update
                    if ( !$tag->add_the_mandatory_tag($ID) ) { exit("The mandatory tag could not be added!"); }
                    echo "Fields updated successfully";
                }else{
                    echo "Sorry! There is some error happen.";
                }
            }
        }
        // General information
        else{
            // echo "<pre>"; print_r($fields); echo "</pre>"; die();
            if(isset($fields->social) && !empty( (array) $fields->social) ){ // Update social table
                $socout = $obj_inf->updateSocial($fields->social_id, $fields->social, $fields->social_crm_id, $ID);
                if (count($socout)==0) {
                    echo "Infution update error. ";
                    $errors['social-crm'] = 'Social CRM is not updated'; 
                }

                if ( $social->is_social_exists($ID) && !isset($errors['social-crm']) ) {
                    // Update db
                    if ( !$social->update_field($fields->social, $ID) ){ $errors['social-db'] = 'Social DB updata error'; } 
                } else {
                    // Insert into db
                    $counter = 0;
                    foreach ($fields->social as $key => $value) {
                        $social_db_insert_array = array(
                            'contact_id'    => $ID, 
                            'social_id'     => $key, 
                            'social_crm_id' => $socout[$counter], 
                            'account_name'  => $value
                            );
                        // echo "<pre>"; print_r($social_db_insert_array); echo "</pre>";
                        if ( !$social->save($social_db_insert_array) ) { $errors['social_db_insert'][] = 'Insertion error for ID : '.$ID; }
                        $counter++;
                    }
                }
                unset($fields->social);
                unset($fields->social_id);
                unset($fields->social_crm_id);
            }

            if (isset($fields->image) && !empty( (array) $fields->image)) {
                // Insert or update image
                if ($image->is_exists($ID)) { $imgUpdate = $image->update(array('url' => $fields->image), $ID); }
                else { $imgUpdate = $image->store($fields->image, $ID); }
                if ( !$imgUpdate ) { $errors['image'] = "Image could not updated.";}
                unset($fields->image);
            }

            if (isset($fields) && !empty( (array) $fields)) {
                if (!$obj_inf->updateContact($ID,$fields)) { $errors['contact-CRM'] = "Infusion update error."; }
                if ( !$teachers->update_field($fields, $ID) ) { $errors['contact-DB'] = "Teachers update error."; }
            }
            
            if ( count($errors) < 1 ) {
                if (!$teachers->truncate_update_field($ID)) { $errors['turncate-field'] = "Trunckate failed."; }
            }

            if ( count($errors) < 1 ){
                // Add the mandatory tag for first update
                if ( !$tag->add_the_mandatory_tag($ID) ) { exit("The mandatory tag could not be added!"); }
                echo "successfully Updated."; }
            else { 
                echo "Sorry! There are some errors."; 
                echo "<pre>"; print_r($errors); echo "</pre>";
            }
        }
    } else {
        // The update field is empty
        echo "No update is remaining for this user.";
    }
}

// Ignore the update request
if ( isset($_GET['update_reject']) && !empty(trim($_GET['update_reject'])) ) {
    $ID = (int) trim($_GET['update_reject']);
    if ( $teachers->truncate_update_field($ID) ) echo "Update is ignored";
    else echo "Sorry! There is some error happen";
}
?>

<br>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<style type="text/css"> 
    .action-btn{
        padding: 3px 9px;
        font-size: 11px;
        margin: 0 2px;
    }
    .no-margin{
        margin: 0;
    }
    .margin-top-10{
        margin-top: 10px;
    }
    .margin-bottom-20{
        margin-bottom: 20px;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="row margin-top-10 margin-bottom-20">
                <div class="col-sm-6">
                    <h3 class="no-margin">All Teachers</h3>
                    
                    <div class="form-inline">
                        <div class="form-group" style="margin-top: 20px;">
                            <label>Filter By:</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option>Select</option>
                                <option>Approved</option>
                                <option>Apply Update</option>
                            </select>
                        </div> 
                    </div>    
                </div>
                <div class="col-sm-6 hidden">
                    <a href="<?php echo esc_url(admin_url('/admin.php?page=tn-add_new')) ?>" type="button" class="btn btn-success pull-right">Add Teacher</a>
                </div>
            </div>
            <?php 
                $data = $teachers->get_teachers_list_table();
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">
jQuery(function($) {
    var table;
    table = $("#list").DataTable({
        "scrollX": true
    });

    $('#category_id').change( function() {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(
        function (settings, data) {
            var statusData = $.trim(data[7]) || "";
            var filterVal = $("#category_id option:selected").text();
            
            if(filterVal != 'Select'){
                if(filterVal.length > 0)
                {
                    if(filterVal == statusData){
                        return true;
                    }
                    else
                        return false;
                }
            }
            else
                return true;
        }
    );
    // $(document).on( 'click', '.publishBtn', function(event){
    //     event.preventDefault();
    //     var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
    //     var user_id = $(this).attr('user_id');
    //     console.log(user_id);
    //     $.ajax({
    //         url : ajax_url,
    //         type : 'post',
    //         data : {
    //             action : 'change_status',
    //             UID : user_id
    //         },
    //         beforeSend : function(){ 
    //             // $(this).attr('disabled', true);
    //             // $('.autoloader').html('Saving ...');
    //         },
    //         success : function( data ) {
    //             alert(data);
    //         },
    //         error : function(e){
    //             alert('error');
    //             console.log(e);
    //         }
    //     });
    // });
});
</script>