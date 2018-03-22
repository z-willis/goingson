<?php
    // This file is used to check for the username to inform the user on the spot if the username is available or not
    include "connect.php";
    // Check if the username already exists
    $user = $_POST["username"];
    $stmt = $con->prepare('SELECT username FROM user WHERE username = ?');
    $stmt->execute(array($user));
    $count = $stmt->rowCount();

    echo $count;

?>