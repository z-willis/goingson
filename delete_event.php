<?php
session_start();
include "connect.php";
// Enter the user's information into the database
$stmt = $con->prepare('DELETE FROM voting WHERE eventid = ?');
$stmt->execute(array($_POST["eventid"]));

$stmt = $con->prepare('DELETE FROM events WHERE eventid = ?');
$stmt->execute(array($_POST["eventid"]));
?>