<?php 
/**
* User Interface class
*/
class tn_ui_frontend extends tn_db{
	private $breaking_number;
	function __construct(){
		// parent::__construct();
		$this->breaking_number = 3;
	}
	public function frontend_single_teacher($data, $counter=0){
		$html = $address = $lat = $lng = '';
		$hours = $specialities = [];

		$tags 		= new tn_tag;
		$teachers 	= new tn_teachers;

		$data->tags = $tags->user_tags($data->contact_id);
		if ( !empty($data->tags) ) {
			foreach ($data->tags as $value) {
				if ($value->category_id == 1) { $hours[$value->icon] = $value->bi_line; } // Hour category array
				else { $specialities[$value->icon] = $value->bi_line; } // Speciality category array
			}
		}
		// var_dump($hours); echo "<br><br>"; var_dump($specialities);
		// if (empty($specialities)) { $specialities =  array('423.png'=>'SH Bi line', '303.png'=>'IQ Bi line', '305.png'=>'HW Bi line'); }
		// echo "<pre>"; print_r($data); echo "</pre>";

		if ( !empty($data->adds[0]) ) {
			$address .= !empty(trim($data->adds[0]->line1)) && trim($data->adds[0]->line1) 	!= 'NO' ? trim($data->adds[0]->line1).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->line2)) 	&& trim($data->adds[0]->line2) 	!= 'NO' ? trim($data->adds[0]->line2).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->locality)) 	&& trim($data->adds[0]->locality) 	!= 'NO' ? trim($data->adds[0]->locality).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->region)) 	&& trim($data->adds[0]->region) 	!= 'NO' ? trim($data->adds[0]->region).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->postal_code)) 	&& trim($data->adds[0]->postal_code) 	!= 'NO' ? trim($data->adds[0]->postal_code).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->country_code)) 	&& trim($data->adds[0]->country_code) 	!= 'NO' ? trim($data->adds[0]->country_code).' ' : ' ';
			$lat 	  = !empty(trim($data->adds[0]->lat)) 	&& trim($data->adds[0]->lat) 	!= 'NO' ? trim($data->adds[0]->lat).' ' : '';
			$lng 	  = !empty(trim($data->adds[0]->lng)) 	&& trim($data->adds[0]->lng) 	!= 'NO' ? trim($data->adds[0]->lng).' ' : '';
		}
		$address =  !empty(trim($address)) ? trim($address) : '';
		$url = site_url('/profile-teacher/?id='.$data->contact_id);
		$btn = "<p class='btnContainer mapProfileBtn text-cernter' style='margin:0;'><a href='#result-div' data-id='".$data->contact_id."' class='btn btn-primary btn-xs'>view profile</a></p>";
		if (!empty($data->avatar)) {
			$img = "<img src='".TNPLUGINURL.'uploads/'.$data->avatar."' alt='Profie Image' style='width:60px;height:60px;border-radius:50%;'>";
		} else{ 
			$defaultImg = TNPLUGINURL.'/assets/images/avatar.png';
			$img = "<img src='".$defaultImg."' alt='Profie Image' style='width:60px;height:60px;border-radius:50%;'>";
		}

		$job_title = !empty($data->job_title) && $data->job_title != 'NO' && $data->is_show_title == "YES" ? '<br>'. $data->job_title : '';

		$message = "'<center>".addslashes($img)."<br><strong> $data->given_name $data->family_name </strong> $job_title <br> $address <br><br>".addslashes($btn)."</center>'";


		// $avatar = !empty(trim($data->avatar)) ? trim(trim($data->avatar)) : 'http://via.placeholder.com/150x150?text=AVATAR';
		$avatar = !empty(trim($data->avatar)) ? TNPLUGINURL.'uploads/'.trim($data->avatar) : TNPLUGINURL.'/assets/images/avatar.png';
		$profileSingle = esc_url( site_url('/profile-teacher/?id='.$data->contact_id) );
		$fullName = $data->given_name.' '.$data->family_name;

		$html .= '<div id="teacher-'.$data->contact_id.'" class="media teacher-list-item common_location"';
		if ( !empty($lat) || !empty($lng) ) {
			$html .= ' onclick="customSetCenter('.$message.','.$lat.','.$lng.','.$counter.')"';
		}
		$html .= '>';
		$html .= '<div class="media-body">';

		$html .= '<div class="row">';
		$html .= '<div class="col-sm-2 col-xs-12">';
		$html .= '<div class="media-left"><img class="media-object" src="'.$avatar.'" alt="..."></div>';
		$html .= '</div>';
		$html .= '<div class="col-sm-5 col-xs-12 no-padding-left">';
		$html .= '<h3 class="media-heading side-info">';
		$html .= '<a href="#result-div" data-id='.$data->contact_id.'> '.$fullName.' </a>';
		if ( !empty($data->job_title) && $data->job_title != 'NO' && $data->is_show_title == "YES" ) { $html .= '<div class="jobTitle"> '.$data->job_title.'</div>'; }
		$html .= '</h3>';
		$html .= '</div>';

		if (!empty($hours)) {
			$html .= '<div class="col-sm-5 col-xs-12 hidden-xs">';
			$html .= '<div class="working-hour margin-left-10">';
			$html .= '<p>Hours</p>';
			$html .= '<ul>';
			foreach ($hours as $key => $value) { 
				//$html .= '<li><a href="#" data-toggle="tooltip" title="'. $value .'"> '.$key.' </a></li>'; 
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

		$html .= '<div class="row">';
		$html .= '<div class="col-sm-12 col-xs-12">';
		$html .= '<div class="search-w70">';

		if (!empty(trim($address))) {
			$html .= '<div class="post-item-content">';
			$html .= '<div class="w10 pull-left"> <span class="glyphicon glyphicon-map-marker margin-right-5"></span> </div>';
			$html .= '<div class="w90 pull-left"> '.$address.' </div>';
			$html .= '</div>';
		}

		$email = trim($data->email);
		if ( !empty($email) && $email != 'NO' ) { 
			$email = json_decode($email);
			if (!empty($email[1])) {
				$html .= '<div class="post-item-content">';
				$html .= '<div class="w10 pull-left"> <span class="glyphicon glyphicon-envelope margin-right-5"></span> </div>';
				$html .= '<div class="w90 pull-left"> '.$email[1].' </div>';
				$html .= '</div>';
			} else {
				$html .= '<div class="post-item-content">';
				$html .= '<div class="w10 pull-left"> <span class="glyphicon glyphicon-envelope margin-right-5"></span> </div>';
				$html .= '<div class="w90 pull-left"> '.$email[0].' </div>';
				$html .= '</div>';
			}
		}

		$website = trim($data->website);
		if ( !empty($website) && $website != 'NO' ) { 
			$html .= '<div class="post-item-content">';
			$html .= '<div class="w10 pull-left"> <i class="fa fa-mouse-pointer" aria-hidden="true"></i> </div>';
			$html .= '<div class="w90 pull-left">'.$website.'</div>';
			$html .= '</div>';
		}

		$phone = trim($data->phone);
		if ( !empty($phone) && $phone != 'NO' ) { 
			$html .= '<div class="post-item-content">';
			$html .= '<div class="w10 pull-left"> <span class="glyphicon glyphicon-earphone margin-right-5"></span> </div>';
			$html .= '<div class="w90 pull-left"> '.$phone.' </div>';
			$html .= '</div>';
		}
		$html .= '</div>';

		if (!empty($hours)) {
			$html .= '<div class="col-sm-5 col-xs-12 visible-xs margin-top-30 no-padding">';
			$html .= '<div class="working-hour margin-left-10">';
			$html .= '<p>Hours</p>';
			$html .= '<ul>';

			$counter = 1;
			$plus_icn = TNPLUGINURL.'/assets/images/add.png';
			$minus_icn = TNPLUGINURL.'/assets/images/minus.png';
			$plus_btn = '<li class="plus_btn"><a href="javascript:;" data-id='.$data->contact_id.' class="expand-btn"><img src="'.$plus_icn.'" alt="icon" class="img-round"></a></li>';
			$minus_btn = '<li class="minus_btn hidden"><a href="javascript:;" data-id='.$data->contact_id.' class="expand-btn"><img src="'.$minus_icn.'" alt="icon" class="img-round"></a></li>';
			$breaking_number = $this->breaking_number;
			$total_spacialities = count($specialities);
			foreach ($hours as $key => $value) { 
				$hidden_class = $total_spacialities > $breaking_number && $counter > $breaking_number? 'extraIcons hidden' : '';
				$html .= '<li class="hourIcons '.$hidden_class.'"><a href="#" data-toggle="ctooltip" title="'. $value .'"> '; 
				if (!empty($key)) { $imgUrl = TNPLUGINURL.'/assets/images/icons/'.$key; }
				else { $imgUrl = TNPLUGINURL.'/assets/images/icons/default.jpg'; }
				$html .= '<img src="'.$imgUrl.'" alt="icon" class="img-round">';
				$html .= ' </a></li>';
				$html .= $total_spacialities > $breaking_number && $counter == $breaking_number ? $plus_btn : '';
				$html .= $total_spacialities > $breaking_number && $counter == $breaking_number ? $minus_btn : '';
				$counter++;
			}

			$html .= '</ul>';
			$html .= '</div>';
			$html .= '</div>';
		}

		if (!empty($specialities)) {
			$html .= '<div class="search-w30">';
			$html .= '<div class="working-hour speciality conditionalHiddenWrapper">';
			$html .= '<p>Completed Trainings</p>';
			$html .= '<ul>';

			$counter = 1;
			$plus_icn = TNPLUGINURL.'/assets/images/add.png';
			$minus_icn = TNPLUGINURL.'/assets/images/minus.png';
			$plus_btn = '<li class="plus_btn"><a href="javascript:;" data-id='.$data->contact_id.' class="expand-btn"><img src="'.$plus_icn.'" alt="icon" class="img-round"></a></li>';
			$minus_btn = '<li class="minus_btn hidden"><a href="javascript:;" data-id='.$data->contact_id.' class="expand-btn"><img src="'.$minus_icn.'" alt="icon" class="img-round"></a></li>';
			$breaking_number = $this->breaking_number;
			$total_spacialities = count($specialities);
			foreach ($specialities as $key => $value) { 
				$hidden_class = $total_spacialities > $breaking_number && $counter > $breaking_number? 'extraIcons hidden' : '';
				$html .= '<li class="specialityIcons '.$hidden_class.'"><a href="#" data-toggle="ctooltip" title="'. $value .'"> '; 
				if (!empty($key)) { $imgUrl = TNPLUGINURL.'/assets/images/icons/'.$key; }
				else { $imgUrl = TNPLUGINURL.'/assets/images/icons/default.jpg'; }
				$html .= '<img src="'.$imgUrl.'" alt="icon" class="img-round">';
				$html .= ' </a></li>';
				$html .= $total_spacialities > $breaking_number && $counter == $breaking_number ? $plus_btn : '';
				$html .= $total_spacialities > $breaking_number && $counter == $breaking_number ? $minus_btn : '';
				$counter++;
			}

			$html .= '</ul>';
			$html .= '</div>';
			$html .= '</div>';
		}
		$html .= '</div>';

		//$html .= '<div class="col-sm-5 col-xs-12">';
		/*if (!empty(trim($address))) {
			$html .= '<div class="post-item-content">';
			$html .= '<div class="w10 pull-left"> <span class="glyphicon glyphicon-home margin-right-5"></span> </div>';
			$html .= '<div class="w90 pull-left"> '.$address.' </div>';
			$html .= '</div>';
		}*/

		/*if ( !empty($data->_YogaStudio) && strtolower($data->_YogaStudio) != 'no' ) {
			$html .= '<div class="post-item-content">';
			$html .= '<div class="w10 pull-left"> <img class="sm-icon" src="'.TNPLUGINURL.'/assets/images/hinduist-yoga-position.png"> </div>';
			$html .= '<div class="w90 pull-left">'.$data->_YogaStudio.'</div>';
			$html .= '</div>';
		}*/

		

		//$html .= '</div>';

		// $allTags = $tags->user_tags_html($data->contact_id);
		// if (!empty($allTags)) {
		// 	$html .= '<div class="col-sm-12 col-xs-12">';
		// 	$html .= '<div class="post-item-content">';
		// 	$html .= '<div class="w5 pull-left"> <span class="glyphicon glyphicon-tag pull-left margin-right-5"></span> </div>';
		// 	$html .= '<div class="w95 pull-left">';
		// 	$html .= $allTags;
		// 	$html .= '</div>';
		// 	$html .= '</div>';
		// 	$html .= '</div>';
		// }

		// $socialIcons = $social->user_socials_html($data->contact_id);
		// if (!empty($socialIcons)) {
		// 	$html .= '<div class="col-sm-12 col-xs-12">';
		// 	$html .= $socialIcons;
		// 	$html .= '</div>';
		// }

		$html .= '</div>';

		$html .= '</div>';
		$html .= '</div>';
		return $html;
	}
	public function view_profile($ID, $json = '', $tags = ''){
        $html       = '';
        $address    = '';
        // $values     = $this->get_structured_info($json);
        $values     = $json;
        // echo "<pre>"; print_r($values); echo "</pre>";
        $adds       = $this->formated_address($values['adds'][0], '<br>', 'profile');
        $tagvals    = $this->get_structured_tags($tags, 'ARRAY');
        // print_r($tagvals);
        $address    .= !empty($values['StreetAddress1']) ? $values['StreetAddress1'].', ' :'';
        $address    .= !empty($values['City']) ? $values['City'].', ' :'';
        $address    .= !empty($values['State']) ? $values['State'].', ' :'';
        $address    .= !empty($values['Country']) ? $values['Country'].', ' :'';
        $address    .= !empty($values['PostalCode']) ? $values['PostalCode'].', ' :'';
        $address     = rtrim($address, ', ');

        $html .= '<div class="container">';
        $html .= '<div class="row">';
        $html .= '<div class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12">';
        $html .= '<div class="profile">';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-6 profile-border">';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-12">';
        $html .= '<div class="profile-header">';
        $html .= '<img class="profile-image" src="https://yogamedicine.com/wp-content/uploads/2016/07/color-less-mc-kd-magenta.jpg" alt="...">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="row">';
        $html .= '<div class="col-sm-12 text-center">';
        $html .= '<p class="profile-name">'.$values['FirstName'].' '. $values['LastName'] .'</p>';
        $html .= '<p class="profile-common-info">'. $values['JobTitle'].'ff</p>';
        $html .= '<p class="profile-common-info">'. $values['Email'].'</p>';
        $html .= '<p class="profile-common-info">'. $values['Phone1'].'</p>';
        $html .= '<p class="profile-common-info"> <a href="#"> '. $values['Website'] .' </a> </p> ';

        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-12">';
        $html .= '<div class="profile-social">';
        $html .= '<a class="btn btn-primary btn-twitter btn-sm" href="'.$values['Facebook'].'">';
        $html .= '<i class="fa fa-facebook"></i>';
        $html .= '</a>';
        $html .= '<a class="btn btn-info btn-sm" rel="publisher" href="'.$values['Twitter'].'">';
        $html .= '<i class="fa fa-twitter"></i>';
        $html .= '</a>';
        $html .= '<a class="btn btn-danger btn-sm" rel="publisher" href="'.$values['LinkedIn'].'">';
        $html .= '<i class="fa fa-linkedin"></i>';
        $html .= '</a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<br>';
        $html .= '</div>';
        $html .= '<div class="col-sm-6">';
        $html .= '<div class="row lg-no-margin-left">';
        $html .= '<div class="col-sm-12 text-center">';
        $html .= 'YogaStudio : '.$values['_YogaStudio'].'<br>';
        $html .= $address;
        $html .='</div>';
        $html .= '</div>';
        $html .= '<br>';

        if ($tagvals) {
            $html .= '<div class="row lg-no-margin-left">';
            $html .= '<div class="col-sm-12">';
            $html .= '<ul class="profile-tag-list">';
            // foreach ($tagvals as $tagval) { $html .= '<li>'. $tagval .'</li>'; }
            foreach ($tagvals as $key => $value) {
            	foreach ($value as $ind => $val) {
            		$html .= '<li>'. $val .'</li>';
            	}
            }
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= $this->profile_buttons($ID);
        return $html;
    }
    private function profile_buttons($ID, $pageName='login-teacher') {
        $html = '';
        if ($ID) {
	        $editPageUrl = esc_url(site_url('/'.$pageName.'/?id='.$ID));

	        $html .= '<div class="dropdown profile-login">';
	        $html .= '<button class="dropbtn btn btn-default" title="User Name">';
	        $html .= '<img class="profile-avator" src="'.TNPLUGINURL.'/assets/images/avatar_2x.png">';
	        $html .= '</button>';
	        $html .= '<div class="profile-login-content">';
	        $html .= '<a href="'. $editPageUrl .'">Edit </a>';
	        $html .= '<a href="#">Log Out</a>';
	        $html .= '</div>';
	        $html .= '</div>';
	        return $html;
        } else return false;
    }
    // pagination
	public function pagination($pages=''){
		$pagination = '<div class="row text-center">';
		$pagination .= '<ul class="pagination">';
		$pagination .= '<li><a href="#">Previous</a></li>';
		$pagination .= '<li><a href="#">Next</a></li>';
		$pagination .= '</ul>';
		$pagination .= '</div>';
		return $pagination;
	}
	public function teachers_details($data){
		$html = $address = $lat = $lng = $short_adds = '';
		$social 	= new tn_social;
		$teachers 	= new tn_teachers;
		// echo "<pre>"; print_r($data); echo "</pre>";
		if ( !empty($data->adds[0]) ) {
			// Full address
			$address .= !empty(trim($data->adds[0]->line1)) && trim($data->adds[0]->line1) 	!= 'NO' ? trim($data->adds[0]->line1).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->line2)) 	&& trim($data->adds[0]->line2) 	!= 'NO' ? trim($data->adds[0]->line2).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->locality)) 	&& trim($data->adds[0]->locality) 	!= 'NO' ? trim($data->adds[0]->locality).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->region)) 	&& trim($data->adds[0]->region) 	!= 'NO' ? trim($data->adds[0]->region).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->postal_code)) 	&& trim($data->adds[0]->postal_code) 	!= 'NO' ? trim($data->adds[0]->postal_code).' ' : ' ';
			$address .= !empty(trim($data->adds[0]->country_code)) 	&& trim($data->adds[0]->country_code) 	!= 'NO' ? trim($data->adds[0]->country_code).' ' : ' ';

			// Short address after name
			$short_adds .= !empty(trim($data->adds[0]->locality)) 	&& trim($data->adds[0]->locality) 	!= 'NO' ? trim($data->adds[0]->locality).' ' : ' ';
			$short_adds .= !empty(trim($data->adds[0]->region)) 	&& trim($data->adds[0]->region) 	!= 'NO' ? trim($data->adds[0]->region).' ' : ' ';
			$short_adds .= !empty(trim($data->adds[0]->country_code)) 	&& trim($data->adds[0]->country_code) 	!= 'NO' ? trim($data->adds[0]->country_code).' ' : ' ';
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
			$html .= '</div>';
		}
		return $html;
	}
}