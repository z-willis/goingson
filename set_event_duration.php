<?php

    session_start();
    include "connect.php";

    $stmt = $con->prepare("UPDATE events SET duration = ? WHERE eventid = ? AND duration IS NULL");
    $stmt->execute(array($_GET["duration"], $_GET["eventid"]));

?>