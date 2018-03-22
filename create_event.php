<?php
include "connect.php";
// Enter the user's information into the database
$stmt = $con->prepare('INSERT INTO events (title, description, latitude, longitude, owner_id) VALUES (:title, :description, :latitude, :longitude, 0)');
$stmt->execute(array(
    ":title" => $_POST["title"],
    ":description" => $_POST["description"],
    ":latitude"	=> $_POST["latitude"],
    ":longitude" => $_POST["longitude"]
));
header("Location: create_event.php");
?>