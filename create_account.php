<?php
session_start();    // Start the session

include 'connect.php';

$userLength = 5;                // the minimum length of the username
$passLength = 5;                // the minimum length of the password
// varaibles for the username, password, hashing the password, email, and checking the user's account
$user = $pass = $hashedPass = $email = $success = '';
$createAccountErrors = array();         // an array to store the errors while creating an account

if(isset($_POST['username'])){
    // Filter the username string from unnecessary characters to prevent the user from causing security issues
    filter_var($_POST['username'], FILTER_SANITIZE_STRING);

    // Check the length of the username after being filtered
    if(strlen($_POST['username']) < $userLength){
        $createAccountErrors[] = "Username has to be more than 4 characters.";
    }else{
        $user = $_POST['username'];
    }
}

if(isset($_POST['password']) && isset($_POST['repeat-password'])){

    // Filter the password string from unnecessary characters to prevent the user from causing security issues
    filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    filter_var($_POST['repeat-password'], FILTER_SANITIZE_STRING);

    // Check the length of the password
    if(strlen($_POST['password']) < $passLength){
        $createAccountErrors[] = "Password has to be more than 4 characters.";
    }else{
        // compare the two passwords the user enters while creating an account
        if(strcmp($_POST['password'], $_POST['repeat-password']) == 0){
            $pass = $_POST['password'];
            $hashedPass = sha1($pass);
        }else{
            $createAccountErrors[] = "Passwords don't match";
        }
    }
}

if(isset($_POST['email'])){

    // Filter the email string from unnecessary characters to prevent the user from causing security issues
    filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $email = $_POST['email'];

}

if(count($createAccountErrors) == 0){ // if there are no errors that took place while the user is creating the account

    // Check if the username already exists
    $stmt = $con->prepare('SELECT username FROM user WHERE username = ?');
    $stmt->execute(array($user));
    $count = $stmt->rowCount();
    if($count > 0){ // if the username is already used
        $createAccountErrors[] = "Username already used";
        echo 0;
    }else{
        // Enter the user's information into the database
        $stmt = $con->prepare('INSERT INTO user (username, password, email) VALUES (:username, :password, :email)');
        $stmt->execute(array(
            ":username" => $user,
            ":password" => $hashedPass,
            ":email"	=> $email
        ));
        $success = "Account created successfully";
        echo 1;
    }
} else {
    echo 0;
}