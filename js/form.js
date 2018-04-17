var passLength = 5,
    userLength = 5,
    loginForm = $(".login-form"),
    loginUser = $(".login-form input[name=username]"),
    loginPass = $(".login-form input[name=password]"),
    loginSubmit = $(".login-form input[type=submit]"),
    loginList = $(".login-form input[name=username], .login-form input[name=password]"),
    loginErrors = $(".login-form > p.errors"),
    createAccountSuccess = $(".login-form > p.success"),
    createAccountForm = $(".create-account-form"),
    createAccountUser = $(".create-account-form .user-input input[name=username]"),
    createAccountPass = $(".create-account-form .user-pass input[name=password]"),
    repeatPass = $(".create-account-form .rep-pass input[name=repeat-password]"),
    createAccountEmail = $(".create-account-form .user-email input[name=email]"),
    createAccountSubmit = $(".create-account-form input[type=submit]"),
    createAccountList = $(".create-account-form .user-input input[name=username], .create-account-form .user-pass input[name=password], .create-                              account-form .rep-pass input[name=repeat-password], .create-account-form .user-email input[name=email]"),
    errors = $(".create-account-form div input[type=submit] + p");

$(document).ready(function(){
   
    'use strict';
    
    $(".all").css("height", window.innerHeight);
        
    // Switch between the login form and the create account form
    $(".login").click(function(){
        $(".main").animate({
            height: "447px"
        },600);
        loginForm.siblings().css("display", "none");
        loginForm.fadeIn(1500);
        document.title = "GoingZ On - Login";
    });
    
    $(".create-account").click(function(){
        $(".main").animate({
            height: "580px"
        },600);
        createAccountForm.siblings().css("display", "none");
        createAccountForm.fadeIn(1500);
        document.title = "GoingZ On - Sign Up";
    });
    
    // Activate the login button only when the user enters all the information needed
    loginList.keyup(function(){
        if(loginUser.val().length >= userLength && loginPass.val().length >= passLength){
            loginSubmit.removeAttr("disabled");
            loginSubmit.css("cursor", "pointer");
        }else{
            loginSubmit.attr("disabled", "disabled");
            loginSubmit.css("cursor", "not-allowed");
        } 
    });
        
    // Activate the create account button only when the user enters all the information needed
    createAccountList.keyup(function(){
        if(createAccountUser.val().length >= userLength && createAccountPass.val().length >= passLength && createAccountPass.val() == repeatPass.val() && validateEmail(createAccountEmail.val())){
            createAccountSubmit.removeAttr("disabled");
            createAccountSubmit.css("cursor", "pointer");
        }else{
            createAccountSubmit.attr("disabled", "disabled");
            createAccountSubmit.css("cursor", "not-allowed");
        } 
    });
    
    // Show a message to the user as a hint to indicate the length necessary for the username
    createAccountUser.focus(function(){
        $(this).keyup(function(){
            if($(this).val().length >= userLength){
                $(this).next().hide("slide", {direction: "left"}, 1000);
            }else{
                $(this).next().show("slide", {direction: "left"}, 1000);
            }
        });
    }).blur(function(){
        $(this).next().hide("slide", {direction: "left"}, 1000);
    });
    
    // Check the availability of the username once the user satisfies the required length of the username
    createAccountUser.change(function(){
        if($(this).val().length >= userLength){
            checkAvailability();
        }
    });
    
    // Show a message to the user as a hint to indicate the length necessary for the password
   createAccountPass.focus(function(){
        $(this).keyup(function(){
            if($(this).val().length >= passLength){
                $(this).next().hide("slide", {direction: "left"}, 1000);
            }else{
                $(this).next().show("slide", {direction: "left"}, 1000);
            }
        });
    }).blur(function(){
        $(this).next().hide("slide", {direction: "left"}, 1000);
    });
    
    // Show a message to the user to indicate if the passwords match or not
    repeatPass.change(function(){        
        if($(this).val() == createAccountPass.val()){
            $(this).next().text("Passwords match").show("slide", {direction: "left"}, 1000).delay(500).hide("slide", {direction: "left"}, 1000);
        }else{
            $(this).next().text("Passwords don't match").show("slide", {direction: "left"}, 1000).delay(500).hide("slide", {direction: "left"}, 1000);
        }
    });
    
    // Show a message to the user to tell him/her to enter a valid email
    createAccountEmail.focus(function(){
        $(this).keyup(function(){
            // Validate the email of the user
            if(validateEmail($(this).val())){
                $(this).next().hide("slide", {direction: "left"}, 1000);
            }else{
                $(this).next().show("slide", {direction: "left"}, 1000);
            }
        });
    }).blur(function(){
        $(this).next().hide("slide", {direction: "left"}, 1000);
    });
    
    // Show the create account form if the user made an error while creating the account (after reloading the page)
    if(errors.length != 0){
        createAccountForm.siblings().css("display", "none");
        createAccountForm.fadeIn(1000);
    }
    
    // Show a error message to the user if something is wrong with the login
    if(!loginErrors.text() == ""){
        loginErrors.slideDown(450).delay(1250).slideUp(450);
    }
    
    // Show a success message to the user a successfully creating an account
    if(!createAccountSuccess.text() == ""){
        createAccountSuccess.slideDown(450).delay(1500).slideUp(450);
    }
    
});

// A function that validate the email of the user
function validateEmail(email){
    var pattern = /\S+@\S+\.com|net|edu|org$/;
    return pattern.test(email);
}


// Checking the database on the spot to inform the user about the availability of the username
function checkAvailability() {
    jQuery.ajax({
        url: "database_function.php?function=checkUsername",
        data:'username=' + createAccountUser.val(),
        type: "POST",
        success:function(data){
            if(data > 0){
                createAccountUser.next().next().text("Username already exists").show("slide", {direction: "left"}, 1000).delay(500).hide("slide", {direction: "left"}, 1000);
            }else{
                createAccountUser.next().next().text("Username available").show("slide", {direction: "left"}, 1000).delay(500).hide("slide", {direction: "left"}, 1000);
            }
        }
    });
}
