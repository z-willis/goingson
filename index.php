<!DOCTYPE html>
<html ng-app="myApp">
    <head>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link type='text/css' rel='stylesheet' href='style/style.css'/>
        <link type='text/css' rel='stylesheet' href='style/normalize.css'/>
        <title>Testing</title>

        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="node_modules/angular/angular.js"></script>
        <script src="js/app.js"></script>
    </head>
    <body ng-controller="baseCtrl">
        <ul class="top-nav-bar" ng-if="!user">
            <li class="tab-left">
                <span onclick="openNav()"><a><image src="images/drawer.png"></image></a></span>
            </li>
            <li class="tab-right">
                <input type="submit"/>
                <input type="button" value="Create Account" ng-click="createAccount(username)">
            </li>
            <li class="tab-right">
                <div>Password:</div>
                <input ng-model="password" placeholder="********"/>
            </li>
            <li class="tab-right">
                <div>Username:</div>
                <input ng-model="username" placeholder="JBob123"/>
            </li>
        </ul>
        <ul class="top-nav-bar" ng-if="user">
            <li class="tab-left">
                <span onclick="openNav()"><a><image src="images/drawer.png"></image></a></span>
            </li>
            <li class="tab-right">
                <div>{{user.name}}</div>
            </li>
        </ul>
        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="#" id="opener">Events</a>
            <a href="#">Services</a>
            <a href="#">Clients</a>
            <a href="#">Contact</a>
        </div>

        <div>{{test}}</div>
        <div id="dialog" title="Events">
            <ul class="events">
                <li class="entry" ng-repeat="event in events">
                    <img class="img" src="images/drawer.png" />
                    <h3 class="title">{{event.title}}</h3>
                    <p class="text">{{event.description}}</p>
                </li>
            </ul>
        </div>
        <script>
            /* Set the width of the side navigation to 250px */
            function openNav() {
                document.getElementById("mySidenav").style.width = "250px";
            }

            /* Set the width of the side navigation to 0 */
            function closeNav() {
                document.getElementById("mySidenav").style.width = "0";
            }

            $( function() {
                $( "#dialog" ).dialog({
                    autoOpen: false,
                    show: false,
                    hide: false,
                    height: 600,
                    width: 600
                });

                $( "#opener" ).on( "click", function() {
                    $( "#dialog" ).dialog( "open" );
                });
            } );
        </script>
    </body>
</html>