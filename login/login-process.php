<?php
try {
    require_once("../includes/functions.php");
    require (BASE_URI . 'controllers/UserClass.php');


    if (!isset($_POST['submit'])) {
        die(header("Location: ../"));
    }

    $_SESSION["formAttempt"] = true;
    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }
    $_SESSION["error"] = array();
    $required = array("email", "password");
    foreach ($required as $requiredField) {
        if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
            $_SESSION["error"][] = $requiredField . " required.";
        }
    }
//check if submitted token is valid...
    $token = null;
    if(isset($_SESSION['initToken'])) $token = $_SESSION['initToken'];
    if (!isset($_POST['init']) || $_POST['init'] !== $token) {
        $_SESSION['error'][] = "Error in the submitted data, please try again.";
        unset($_SESSION['initToken']);
    }

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"][] = "Invalid email address";
    }


/** the next code retrieve all failed login attempt with ip and blocks the login process
    for that ip address in the last 10 min  */
    $con = connectPDO();

    $ip = $_SERVER['REMOTE_ADDR'];
    $query = $con->prepare("SELECT id FROM login_attempt WHERE ip=?
    AND time >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
    $query->execute(array($ip));
    if($query->rowCount() > 4){
        $_SESSION['error'][] = "You have exceeded the number of login attempts. We have 
        blocked IP address {$_SERVER['REMOTE_ADDR']} for a few minutes.";

        $con = $query = $ip = null;
        unset($query, $ip);
        header("Location: ../");
        exit();
    }


    if (count($_SESSION["error"]) > 0) {
        sleep(3);
        header("Location: index.php");
        exit();

    } else {

        $user = new User;
        if ($user->authenticate($_POST['email'], $_POST['password'])) {
            unset($_SESSION['formAttempt']);

            if(!$user->isActive()) $_SESSION['error'][]= "Your account is not activated. 
            Please wait for the Admin or send a message via contact form for activation.";

            if($user->getAttribute('deleted_at')!== null){
                $user->logout();
                session_start();
                $_SESSION['error'][] = "<b>Your account is been blocked.</b><br> 
                Possible reason is if you requested a deletion of your account 
                or it is suspended by the admin. Please contact admin for any request.";
            }

            die(header("Location: ../"));
        } else {
            //Not authenticated...

            $con = connectPDO();
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $ip = $_SERVER['REMOTE_ADDR'];
            $query = $con->prepare("INSERT INTO login_attempt (email, ip, time) 
            VALUES (?, ?, NOW())");
            $result = $query->execute(array($email, $ip));
            $con = $query = $email = $ip = null;
            unset($con, $query, $email, $ip);

            $_SESSION['error'][] = "Wrong email or password.";
            die(header("Location: index.php"));
        }
    }
}catch (Exception $e){
    errorMessage($e);
}
