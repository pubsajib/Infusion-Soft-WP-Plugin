<?php
get_header();
$teachers   = new tn_teachers;
$countries  = $teachers->getAllCountries();
$data       = $teachers->frontend_all_teachers();
// =========================================================================================================
// =========================================================================================================			    	
// $ins_con = new tn_db;
// $address    = new tn_address;
//$theAddress = $teachers->get_latlong_by_zip('Bangladesh','3801');
// =========================================================================================================
// =========================================================================================================
//echo '<pre>'; print_r($theAddress); echo '</pre>';
 $country    = isset($_POST['country']) && !empty(trim($_POST['country'])) ? trim($_POST['country']) : false;
 $state      = isset($_POST['state']) && !empty(trim($_POST['state'])) ? trim($_POST['state']) : false;
$zip        = isset($_POST['zip']) && !empty(trim($_POST['zip'])) ? trim($_POST['zip']) : false;

 if ( !empty($zip) ) { $values = array('country' => $country, 'zip' => $zip);
        $data = $teachers->frontend_all_teachers($values); }
 elseif ( !empty($country) || !empty($state) ) { 
    $values = array('state' => $state, 'country' => $country );
    $data = $teachers->frontend_all_teachers($values); 
}
else { $data = $teachers->frontend_all_teachers(); }
echo '<div class="fusion-builder-preview-image bannerImage"><img src="'.TNPLUGINURL.'assets/images/header-bg.jpg"></div>';
?>
<style>@media screen and (min-width: 56.875em){ .site-content { padding: 0; } }</style>
<link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,400,600" rel="stylesheet">
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
        </div>
        <div class="total-searchare clear">
            <div class="teacher-list search-option-parent col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-sm-7 col-xs-12">
                        <form action="" method="post" id="teachersearchForm">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <select name="country" class="form-control teachersearch" id="country">
                                            <?php 
                                            echo '<option selected value="" style="display: none;">Country</option>';
                                            foreach ($countries as $country) {
                                                if ( !empty(trim($country)) ) {
                                                    echo '<option value="'.$country.'">'.$country.'</option>';
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                    <p id="coun_message" style="color:red;"></p>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <select name="state" class="form-control teachersearch" disabled="" id="state">
                                            <option selected value="" style="display: none;">State</option>
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        OR
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <input class="form-control teachersearch" placeholder="Enter ZIP Code" type="text" name="zip" id="zip" disabled >
                                </div>

                                <div class="col-sm-2">
                                    <button type="submit" name="teachersearchSubmit" class="btn btn-search btn-success" id="teachersearchSubmit"> Search </button>
                                </div>
                            </div>
                        </form>

                        <div id="map"></div>
                        <div class="map-areya search-map" style="width: 100%; height: 450px;" id="mapID"></div>
                    </div>

                    <div class="col-sm-5 col-xs-12 no-padding-left">
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

<div class="search-result hidden" id="result-div">
    <div class="container">                    
        <div class="row">
            <div class="col-sm-12">
                <div class="teacher-list clear">
                    <div class="row">
                        <div class="teacher-list col-lg-10 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12"> 
                            <div id="installerAddressDetails" class="media teacher-list-item common_location" >
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1tbIAqN0XqcgTR1-FxYoVTVq6Is6lD98&callback=initialize"></script>
<script>
    var map = null;
    var oldmarkers = [];
    var centerLoc = '';
    var lat = '';
    var lng = '';
    var address = '';
    var infoWindow = null;
    var locations = <?php echo $data['location']; ?>;
    // console.log(locations);
    function initialize() {
        var initialLatLng = [["Initial","43.719151", "-74.944258" ]];
        map = new google.maps.Map(document.getElementById('mapID'), {
          zoom: 6,
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

    function removeOldLocations(locations) {
        for( var i=0; i < locations.length;i++){ 
            locations[i].setMap(null);
        }
    }
    function customSetCenter(message, center_lat, center_long, time){
        var address = message;
    	closeInfoWindow();
        latlngset = new google.maps.LatLng(center_lat,center_long);
        infoWindow = new google.maps.InfoWindow({
            content: message
        });

        var marker = new google.maps.Marker({
            map: map, title: "Locations", position: latlngset, infoWindow: infoWindow
        });
        
        google.maps.event.addListener(marker, 'click', (function(marker,address,infoWindow) {
            return function() {
                infoWindow.setContent(address);
                infoWindow.open(map, marker);
                map.setCenter(marker.getPosition());
            }
            })(marker,address,infoWindow));
            
        // marker.setAnimation(google.maps.Animation.BOUNCE);
        map.setCenter(marker.getPosition());
        //marker.showInfoWindow();
        infoWindow.close();
        infoWindow.open(map, marker);
        
    }
    function closeInfoWindow() {
        if (infoWindow) {
	        google.maps.event.clearInstanceListeners(infoWindow);  // just in case handlers continue to stick around
            infoWindow.close();
            infoWindow = null;
	    }
    }
    // $(document).on( 'click', '#buttton', function(event){
    //     event.preventDefault();
    //     removeOldLocations(oldmarkers);
    //     setMarkers2(new_locations);
    //     // alert(oldmarkers);
    // });
</script>
<script>
    jQuery(function($){
        //click to scroll result div
        $(document).on('click', '.side-info>a, .mapProfileBtn a', function(e) {
            // prevent default anchor click behavior
            e.preventDefault();
            $("#result-div").removeClass("hidden");
            $('html, body').animate({
                scrollTop: $(this.hash).offset().top
            }, 1000, function(){
            window.location.hash = this.hash;
            });
        });


        $(document).on( 'change', '#country', function(event){
            event.preventDefault();
	    $("#zip").removeAttr("disabled");
            var country = $(this).val();
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
                    $('#state').html('Search').removeAttr('disabled').html(response);
                },
                error : function(e){
                    console.log(e);
                }
            });
        });

        $(document).on( 'submit', '#teachersearchForm_old', function(event){
            event.preventDefault();
            var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
            var country = $('#country').val().trim();
            var state = $('#state').val().trim();
            var zip = $('#zip').val().trim();
            // alert('country.length : '+ country.length +' == state.length : '+state.length +' == zip.length : '+ zip.length);
            if ( country.length > 0 || state.length > 0 || zip.length > 0 ) {
                jQuery.ajax({
                    url : ajax_url,
                    type : 'post',
                    data : { 
                        action : 'searchTeachers', 
                        country : country,
                        state : state,
                        zip : zip
                    },
                    beforeSend : function(){
                        $('#teachersearchSubmit').html('<span><i class="fa fa-spinner fa-spin" style="font-size:18px"></i></span> Search');
                    },
                    success : function( response ) {
			alert(response); return false;
                        $('#teachersearchSubmit').html('Search');
                        if (response != 'false') {
                            var values = JSON.parse(response);
                            var element = values.element;
                            var newlocations = JSON.parse(values.location);

                            // update map
                            removeOldLocations(oldmarkers);
                            setMarkers(newlocations);
                            setCenterMarker(newlocations);
                            $('#installerAddressUpdate').html(element);
                            // console.log(new_locations);
                            // console.log(newlocations);
                        } else {
                            removeOldLocations(oldmarkers);
                            $('#installerAddressUpdate').html('<p class="text-center" style="color:red;">Sorry! Could not found any record.</p>');
                        }
                    },
                    error : function(e){
                        $('#teachersearchSubmit').html('Search');
                        // alert(e);
                        console.log(e);
                    }
                });
            } else {
                alert('empty file');
            }
        });

        $(document).on( 'click', '#installerAddressUpdate .media-heading a, .mapProfileBtn a', function(event){
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
                },
                error : function(e){
                    console.log(e);
                }
            });
        });
    });
</script>
<script type="text/javascript">
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
        $(document).on( 'change', '#country', function(event){
            var count = $(this).val();
            if(count!=''){
                $('#teachersearchSubmit').prop('disabled', false);
            }
        });
    });
</script>
<?php get_footer();

