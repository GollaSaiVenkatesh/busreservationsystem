<?php
session_start();
if(isset($_SESSION["username"]) && $_SESSION["username"] === true){
    header("location: mainpage.html");
    exit;
}
?>