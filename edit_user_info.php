<?php

    session_start();
    include "connect.php";

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