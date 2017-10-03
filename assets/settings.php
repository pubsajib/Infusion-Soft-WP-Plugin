<?php 
session_start();
$db 		= new tn_db;
$teacher 	= new tn_teachers;
$address 	= new tn_address;
$obj_cam 	= new tn_campaign;
$i_token 	= $db->select_token();
// test

//test end
if(isset($i_token['access_t'])){
	$camprest = new RestApi;
	$camprest->uri = "https://api.infusionsoft.com/crm/rest/v1/campaigns?access_token=".$i_token['access_t'];
	$allCampaigns = $camprest->rest_get_method();
	$resultcam = json_decode($allCampaigns, true);
	// echo "<pre>"; print_r($result); echo "</pre>";
}
if(!empty($_POST['welcome_campaign']) && !empty($_POST['welcome_sequence'])){
	$obj_wel = new tn_campaign;
	$campVal = $_POST['welcome_campaign'];
	$sequVal = $_POST['welcome_sequence'];
	$wel_camp_error = 0;

	$camKey = $obj_wel->is_exists('welcome_campaign');
	echo $camKey;
	if($camKey){
		// echo "inside updated.";exit();
		$wel_cam_up = $obj_wel->update($campVal,$camKey);
		if(!$wel_cam_up){$wel_camp_error++;echo "not update.";}
	}else{
		// echo "inside save.";exit();
		$wel_cam_save = $obj_wel->save($campVal, 'welcome_campaign');
		if(!$wel_cam_save){$wel_camp_error++;}else{}
	}
	if($wel_camp_error==0){
		$sequKey = $obj_wel->is_exists('welcome_sequence');
		if($sequKey){
			$wel_seq_up = $obj_wel->update($sequVal,$sequKey);
			if(!$wel_seq_up){$wel_camp_error++;}
		}else{
			$wel_seq_save = $obj_wel->save($sequVal, 'welcome_sequence');
			if(!$wel_seq_save){$wel_camp_error++;}else{}
		}
	}
	if($wel_camp_error==0){
		echo "Operation successfull.";
	}else{
		echo "Operation not successfull.";
	}
}
$url = $obj_cam->is_exists('autolog_url');
if($url){
	$val_url = $obj_cam->select($url);
	if(isset($val_url) && $val_url!=false){
		// Do nothing
	}else{
        $gene_url = $obj_cam->generate_autologin_url();
        if($gene_url!=false){
            $obj_cam->update($gene_url,$url);
        }else{ echo "Please refresh this page. Auto login link not set."; }
	}
}else{
    $gene_url = $obj_cam->generate_autologin_url();
    if($gene_url!=false){
        if($obj_cam->save($gene_url, 'autolog_url')){
            // echo "Auto login active.";
        }
    }else{echo "Auto login link not generated.";}
}
/*
if(isset($_POST['autolog_camp_setting'])){
	$obj_auto = new tn_campaign;
	$auto_camp_error = 0;
	{
		// if( !empty($_POST['autolog_campaign']) && !empty($_POST['autolog_sequence']) ){
		// 	$campAuto = $_POST['autolog_campaign'];
		// 	$sequAuto = $_POST['autolog_sequence'];

		// 	$camKey = $obj_auto->is_exists('autolog_campaign');
		// 	if($camKey){
		// 		echo "Auto camp:".$campAuto.' Id:'.$camKey;
		// 		$auto_cam_up = $obj_auto->update($campAuto,$camKey);
		// 		if(!$auto_cam_up){$auto_camp_error++; echo "autologcampaign";}
		// 	}else{
		// 		$auto_cam_save = $obj_auto->save($campAuto, 'autolog_campaign');
		// 		if(!$auto_cam_save){$auto_camp_error++;}
		// 	}

		// 	if($auto_camp_error==0){
		// 		$sequKey = $obj_auto->is_exists('autolog_sequence');
		// 		if($sequKey){
		// 			$auto_seq_up = $obj_auto->update($sequAuto,$sequKey);
		// 			if(!$auto_seq_up){$auto_camp_error++; echo "autolog_sequence";}
		// 		}else{
		// 			$auto_seq_save = $obj_auto->save($sequAuto, 'autolog_sequence');
		// 			if(!$auto_seq_save){$auto_camp_error++;}
		// 		}
		// 	}

		// 	if($auto_camp_error==0){
		// 		echo "Operation successfull.";
		// 	}else{
		// 		echo "Operation not successfull.";
		// 	}
		// }
	}
}
*/

// Activity for "API Configuration" tab. Save settings.
if ( isset($_POST['tn_api_settings']) ) {
	$data = [];
	$client_id 	= isset($_POST['client_id']) && !empty(trim($_POST['client_id'])) ? trim($_POST['client_id']) : '';
	$client_secret = isset($_POST['client_secret']) && !empty(trim($_POST['client_secret'])) ? trim($_POST['client_secret']) : '';

	$data = array($client_id, $client_secret);
	if ( $db->insert_data($data) ) { echo " Data inserted successfully. "; }
	else { echo "Something wrong please try again!"; }
	} else {
	$result = $db->select_data();
	$client_id 	= $result[0]->client_id;
	$client_secret = $result[0]->client_secret;

	if ( (is_null($client_id) || empty(trim($client_id))) && (is_null($client_secret) || empty(trim($client_secret))) ) {
		$applyReadonly = '';
	}else {
		$applyReadonly = ' readonly=""';
	}
}

function currentPageUrl(){
	$isSecure = false;
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
	    $isSecure = true;
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	    $isSecure = true;
	}
	$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
	return admin_url( 'admin.php?page=tn-settings', $REQUEST_PROTOCOL );
}

function generate_login_url(){
    $isSecure = false;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $isSecure = true;
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
        $isSecure = true;
    }
    $REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
    $url = site_url( '/login-teacher' , $REQUEST_PROTOCOL );
    if($url){
        return $url;
    }else{
        return false;
    }
}

// Activity for "Connection" tab.
if( isset($_GET['code']) && !empty($_GET['code'])){
	$db 	= new tn_db;
	// parameters ( CRM code,  $mustTagRequired)
	$db->insert_CRM_data_into_DB_from_settings_page($_GET['code'], TNISMUSTTAG);
}
?>

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<style type="text/css">
	.tab-content {padding: 15px; border: 1px solid #dedede; border-top: 0; }
	legend {font-size: 21px; }
	.api-info p{
	  font-size: 14px;
	}
</style>
<br><br>
<div class="container">
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<div>
			  	<ul class="nav nav-tabs" role="tablist">
			    	<li role="presentation"><a href="#Configuration" aria-controls="Configuration" role="tab" data-toggle="tab">API Configuration</a></li>
			    	<li role="presentation" class=""><a href="#connection" aria-controls="connection" role="tab" data-toggle="tab">Connection</a></li>
			    	<li role="presentation" class="active"><a href="#API" aria-controls="API" role="tab" data-toggle="tab">API Info</a></li>
			    	<!-- <li role="presentation"><a href="#Campaign" aria-controls="Campaign" role="tab" data-toggle="tab">Welcome Campaign</a></li> -->
			    	<li role="presentation"><a href="#Auto_Login" aria-controls="Auto_Login" role="tab" data-toggle="tab">Login Info</a></li>
			  	</ul>

			  	<div class="tab-content">
			    	<div role="tabpanel" class="tab-pane" id="Configuration">
			    		<form action="" method="POST" role="form">

							<legend> API Settings </legend>
							
							<div class="form-group">
								<label for="">Client Key</label>
								<input type="text" class="form-control" value="<?php echo $client_id; ?>" placeholder="User ID" name="client_id"<?php echo $applyReadonly; ?>>
							</div>

							<div class="form-group">
								<label for="">Client Secret</label>
								<input type="text" class="form-control" value="<?php echo $client_secret; ?>" placeholder="Secret" name="client_secret"<?php echo $applyReadonly; ?>>
							</div>
							
							<div class="row">
								<div class="col-sm-6">
									<div>
										<small style="font-size: 10px;">Don't have client key and secret? <a href="https://keys.developer.infusionsoft.com/member/register">Get client key and security.</a></small>
									</div>
								</div>

								<div class="col-sm-6">
									<div class="text-right">
										<?php if (!empty($applyReadonly)): ?>
											<button type="button" id="edit_setting" class="btn btn-primary" name="">Edit</button>
											<button type="submit" class="btn btn-success setting-save hidden" name="tn_api_settings">Save</button>
										<?php else: ?>
											<button type="submit" class="btn btn-success setting-save" name="tn_api_settings">Save</button>
										<?php endif ?>
									</div>
								</div>
							</div>
							
						</form>
			    	</div>

			    	<div role="tabpanel" class="tab-pane" id="connection">
			    		<!-- <form role="form" action="https://signin.infusionsoft.com/app/oauth/authorize" method="GET">
			    			<legend>Get Connected</legend>
			    			<div class="form-group">
			    				<input type="text" class="form-control" name="client_id" id="client_id" value="<?php echo $client_id; ?>">
			    			</div>
			    			<div class="form-group">
					        	<input type="text" class="form-control" name="response_type" value="code">            
			    			</div>
			    			<div class="form-group">
					        	<input type="text" class="form-control" name="scope" value="full"> 
			    			</div>
			    			<div class="form-group">
					        	<input type="text" class="form-control" name="redirect_uri" id="redirect_uri" value="<?php echo currentPageUrl(); ?>">
			    			</div>
			    			<div class="text-right">
			    				<button type="submit" class="btn btn-success connect-btn" name="submit" id="submit">Connect</button>
			    			</div>
			    		</form> -->
			    		<?php	
			    			$objAuth 	= new tn_infusion;		    		 
			    			$objAuth->getAuthencation();
			    		?>
			    	</div>

			    	<div role="tabpanel" class="tab-pane active" id="API">
			    		<legend>API Info</legend>
			    		<div class="api-info">
				    		<?php 
				    		if(isset($i_token['scope'])){
				    			$app_name = explode('|', $i_token['scope']);
				    		}
				    		$total = $teacher->allteachersRows();
				    		$sync_date = $teacher->lastsyncdate();
				    		$url_login = generate_login_url();
				    		?>
			    			<p>
			    				<?php 
			    				if(is_array($app_name)){
			    					echo "<b>Connected App: </b>";
			    					echo $app_name[1];
			    				}
			    				?>	
			    			</p>

			    			<p>
			    				<?php 
			    				if(isset($total)){ 
			    					echo "<b>Total Contacts: </b>";
			    					echo $total[0]['total'];
			    				}
			    				?>
			    			</p>

			    			<!-- <p>
			    				<b>Listed Tags:</b>
			    				Duis aute irure,  Duis aute irure dolor in reprehenderit, qui officia deserunt, labore et dolore magna, Tag 5, Tag 6, Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			    			</p> -->

			    			<p>
			    				<?php 
			    				if($sync_date){
			    					echo "<b>Last Synchronize Date: </b>";
			    					echo $sync_date[0]['created_at'];
			    				}
			    				?>
			    			</p>

			    			<p>
			    				<?php 
			    					if($url_login){ 
			    						echo "<b>Login URL: </b>";
			    						echo $url_login;
			    					}
			    				?>
			    			</p>

							<div class="text-right">
			    				<button id="sync_now" class="btn btn-success">Synchronize Now</button>
			    			</div>
			    			<div class="alert-success" id="sync_result" style="padding:10px;"></div>
			    		</div>
			    	</div>

			    	<!-- <div role="tabpanel" class="tab-pane" id="Campaign">
			    		<?php // echo "<pre>"; print_r($resultcam); echo "</pre>"; ?>
			    		<form role="form" action="" method="POST">
			    			<legend>Welcome Campaign Info</legend>
			    			<div class="row">
			    				<div class="col-sm-6 col-xs-12">
			    					<div class="form-group">
					    				<label>Select Campaign</label>
					    				<select name="welcome_campaign" class="form-control" id="selCampaign">
						    				<option value="" selected="selected" style="display:none;">Select One</option>
										    <?php
										    if(is_array($resultcam['campaigns'])){
										    	foreach ($resultcam['campaigns'] as $key => $value) {
										        	echo "<option value=".$value['id'].">".$value['name']."</option>";
										      	}
										    }
									    ?>
					    				</select>
					    			</div>
			    				</div>
			    				<div class="col-sm-6 col-xs-12">
			    					<div class="form-group">
					    				<label>Select Sequence</label>
					    				<select name="welcome_sequence" class="form-control" id="sequence_result">
					    					<option value=''>First Select Campaign</option>
					    				</select>
					    			</div>
			    				</div>
			    			</div>

			    			<div class="text-right">
			    				<button type="submit" class="btn btn-success" name="welcome_camp_setting" id="">submit</button>
			    			</div>
			    		</form>
			    	</div> -->

			    	<div role="tabpanel" class="tab-pane" id="Auto_Login">
			    		<form role="form" action="" method="POST">
			    			<legend>Login Info</legend>
							<div class="row">
								<div class="col-sm-3 col-xs-12">
									<?php
									$is_checked='';
									$autolog = $obj_cam->is_exists('autolog_status');
									if($autolog){
										$val_stat = $obj_cam->select($autolog);
										if($val_stat == 'true'){$is_checked = 'checked';}
									}
									$url_id = $obj_cam->is_exists('autolog_url');
									if($url_id){
										$val_cam = $obj_cam->select($url_id);
									}
									?>
									<div class="form-inline">
						    			<div class="form-group">
										    <label for="">Auto Login</label><br>
										    <input id="loginStatusBtn" type="checkbox" name="autolog_statu" <?php echo $is_checked;?> data-toggle="toggle" data-size="small"> <span class="autoloader" style="margin-left: 10px; color: red;"></span>
									  	</div>
								  	</div>
								</div>
								<div class="col-sm-9 col-xs-12">
									<div id="showautologinurl" style="word-break: break-all;">
									  	<?php
										  	if($val_cam){
										  		echo "<b>Autologin Url:</b><br>";
										  		echo $val_cam;
										  	}
									  	?>
								  	</div>
								</div>
							</div>
			    		</form>

				    	<!-- on/off admin approval -->
				    	<?php if ($obj_cam->get_value('admin_approval_status') == 'true' ) {
				    		$checkboxStatus =  'checked';
				    	} else { $checkboxStatus =  ''; } ?>
				    	<div class="row">
			    			<br><br>
			    			<form action="" method="POST" class="form-inline" role="form">
					    		<div class="col-sm-6">
					    			<label for="">Admin Approval</label><br>
								    <input id="adminApprovalBtn" type="checkbox" name="admin_approval" data-toggle="toggle" data-size="small" <?php echo $checkboxStatus; ?>> <span class="loader" style="margin-left: 10px; color: red;"></span>
					    		</div>
				    			<div class="col-sm-6 text-right hidden"><button id="" type="submit" class="btn btn-success">Submit</button></div>
				    		</form>
				    		<div class="clearfix"></div>
				    		<div class="col-sm-12"> <div class="adminApprovalSaveStatus" style="margin-top: 20px;"></div> </div>
				    	</div>
			    	</div>
			  	</div>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery( document ).ready(function() {
	    jQuery('select#selCampaign').on('change', function() {
	    	var option = jQuery(this).find('option:selected');
		    var value = parseInt(option.val());
		    var text = option.text();
		    console.log(value+text);
		    var ajaxurl = '<?php echo admin_url( "admin-ajax.php" );?>';
		    jQuery.ajax({
	            url:ajaxurl,
	            type:'POST',
	            data:{action:'load_sequences',campId:value},
	            dataType: 'json',
	            success:function(response)
	            {
	                console.log(response);
	                jQuery('#sequence_result').html(response);
	            },
	            error:function (error) {
	                console.log(error);
	            }
	        });

	    });

	    jQuery('select#autocampaign').on('change', function() {
	    	var option = jQuery(this).find('option:selected');
		    var autovalue = parseInt(option.val());
		    var autotext = option.text();
		    // console.log(autovalue+autotext);
		    var ajaxurl = '<?php echo admin_url( "admin-ajax.php" );?>';
		    jQuery.ajax({
	            url:ajaxurl,
	            type:'POST',
	            data:{action:'load_sequences',campId:autovalue},
	            dataType: 'json',
	            success:function(response)
	            {
	                console.log(response);
	                jQuery('#autosequence').html(response);
	            },
	            error:function (error) {
	                console.log(error);
	            }
	        });
	    });
	});
</script>
<script>
	jQuery(function ($) {
		if($('#loginStatusBtn').is(":checked"))
	        $("#showautologinurl").show(500);
	    else
	        $("#showautologinurl").hide(500);

		$(document).on( 'change', '#adminApprovalBtn', function(event){
			event.preventDefault();
			if($(this).is(':checked')){ var approvalVal = true; } 
			else{var approvalVal = false; }
			var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
			jQuery.ajax({
                url : ajax_url,
                type : 'post',
                data : {
                    action : 'admin_approval_status',
                    status : approvalVal
                },
                beforeSend : function(){ 
                	$('#adminApprovalBtn').attr('disabled', true);
                	$('.loader').html('Saving ...');
                },
                success : function( response ) {
                    // alert(response);
                    $('#adminApprovalBtn').removeAttr('disabled');
                	$('.loader').html('');
                	$('.adminApprovalSaveStatus').html('<strong style=" margin-top: 20px; padding: 10px 20px;border: 1px solid grey;">'+ response +'</strong>').show().delay(5000).hide(500);
                },
                error : function(e){
                    console.log(e);
                }
            });
		});

		$(document).on( 'change', '#loginStatusBtn', function(event){
			event.preventDefault();
			if($(this).is(':checked')){ var autologVal = true; } 
			else{var autologVal = false; }
			// alert(autologVal);
			var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
			jQuery.ajax({
                url : ajax_url,
                type : 'post',
                data : {
                    action : 'admin_autolog_status',
                    status : autologVal
                },
                beforeSend : function(){ 
                	$('#loginStatusBtn').attr('disabled', true);
                	$('.autoloader').html('Saving ...');
                },
                success : function( response ) {
                    // alert(response);
                    $('#loginStatusBtn').removeAttr('disabled');
                	$('.autoloader').html('');
                	$('.adminApprovalSaveStatus').html('<strong style=" margin-top: 20px; padding: 10px 20px;border: 1px solid grey;">'+ response +'</strong>').show().delay(5000).hide(500);
                	if($('#loginStatusBtn').is(":checked"))
				        $("#showautologinurl").show(500);
				    else
				        $("#showautologinurl").hide(500);
                },
                error : function(e){
                    console.log(e);
                }
            });
		});
	});
</script>


