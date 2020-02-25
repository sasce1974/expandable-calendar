<?php
require_once('../includes/functions.php');
//prevent access if they haven't submitted the form.
if (!isset($_POST['submit'])) {
    die(header("Location: ../"));
}
$_SESSION['formAttempt'] = true;
if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}
$_SESSION['error'] = array();
$required = array("email");
//Check required fields
foreach ($required as $requiredField) {
    if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
        $_SESSION['error'][] = $requiredField . " is required.";
    }
}
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'][] = "Invalid e-mail address";
}

//check if submitted token is valid...
if(!isset($_POST['init']) || $_POST['init'] !== $_SESSION['initToken']){
    $_SESSION['error'][] = "Error in the submitted data, please try again.";
    unset($_SESSION['initToken']);
}

if (count($_SESSION['error']) > 0) {
    die(header("Location: emailpass.php"));
} else {
    require (BASE_URI . 'controllers/UserClass.php');
    $user = new User;
    if ($user->emailPass($_POST['email'])) {
        unset($_SESSION['formAttempt']);
        die(header("Location: email-success.php"));
    } else {
        $_SESSION['error'][] = "This e-mail address is not registered.";
        die(header("Location: emailpass.php"));
    }
}
?>