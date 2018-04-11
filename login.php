<?php

session_start();    // Start the session

// If a user has already logged in redirect him to the map without having to log in again
/*if(isset($_SESSION['user'])){
    header('Location: map.php');
}*/

include 'connect.php';

// varaibles for the username, password, hashing the password, email, and checking the user's account
$user = $pass = $hashedPass = $email = $existingAccount = $success = '';

if(isset($_POST['username'])){
    // filter the username string from unnecessary characters to prevent the user from causing security issues
    filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    // store the value of the username in the $user variable
    $user = $_POST['username'];
}

if(isset($_POST['password'])){

    // filter the password string from unnecessary characters to prevent the user from causing security issues
    filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    // store the value of the password in the $pass variable
    $pass = $_POST['password'];
    // hashing the password and storing the hasded pass in the variable
    $hashedPass = sha1($pass);
}

// checking if the information entered by the user exists in the database or not (if the user exists or not)
$stmt = $con->prepare('SELECT userid, username, password, email FROM user WHERE username = ? AND password = ?');
$stmt->execute(array($user , $hashedPass));
$count = $stmt->rowCount();
$row = $stmt->fetch(PDO::FETCH_ASSOC);      // get the info of the user from the database (information comes as an associative array)

if($count > 0){ // if a user exists
    // Initialize the session of the user
    $_SESSION['user'] = $user;
    $_SESSION['userid'] = $row['userid'];       // store the user's id in the session
//    header('Location: map.php');
    echo 1;
} else{
    $loginErrors[] = 'No such account exists';
    echo 0;
}