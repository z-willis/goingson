<?php

    session_start();
    include "connect.php";


    if($_GET['function'] == "getEvents"){ // when we want to get all the events
        
        $query = 'SELECT * FROM events';

        if($_POST["userFilter"] != null){
            if($_POST["type"] != null){
                $query = $query.' WHERE userid = ' . $_POST["userFilter"] .' AND deleted_at IS NULL AND typeid = '.$_POST["type"];
            }else{
                $query = $query.' WHERE userid = ' . $_POST["userFilter"] .' AND deleted_at IS NULL';
            }   
        } else {
            if($_POST["type"] != null){
                $query = $query.' WHERE deleted_at IS NULL AND typeid = '.$_POST["type"];
            }else{
                $query = $query.' WHERE deleted_at IS NULL';
            }
        }

        $stmt = $con->prepare($query);
        $stmt->execute();

        $res = $stmt->fetchAll();

        echo json_encode($res);

    }else if($_GET['function'] == "createEvent"){ // when we want to create an event
        
        $stmt = $con->prepare('INSERT INTO events (title, description, latitude, longitude, userid, typeid) VALUES (:title, :description, :latitude, :longitude, :userid, :typeId)');
        $stmt->execute(array(
            ":title" => $_POST["title"],
            ":description" => $_POST["description"],
            ":latitude"	=> $_POST["latitude"],
            ":longitude" => $_POST["longitude"],
            ":userid" => $_SESSION["userid"],
            ":typeId" => $_POST["typeId"]
        ));
        
    }else if($_GET['function'] == "getEvent"){ // when we want to get a specific event
        
        $stmt = $con->prepare('SELECT events.*, user.username FROM events JOIN user ON events.userid = user.userid WHERE eventid = ?');
        $stmt->execute(array($_GET["eventId"]));

        $res = $stmt->fetch();

        echo json_encode($res);
        
    }else if($_GET['function'] == "checkVoting"){ // when we want to check for the user's voting status
        
        $stmt = $con->prepare("SELECT * FROM voting WHERE userid = ? AND eventid = ?");
        $stmt->execute(array($_SESSION['userid'], $_GET['id']));
        $count = $stmt->rowCount();

        echo $count;
        
    }else if($_GET['function'] == "incVote"){ // increasing the voting of a specific event
        
        $stmt = $con->prepare("SELECT votes FROM events WHERE eventid = ?");
        $stmt->execute(array($_GET["id"]));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();

        if($count > 0){
            $votes = $row[votes];
            $votes++;
        }

        $stmt = $con->prepare("UPDATE events SET votes = ? WHERE eventid = ?");
        $stmt->execute(array($votes, $_GET["id"]));

        $stmt = $con->prepare("INSERT INTO voting (userid, eventid) VALUES (:userid, :eventid)");
        $stmt->execute(array(
            ":userid" => $_SESSION["userid"],
            ":eventid" => $_GET["id"]
        ));
        
    }else if($_GET['function'] == "decVote"){ // decreasing the votes of a specific event
        
        $stmt = $con->prepare("SELECT votes FROM events WHERE eventid = ?");
        $stmt->execute(array($_GET["id"]));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();

        if($count > 0){
            $votes = $row[votes];
            $votes--;
        }

        $stmt = $con->prepare("UPDATE events SET votes = ? WHERE eventid = ?");
        $stmt->execute(array($votes, $_GET["id"]));

        $stmt = $con->prepare("INSERT INTO voting (userid, eventid) VALUES (:userid, :eventid)");
        $stmt->execute(array(
            ":userid" => $_SESSION["userid"],
            ":eventid" => $_GET["id"]
        ));
        
    }else if($_GET['function'] == "editEvent"){ // when we want to update and edit the information of an event
        
        $stmt = $con->prepare('UPDATE events SET title = ?, description = ?, typeid = ? WHERE eventid = ?');
        $stmt->execute(array(
            $_POST["title"],
            $_POST["description"],
            $_POST["typeid"],
            $_POST["eventid"]
        ));
        
    }else if($_GET['function'] == "deleteEvent"){ // when we want to delete an event
        
        $stmt = $con->prepare('UPDATE events SET deleted_at = ? WHERE eventid = ?');
        $stmt->execute(array(date("Y-m-d H:i:s"), $_POST["eventid"]));
    
    }else if($_GET['function'] == "setDuration"){ // when we want to set the duration of an event
        
        $stmt = $con->prepare("UPDATE events SET duration = ? WHERE eventid = ? AND duration IS NULL");
        $stmt->execute(array($_GET["duration"], $_GET["eventid"]));
    
    }else if($_GET['function'] == "setEndDate"){ // when we want to set the endDate of an event
        
        $stmt = $con->prepare("UPDATE events SET endDate = ? WHERE eventid = ? AND deleted_at IS NULL");
        $stmt->execute(array(
            $_GET["endDate"],
            $_GET["eventid"]
        ));

    }else if($_GET['function'] == "getDuration"){ // when we want to get the duration specified for an event
        
        $duration = 0;
        $stmt = $con->prepare("SELECT duration FROM events WHERE eventid = ? AND duration IS NOT NULL");
        $stmt->execute(array($_GET["eventid"]));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();

        if($count > 0){
            $duration =  $row["duration"];
        }

        echo $duration;
        
    }else if($_GET['function'] == "getEndDate"){ // when we want to get the endDate specified for an event
        
        $endDate = 0;
        $stmt = $con->prepare("SELECT endDate FROM events WHERE eventid = ? AND endDate IS NOT NULL AND deleted_at IS NULL");
        $stmt->execute(array($_GET["eventid"]));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();

        if($count > 0){
            $endDate =  $row["endDate"];
        }

        echo $endDate;
        
    }else if($_GET['function'] == "getUserInfo"){ // when we want to get the user's information
        
        $stmt = $con->prepare("SELECT * FROM user WHERE userid = ?");
        $stmt->execute(array($_SESSION["userid"]));
        $count = $stmt->rowCount();

        if($count > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        echo json_encode($row);

    }else if($_GET['function'] == "checkUsername"){ // when we want to check the availability of the user's username
        
        $user = $_POST["username"];
        $stmt = $con->prepare('SELECT username FROM user WHERE username = ?');
        $stmt->execute(array($user));
        $count = $stmt->rowCount();

        echo $count;

    }else if($_GET['function'] == "editUserInfo"){ // when we want to edit the user's information
        
        $username = $_POST["username"];
        $password = sha1($_POST["password"]);
        $email = $_POST["email"];
        $name = $_POST["name"];

        $stmt = $con->prepare("UPDATE user SET username = ?, password = ?, email = ?, name = ? WHERE userid = ?");
        $stmt->execute(array(
            $username, 
            $password, 
            $email,
            $name,
            $_SESSION["userid"]
        ));

    }else if($_GET['function'] == "update_endDate_duration"){ // when an event changes types (event or question) make sure to update the event
        
        $stmt = $con->prepare("UPDATE events SET duration = NULL, endDate = NULL WHERE eventid = ?");
        $stmt->execute(array($_GET["eventId"]));
        
    }else if($_GET['function'] == "answerQuestion"){

        $stmt = $con->prepare('INSERT INTO answers (userid, eventid, answertext) VALUES (:userid, :eventid, :answertext)');
        $stmt->execute(array(
            ":userid" => $_SESSION["userid"],
            ":eventid" => $_POST["eventid"],
            ":answertext" => $_POST["answertext"]
        ));

    }else if($_GET["function"] == "getAnswers"){

        $query = 'SELECT answers.*, user.username FROM answers JOIN user ON answers.userid = user.userid WHERE eventid = ' . $_GET['eventid'];
        $stmt = $con->prepare($query);
        $stmt->execute();

        $res = $stmt->fetchAll();

        echo json_encode($res);

    }