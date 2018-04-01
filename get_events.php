<?php
    include "connect.php";
    $stmt = $con->prepare('SELECT * FROM events WHERE deleted_at IS NULL');
    $stmt->execute();

    $res = $stmt->fetchAll();

    echo json_encode($res);
?>