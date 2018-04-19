angular.module('myApp', [])
.controller('baseCtrl', function($scope) {
    $scope.canVote = false;
    $scope.currentUserId = currentUserId;
    $scope.currentUsername = currentUsername;
    $scope.displayedEvent = null;
    $scope.events = [];
    $scope.questions = [];
    $scope.userInfo = [];
    $scope.newEventType = "Event";
    $scope.markers = [];
    $scope.canSetEndDate = false;
    $scope.counterStarted = false;
    $scope.canSetDuration = false;
    $scope.eventEnded = false;

    var map, infoWindow;


    $( "#opener" ).on( "click", function() {
        $( "#eventsDialog" ).dialog( "open" );
        $scope.populateMarkers(null, "1");
        $( "#eventsDialog" ).dialog({
            buttons:{
                "My Events" : function() {
                    $scope.populateMarkers(currentUserId, "1")
                },
                "All Events" : function () {
                    $scope.populateMarkers(null, "1")
                }
            },
            
            close: function(){
                $scope.populateMarkers(null, null);
            }
        });
        
    });
    
    $( "#questions" ).on( "click", function() {
        $( "#questionsDialog" ).dialog( "open" );
        $scope.populateMarkers(null, "2");
        $( "#questionsDialog" ).dialog({
            buttons:{
                "My Questions" : function() {
                    $scope.populateMarkers(currentUserId, "2")
                },
                "All Questions" : function () {
                    $scope.populateMarkers(null, "2")
                }
            },
            
            close: function(){
                $scope.populateMarkers(null, null);
            }
        });
    });

    $scope.populateMarkers = function(userid, type){
        $scope.clearMarkers();
        $scope.questions = [];
        jQuery.ajax({
            url: "database_function.php?function=getEvents",
            type: "POST",
            data:{
                "userFilter": userid,
                "type": type
            },
            success:function(data){
                $scope.events = JSON.parse(data);
                angular.forEach($scope.events, function (event) {
                    $scope.addMarker(event);
                    if(event.typeid == "2"){
                        $scope.questions.push(event);
                    }
                });
                $scope.$apply();
            }
        });
    };

    $scope.clearMarkers = function(){
        for (var i = 0; i < $scope.markers.length; i++) {
            $scope.markers[i].setMap(null);
        }
        $scope.markers = [];
    };

    $scope.initialize = function() {
        // Default location of the map if the user doens't allow the geolocation service
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 31.801890, lng: -85.957228},
            zoom: 18,
            disableDoubleClickZoom: false,
            disableDefaultUI: true,
            zoomControl: true
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

        $scope.populateMarkers(null, null);
    };

    // Add a marker to the map
    $scope.addMarker = function(event){
        var pos = new google.maps.LatLng(event.latitude, event.longitude);
        var icon;
        if (event.typeid == 1) {
            icon = {
                url: "images/markerPurple.png"
            };
        } else {
            icon = {
                url: "images/markerYellow.png"
            };
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
            
            jQuery.ajax({
                url: "database_function.php?function=getEvent",
                data: {
                    "eventId": marker.eventId
                },
                type: "GET",
                success: function(data){
                    if(JSON.parse(data)["typeid"] != 1){
                        $scope.eventEnded = true;
                        $scope.canSetEndDate = false;
                    }else{
                        $scope.eventEnded = false;
                    }
                }
            });
            
            // Check if the duration of an event is set or not
            jQuery.ajax({
                url: "database_function.php?function=getDuration",
                data: {
                    "eventid": marker.eventId
                },
                type: "GET",
                success: function(data){
                    if(data != 0){
                        $scope.canSetDuration = false;
                        // Check if the event has a due date or not
                        jQuery.ajax({
                            url: "database_function.php?function=getEndDate",
                            data:{
                                "eventid": marker.eventId
                            },
                            type: "GET",
                            success: function(data){

                                // Allow the event's owner to set the end date for the current event if he/she didn't set before
                                if(data == 0){
                                    $scope.canSetEndDate = true;
                                    $scope.counterStarted = false;
                                    $( "#finishTime" ).html("");
                                } else {
                                    $scope.canSetEndDate = false; 
                                    var endDate = new Date(data);
                                    $scope.startCountdown();
                                }
                                $scope.$apply();
                            }
                        });
                    }else{ // if the event doesn't have a duration yet, allow the user to be able to set the duration later on
                        $scope.canSetDuration = true;
                        $scope.counterStarted = false;
                        $scope.canSetEndDate = true;
                        $scope.eventEnded = false;
                        $( "#finishTime" ).html("");
                    }
                }
            });
            
            $( "#eventDialog" ).dialog( "open" );
            var isOpen = $( "#eventDialog" ).dialog( "isOpen" );
            
            
            //Check if the eventDialog is opened or not
            if(isOpen){
                
            
                
                // Check if the user has already voted for the current event
                jQuery.ajax({
                    url: "database_function.php?function=checkVoting",
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

        $scope.markers.push(marker);
    };
    
    $scope.startCountdown = function(){
        
        var duration,           
            currentDate,    
            endDate,
            timeDifference,     
            hours,
            minutes,
            seconds,
            day,
            month,
            year;
        $scope.counterStarted = true;
        
        // get the duration of an event
        jQuery.ajax({
            url: "database_function.php?function=getDuration",
            data: {
                "eventid": $scope.displayedEvent.eventid
            },
            type: "GET",
            success: function(data){
                // if duration is found in the database
                if(data != 0){
                    
                    // set the duration to the (duration) coming from the database
                    duration = data;
                    // get the event's end date
                    jQuery.ajax({
                        url: "database_function.php?function=getEndDate",
                        data: {
                            "eventid": $scope.displayedEvent.eventid
                        },
                        type: "GET",
                        success: function(data){
                            // if the end date is found in the database, check if it has passed or not
                            if(data != 0){
                                endDate = new Date(data);
                                currentDate = new Date();
                                timeDifference = endDate.getTime() - currentDate.getTime();
                                
                                /* if the current date passed the end date of the event (timeDifference represents the difference between the dates) 
                                    delete the event */
                                if(timeDifference <= 0){
                                    $( "#finishTime" ).html("Event Ended");
                                    $scope.eventEnded = true;
                                    $scope.populateMarkers(null, null);
                                    $scope.$apply();
                                    
                                }else{ /* if the current date didn't still pass the end date of the event, calculate the remaining time and show it to          the user */
                                    $scope.eventEnded = false;
                                    timeDifference /= 1000;
                                    var intervalId = setInterval(function () {
                                        hours = parseInt(timeDifference / 3600, 10)
                                        minutes = parseInt((timeDifference % 3600) / 60, 10)
                                        seconds = parseInt(timeDifference % 60, 10);

                                        hours = hours < 10 ? "0" + hours : hours;
                                        minutes = minutes < 10 ? "0" + minutes : minutes;
                                        seconds = seconds < 10 ? "0" + seconds : seconds;

                                        $( "#finishTime" ).html("Event ends in: " + hours + ":" + minutes + ":" + seconds);

                                        if(--timeDifference < 0){
                                            clearInterval(intervalId);
                                            $( "#finishTime" ).html("Event Ended");
                                            $scope.eventEnded = true;
                                            $scope.populateMarkers(null, null);
                                            $scope.$apply();
                                        }
                                        
                                        $( "#eventDialog" ).on("dialogclose", function(event){
                                            clearInterval(intervalId);
                                            $( "#finishTime" ).html("");
                                        });
                                    }, 1000);
                                }
                            }else{ /* if the end date is not in the database, set the end date, store it in the database, and start counting the
                                        remaining time and show it to the user */
                                $( "#finishTime" ).html("");
                                currentDate = new Date();
                                hours = currentDate.getHours() + parseInt(duration/3600, 10)
                                minutes = currentDate.getMinutes() + parseInt((duration % 3600) / 60, 10)
                                seconds = currentDate.getSeconds() + parseInt(duration % 60, 10);
                                day = currentDate.getDate();
                                month = currentDate.getMonth();
                                year = currentDate.getFullYear();
                                endDate = new Date(year, month, day, hours, minutes, seconds);

                                jQuery.ajax({
                                    url: "database_function.php?function=setEndDate",
                                    data: {
                                        "eventid": $scope.displayedEvent.eventid,
                                        "endDate": endDate
                                    },
                                    type: "GET",
                                    success: function(data){
                                        console.log("inserted");
                                    }
                                });

                                timeDifference = endDate.getTime() - currentDate.getTime();
                                if(timeDifference <= 0){
                                    $( "#finishTime" ).html("Event Ended");
                                    $scope.eventEnded = true;
                                    $scope.populateMarkers(null, null);
                                    $scope.$apply();
                                }else{
                                    $scope.eventEnded = false;
                                    timeDifference /= 1000;
                                    var intervalId = setInterval(function () {
                                        hours = parseInt(timeDifference / 3600, 10)
                                        minutes = parseInt((timeDifference % 3600) / 60, 10)
                                        seconds = parseInt(timeDifference % 60, 10);

                                        hours = hours < 10 ? "0" + hours : hours;
                                        minutes = minutes < 10 ? "0" + minutes : minutes;
                                        seconds = seconds < 10 ? "0" + seconds : seconds;

                                        $( "#finishTime" ).html("Event ends in: " + hours + ":" + minutes + ":" + seconds);

                                        if(--timeDifference < 0){
                                            clearInterval(intervalId);
                                            $( "#finishTime" ).html("Event Ended");
                                            $scope.eventEnded = true;
                                            $scope.populateMarkers(null, null);
                                            $scope.$apply();
                                        }

                                        $( "#eventDialog" ).on("dialogclose", function(event){
                                            clearInterval(intervalId);
                                            $( "#finishTime" ).html("");
                                        });
                                    }, 1000);
                                }
                            }
                        }
                    });   
                }else{
                    $( "#finishTime" ).html("Ahmed");
                }
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
            url: "database_function.php?function=createEvent",
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
                $scope.populateMarkers(null, null);
                $scope.newEventTitle = "";
                $scope.newEventDesc = "";
                $scope.newEventType = "Event";
                $scope.eventEnded = false;
            }
        });
    };

    $scope.getEvent = function(eventId){
        jQuery.ajax({
            url: "database_function.php?function=getEvent",
            type: "GET",
            data:{
                "eventId": eventId
            },
            success:function(data){
                $scope.displayedEvent = JSON.parse(data);
                if($scope.displayedEvent.typeid == "1"){
                    $scope.displayedEvent.typeString = "Event";
                } else if ($scope.displayedEvent.typeid == "2"){
                    $scope.displayedEvent.typeString = "Question";
                }
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
                        url: "database_function.php?function=incVote",
                        data:{
                            "id": eventId
                        },
                        type: "GET",
                        success:function(data){
                            $scope.canVote = false;
                            $scope.$apply();
                            $( "#votingDialog" ).dialog( "close" );
                        }
                    })

                },

                // Decrease the number of votes for the event if the user chooses "NO"
                "No": function() {
                    jQuery.ajax({
                        url: "database_function.php?function=decVote",
                        data: {
                            "id": eventId
                        },
                        type: "GET",
                        success: function (data) {
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
                    var typeid;
                    if($("#types option:selected").text() == "Event"){
                        typeid = 1;
                    }else if($("#types option:selected").text() == "Question"){
                        typeid = 2;
                        
                        // If the event type changes to a question make sure to delete the event's duration and endDate from the database so
                        // that the timer doesn't start
                        jQuery.ajax({
                            url: "database_function.php?function=getEvent",
                            data: {
                                "eventId": $scope.displayedEvent.eventid
                            },
                            type: "GET",
                            success: function(data){
                                if(JSON.parse(data)["duration"] != "NULL" || JSON.parse(data)["endDate"] != "NULL"){
                                    jQuery.ajax({
                                        url: "database_function.php?function=update_endDate_duration",
                                        data: {
                                            "eventId": $scope.displayedEvent.eventid
                                        },
                                        type: "GET",
                                        success: function(data){
                                            console.log("Duration and endDate updated in the database.");
                                        }
                                    });
                                }
                            }
                        });
                    }
                    
                    jQuery.ajax({
                        url: "database_function.php?function=editEvent",
                        data: {
                            "eventid": $scope.displayedEvent.eventid,
                            "title": $scope.displayedEvent.title,
                            "description": $scope.displayedEvent.description,
                            "typeid": typeid
                        },
                        type: "POST",
                        success: function (data) {
                            $("#editEventDialog").dialog("close");
                            $scope.populateMarkers(null, null);
                        }
                    });
                },
                "Delete": function(){
                    jQuery.ajax({
                        url: "database_function.php?function=deleteEvent",
                        data: {
                            "eventid": $scope.displayedEvent.eventid
                        },
                        type: "POST",
                        success: function (data) {
                            $scope.clearMarkers();
                            $scope.populateMarkers(null, null);
                            $("#editEventDialog").dialog("close");
                        }
                    });
                },
                "Cancel": function(){
                    $( "#editEventDialog" ).dialog( "close" );
                }
            }
        })
    };
    
    $scope.openTimerDialog = function(eventId){
        
        var hours,
            minutes;
        $( "#hourVal" ).css("width", "75px");
        $( "#minVal" ).css("width", "75px");
        
        $( "#eventTimer" ).dialog( "open" );
        $( "#eventTimer" ).dialog({
            buttons:{
                "Save": function(){
                    hours = $( "#hourVal" ).val();
                    minutes = $( "#minVal" ).val();
                    
                    var duration = 60 * minutes + hours * 3600; // calculate the duration of the event in seconds
                    
                    jQuery.ajax({
                        url: "database_function.php?function=setDuration",
                        data:{
                            "duration": duration,
                            "eventid": eventId
                        },
                        type: "GET",
                        success: function(data){
                            $scope.canSetDuration = false;
                            $scope.$apply();
                            $( "#eventTimer" ).dialog( "close" );
                        }
                    });
                },
                "Cancel": function(){
                    $( "#eventTimer" ).dialog("close");
                }
            }
        });
    };
    
    $scope.viewProfile = function(){
        jQuery.ajax({
            url: "database_function.php?function=getUserInfo",
            success: function(data){
                $scope.userInfo = JSON.parse(data);
                $scope.$apply();
            }
        });
        $("#viewProfileDialog").dialog("open");
        $("#viewProfileDialog").dialog({
            buttons:{
                "Edit": function () {
                    $scope.updateProfile();
                    $("#viewProfileDialog").dialog("close");
                },
                "Close": function () {
                    $("#viewProfileDialog").dialog("close");
                }
            }
        });
    };
    
    // The function the update the user's infromation in the database
    $scope.updateProfile = function(){
        
        var username = "", password = "", email = "", name = "", usernameExists = true, isValidEmail = true;
        
        // get the user's info from the database
        jQuery.ajax({
            url: "database_function.php?function=getUserInfo",
            success: function(data){
                $scope.userInfo = JSON.parse(data);
                if($scope.userInfo.name) {
                    $("#name").val($scope.userInfo.name);
                } else {
                    $("#name").val("");
                }
                $( "#username" ).val($scope.userInfo.username);
                $( "#password" ).val(""); // make the password empty because it is encrypted in the database and can't be decrypted
                $( "#email" ).val($scope.userInfo.email);
                
                username = $scope.userInfo.username;
                email = $scope.userInfo.email;
                name = $scope.userInfo.name;
            }
        });
        
        $( "#profileDialog" ).dialog( "open" );
        
        // make sure the new username isn't already used in the database
        $( "#username" ).blur(function(){

            jQuery.ajax({
                url: "database_function.php?function=checkUsername",
                data: {
                    "username": $( "#username" ).val()
                },
                type: "POST",
                success:function(data){
                    if(data > 0){ // if user name already exists in the database
                        usernameExists = true;
                        username = $( "#username" ).val();
                    }else{
                        usernameExists = false;
                        username = $( "#username" ).val();
                    }
                }
            });     
        });
        
        $( "#password" ).blur(function(){
            password = $( "#password" ).val();
        });

        $( "#name" ).blur(function(){
            name = $( "#name" ).val();
        });
        
        $( "#email" ).blur(function(){
            
            // validat the email of the user
            if(!validateEmail($( "#email" ).val())){
                isValidEmail = false;
                email = $( "#email" ).val();
            }else{
                isValidEmail = true;
                email = $( "#email" ).val();
            }
        });
        
        $( "#profileDialog" ).dialog({
            buttons: {
                "Save": function(){
                
                    // Check the length of the username
                    if(username.length < 5){
                        if($( "#errorsDialog" ).children().hasClass(".shortUsername")){
                            $( ".shortUsername" ).remove();
                        }else{
                            $( "#errorsDialog" ).append("<div class='.shortUsername'><h3>Short Username</h3><p>- Username has to be more than 4 characters</p></div>");
                        }
                    }else{
                        
                        // show an error to the user if the username is already in the database and isn't the current username of the user
                        if(usernameExists && (username != $scope.currentUsername)){
                            if($( "#errorsDialog" ).children().hasClass(".userExists")){
                                $( ".userExists" ).remove();
                            }else{
                                $( "#errorsDialog" ).append("<div class='.userExists'><h3>Username Unavailable</h3><p>- Username already used</p></div>");
                            }
                        }
                    }
                    
                    // make sure the password passes the required length, otherwise show an error
                    if(password.length < 5 || password == ""){
                        if($( "#errorsDialog" ).children().hasClass(".shortPass")){
                            $( ".shortPass" ).remove();
                        }else{
                            $( "#errorsDialog" ).append("<div class='.shortPass'><h3>Short Password</h3><p>- Password has to be more than 4 characters</p></div>");
                        }
                    }
                    
                    // make sure the email is valid, otherwise show an error
                    if(!isValidEmail){
                        if($( "#errorsDialog" ).children().hasClass(".invalidEmail")){
                            $( ".invalidEmail" ).remove();
                        }else{
                            $( "#errorsDialog" ).append("<div class='.invalidEmail'><h3>Invalid Email</h3><p>- Please enter a valid email</p></div>");
                        }
                    }
                                        
                    // make sure there are no errors in the error dialog box
                    if($( "#errorsDialog" ).children().length == 0){
                        jQuery.ajax({
                            url: "database_function.php?function=editUserInfo",
                            data:{
                                "name": name,
                                "username": username,
                                "password": password,
                                "email": email
                            },
                            type: "POST",
                            success: function(data){
                                $scope.viewProfile();
                                $( "#profileDialog" ).dialog( "close" );
                            }
                        });
                    }else{
                        
                        $( "#errorsDialog" ).dialog("open");
                        $( "#errorsDialog" ).dialog({
                            buttons: {
                                "Close": function(){
                                    $( "#errorsDialog" ).empty();
                                    $( "#errorsDialog" ).dialog( "close" );
                                    $( "#profileDialog" ).dialog( "close" );
                                    $( "#profileDialog" ).dialog( "open" );
                                }
                            }
                        });
                    }                        
                },
                
                "Cancel": function(){
                    $scope.viewProfile();
                    $( "#profileDialog" ).dialog( "close" );
                }
            }
        });
    };

    $scope.getAnswers = function(eventid){
        jQuery.ajax({
            url: "database_function.php?function=getAnswers&eventid=" + eventid,
            type: "GET",
            success: function (data) {
                $scope.displayedAnswers = JSON.parse(data);
                $scope.$apply();
            }
        });
    };
    
    $scope.openAnswerDialog = function(){
        $("#answerDialog").dialog("open");
        $scope.getAnswers($scope.displayedEvent.eventid);
        $("#answerDialog").dialog({
            buttons: {
                "Submit": function(){
                    
                    // Check if the answer field is empty or not
                    if($(".answerDialog textarea").val().trim() != ""){
                        jQuery.ajax({
                            url: "database_function.php?function=answerQuestion",
                            data: {
                                "eventid": $scope.displayedEvent.eventid,
                                "userid": $scope.currentUserId,
                                "answertext": $scope.newAnswer //Or however the answer text will be passed
                            },
                            type: "POST",
                            success: function (data) {
                                $scope.getAnswers($scope.displayedEvent.eventid);
                            }
                        });

                        $(".answerDialog textarea").val("");
                    }
                }
            }
        });
    };
    
    $scope.viewAnswers = function(eventid){
        
        jQuery.ajax({
            url: "database_function.php?function=getAnswers&eventid=" + eventid,
            type: "GET",
            success: function (data) {
                if(data.trim() != "[]"){
                    $("#answerDialog").dialog("open");
                    $scope.getAnswers(eventid);
                    $("#answerDialog textarea").css("display", "none");
                    $("#answerDialog").dialog({
                        buttons: {},

                        close: function(){
                            $("#answerDialog textarea").css("display", "block");
                        }
                    });
                }else{
                    $("#noAnswersDialog").dialog("open");
                    $("#noAnswersDialog").dialog({
                        buttons: {
                            "Close": function(){
                                $("#noAnswersDialog").dialog("close");
                            }
                        }
                    });
                }
            }
        });
    }
    
    // Validate the update email that the user enters
    function validateEmail(email){
        var pattern = /\S+@\S+\.com|net|edu|org$/;
        return pattern.test(email);
    }
    
    google.maps.event.addDomListener(window, 'load', $scope.initialize);
})
.controller('loginCtrl', function($scope, $window) {
    $scope.active = 'login';
    $scope.setFocus = function (clicked) {
        $scope.active = clicked;
    };

    $scope.login = function(username, password){
        jQuery.ajax({
            url: "login.php",
            data: {
                "username": username,
                "password": password
            },
            type: "POST",
            success: function (data) {
                if (data == 1) {
                    $window.location.href = '/testing/map.php';
                } else {
                    console.log('error');
                }
            }
        });
    };

    $scope.createAccount = function(username, password, repeatPassword, email){
        jQuery.ajax({
            url: "create_account.php",
            data: {
                "username": username,
                "password": password,
                "repeat-password": repeatPassword,
                "email": email
            },
            type: "POST",
            success: function (data) {
                if (data == 1) {
                    $window.location.reload();
                } else {
                    console.log('error');
                }
            }
        });
    }
});