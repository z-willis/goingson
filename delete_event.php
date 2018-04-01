<?php
session_start();
include "connect.php";
$stmt = $con->prepare('UPDATE events SET deleted_at = ? WHERE eventid = ?');
$stmt->execute(array(date("Y-m-d H:i:s"), $_POST["eventid"]));
?>