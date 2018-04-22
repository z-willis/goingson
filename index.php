<?php

    session_start();    // Start the session

    // If a user has already logged in redirect him to the map without having to log in again
    /*if(isset($_SESSION['user'])){
        header('Location: map.php');
    }*/

    include 'connect.php';

    $userLength = 5;                // the minimum length of the username
    $passLength = 5;                // the minimum length of the password
    // varaibles for the username, password, hashing the password, email, and checking the user's account 
    $user = $pass = $hashedPass = $email = $existingAccount = $success = '';
    $createAccountErrors = array();         // an array to store the errors while creating an account
    $loginErrors = array();                 // an array to store the errors while logging in

    if($_SERVER['REQUEST_METHOD'] == 'POST'){        
        switch($_POST['account']){

            // when the user presses the sign in buttom
            case 'Login':{
                
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
                    header('Location: map.php');
                } else{
                    $loginErrors[] = 'No such account exists';
                }
            }
            break;

            //When the user clicks on the create account button
            case 'Create Account':{
                            
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
                    }else{
                        // Enter the user's information into the database
                        $stmt = $con->prepare('INSERT INTO user (username, password, email) VALUES (:username, :password, :email)');
                        $stmt->execute(array(
                            ":username" => $user,
                            ":password" => $hashedPass,
                            ":email"	=> $email
                        ));
                        $success = "Account created successfully";
                    }
                }
            }
            break;
        }  
    }
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>GoingZ On - Login</title>
        <link rel="stylesheet" href="style/normalize.css">
        <link rel="stylesheet" href="style/form.css">
    </head>
    <body class="all">
        <section class="main">
            <h1 class="title">GoingZ On</h1>
            <p class="body">GoingZ On is Google Maps-based web app that allows users to view events and ask/answer questions about their surroundings.</p>

            <div class="options">
                <h2 class="login">Login</h2>
                <h2 class="blocker"> | </h2>
                <h2 class="create-account">Sign Up</h2>
            </div>
            <section class="container">
                
                <!-- Start login form -->
                <form class="login-form"  method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" >
                    <div class="user-input">
                        <input class="input" type="text" name="username" placeholder="Username" autocomplete="off">
                    </div>
                    <div class="user-pass">
                        <input type="password" name="password" placeholder="Password" autocomplete="new-password">
                    </div>
                    <input type="submit" value="Login" name="account" disabled>
                    <p class="errors"><?php foreach($loginErrors as $error){echo $error;}?></p>
                    <p class="success"><?php if(strlen($success) != 0){echo $success;}?></p>
                </form>
                <!-- End login form -->
                
                <!-- Start create account form -->
                <form class="create-account-form " method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>">
                    <div class="user-input">
                        <input type="text" name="username" placeholder="Username" autocomplete="off">
                        <p class="temporary">Username length must be more than 4 characters</p>
                        <p class="availability"></p>
                    </div>
                    <div class="user-pass">
                        <input type="password" name="password" placeholder="Password" autocomplete="new-password">
                        <p class="temporary">Password length must be more than 4 characters</p>
                    </div>
                    <div class="rep-pass">
                        <input type="password" name="repeat-password" placeholder="Re-enter password" autocomplete="new-password">
                        <p class="temporary"></p>
                    </div>
                    <div class="user-email">
                        <input type="text" name="email" placeholder="Email" autocomplete="off">
                        <p class="temporary">Please enter a valid email</p>
                    </div>
                    <input type="submit" value="Create Account" name="account" disabled>
                    <?php 
                        foreach($createAccountErrors as $error){
                            echo "<p>" . $error . "</p>";
                        }
                    ?>
                </form>
                <!-- End create account form -->
            </section>
        </section>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="js/form.js"></script>
        <!--<script src="js/oldform.js"></script>-->
    </body>
</html>