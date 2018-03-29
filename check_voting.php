<?php

    session_start();
    include "connect.php";
    $stmt = $con->prepare("SELECT * FROM voting WHERE userid = ? AND eventid = ?");
    $stmt->execute(array($_SESSION['id'], $_GET['id']));
    $count = $stmt->rowCount();

    echo $count;