<?php
    
    session_start();
    include "connect.php";

    $stmt = $con->prepare("UPDATE events SET endDate = ? WHERE eventid = ? AND deleted_at IS NULL");
    $stmt->execute(array(
        $_GET["endDate"],
        $_GET["eventid"]
    ));

    echo $_GET["endDate"];

?>


