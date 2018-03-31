angular.module('myApp', []).controller('baseCtrl', function($scope) {
    $scope.canVote = false;
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
        if (event.typeid == 1) {
            icon = {
                url: "images/marker.png"
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
                            $scope.canVote = true;
                        } else {
                            $scope.canVote = false;
                        }
                        $scope.$apply();
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
                $( "#createEventDialog" ).dialog( "close" );
                $scope.populateMarkers();
                $scope.newEventTitle = "";
                $scope.newEventDesc = "";
                $scope.newEventType = "Event";
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

    $scope.openVoteDialog = function(eventId) {
        $( "#votingDialog" ).dialog( "open" );
        $( "#votingDialog" ).dialog({
            buttons:{

                // Increase the number of votes for the event if the user chooses "YES"
                "Yes": function(){
                    jQuery.ajax({
                        url: "inc_vote.php",
                        data:{
                            "id": eventId
                        },
                        type: "GET",
                        success:function(data){
                            console.log(data);
                            $scope.canVote = false;
                            $scope.$apply();
                            $( "#votingDialog" ).dialog( "close" );
                        }
                    })

                },

                // Decrease the number of votes for the event if the user chooses "NO"
                "No": function() {
                    jQuery.ajax({
                        url: "dec_vote.php",
                        data: {
                            "id": eventId
                        },
                        type: "GET",
                        success: function (data) {
                            console.log(data);
                            $scope.canVote = false;
                            $scope.$apply();
                            $("#votingDialog").dialog("close");
                        }
                    })
                }
            }
        })
    };

    $scope.openEditDialog = function(){
        $( "#eventDialog" ).dialog( "close" );
        $( "#editEventDialog" ).dialog( "open" );
        $( "#editEventDialog" ).dialog({
            buttons:{
                "Save": function(){
                    console.log($scope.displayedEvent);
                    jQuery.ajax({
                        url: "edit_event.php",
                        data: {
                            "eventid": $scope.displayedEvent.eventid,
                            "title": $scope.displayedEvent.title,
                            "description": $scope.displayedEvent.description
                        },
                        type: "POST",
                        success: function (data) {
                            console.log(data);
                            $("#editEventDialog").dialog("close");
                        }
                    });
                    $scope.populateMarkers();
                },
                "Delete": function(){
                    jQuery.ajax({
                        url: "delete_event.php",
                        data: {
                            "eventid": $scope.displayedEvent.eventid
                        },
                        type: "POST",
                        success: function (data) {
                            $("#editEventDialog").dialog("close");
                        }
                    });
                    $scope.populateMarkers();
                },
                "Cancel": function(){
                    $( "#editEventDialog" ).dialog( "close" );
                }
            }
        })
    };

    google.maps.event.addDomListener(window, 'load', $scope.initialize);
});