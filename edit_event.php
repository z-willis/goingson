<?php
session_start();
include "connect.php";
// Enter the user's information into the database
$stmt = $con->prepare('UPDATE events SET title = ?, description = ?, typeid = ? WHERE eventid = ?');
$stmt->execute(array(
    $_POST["title"],
    $_POST["description"],
    $_POST["typeid"],
    $_POST["eventid"]
    ));
?>