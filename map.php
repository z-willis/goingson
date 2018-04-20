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
                <image src="images/legend.png"></image>
            </li>
        </ul>
        
        <dl class="legend">
            <dt><img src="images/markerPurple.png" /><span> Event</span></dt>
            <dt><img src="images/markerYellow.png" /><span> Question</span></dt>
        </dl>
        
        
        <div id="mySidenav" class="sidenav">
            <a class="top" href="#" id="opener">Events</a>
            <a class="top" href="#" id="questions">Questions</a>
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

        <div id="viewProfileDialog" title="Profile" style="display:none">
            <img src="images/default.png">
            <h1>{{userInfo.name}} - {{userInfo.username}}</h1>
            <h3>{{userInfo.email}}</h3>
        </div>
        
        <div id="errorsDialog" title="Update Errors"></div>

        <div id="map"></div>

        <div id="eventsDialog" title="Events" style="display:none">
            <ul class="events">
                <li class="entry" ng-repeat="event in events">
                    <h3 class="title">{{event.title}}</h3>
                    <p class="text">{{event.description}}</p>
                </li>
            </ul>
        </div>
        
        <div id="questionsDialog" title="Questions" style="display:none">
            <ul class="questions">
                <li class="entry" ng-repeat="question in questions">
                    <h3 class="title">{{question.title}}</h3>
                    <p class="text">{{question.description}}</p>
                    <button ng-click="viewAnswers(question.eventid)">Answers</button>
                </li>
            </ul>
        </div>
        
        <div id="eventDialog" title="Details" style="display:none">
            <h3>Title</h3>
            <p>{{displayedEvent.title}}</p>
            <h3>Description</h3>
            <p>{{displayedEvent.description}}</p>
            <div id="votingDialog" title="Verify Event">
                <p>Is this event happening?</p>
            </div>
            
            <div id="eventTimer" title="Set Event Duration" style="display:none">
                <h3>Duration of the event</h3>
                <input type="number" id="hourVal" min="0" value="0" ><span> Hour(s)</span>
                <input type="number" id="minVal" min="0" max="59" value="0"><span> Minute(s)</span>
                <p>Please note that the event duration can't be changed once it is set.</p>
            </div>
            
            <div id="countdown">
                <h5 id="finishTime"></h5>
            </div>
            
            <div class="allbuttons">
                <button ng-if="canVote && !eventEnded && displayedEvent.typeid == '1'" ng-click="openVoteDialog(displayedEvent.eventid)">Verify Event</button>
                <button ng-if="displayedEvent.userid == currentUserId" ng-click="openEditDialog()">Edit</button>
                <button ng-if="displayedEvent.userid == currentUserId && canSetEndDate && canSetDuration && displayedEvent.typeid == '1'" ng-click="openTimerDialog(displayedEvent.eventid)">Set Duration</button>
                <button ng-if="displayedEvent.userid == currentUserId && canSetEndDate && !counterStarted && !canSetDuration && displayedEvent.typeid == '1'" ng-click="startCountdown()">Start Event</button>
                <button ng-if="displayedEvent.typeid == '2'" ng-click="openAnswerDialog()">Answers</button>
            </div>
            
            
        </div>
        
        <div id="createEventDialog" title="Create Event" style="display:none">
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
        
        <div id="editEventDialog" title="Edit" style="display:none">
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
        
        <div id="answerDialog" title="Answers" style="display:none">
            <ul class="existingAnswers">
                <li class="entry" ng-repeat="answer in displayedAnswers">
                    <h3>{{answer.username}}: </h3>
                    <p>{{answer.answertext}}</p>
                </li>
            </ul>
            <textarea ng-model="newAnswer"></textarea>
        </div>
        
        <div id="noAnswersDialog" title="Answers" style="display:none">
            <p>No answers available for this question!</p>
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

            $(".tab-right img").hover(function(){
                $(".legend").slideToggle();
            });
            
            $( function() {
                $( "#eventsDialog" ).dialog({
                    dialogClass: "allEventsDialog",
                    autoOpen: false,
                    modal: true,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#questionsDialog" ).dialog({
                    dialogClass: "allQuestionsDialog",
                    autoOpen: false,
                    modal: true,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#eventDialog" ).dialog({
                    dialogClass: "eventDialog",
                    autoOpen: false,
                    modal: true,
                    show: false,
                    hide: false,
                    height: 450,
                    width: 500
                });
                $( "#createEventDialog" ).dialog({
                    dialogClass: "createDialog",
                    autoOpen: false,
                    modal: true,
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
                    modal: true,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#eventTimer" ).dialog({
                    dialogClass: "setEventDuration",
                    autoOpen: false,
                    modal: true,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#profileDialog" ).dialog({
                    dialogClass: "editProfile",
                    autoOpen: false,
                    modal: true,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#viewProfileDialog" ).dialog({
                    dialogClass: "viewProfile",
                    autoOpen: false,
                    modal: true,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#errorsDialog" ).dialog({
                    dialogClass: "errorsDialog",
                    autoOpen: false,
                    modal: true,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#answerDialog" ).dialog({
                    dialogClass: "answerDialog",
                    autoOpen: false,
                    modal: true,
                    draggable: false,
                    show: false,
                    hide: false,
                    height: 500,
                    width: 500
                });
                $( "#noAnswersDialog").dialog({
                    dialogClass: "noAnswersDialog",
                    autoOpen: false,
                    modal: true,
                    draggable: false,
                    show: false,
                    hide: false,
                    height: 250,
                    width: 350
                })
            } );
        </script>
    </body>
</html>