<?php 
/**
* Common user interface
*/
class tn_ui_common extends tn_db {
	
	function __construct() {}
    public function basic_info_test($values = '', $type='edit', $ui_type='backend', $socials='', $updatedVal=false){
        // echo "<pre>"; print_r($values->adds[0]); echo "</pre>";
        // echo "<pre>"; print_r($updatedVal); echo "</pre>";
        $image      = new tn_image;
        $teacher    = new tn_teachers;
        if ( $teacher->get_update_field($values->contact_id) ) { $is_disabled = 'disabled'; }
        else { $is_disabled = ''; }
        // if ($updatedVal) { $updatedVal = json_decode($updatedVal); }
        $html = '';
        $mode = ''; if ($type == 'readonly') { $mode = 'disabled'; }
        $html .='<form id="userEditFormw" action="" method="POST" role="form" enctype="multipart/form-data">';
        $html .='<div class="form-section">';
        $html .='<legend>Basic Info</legend>';
        $html .='<div class="form-group">';
        $html .='<label for="">Avatar</label>';
        // $html .='<input type="file" name="fileToUpload" id="fileToUpload">';
        $html .= $image->upload($values->contact_id, $type, $updatedVal);
        $html .='</div>';
        $html .='</div>';
        

        $html .= $this->address_form($values->adds[0], $type, $ui_type, $values->contact_id, $updatedVal);
        // $html .= $this->submit_button($type, $is_disabled);
        if ( $type == 'edit' ){
            $html .='<div class="text-right">';
            $html .= '<button type="submit" id="form-edit" class="btn btn-success" name="update_contact_button" '.$is_disabled.'>Save</button>';
            $html .='</div>';
        }
        $html .='</form>';
        return $html;
    }
    
	public function basic_info($values = '', $type='edit', $ui_type='backend', $socials='', $updatedVal=false){
        // echo "<pre>"; print_r($updatedVal); echo "</pre>";
        $image      = new tn_image;
        $teacher    = new tn_teachers;
        if ( $teacher->get_update_field($values->contact_id) ) { $is_disabled = 'disabled'; }
        else { $is_disabled = ''; }
        // if ($updatedVal) { $updatedVal = json_decode($updatedVal); }
        $html = '';
        $mode = ''; if ($type == 'readonly') { $mode = 'disabled'; }
        $html .='<form action="" method="POST" role="form" enctype="multipart/form-data">';
        $html .='<div class="form-section">';
        $html .='<legend>Basic Info</legend>';
        $html .='<div class="form-group">';
        $html .='<label for="">Avatar</label>';
        $html .= $image->upload($values->contact_id, $type, $updatedVal);
        $html .='</div>';
        
        if (isset($updatedVal->given_name) ){
            $values->given_name = $updatedVal->given_name;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .='<div class="form-group">';
        $html .='<label for="">First Name</label>';
        if ( $type == 'edit' ) 
        $html .='<input type="hidden" class="form-control" name="h_given_name" value="'.@$values->given_name.'">';
        $html .='<input type="text" class="form-control" name="given_name" value="'.@$values->given_name.'" '.$mode.$border.'>';
        $html .='</div>';
        
        if (isset($updatedVal->family_name) ){
            $values->family_name = $updatedVal->family_name;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .='<div class="form-group">';
        $html .='<label for="">Last Name</label>';
        if ( $type == 'edit' ) 
        $html .='<input type="hidden" class="form-control" name="h_family_name" value="'.@$values->family_name.'">';
        $html .='<input type="text" class="form-control" name="family_name" value="'.@$values->family_name.'" '.$mode.$border.'>';
        $html .='</div>';

        // Show title checkbox
        if (isset($updatedVal->is_show_title) ){
            $values->is_show_title = $updatedVal->is_show_title;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $is_checked = $values->is_show_title == 'YES' ? 'checked ' : '';
        $html .='<div class="form-group" '.$border.'>';
        $html .='<input type="checkbox" class="formCheckbox" style="float: left; margin-top: 2px;" '.$is_checked.$mode.'>';
        $html .='<label for="">Show Title</label>';
        $html .='<button type="button" class="btn btn-default tooltipShow" data-toggle="tooltip" data-placement="top" title="Check this box to display your job title">?</button>';
        // $html .='<script>jQuery(".tooltipShow").tooltip();</script>';
        if ( $type == 'edit' ) 
        $html .='<input type="hidden" class="form-control" name="h_is_show_title" value='.@$values->is_show_title.'>';
        $html .='<input type="hidden" class="formCheckboxHiddenField" name="is_show_title" value="'.@$values->is_show_title.'">';
        $html .='</div>';

        // Job title 
        if (isset($updatedVal->job_title) ){
            $values->job_title = $updatedVal->job_title;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .='<div class="form-group">';
        $html .='<label for="">Job Title</label>';
        if ( $type == 'edit' ) 
        $html .='<input type="hidden" class="form-control" name="h_job_title" value="'.@$values->job_title.'">';
        $html .='<input type="text" class="form-control" name="job_title" value="'.@$values->job_title.'" '.$mode.$border.'>';
        $html .='</div>';

        // Email addresses
        $emails = [];
        if (empty($mode)) { $firstEmailCustomMode = 'readonly=""'; }
        else { $firstEmailCustomMode = $mode; }
        if (!empty($values->email)) { $emails = json_decode($values->email); }

        if ( isset($updatedVal->email[0]) && ($updatedVal->email[0] != $emails[0]) ){
            $emails[0] = $updatedVal->email[0];
            $border = ' style="border:1px solid red;"';
        } else { $border = ''; }
        $html .='<div class="form-group">';
        $html .='<label for="">Email 1</label>';
        if ( $type == 'edit' ) 
        $html .='<input type="hidden" class="form-control" name="h_email[]" value="'.@$emails[0].'">';
        $html .='<input type="text" class="form-control" name="email[]" value="'.@$emails[0].'" '.$firstEmailCustomMode.$border.'>';
        $html .='</div>';
        
        if ( isset($updatedVal->email[1]) && ($updatedVal->email[1] != $emails[1]) ){
            $emails[1] = $updatedVal->email[1];
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .='<div class="form-group">';
        $html .='<label for="">Email 2</label>';
        if ( $type == 'edit' ) 
        $html .='<input type="hidden" class="form-control" name="h_email[]" value="'.@$emails[1].'">';
        $html .='<input type="text" class="form-control" name="email[]" value="'.@$emails[1].'" '.$mode.$border.'>';
        $html .='</div>';
        
        if (isset($updatedVal->phone) ){
            $values->phone = $updatedVal->phone;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .='<div class="form-group">';
        $html .='<label for="">Phone</label>';
        if ( $type == 'edit' ) 
        $html .='<input type="hidden" class="form-control" name="h_phone" value="'.@$values->phone.'">';
        $html .='<input type="text" class="form-control" name="phone" value="'.@$values->phone.'" '.$mode.$border.'>';
        $html .='</div>';
        
        if (isset($updatedVal->website) ){
            $values->website = $updatedVal->website;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .='<div class="form-group">';
        $html .='<label for="">Website</label>';
        if ( $type == 'edit' ) 
        $html .='<input type="hidden" class="form-control" name="h_website" value="'.@$values->website.'">';
        $html .='<input type="text" class="form-control" name="website" value="'.@$values->website.'" '.$mode.$border.'>';
        $html .='</div>';
        
        if (isset($updatedVal->_YogaStudio) ){
            $values->_YogaStudio = $updatedVal->_YogaStudio;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .='<div class="form-group">';
        $html .='<label for="">Yoga Studio</label>';
        if ( $type == 'edit' ) 
        $html .='<input type="hidden" class="form-control" name="h__YogaStudio" value="'.@$values->_YogaStudio.'">';
        $html .='<input type="text" class="form-control" name="_YogaStudio" value="'.@$values->_YogaStudio.'" '.$mode.$border.'>';
        $html .='</div>';
        
        if (isset($updatedVal->bio) ){
            $values->bio = $updatedVal->bio;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .='<div class="form-group">';
        $html .='<label for="">BIO</label>';
        if ( $type == 'edit' ) 
        $html .='<input type="hidden" class="form-control" name="h_bio" value="'.@$values->bio.'">';
        $html .='<textarea class="form-control" name="bio" '.$mode.$border.'>'.@$values->bio.'</textarea>';
        $html .='</div>';
        
        $html .='</div>';
        $socialUpdatedVal = isset($updatedVal->social) ? $updatedVal->social : false; 
        $html .= $this->social_form($socials,$type, $socialUpdatedVal);
        if ( $type == 'edit' ){
            $html .='<div class="text-right">';
            $html .= '<button type="submit" id="form-edit" class="btn btn-success" name="update_contact_button" '.$is_disabled.'>Save</button>';
            $html .='</div>';
        }

        // Update Apply or cancel button
        if (!empty($updatedVal) && $ui_type == 'backend') { $html .= $this->update_reject_buttons($values->contact_id); }
        $html .='</form>';
        return $html;
    }

    public function basic_tags($json = ''){
        $html = '';
        $this->tags = $this->get_structured_tags($json);
        //echo "<pre>"; print_r($this->tags); echo "</pre>";
        $html .= '<div class="form-group">';
        $html .= '<label for="">Our Tags</label>';
        $html .= '<input type="text" class="form-control" name="tags" value="'. @$this->tags .'">';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="">Specialities tag</label>';
        $html .= '<input type="text" class="form-control" name="special_tags" value="">';
        $html .= '</div>';
        return $html;
    }

    public function all_tags($allTags){
        $html = '';
        if (!empty($allTags)) {
            foreach ($allTags as $tName => $tags) {
                $html .= '<div class="form-section">';
                $html .= '<legend>Tag</legend>';
                $html .= '<div class="form-group">';
                $html .= '<label for="">'.$tName.'</label>';
                if ( !empty($tags) ) {
                    $html .= '<select name="tags['.$tName.'][]" class="select2-multiple" multiple="multiple" >';
                    foreach ($tags as $key => $tag) {
                        $html .= '<option value="'.$key.'">'.$tag.'</option>';
                    }
                    $html .= '</select>';
                } else {
                    $html .= '<input type="text" class="form-control" name="tags" value="'. @$this->tags .'">';
                }
                $html .= '</div>';
                $html .= '</div>';
            }
            return $html;
        } else{
            return false;
        }
    }
    // Address form
    public function address_form($addr='', $type='readonly', $ui_type='backend', $contactID=0, $updatedVal=''){
        // echo "<pre>"; print_r($updatedVal); echo "</pre>";
        $teacher    = new tn_teachers;
        $html = '';
        $mode = ''; if ($type == 'readonly') { $mode = 'disabled'; }
        if ( $teacher->is_update_pending($contactID) ) { $is_disabled = 'disabled'; }
        else { $is_disabled = ''; }
        // $html .= '<form action="" method="post" role="form">';
        $html .= '<div class="form-section">';
        $html .= '<legend>Address Info</legend>';

        if(isset($updatedVal->line1)) {
            $addr->line1 = $updatedVal->line1;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">Address Line</label>';
        if ( $type == 'edit' ) $html .='<input type="hidden" class="form-control" name="h_line1" value="'.@$addr->line1.'">';
        $html .= '<input type="text" class="form-control" value="'.$addr->line1.'" name="line1" '.$mode.$border.'>';
        $html .= '</div>';

        if(isset($updatedVal->line2)) {
            $addr->line2 = $updatedVal->line2;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">Street</label>';
        if ( $type == 'edit' ) $html .='<input type="hidden" class="form-control" name="h_line2" value="'.@$addr->line2.'">';
        $html .= '<input type="text" class="form-control" value="'.$addr->line2.'" name="line2" '.$mode.$border.'>';
        $html .= '</div>';

        if(isset($updatedVal->locality)) {
            $addr->locality = $updatedVal->locality;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">City</label>';
        if ( $type == 'edit' ) $html .='<input type="hidden" class="form-control" name="h_locality" value="'.@$addr->locality.'">';
        $html .= '<input type="text" class="form-control" value="'.$addr->locality.'" name="locality" '.$mode.$border.'>';
        $html .= '</div>';

        if(isset($updatedVal->region)) {
            $addr->region = $updatedVal->region;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">State</label>';
        if ( $type == 'edit' ) $html .='<input type="hidden" class="form-control" name="h_region" value="'.@$addr->region.'">';
        $html .= '<input type="text" class="form-control" value="'.$addr->region.'" name="region" '.$mode.$border.'>';
        $html .= '</div>';

        if(isset($updatedVal->postal_code)) {
            $addr->postal_code = $updatedVal->postal_code;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">ZIP</label>';
        if ( $type == 'edit' ) $html .='<input type="hidden" class="form-control" name="h_postal_code" value="'.@$addr->postal_code.'">';
        $html .= '<input type="text" class="form-control" value="'.$addr->postal_code.'" name="postal_code" '.$mode.$border.'>';
        $html .= '</div>';

        if(isset($updatedVal->country_code)) {
            $addr->country_code = $updatedVal->country_code;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">Country</label>';
        if ( $type == 'edit' ) $html .='<input type="hidden" class="form-control" name="h_country_code" value="'.@$addr->country_code.'">';
        $html .= '<input type="text" class="form-control" value="'.$addr->country_code.'" name="country_code" '.$mode.$border.'>';
        $html .= '</div>';

        $html .= '</div>';
        // $html .= '</form>';

        return $html;
    }
    public function address_form_old($addr='', $type='readonly', $ui_type='backend', $contactID=0, $updatedVal=''){
        $teacher    = new tn_teachers;
        $html = '';
        $mode = ''; if ($type == 'readonly') { $mode = 'disabled'; }
        if ( $teacher->is_update_pending($contactID) ) { $is_disabled = 'disabled'; }
        else { $is_disabled = ''; }
        $html .= '<form action="" method="post" role="form">';
        $html .= '<div class="form-section">';
        $html .= '<legend>Address Info</legend>';

        if(isset($updatedVal->line1)) {
            $addr->line1 = $updatedVal->line1;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">Address Line</label>';
        $html .= '<input type="text" class="form-control" value="'.$addr->line1.'" name="line1" '.$mode.$border.'>';
        $html .= '</div>';

        if(isset($updatedVal->line2)) {
            $addr->line2 = $updatedVal->line2;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">Street</label>';
        $html .= '<input type="text" class="form-control" value="'.$addr->line2.'" name="line2" '.$mode.$border.'>';
        $html .= '</div>';

        if(isset($updatedVal->locality)) {
            $addr->locality = $updatedVal->locality;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">City</label>';
        $html .= '<input type="text" class="form-control" value="'.$addr->locality.'" name="locality" '.$mode.$border.'>';
        $html .= '</div>';

        if(isset($updatedVal->region)) {
            $addr->region = $updatedVal->region;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">State</label>';
        $html .= '<input type="text" class="form-control" value="'.$addr->region.'" name="region" '.$mode.$border.'>';
        $html .= '</div>';

        if(isset($updatedVal->postal_code)) {
            $addr->postal_code = $updatedVal->postal_code;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">ZIP</label>';
        $html .= '<input type="text" class="form-control" value="'.$addr->postal_code.'" name="postal_code" '.$mode.$border.'>';
        $html .= '</div>';

        if(isset($updatedVal->country_code)) {
            $addr->country_code = $updatedVal->country_code;
            $border = ' style="border:1px solid red;"'; 
        } else { $border = ''; }
        $html .= '<div class="form-group">';
        $html .= '<label for="">Country</label>';
        $html .= '<input type="text" class="form-control" value="'.$addr->country_code.'" name="country_code" '.$mode.$border.'>';
        $html .= '</div>';
        if ($type == 'edit') {
            if ( $mode == 'disabled' ) {
                $html .= '<div class="text-right">';
                $html .= '<button type="button" id="address-edit" class="btn btn-primary" name=""'.$is_disabled.'>Edit</button>';
                $html .= '<button type="submit" class="btn btn-success form-save hidden" name="update_addrs_btn"'.$is_disabled.'>Save</button>';
                $html .= '</div>';
            } else {
                $html .= '<div class="text-right">';
                $html .= '<button type="submit" class="btn btn-success form-save" name="update_addrs_btn"'.$is_disabled.'>Save</button>';
                $html .= '</div>';
            }
        }

        // Update Apply or cancel button
        if (!empty($updatedVal) && $ui_type == 'backend') { $html .= $this->update_reject_buttons($contactID); }

        $html .= '</div>';
        $html .= '</form>';

        return $html;
    }
    public function social_form($values='',$formType='edit', $updatedVal='') {
        // echo "<pre>"; print_r($updatedVal); echo "</pre>";
        $mode = ''; if ($formType == 'readonly') { $mode = 'disabled'; }
        $social = new tn_social;
        $types = $social->social_types(); // fb, tw, ln
        if ($types) {
            $html = '';
            $counter = 0;
            $html .= '<div class="form-section">';
            $html .= '<legend>Social Media</legend>';
            foreach ($types as $type) {
                $socialID = $type->social_id;
                $name = $values[$counter]->social_name;
                $value = $values[$counter]->account_name;
                $crm_id = $values[$counter]->social_crm_id;
                $border = '';
                if ($updatedVal) {
                    if ( $updatedVal->$socialID != $value && $formType != 'edit') {
                        $border = ' style="border:1px solid red;"';
                        $value = $updatedVal->$socialID;
                    }
                }
                // if (!empty($updatedVal)) { echo "<br>name : $name == value : $value == socialID : $socialID == updatedVal".$updatedVal->$socialID; }

                $html .= '<div class="form-group">';
                $html .= '<label for="">'.$type->social_name.'</label>';
                if ($formType == 'edit') {
                    $html .= '<input type="hidden" class="form-control" name="social_crm_id['.$socialID.']" value="'.$crm_id.'">';
                    $html .= '<input type="hidden" class="form-control" name="h_social['.$socialID.']" value="'.$value.'">';
                }
                $html .= '<input type="text" class="form-control" name="social['.$socialID.']" value="'.$value.'" '.$mode.$border.'>';

                // $html .= '<div class="input-group">';
                // $html .= '<span class="input-group-addon" id="basic-addon3">https://'.strtolower($name).'/</span>';
                // $html .= '<input type="text" class="form-control" name="social['.$socialID.']" value="'.$value.'" '.$mode.$border.'>';
                // $html .= '</div>';

                $html .= '</div>';
                $counter++;
            }
            $html .= '</div>';
            return $html;
        } else return false;
    }

    // Update Apply or cancel button
    public function update_reject_buttons($contactID){
        $html = ''; 
        $html .='<div class="text-right">';
        $html .= '<a href="'.esc_url(admin_url("?page=tn-teachers&update_reject=".$contactID)).'" class="btn btn-danger">Reject Update</a>';
        $html .= '&nbsp; &nbsp; &nbsp; ';
        $html .= '<a href="'.admin_url("?page=tn-teachers&update_apply=".$contactID).'" class="btn btn-success">Apply Update</a>';
        $html .='</div>';
        return $html;
    }

    // Teacher details
    public function teachers_details($data){
        $html = $address = $lat = $lng = $short_adds = '';
        $social     = new tn_social;
        $teachers   = new tn_teachers;
        // echo "<pre>"; print_r($data); echo "</pre>";
        if ( !empty($data->adds[0]) ) {
            // Full address
            $address .= !empty(trim($data->adds[0]->line1)) && trim($data->adds[0]->line1)  != 'NO' ? trim($data->adds[0]->line1).' ' : ' ';
            $address .= !empty(trim($data->adds[0]->line2))     && trim($data->adds[0]->line2)  != 'NO' ? trim($data->adds[0]->line2).' ' : ' ';
            $address .= !empty(trim($data->adds[0]->locality))  && trim($data->adds[0]->locality)   != 'NO' ? trim($data->adds[0]->locality).' ' : ' ';
            $address .= !empty(trim($data->adds[0]->region))    && trim($data->adds[0]->region)     != 'NO' ? trim($data->adds[0]->region).' ' : ' ';
            $address .= !empty(trim($data->adds[0]->postal_code))   && trim($data->adds[0]->postal_code)    != 'NO' ? trim($data->adds[0]->postal_code).' ' : ' ';
            $address .= !empty(trim($data->adds[0]->country_code))  && trim($data->adds[0]->country_code)   != 'NO' ? trim($data->adds[0]->country_code).' ' : ' ';

            // Short address after name
            $short_adds .= !empty(trim($data->adds[0]->locality))   && trim($data->adds[0]->locality)   != 'NO' ? trim($data->adds[0]->locality).' ' : ' ';
            $short_adds .= !empty(trim($data->adds[0]->region))     && trim($data->adds[0]->region)     != 'NO' ? trim($data->adds[0]->region).' ' : ' ';
            $short_adds .= !empty(trim($data->adds[0]->country_code))   && trim($data->adds[0]->country_code)   != 'NO' ? trim($data->adds[0]->country_code).' ' : ' ';
        }
        $address =  !empty(trim($address)) ? trim($address) : '';
        $avatar = !empty(trim($data->avatar)) ? TNPLUGINURL.'uploads/'.trim($data->avatar) : TNPLUGINURL.'assets/images/avatar.png';
        $yogaIcn = TNPLUGINURL.'assets/images/hinduist-yoga-position.png';
        $fullName = $data->given_name.' '.$data->family_name;

        $html .= '<div class="row">';
        $html .= '<div class="col-sm-12 col-xs-12 text-center">';
        $html .= '<h1 class="lg-title text-uppercase">'.$fullName.'</h1>';
        if ( !empty($data->job_title) && $data->job_title != 'NO' && $data->is_show_title == "YES" ) { $html .= '<p class="details-job-title">'.$data->job_title.'</p>'; } // Job title
        $html .= '<p class="details-sub-title">'.$short_adds.'</p>'; // partial address
        $html .= '</div>'; // col-12
        $html .= '</div>'; // row

        $html .= '<div class="row margin-top-10">';
        $html .= '<div class="row">';
        $html .= '<div class="col-lg-10 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">';
        $html .= '<div class="col-sm-5 col-xs-12">';
        if (!empty($avatar)) {
            $html .= '<div class="details-profile-image">';
            $html .= '<img class="img-responsive" src="'.$avatar.'">';
            $html .= '</div>';
        }

        // $html .= '<p class="certificate text-center">Yoga Medicine certifications</p>'; // static part
        $html .= '<p class="certificate text-center">&nbsp;</p>'; // static part
        $html .= '</div>';

        $html .= '<div class="col-sm-7  col-xs-12">';
        if (!empty($data->bio) && trim($data->bio) != 'NO') {
            $html .= '<p class="details-bio">'.$data->bio.'</p>';
        }
        $html .= '<div class="details-profile-info">';

        $website = trim($data->website);

        // Full address or mailing address
        if (!empty($address)) {
            $html .= '<div class="post-item-content">';
            $html .= '<div class="pull-left lg-w8"> ';
            $html .= '<span class="glyphicon glyphicon-map-marker margin-right-5" style="padding: 5px 2px;"></span> ';
            $html .= '</div>';
            $html .= '<div class="pull-left lg-w92"> '.$address.' </div>';
            $html .= '</div>';
        }

        $email = trim($data->email);
        if ( !empty($email) && $email != 'NO' ) { 
            $email = json_decode($email);
            if (!empty($email[1])) {
                $html .= '<div class="post-item-content">';
                $html .= '<div class="pull-left lg-w8"> ';
                $html .= '<span class="glyphicon glyphicon-envelope margin-right-5" style="padding: 4px 3px;"></span>';
                $html .= '</div>';
                $html .= '<div class="pull-left lg-w92"> '.$email[1].' .</div>';
                $html .= '</div>';
            }else {
                $html .= '<div class="post-item-content">';
                $html .= '<div class="pull-left lg-w8"> ';
                $html .= '<span class="glyphicon glyphicon-envelope margin-right-5" style="padding: 4px 3px;"></span>';
                $html .= '</div>';
                $html .= '<div class="pull-left lg-w92"> '.$email[0].' .</div>';
                $html .= '</div>';
            }
        }

        if ( !empty($website) && $website != 'NO' ) { 
            $html .= '<div class="post-item-content">';
            $html .= '<div class="pull-left lg-w8"> ';
            $html .= '<span class="margin-right-5">';
            $html .= '<i class="fa fa-mouse-pointer" aria-hidden="true" style="padding: 5px 6px;"></i>';
            $html .= '</span> ';
            $html .= '</div>';
            $html .= '<div class="pull-left lg-w92">'.$website.'</div>';
            $html .= '</div>';
        }


        $phone = trim($data->phone);
        if ( !empty($phone) && $phone != 'NO' ) { 
            $html .= '<div class="post-item-content">';
            $html .= '<div class="pull-left lg-w8"> ';
            $html .= '<span class="glyphicon glyphicon-earphone margin-right-5" style="padding: 4px 3px;"></span> ';
            $html .= '</div>';
            $html .= '<div class="pull-left lg-w92"> '.$phone.' </div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        // Social icons
        $html .= $social->user_socials_html($data->contact_id);
        $html .= '</div>';  
        $html .= '</div>';
        $html .= '</div>';

        if ( !empty($data->tags) ) {
            $hours = $specialities = [];
            foreach ($data->tags as $value) {
                if ($value->category_id == 1) { // Hour category array
                    // $hours[] = str_replace('HR Complete', '', $value->tag_name);
                    $hours[$value->icon] = $value->bi_line; 
                }
                // Speciality category array
                else { $specialities[$value->icon] = $value->bi_line; }
            }
            // if (empty($specialities)) { $specialities =  array('423.png'=>'SH Bi line', '303.png'=>'IQ Bi line', '305.png'=>'HW Bi line'); }
            // echo "Hours : "; var_dump($hours); echo "<br><br> Specialities : "; var_dump($specialities);
            $html .= '<div class="row details-specification">';
            $html .= '<div class="col-lg-10 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">';
            if (!empty($hours)) {
                $html .= '<div class="col-sm-6 col-xs-12">';
                $html .= '<div class="working-hour">';
                $html .= '<p>Hours</p>';
                $html .= '<ul>';
                foreach ($hours as $key=>$value) { 
                    //$html .= '<li>'.$value.'</li>'; 
                    $html .= '<li><a href="#" data-toggle="ctooltip" title="'. $value .'"> '; 
                    if (!empty($key)) { $imgUrl = TNPLUGINURL.'/assets/images/icons/'.$key; }
                    else { $imgUrl = TNPLUGINURL.'/assets/images/icons/default.jpg'; }
                    $html .= '<img src="'.$imgUrl.'" alt="icon" class="img-round">';
                    $html .= ' </a></li>';
                }
                $html .= '</ul>';
                $html .= '</div>';
                $html .= '</div>';
            }
            
            if (!empty($specialities)) {
                $html .= '<div class="col-sm-6  col-xs-12">';
                $html .= '<div class="working-hour">';
                $html .= '<p>Completed Trainings</p>';
                $html .= '<ul>';
                foreach ($specialities as $key => $value) { 
                    //$html .= '<li>'.$value.'</li>'; 
                    $html .= '<li><a href="#" data-toggle="ctooltip" title="'. $value .'"> '; 
                    if (!empty($key)) { $imgUrl = TNPLUGINURL.'/assets/images/icons/'.$key; }
                    else { $imgUrl = TNPLUGINURL.'/assets/images/icons/default.jpg'; }
                    $html .= '<img src="'.$imgUrl.'" alt="icon" class="img-round">';
                    $html .= ' </a></li>';
                }
                $html .= '</ul>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            // $html .= '</div>';
        }
        return $html;
    }
}