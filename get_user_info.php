<?php
    
    session_start();
    include "connect.php";
    
    $stmt = $con->prepare("SELECT * FROM user WHERE userid = ?");
    $stmt->execute(array($_SESSION["userid"]));
    $count = $stmt->rowCount();

    if($count > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    echo json_encode($row);
    