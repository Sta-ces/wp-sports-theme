<?php
    /**
     * Template Maps
     */

    if(!get_infoth('is-google-maps') || get_infoth('api-key-google-maps') == '' || get_infoth("address-google-maps") == '') return;
?>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php infoth('api-key-google-maps'); ?>&callback=GetLocation" async></script>
<script type="text/javascript">
    function GetLocation() {
        const GEOCODER = new google.maps.Geocoder();
        const ADDRESS = "<?php infoth("address-google-maps"); ?>";
        if(ADDRESS != ""){
            GEOCODER.geocode({ 'address': ADDRESS }, (results, status) => {
                if (status == google.maps.GeocoderStatus.OK) {
                    let latitude = results[0].geometry.location.lat();
                    let longitude = results[0].geometry.location.lng();
                    InitMap({lat: latitude, lng: longitude});
                }
            });
        }
    };

    function InitMap(loc){
        const MAP = new google.maps.Map(document.getElementById("map"), {
            center: loc,
            zoom: 16
        });
        const MARKER = new google.maps.Marker({
            position: loc,
            animation: google.maps.Animation.DROP
        });

        MARKER.setMap(MAP);
    }
</script>

<section id="map"></section>