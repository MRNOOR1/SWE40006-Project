<?php
 
 if(empty($_POST["username"])){
    die("Username is required");
 }
 if(strlen($_POST["password"]) < 8){
    die("password must be at least 8 characters");
 }

 if (! preg_match("/[a-z]/i", $_POST["password"])){
    die("password must contain at least 1 letter");
 }

 $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);


?>