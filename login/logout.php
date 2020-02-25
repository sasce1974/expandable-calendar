<?php 
require_once("../includes/functions.php");
require (BASE_URI . 'controllers/UserClass.php');
$user = new User;
$user->logout();
die(header("Location: ../"));
?>