angular.module('myApp', []).controller('baseCtrl', function($scope) {
    $scope.currentUserId = currentUserId;
    $scope.displayedEvent = null;
    var map, infoWindow;
    $scope.events = [];
    $scope.newEventType = "Event";

    $scope.populateMarkers = function(){
        jQuery.ajax({
            url: "get_events.php",
            type: "GET",
            success:function(data){
                $scope.events = JSON.parse(data);
                angular.forEach($scope.events, function (event) {
                    $scope.addMarker(event);
                });
                $scope.$apply();
            }
        });
    };

    $scope.initialize = function() {
        // Default location of the map if the user doens't allow the geolocation service
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 31.801890, lng: -85.957228},
            zoom: 18,
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
    $scope.addMarker = function(event){
        var pos = new google.maps.LatLng(event.latitude, event.longitude);
        var icon;
        if (event.type_id == 1) {
            icon = {
                url: "images/marker.png"
                //scaledSize: new google.maps.Size(50, 50)
            };
        } else {
            icon = null;
        }
        var marker = new google.maps.Marker({
            position: pos,
            map: map,
            icon: icon,
            title: event.title,
            eventId: event.eventid
        });

        marker.addListener('click', function() {
            $scope.getEvent(marker.eventId);
            $( "#eventDialog" ).dialog( "open" );
            var isOpen = $( "#eventDialog" ).dialog( "isOpen" );
            //Check if the eventDialog is opened or not
            if(isOpen){
                
                // Check if the user has already voted for the current event
                jQuery.ajax({
                    url: "check_voting.php",
                    data:{
                        "id": marker.eventId
                    },
                    type: "GET",
                    success: function(data){
                        
                        // Allow the user to vote for the current event if he/she didn't vote before
                        if(data == 0){
                            $( "#votingDialog" ).dialog( "open" );
                            $( "#votingDialog" ).dialog({
                                buttons:{
                                    
                                    // Increase the number of votes for the event if the user chooses "YES"
                                    "Yes": function(){
                                        jQuery.ajax({
                                            url: "inc_vote.php",
                                            data:{
                                                "id": marker.eventId
                                            },
                                            type: "GET",
                                            success:function(data){
                                                console.log(data);
                                                $( "#votingDialog" ).dialog( "close" );
                                            }
                                        })

                                    },
                                    
                                    // Decrease the number of votes for the event if the user chooses "NO"
                                    "No": function(){
                                        jQuery.ajax({
                                            url: "dec_vote.php",
                                            data:{
                                                "id": marker.eventId
                                            },
                                            type: "GET",
                                            success:function(data){
                                                console.log(data);
                                                $( "#votingDialog" ).dialog( "close" );
                                            }
                                        })
                                    },
                                    
                                    // Close the dialog box if the user click on this option (the user will be allowed to vote when he/she will open the event again)
                                    "I am not there": function(){
                                        $( this ).dialog( "close" );
                                    }
                                }
                            })
                        }
                    }
                });
            }
        });
    };
    
    $scope.createEvent = function (title, desc, type) {
        var typeId;
        if (type == "Event"){
            typeId = 1;
        } else if (type == "Question"){
            typeId = 2;
        }
        jQuery.ajax({
            url: "create_event.php",
            data:{
                "title": title,
                "description": desc,
                "latitude": $scope.newEventLat,
                "longitude": $scope.newEventLong,
                "typeId": typeId
            },
            type: "POST",
            success:function(data){
                console.log(data);
                $( "#createEventDialog" ).dialog( "close" );
                $scope.populateMarkers();
            }
        });
    };

    $scope.getEvent = function(eventId){
        jQuery.ajax({
            url: "get_event.php",
            type: "GET",
            data:{
                "eventId": eventId
            },
            success:function(data){
                $scope.displayedEvent = JSON.parse(data);
                $scope.$apply();
            }
        });
    };

    google.maps.event.addDomListener(window, 'load', $scope.initialize);
});