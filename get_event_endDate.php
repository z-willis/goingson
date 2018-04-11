<?php

    session_start();
    include "connect.php";

    $endDate = 0;
    $stmt = $con->prepare("SELECT endDate FROM events WHERE eventid = ? AND endDate IS NOT NULL AND deleted_at IS NULL");
    $stmt->execute(array($_GET["eventid"]));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();

    if($count > 0){
        $endDate =  $row["endDate"];
    }

    echo $endDate;
?>