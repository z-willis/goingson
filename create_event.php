<?php
    session_start();
    include "connect.php";
    $stmt = $con->prepare('INSERT INTO events (title, description, latitude, longitude, userid, typeid) VALUES (:title, :description, :latitude, :longitude, :userid, :typeId)');
    $stmt->execute(array(
        ":title" => $_POST["title"],
        ":description" => $_POST["description"],
        ":latitude"	=> $_POST["latitude"],
        ":longitude" => $_POST["longitude"],
        ":userid" => $_SESSION["userid"],
        ":typeId" => $_POST["typeId"]
    ));
?>