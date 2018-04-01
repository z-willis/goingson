<?php
    include "connect.php";
    $stmt = $con->prepare('SELECT * FROM events WHERE deleted_at = null');
    $stmt->execute();

    $res = $stmt->fetchAll();

    echo json_encode($res);
?>