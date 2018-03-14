angular.module('myApp', []).controller('baseCtrl', function($scope) {
    var map, infoWindow;

    $scope.test = "I am testing";
    $scope.events = [
        {title:"Event1", description:"The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least."},
        {title:"Event2", description:"The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least."},
        {title:"Event3", description:"The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least."},
        {title:"Event4", description:"The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least."},
        {title:"Event5", description:"The HTML on this one is a little more complicated. Each list item needs to have three children: an image, a headline and a paragraph. The images that I’m using are 100px by 100px so keep that in mind if you want to customize this to be a different size. Overall, this is all still really simple markup that shouldn’t trip you up in the least."}
    ];

    $scope.initialize = function() {
        'use strict';
        // Default location of the map if the user doens't allow the geolocation service
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 31.801890, lng: -85.957228},
            zoom: 16,
            disableDoubleClickZoom: false
        });

         var infoWindow = new google.maps.InfoWindow;

        // Try HTML5 geolocation.
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                map.setCenter(pos);
            }, function () {
                handleLocationError(true, infoWindow, map.getCenter());
            });
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
        }

        map.disableDoubleClickZoom = true;

        map.addListener('dblclick', function (e) {
            $( "#createEventDialog" ).dialog( "open" );
            $scope.newEventLat = e.latLng.lat();
            $scope.newEventLong = e.latLng.lng();
        });
        $scope.populateMarkers();
    };

    // Add a marker to the map
    $scope.addMarker = function(title, pos){
        'use strict';
        var marker = new google.maps.Marker({
            position: pos,
            map: map,
            title: title
        });
        marker.addListener('click', function() {
            $( "#eventDialog" ).dialog( "open" );
        });
    };
    
    $scope.createEvent = function (title, desc) {
        jQuery.ajax({
            url: "create_event.php",
            data:{
                "title": title,
                "description": desc,
                "latitude": $scope.newEventLat,
                "longitude": $scope.newEventLong
            },
            type: "POST",
            success:function(data){
                $( "#createEventDialog" ).dialog( "close" );
                $scope.addMarker(title, new google.maps.LatLng($scope.newEventLat, $scope.newEventLong));
            }
        });
    };

    $scope.populateMarkers = function(){
        jQuery.ajax({
            url: "get_events.php",
            type: "GET",
            success:function(data){
                $scope.events = JSON.parse(data);
                angular.forEach($scope.events, function (event) {
                    var pos = new google.maps.LatLng(event.latitude, event.longitude);
                    $scope.addMarker(event.title, pos);
                });
            }
        });
    }

    google.maps.event.addDomListener(window, 'load', $scope.initialize);
});