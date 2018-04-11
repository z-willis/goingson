<?php
    include "connect.php";
    $query = 'SELECT * FROM events';

    if($_POST["userFilter"] != null){
        $query = $query.' WHERE userid = ' . $_POST["userFilter"] .' AND deleted_at IS NULL';
    } else {
        $query = $query.' WHERE deleted_at IS NULL';
    }

    $stmt = $con->prepare($query);
    $stmt->execute();

    $res = $stmt->fetchAll();

    echo json_encode($res);
?>