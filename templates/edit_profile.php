<?php 
session_start();
    $formType       = 'edit';
    $db             = new tn_db;
    $sync           = new RestApi;
    $api            = new tn_api;
    $teacher        = new tn_teachers;
    $UI             = new tn_ui_common;
    $objInf         = new tn_infusion;
    $i_token        = $db->select_token(); // Get access token
    $contactID      = $_SESSION['contactID'];

    $address = $socials = $contact_array = [];
    if ( $contactID && $contactID > 0 ) {
        $formType = 'edit';
        $contact_array = $teacher->teachersRow($contactID);
        $contact_array = $contact_array[0];
        $socials = $contact_array->social;
        $address = $contact_array->adds[0];
    }

    // Make changes on CRM and DB upon update
    if ( isset($_POST['update_contact_button']) ) {
        unset($_POST['update_contact_button']);
        $db->update_contact($_POST, $contactID, 'user');
    }

    $updatedVal = '';
    $updatedResult = $teacher->get_update_field($contactID);
    if ($updatedResult) {
        if ( isset($updatedResult->address)) {
            $updatedVal = $updatedResult->address;
        }else{
            $updatedVal = $updatedResult;
        }
    }
    $basicInfo_edit = $UI-> basic_info($contact_array, 'edit','backend',$socials);
    $basicInfo_update = $UI-> basic_info($contact_array, 'update','frontend', $socials, $updatedVal);
    // echo "<pre>"; print_r($updatedVal); echo "</pre>";
?>
<?php 
    echo '<div class="clearfix"></div>';
    echo '<a href="'.site_url('login-teacher/?logout=true&id='.$_SESSION['contactID']).'" class="btn btn-sm btn-primary pull-right">Logout</a>';
    echo '<div class="clearfix"></div>';
 ?>
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
            <div>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="<?php echo !$_SESSION['teacher_loged_in'] && empty($updatedResult) ? 'active' : ''; ?>"><a href="#viewListing" aria-controls="viewListing" role="tab" data-toggle="tab">View Listing</a></li>
                    <li role="presentation" class="<?php echo $_SESSION['teacher_loged_in'] && empty($updatedResult) ? 'active' : ''; ?>"><a href="#editListing" aria-controls="editListing" role="tab" data-toggle="tab">Edit Listing</a></li>
                    <?php if ($updatedResult): ?>
                    <li role="presentation" class="active"><a href="#changedData" aria-controls="changedData" role="tab" data-toggle="tab">Changed Data</a></li>
                    <?php endif ?>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane <?php echo !$_SESSION['teacher_loged_in'] && empty($updatedResult) ? 'active' : ''; ?>" id="viewListing">
                        <?php //echo $basicInfo_readonly; echo '<br><br>'; echo $addressForm_readonly; ?>
                        <?php echo $UI->teachers_details($contact_array); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane <?php echo $_SESSION['teacher_loged_in'] && empty($updatedResult) ? 'active' : ''; ?>" id="editListing">
                        <?php echo $basicInfo_edit;
                        // echo '<br><br>'; echo $addressForm_edit; ?>
                    </div>
                    <?php if ($updatedResult): ?>
                        <div role="tabpanel" class="tab-pane active" id="changedData">
                            <?php echo $basicInfo_update;
                            // echo '<br><br>'; echo $addressForm_update; ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            
        </div>
    </div>
</div>
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

    $(".select2-multiple").select2({
        tags: true
    });
});
</script>