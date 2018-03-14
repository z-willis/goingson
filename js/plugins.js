var map, infoWindow, marker;

// This function initializes the map
function initMap() {
    'use strict';
    // Default location of the map if the user doens't allow the geolocation service
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 31.801890, lng: -85.957228},
        zoom: 16,
        disableDoubleClickZoom: false
    });

    infoWindow = new google.maps.InfoWindow;

    // Try HTML5 geolocation.
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };
            map.setCenter(pos);
            marker = new google.maps.Marker({
                position: pos,
                map: map,
                title: 'Hello World!'
            })            
        }, function () {
            handleLocationError(true, infoWindow, map.getCenter());
        });
    } else {
      // Browser doesn't support Geolocation
        handleLocationError(false, infoWindow, map.getCenter());
    }

    map.addListener('dblclick', function (e) {
        addMarker(e.latLng);
    });
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    'use strict';
    infoWindow.setPosition(pos);
    infoWindow.setContent(browserHasGeolocation ?
                          'Error: The Geolocation service failed.' :
                          'Error: Your browser doesn\'t support geolocation.');
    infoWindow.open(map);
}

// Add a marker to the map
function addMarker(pos){
    'use strict';
    map.setCenter(pos);
    marker = new google.maps.Marker({
        position: pos,
        map: map
    });
    marker.addListener('click', function() {
        $( "#eventDialog" ).dialog( "open" );
    });
}