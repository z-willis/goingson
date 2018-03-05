
<?php
    
    // Connect to the database

    $dsn = 'mysql:host=localhost;dbname=webdev';
    $user = 'root';
    $pass = '';
    $option = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    );

    try{
        $con = new PDO($dsn, $user, $pass, $option);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    catch(PDOexception $e){
        echo 'Failed to connect to the database' . $e->getMessage();
    }

?>