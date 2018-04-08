<?php

    session_start();
    include "connect.php";

    $duration = 0;
    $stmt = $con->prepare("SELECT duration FROM events WHERE eventid = ? AND duration IS NOT NULL");
    $stmt->execute(array($_GET["eventid"]));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();

    if($count > 0){
        $duration =  $row["duration"];
    }

    echo $duration;
?>