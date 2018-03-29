<?php
    session_start();
    include "connect.php";

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
        ":userid" => $_SESSION["id"],
        ":eventid" => $_GET["id"]
    ));


?>