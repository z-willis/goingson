<?php

    session_start();
    include "connect.php";

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