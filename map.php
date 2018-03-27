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
        <title>Testing</title>

        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzMHc5MQBw4UrHMCKTCVmwSngKzo1Kh6I"></script>
        <script src="node_modules/angular/angular.js"></script>
        <script src="js/app.js"></script>
    </head>
    <body ng-controller="baseCtrl">
        <ul class="top-nav-bar" ng>
            <li class="tab-left">
                <span onclick="openNav()"><a><image src="images/drawer.png"></image></a></span>
            </li>
        </ul>
        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="#" id="opener">Events</a>
            <a href="#">Services</a>
            <a href="#">Clients</a>
            <a href="#" ng-click="populateMarkers()">Refresh</a>
            <a href="logout.php">Logout</a>
        </div>

        <div id="map"></div>

        <div id="eventsDialog" title="Events">
            <ul class="events">
                <li class="entry" ng-repeat="event in events">
                    <img class="img" src="images/drawer.png" />
                    <h3 class="title">{{event.title}}</h3>
                    <p class="text">{{event.description}}</p>
                </li>
            </ul>
        </div>
        <div id="eventDialog" title="Event">
            <h3>Title</h3>
            <p>{{displayedEvent.title}}</p>
            <h3>Description</h3>
            <p>{{displayedEvent.description}}</p>
        </div>
        <div id="createEventDialog" title="Create Event">
            <form>
                <h3>Title</h3>
                <input ng-model="newEventTitle"/>
                <h3>Description</h3>
                <input ng-model="newEventDesc"/>
                <input style="display: block;" ng-click="createEvent(newEventTitle, newEventDesc)" type="submit"/>
            </form>
        </div>
        <script>
            var currentUserId = "<?php echo $_SESSION["userid"]; ?>";
            /* Set the width of the side navigation to 250px */
            function openNav() {
                document.getElementById("mySidenav").style.width = "250px";
            }

            /* Set the width of the side navigation to 0 */
            function closeNav() {
                document.getElementById("mySidenav").style.width = "0";
            }

            $( function() {
                $( "#eventsDialog" ).dialog({
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 600,
                    width: 600
                });
                $( "#eventDialog" ).dialog({
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 600,
                    width: 600
                });
                $( "#createEventDialog" ).dialog({
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 300,
                    width: 300
                });

                $( "#opener" ).on( "click", function() {
                    $( "#eventsDialog" ).dialog( "open" );
                });
            } );
        </script>
    </body>
</html>