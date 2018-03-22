<?php
    include 'connect.php';

    $userLength = 5;                // the minimum length of the username
    $passLength = 5;                // the minimum length of the password
    // varaibles for the username, password, hashing the password, email, and checking the user's account 
    $user = $pass = $hashedPass = $email = $existingAccount = '';
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
                $stmt = $con->prepare('SELECT username, password FROM user WHERE username = ? AND password = ?');
                $stmt->execute(array($user , $hashedPass));
                $count = $stmt->rowCount();

                if($count > 0){ // if a user exists
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
                        header("Location: login.php");
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
        <title>Login</title>
        <link rel="stylesheet" href="style/normalize.css">
        <link rel="stylesheet" href="style/form.css">
    </head>
    <body>
        <section class="main">
            <p>
                <span class="login">Login | </span>
                <span class="create-account">Sign Up</span>
            </p>
            <section class="container">
                <form class="login-form"  method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" > 
                    <input type="text" name="username" placeholder="Username" autocomplete="off">
                    <input type="password" name="password" placeholder="Password" autocomplete="new-password">
                    <input type="submit" value="Login" name="account" disabled>
                    <?php 
                        foreach($loginErrors as $error){
                            echo "<p>" . $error . "</p>";
                        }
                    ?>
                </form>
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
                        <input type="text" name="email" placeholder="Email">
                        <p class="temporary">Please enter a valid email</p>
                    </div>
                    <input type="submit" value="Create Account" name="account" disabled>
                    <?php 
                        foreach($createAccountErrors as $error){
                            echo "<p>" . $error . "</p>";
                        }
                    ?>
                </form>
            </section>
        </section>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="js/form.js"></script>
    </body>
</html>