<?php
get_header();
$teachers   = new tn_teachers;
$countries  = $teachers->getAllCountries();
$data       = $teachers->frontend_all_teachers();
echo '<div class="fusion-builder-preview-image bannerImage"><img src="'.TNPLUGINURL.'assets/images/header-bg.jpg"></div>';
// =========================================================================================================
// Test
// =========================================================================================================
// $statess = $teachers->getStates('united states'); 
// echo '<pre>'; print_r($statess); echo '</pre>';
// die('asdfsfsafsadfsfasfasfsafaf HHHHHHHHHHHHHHHHHHHHHHHH');

// =========================================================================================================
// Search 
// =========================================================================================================
// $country    = isset($_POST['country']) && !empty(trim($_POST['country'])) ? trim($_POST['country']) : false;
// $state      = isset($_POST['state']) && !empty(trim($_POST['state'])) ? trim($_POST['state']) : false;
// $zip        = isset($_POST['zip']) && !empty(trim($_POST['zip'])) ? trim($_POST['zip']) : false;

// if ( !empty($zip) ) { 
//     $values = array('country' => $country, 'zip' => $zip);
//     $data = $teachers->frontend_all_teachers($values); }
// elseif ( !empty($country) || !empty($state) ) { 
//     $values = array('country' => $country, 'state' => $state );
//     $data = $teachers->frontend_all_teachers($values); 
// }
// else { $data = $teachers->frontend_all_teachers(); }
// =========================================================================================================
// Search end
// =========================================================================================================
?>
<style>@media screen and (min-width: 56.875em){ .site-content { padding: 0; } }</style>
<link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,400,600" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.min.css">
<div class="plugin-warpper">
<div class="container notFoundResults hidden">
    <div class="row">
        <div class="col-sm-12 col-xs-12 text-center">
            <h1 class="lg-title">FIND A TEACHER</h1>
            <p style="color:red;">Sorry! Could not found any record.</p>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-sm-12 col-xs-12 text-center">
            <h1 class="lg-title">FIND A TEACHER</h1>
            <p class="main-sub-title">A free service we provide to connect you with teachers</p>
        </div>
        <div class="total-searchare clear">
            <div class="teacher-list search-option-parent col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12 ">
                        <form action="" method="post" id="teachersearchForm" class="margin-bottom-30">
                            <div class="row">
                                <div class="col-lg-5 col-md-5 col-sm-5 custom-col-sm-5 col-xs-12">
                                    <select class="form-control pull-left search-form-item" name="country" id="country">
                                        <?php 
                                        echo '<option value="">Select Country</option>';
                                        foreach ($countries as $country) {
                                            if ( !empty(trim($country)) ) {
                                                echo '<option value="'.$country.'">'.$country.'</option>';
                                            }
                                        } ?>
                                    </select> 
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-2 custom-col-sm-2 sm-margin-top-20 xs-margin-top-20 col-xs-12">
                                    
                                </div>

                                <div class="col-lg-5 col-md-5 col-sm-5 custom-col-sm-5 sm-margin-top-20 xs-margin-top-20 col-xs-12 hidden-xs">
                                    <button type="submit" name="teachersearchSubmit" class="btn btn-search btn-success btn-block teachersearchSubmit" id="teachersearchSubmit"> 
                                        <span id="searchTxt" class="pull-left"> Search </span> 
                                        <span class="ico"> <i class="icon-arrow-right icons"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <div class="row margin-top-30">
								<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
									<select class="form-control pull-left search-form-item" name="state" id="state" disabled="">
                                        <option selected value="" style="display: none;">Select State</option>
                                    </select>
								</div>

								<div class="col-lg-2 col-md-2 col-sm-2 custom-col-sm-2 sm-margin-top-20 xs-margin-top-20 col-xs-12 xs-seach-or">
									<div class="text-center search-form-item"> <p class="circle-textbox">OR</p> </div>
								</div>

								<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
									<input class="form-control search-form-item pull-left" placeholder="ZIP OR POSTAL CODE" name="zip" id="zip" disabled="">
								</div>

								<div class="xs-margin-top-20 col-xs-12 hidden-sm hidden-md hidden-lg">
                                    <button type="submit" name="teachersearchSubmit" class="btn btn-search btn-success btn-block teachersearchSubmit" id="teachersearchSubmit"> 
                                        <span id="searchTxt" class="pull-left"> Search </span> 
                                        <span class="ico"> <i class="icon-arrow-right icons"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <!-- <div id="map"></div> -->
                        <div class="map-areya search-map" style="width: 100%; height: 500px;" id="mapID"></div>
                    </div>

                    <div class="col-lg-6 col-md-6  col-sm-12 col-xs-12 no-padding-left">
                        <div class="map-search-option">
                            <div class="teacher-list clear">
                                <div class="row">
                                    <div id="installerAddressUpdate" class="teacher-list teacher-list-top col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <?php echo $data['element']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact detail view div -->
<div class="search-result hidden" id="result-div">
    <div class="container">                    
        <div class="row">
            <div class="col-sm-12">
                <div class="teacher-list clear">
                    <div class="row">
                        <div class="teacher-list col-lg-10 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12"> 
                            <div id="installerAddressDetails" class="media teacher-list-item common_location" > Contact details will go here .... </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div> <!-- plugin warpper end -->
<script type="text/javascript">
    $('[data-toggle="ctooltip"]').tooltip();

    var map = null;
    var oldmarkers = [];
    var centerLoc = '';
    var lat = '';
    var lng = '';
    var address = '';
    var infowindow = null;
    var locations = <?php echo $data['location']; ?>;
    // console.log(locations);
    function initialize() {
        var initialLatLng = [["Initial","43.719151", "-74.944258" ]];
        map = new google.maps.Map(document.getElementById('mapID'), {
          zoom: 2,
          // center: centerLoc,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        setMarkers(locations);
        setCenterMarker(initialLatLng);
    }
    function setCenterMarker(locations) {
        if ( locations[0][1] && locations[0][2] ) {
            lat = locations[0][1];
            lng = locations[0][2];
        } else if ( locations[1][1] && locations[1][2] ) {
            lat = locations[1][1];
            lng = locations[1][2];
        }
        centerLoc = new google.maps.LatLng(lat, lng );
        map.setCenter(centerLoc);
    }
    
    function setMarkers(locations) {
        var marker, i;
        oldmarkers = [];
        infowindow = new google.maps.InfoWindow();
        for (i = 0; i < locations.length; i++) {  
            if ( locations[i][1] && locations[i][2] ) { // if has geolocation values
                address = locations[i][0];
                marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map
            });
            oldmarkers.push(marker);
            // console.log('length: '+i); console.log(locations);
            google.maps.event.addListener(marker, 'click', (function(marker,address,infowindow) {
            return function() {
                infowindow.setContent(address);
                infowindow.open(map, marker);
                map.setCenter(marker.getPosition());
            }
            })(marker,address,infowindow));
          }
        }
    }

    function removeOldLocations(oldlocations) {
        for( var i=0; i < oldlocations.length;i++){ 
            oldlocations[i].setMap(null);
            // console.log(locations[i]);
        }
        closeInfoWindow();
    }
    function customSetCenter(message, center_lat, center_long, time){
        var address = message;
        closeInfoWindow();
        latlngset = new google.maps.LatLng(center_lat,center_long);
        infowindow = new google.maps.InfoWindow({
            content: message
        });

        var marker = new google.maps.Marker({
            map: map, title: "Locations", position: latlngset, infowindow: infowindow
        });
        oldmarkers.push(marker);
        google.maps.event.addListener(marker, 'click', (function(marker,address,infowindow) {
            return function() {
                infowindow.setContent(address);
                infowindow.open(map, marker);
                map.setCenter(marker.getPosition());
            }
            })(marker,address,infowindow));
            
        // marker.setAnimation(google.maps.Animation.BOUNCE);
        map.setCenter(marker.getPosition());
        //marker.showInfoWindow();
        infowindow.close();
        infowindow.open(map, marker);
        
    }
    function closeInfoWindow() {
        if (infowindow) {
            google.maps.event.clearInstanceListeners(infowindow);  // just in case handlers continue to stick around
            infowindow.close();
            infowindow = null;
        }
    }
    // $(document).on( 'click', '#buttton', function(event){
    //     event.preventDefault();
    //     removeOldLocations(oldmarkers);
    //     setMarkers2(new_locations);
    //     // alert(oldmarkers);
    // });
</script>
<script type="text/javascript">
    jQuery(function($){
        //click to scroll result div
        $(document).on('click', '.side-info>a, .mapProfileBtn a', function(e) {
            // prevent default anchor click behavior
            e.preventDefault();
            $("#result-div").removeClass("hidden");
            $('html, body').animate({
                scrollTop: $(this.hash).offset().top
            }, 1000, function(){
                // window.location.hash = this.hash;
            });
        });

        $(document).on('click', '.expand-btn', function(e) {
            // prevent default anchor click behavior
            //e.preventDefault();
            $("#result-div").removeClass("hidden");
        });

        $(document).on( 'submit', '#teachersearchForm', function(event){
            event.preventDefault();
            var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
            var zip = $('#zip').val().trim();
            var state = $('#state').val().trim();
            var country = $('#country').val().trim();
            // alert('country.length : '+ country.length +' == state.length : '+state.length +' == zip.length : '+ zip.length);
            if ( country.length > 0 || state.length > 0 || zip.length > 0 ) {
                jQuery.ajax({
                    url : ajax_url,
                    type : 'post',
                    data : { 
                        action : 'searchTeachers', 
                        zip : zip,
                        state : state,
                        country : country
                    },
                    beforeSend : function(){
                        $('#teachersearchSubmit #searchTxt').html('<span><i class="fa fa-spinner fa-spin" style="font-size:18px"></i></span> Search');
                    },
                    success : function( response ) {
                        $('#teachersearchSubmit #searchTxt').html('Search');
			             // alert(response); return false;
                        if (response != 'false') {
                            var values = JSON.parse(response);
                            var element = values.element;
                            var newlocations = JSON.parse(values.location);

                            // update map
                            removeOldLocations(oldmarkers);
                            setMarkers(newlocations);
                            setCenterMarker(newlocations);
                            $('#installerAddressUpdate').html(element);

                            //Speciality bar height
                            $(".teacher-list-item").each(function(){
                                var w30_height = $(this).find(".search-w70").height();
                                $(this).find(".search-w30").height(w30_height);
                            });

                            // Tooltip auto call
                            ajaxTootltip();
                            // console.log(new_locations);
                            // console.log(newlocations);
                        } else {
                            removeOldLocations(oldmarkers);
                            $('#installerAddressUpdate').html('<p class="text-center" style="color:red;">Sorry! Could not found any record.</p>');
                        }
                    },
                    error : function(e){
                        $('#teachersearchSubmit #searchTxt').html('Search');
                        // alert(e);
                        console.log(e);
                    }
                });
            } else {
                alert('empty file');
            }
        });

        $(document).on( 'click', '#installerAddressUpdate .media-heading a, .mapProfileBtn a, .expand-btn', function(event){
            event.preventDefault();
            var user_id = $(this).attr('data-id');
            var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
            jQuery.ajax({
                url : ajax_url,
                type : 'post',
                data : {
                    action : 'show_details',
                    userID : user_id
                },
                beforeSend : function(){ $('#installerAddressDetails').html('<span><i class="fa fa-spinner fa-spin" style="font-size:18px"></i></span> Loading ...'); },
                success : function( response ) {
                    // alert(response);
                    $('#installerAddressDetails').html(response);
                    ajaxTootltip();
                },
                error : function(e){
                    console.log(e);
                }
            });
        });
    });
</script>
<script type="text/javascript">
    // Form validation for zip and country select
    jQuery(function($){
        $("#zip").blur(function(){
            var country = $("#country").val();
            if( country == '' ){
                $('#coun_message').html('Please select a country.').delay(3000).fadeOut(500);
                $('#teachersearchSubmit').prop('disabled', true);
            }else{
                $('#teachersearchSubmit').prop('disabled', false);
            }
        });
        // Load states upon select countries
        $(document).on( 'change', '#country', function(event){
            event.preventDefault();
            var country = $(this).val();
            if (country.length > 0) { $("#zip").removeAttr("disabled"); }
            else { $("#zip").attr("disabled", true); }
            var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
            jQuery.ajax({
                url : ajax_url,
                type : 'post',
                data : {
                    action : 'selectStates',
                    country : country
                },
                beforeSend : function(){ $('#state').attr('disabled', true); },
                success : function( response ) {
                    // alert(response);
                    if ( response.length > 0 ) { $('#state').html('Search').removeAttr('disabled').html(response); }
                    else { $('#state').html('Search').attr('disabled', true).html(response); }
                },
                error : function(e){
                    console.log(e);
                }
            });
        });
    }); // End of ready function

    
    //$('[data-toggle="tooltip"]').tooltip();
    $(".teacher-list-item").each(function(){
    	var w30_height = $(this).find(".search-w70").height();
		$(this).find(".search-w30").height(w30_height);
	});


	// Tool tip on hover
	function ajaxTootltip() { 
      $( "[data-toggle='ctooltip']" ).each(function(k,el){
        defaults = {
          	delay: { show: 0, hide: 0},
          	container: 'body'
        }
        inline_vals = {
          	delay: $(el).data('delay') 
        }
        $(el).tooltip($.extend(defaults, inline_vals));
      }); 
    }

    $(document).on("click",".plus_btn",function(){
    	$(this).addClass("hidden");
    	$(this).parents("ul").children(".minus_btn").removeClass("hidden");
        $(this).parents("ul").children(".extraIcons").removeClass("hidden");
        $(this).parents(".search-w30").addClass("auto-height");
    	$(this).parents(".speciality").addClass("relative");
    });

    $(document).on("click",".minus_btn",function(){
    	$(this).addClass("hidden");
    	$(this).parents("ul").children(".plus_btn").removeClass("hidden");
    	$(this).parents("ul").children(".extraIcons").addClass("hidden");
        $(this).parents(".search-w30").removeClass("auto-height");
        $(this).parents(".speciality").removeClass("relative");
    });
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo TNMAPAPIKEY; ?>&callback=initialize"></script>
<?php get_footer();