<?php
    session_start();    // Start the session

    // Don't allow the user to access the map without logging in first
    if(!isset($_SESSION['user'])){
        header('Location: index.php');
    }

?>

<!DOCTYPE html>
<html ng-app="myApp">
    <head>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link type='text/css' rel='stylesheet' href='style/style.css'/>
        <link type='text/css' rel='stylesheet' href='style/normalize.css'/>
        <title>GoingZ On</title>

        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzMHc5MQBw4UrHMCKTCVmwSngKzo1Kh6I"></script>
        <script src="node_modules/angular/angular.js"></script>
        <script src="js/app.js"></script>
    </head>
    <body ng-controller="baseCtrl">
        <ul class="top-nav-bar" ng>
            <li class="tab-left">
                <span onclick="toggleNav()"><a><image class="drawer" src="images/drawer.png"></image></a></span>
            </li>
            <li class="tab-middle">
                <h1>GoingZ On</h1>
            </li>
            <li class="tab-right">
                <h1>Legend</h1>
            </li>
        </ul>
        
        <dl class="legend">
            <dt><img src="images/markerPurple.png" /><span> Event</span></dt>
            <dt><img src="images/markerYellow.png" /><span> Question</span></dt>
        </dl>
        
        
        <div id="mySidenav" class="sidenav">
            <a class="top" href="#" id="opener">Events</a>
            <a href="#" ng-click="viewProfile()">Profile</a>
            <a href="#" ng-click="populateMarkers(null)">Refresh</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <div id="profileDialog" title="Edit Profile" style="display:none">
            <form>
                <h3>Name: </h3>
                <input type="text" id="name" autocomplete="off" />
                <h3>Username: </h3>
                <input type="text" id="username" autocomplete="off" />
                <h3>Password: </h3>
                <input type="password" id="password" autocomplete="new-password" />
                <h3>Email: </h3>
                <input type="text" id="email" autocomplete="off" />
            </form>
        </div>

        <div id="viewProfileDialog" title="Profile">
            <img src="images/default.png">
            <h1>{{userInfo.name}} - {{userInfo.username}}</h1>
            <h3>{{userInfo.email}}</h3>
        </div>
        
        <div id="errorsDialog" title="Update Errors">
            
        </div>

        <div id="map"></div>

        <div id="eventsDialog" title="Events">
            <ul class="events">
                <li class="entry" ng-repeat="event in events">
                    <!--<img class="img" src="images/drawer.png" /> -->
                    <h3 class="title">Title: {{event.title}}</h3>
                    <p class="text">Description: {{event.description}}</p>
                </li>
            </ul>
        </div>
        
        <div id="eventDialog" title="Event Details">
            <h3>Title</h3>
            <p>{{displayedEvent.title}}</p>
            <h3>Description</h3>
            <p>{{displayedEvent.description}}</p>
            <div id="votingDialog" title="Verify Event">
                <p>Is this event happening?</p>
            </div>
            
            <div id="eventTimer" title="Set Event Duration">
                <h3>Duration of the event</h3>
                <input type="number" id="hourVal" min="0" value="0" ><span> Hour(s)</span>
                <input type="number" id="minVal" min="0" max="59" value="0"><span> Minute(s)</span>
                <p>Please note that the event duration can't be changed once it is set.</p>
            </div>
            
            <div class="allbuttons">
                <button ng-if="canVote && !eventEnded" ng-click="openVoteDialog(displayedEvent.eventid)">Verify Event</button>
                <button ng-if="displayedEvent.userid == currentUserId" ng-click="openEditDialog()">Edit Event</button>
                <button ng-if="displayedEvent.userid == currentUserId && canSetEndDate && canSetDuration" ng-click="openTimerDialog(displayedEvent.eventid)">Set Duration</button>
                <button ng-if="displayedEvent.userid == currentUserId && canSetEndDate && !counterStarted && !canSetDuration" ng-click="startCountdown()">Start Event</button>
            </div>
            
            <div id="countdown">
                <!--<span id="time"></span>-->
                <h5 id="finishTime"></h5>
            </div>
        </div>
        
        <div id="createEventDialog" title="Create Event">
            <form>
                <h3>Title</h3>
                <input type="text" ng-model="newEventTitle"/>
                <h3>Description</h3>
                <textarea rows="5" ng-model="newEventDesc"></textarea>
                <h3>Type</h3>
                <select value="Event" ng-model="newEventType">
                    <option value="Event">Event</option>
                    <option value="Question">Question</option>
                </select>
                <input style="display: block;" ng-click="createEvent(newEventTitle, newEventDesc, newEventType)" type="submit"/>
            </form>
        </div>
        
        <div id="editEventDialog" title="Edit Event">
            <form>
                <h3>Title</h3>
                <input type="text" ng-model="displayedEvent.title"/>
                <h3>Description</h3>
                <textarea rows="5" ng-model="displayedEvent.description"></textarea>
                <h3>Type</h3>
                <select id="types" value="Event" ng-model="displayedEvent.typeString">
                    <option value="Event">Event</option>
                    <option value="Question">Question</option>
                </select>
            </form>
        </div>
        <script>
            var currentUserId = "<?php echo $_SESSION["userid"]; ?>";
            var currentUsername = "<?php echo $_SESSION["user"]; ?>";

            function toggleNav() {
                if(document.getElementById("mySidenav").style.width == 0 || document.getElementById("mySidenav").style.width == "0px"){
                    document.getElementById("mySidenav").style.width = "200px";
                } else {
                    document.getElementById("mySidenav").style.width = "0px";
                }
            }

            $(".tab-right h1").hover(function(){
                $(".legend").fadeToggle(1000);
            });
            
            $( function() {
                $( "#eventsDialog" ).dialog({
                    dialogClass: "allEventsDialog",
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#eventDialog" ).dialog({
                    dialogClass: "eventDialog",
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#createEventDialog" ).dialog({
                    dialogClass: "createDialog",
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#votingDialog" ).dialog({
                    dialogClass: "votingDialog",
                    autoOpen: false,
                    show: false,
                    hide: false,
                    resizable: false,
                    draggable: false,
                    modal: true,
                    height: 250,
                    width: 350
                });
                $( "#editEventDialog" ).dialog({
                    dialogClass: "editEventDialog",
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#eventTimer" ).dialog({
                    dialogClass: "setEventDuration",
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#profileDialog" ).dialog({
                    dialogClass: "editProfile",
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#viewProfileDialog" ).dialog({
                    dialogClass: "viewProfile",
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#errorsDialog" ).dialog({
                    dialogClass: "errorsDialog",
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
            } );
        </script>
    </body>
</html>