<?php session_start();
    $formType       = 'new';
    $db             = new tn_db;
    $sync           = new RestApi;
    $api            = new tn_api;
    $image          = new tn_image;
    $teacher        = new tn_teachers;
    $UI             = new tn_ui_common;
    $objInf         = new tn_infusion;
    $tags           = new tn_tag;
    $obj_wel        = new tn_campaign;

    $contactID      = isset($_GET['id']) && trim($_GET['id']) ? (int) trim($_GET['id']) : false;
    $i_token        = $db->select_token(); // Get access token
    $client         = $db->select_data();
    $client_id      = $client[0]->client_id;
    $client_secret  = $client[0]->client_secret;
    $socialClass        = new tn_social;

    // echo "<br><br><br><br><pre>"; print_r($socialClass->delete_contact_social(50692, 46)); die();
    // $camKey = $obj_wel->is_exists('welcome_campaign');
    // if($camKey){
    //     $cam = $obj_wel->select($camKey);
    //     $seqKey = $obj_wel->is_exists('welcome_sequence');
    //      if($seqKey){
    //         $seq = $obj_wel->select($seqKey);}
    //     else{
    //         echo "No campaign selected yet.";
    //     }
    // }else{ echo "No campaign selected yet."; }

    $address = $socials = $contact_array = $errors = [];
    if ( $contactID && $contactID > 0 ) {
        $formType = 'edit';
        // $contactID = (int) trim($_GET['id']);
        $contact_array = $teacher->teachersRow($contactID);
        $contact_array = $contact_array[0];
        $socials = $contact_array->social;
        $address = $contact_array->adds[0];
    }

    // Make changes on CRM and DB upon update
    if ( isset($_POST['update_contact_button']) || isset($_POST['update_addrs_btn']) ) {
        $db->update_contact($_POST, $contactID);
    }

    $basicInfo_readonly = $UI->basic_info($contact_array, 'readonly','backend',$socials);
    $addressForm_readonly = $UI->address_form($address, 'readonly');
    // $socialForm = $UI->social_form($socials, 'readonly');

    $basicInfo_edit = $UI-> basic_info($contact_array, 'edit','backend',$socials);
    $addressForm_edit = $UI->address_form($address, 'edit', $contactID);

    $updatedVal = $updatedAdds = '';
    $updatedResult = $teacher->get_update_field($contactID);
    if ($updatedResult) {
        if ( isset($updatedResult->address)) {
            $updatedAdds = $updatedResult->address;
        }else{
            $updatedVal = $updatedResult;
        }
    }

    $basicInfo_update = $UI-> basic_info($contact_array, 'update','backend',$socials, $updatedVal);
    $addressForm_update = $UI->address_form($address, 'update', 'backend', $contactID, $updatedAdds);
?>
<br><br>
<?php if ( isset($_GET['id']) && !empty(trim($_GET['id'])) ) : ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#viewListing" aria-controls="viewListing" role="tab" data-toggle="tab">View Listing</a></li>
                        <li role="presentation"><a href="#editListing" aria-controls="editListing" role="tab" data-toggle="tab">Edit Listing</a></li>
                        <?php if ($updatedResult): ?>
                        <li role="presentation"><a href="#changedData" aria-controls="changedData" role="tab" data-toggle="tab">Changed Data</a></li>
                        <?php endif ?>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="viewListing">
                            <?php //echo $basicInfo_readonly; echo '<br><br>'; echo $addressForm_readonly; ?>
                            <?php echo $UI->teachers_details($contact_array); ?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="editListing">
                            <?php echo $basicInfo_edit;
                            echo '<br><br>';
                            echo $addressForm_edit; ?>
                        </div>
                        <?php if ($updatedResult): ?>
                            <div role="tabpanel" class="tab-pane" id="changedData">
                                <?php echo $basicInfo_update;
                                echo '<br><br>';
                                echo $addressForm_update; ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
<?php else: ?>
    <h3 class="text-center" style="color:red;"> Please select a teacher to edit</h3>
<?php endif; ?>
<!--search with auto complte (bootstrap-select2-master)-->
<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<!-- For Tag auto complete END-->

<script type="text/javascript">
jQuery(function($) {
    //for diabled/enable tags plugins
    //$(".select2-multiple").prop("disabled", false);

    // for diabled address fields
    $("#address-edit").click(function(){
        $(this).addClass("hidden");
        $(".form-save").removeClass("hidden");
        $(this).parents("form").find(".form-control").removeAttr( "readonly");
    });

    $(".select2-multiple, #tags").select2({
        //tags: true
    });

    // Image upload via ajax
    // $("#fileToUploadsdf").on("change",function(event){
    //     // fd.append( 'file', input.files[0] );
    //      var formdata = new FormData($("#fileToUpload"));
    //     console.log(formdata);
    //     event.preventDefault();
    //     var fileInfo = [];
    //     var file_data = $(this).prop("files")[0]; // Getting the properties of file from file field
    //     var file_data1 = $(this).prop("files"); // Getting the properties of file from file field
    //     var ajaxurl = '<?php //echo admin_url( "admin-ajax.php" );?>';
    //     var userID   = '<?php //echo $_GET["id"]; ?>';
    //     fileInfo.push(userID);
    //     fileInfo.push(file_data.name);
    //     fileInfo.push(file_data.size);
    //     fileInfo.push(file_data.type);
    //     // fileInfo.push(file_data.tmp_name);
    //     var file = JSON.stringify(fileInfo);

        
    //     jQuery.ajax({
    //         url:ajaxurl,
    //         type:'POST',
    //         data:{ action : 'fileToUpload', file : file },
    //         success:function(response){
    //             console.log(response);
    //             alert(response);
                
    //         },
    //         error:function (error) {
    //             console.log(error);
    //         }
    //     });
    //     return false;
    // });
});
jQuery(".tooltipShow").tooltip();
</script>