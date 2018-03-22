<?php
include "connect.php";
$stmt = $con->prepare('SELECT * FROM events');
$stmt->execute();

$res = $stmt->fetchAll();

echo json_encode($res);
?>