<?php

    session_start();
    include "connect.php";


    if($_GET['function'] == "getEvents"){
        
        $query = 'SELECT * FROM events';

        if($_POST["userFilter"] != null){
            $query = $query.' WHERE userid = ' . $_POST["userFilter"] .' AND deleted_at IS NULL';
        } else {
            $query = $query.' WHERE deleted_at IS NULL';
        }

        $stmt = $con->prepare($query);
        $stmt->execute();

        $res = $stmt->fetchAll();

        echo json_encode($res);

    }else if($_GET['function'] == "createEvent"){
        
        $stmt = $con->prepare('INSERT INTO events (title, description, latitude, longitude, userid, typeid) VALUES (:title, :description, :latitude, :longitude, :userid, :typeId)');
        $stmt->execute(array(
            ":title" => $_POST["title"],
            ":description" => $_POST["description"],
            ":latitude"	=> $_POST["latitude"],
            ":longitude" => $_POST["longitude"],
            ":userid" => $_SESSION["userid"],
            ":typeId" => $_POST["typeId"]
        ));
        
    }else if($_GET['function'] == "getEvent"){
        
        $stmt = $con->prepare('SELECT * FROM events WHERE eventid = ?');
        $stmt->execute(array($_GET["eventId"]));

        $res = $stmt->fetch();

        echo json_encode($res);
        
    }else if($_GET['function'] == "checkVoting"){
        
        $stmt = $con->prepare("SELECT * FROM voting WHERE userid = ? AND eventid = ?");
        $stmt->execute(array($_SESSION['userid'], $_GET['id']));
        $count = $stmt->rowCount();

        echo $count;
        
    }else if($_GET['function'] == "incVote"){
        
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
        
    }else if($_GET['function'] == "decVote"){
        
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
        
    }else if($_GET['function'] == "editEvent"){
        
        $stmt = $con->prepare('UPDATE events SET title = ?, description = ?, typeid = ? WHERE eventid = ?');
        $stmt->execute(array(
            $_POST["title"],
            $_POST["description"],
            $_POST["typeid"],
            $_POST["eventid"]
        ));
        
    }else if($_GET['function'] == "deleteEvent"){
        
        $stmt = $con->prepare('UPDATE events SET deleted_at = ? WHERE eventid = ?');
        $stmt->execute(array(date("Y-m-d H:i:s"), $_POST["eventid"]));
    
    }else if($_GET['function'] == "setDuration"){
        
        $stmt = $con->prepare("UPDATE events SET duration = ? WHERE eventid = ? AND duration IS NULL");
        $stmt->execute(array($_GET["duration"], $_GET["eventid"]));
    
    }else if($_GET['function'] == "setEndDate"){
        
        $stmt = $con->prepare("UPDATE events SET endDate = ? WHERE eventid = ? AND deleted_at IS NULL");
        $stmt->execute(array(
            $_GET["endDate"],
            $_GET["eventid"]
        ));

    }else if($_GET['function'] == "getDuration"){
        
        $duration = 0;
        $stmt = $con->prepare("SELECT duration FROM events WHERE eventid = ? AND duration IS NOT NULL");
        $stmt->execute(array($_GET["eventid"]));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();

        if($count > 0){
            $duration =  $row["duration"];
        }

        echo $duration;
        
    }else if($_GET['function'] == "getEndDate"){
        
        $endDate = 0;
        $stmt = $con->prepare("SELECT endDate FROM events WHERE eventid = ? AND endDate IS NOT NULL AND deleted_at IS NULL");
        $stmt->execute(array($_GET["eventid"]));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();

        if($count > 0){
            $endDate =  $row["endDate"];
        }

        echo $endDate;
        
    }else if($_GET['function'] == "getUserInfo"){
        
        $stmt = $con->prepare("SELECT * FROM user WHERE userid = ?");
        $stmt->execute(array($_SESSION["userid"]));
        $count = $stmt->rowCount();

        if($count > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        echo json_encode($row);

    }else if($_GET['function'] == "checkUsername"){
        
        $user = $_POST["username"];
        $stmt = $con->prepare('SELECT username FROM user WHERE username = ?');
        $stmt->execute(array($user));
        $count = $stmt->rowCount();

        echo $count;

    }else if($_GET['function'] == "editUserInfo"){
        
        $username = $_POST["username"];
        $password = sha1($_POST["password"]);
        $email = $_POST["email"];

        $stmt = $con->prepare("UPDATE user SET username = ?, password = ?, email = ? WHERE userid = ?");
        $stmt->execute(array(
            $username, 
            $password, 
            $email, 
            $_SESSION["userid"]
        ));

    }else if($_GET['function'] == "update_endDate_duration"){
        
        $stmt = $con->prepare("UPDATE events SET duration = NULL, endDate = NULL WHERE eventid = ?");
        $stmt->execute(array($_GET["eventId"]));
        
    }