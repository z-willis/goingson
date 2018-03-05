var map, infoWindow, marker;

// This function initializes the map
function initMap() {
    'use strict';
    // Default location of the map if the user doens't allow the geolocation service
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 31.801890, lng: -85.957228},
        zoom: 16
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
            })            
        }, function () {
            handleLocationError(true, infoWindow, map.getCenter());
        });
    } else {
      // Browser doesn't support Geolocation
        handleLocationError(false, infoWindow, map.getCenter());
    }
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
function addMarker(latitude, longitude){
    'use strict';
    var pos = {lat: latitude, lng: longitude};
    map.setCenter(pos);
    marker = new google.maps.Marker({
        position: pos,
        map: map
    })
}

