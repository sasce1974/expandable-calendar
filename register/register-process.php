<?php
require_once("../includes/functions.php");
require BASE_URI . "controllers/UserClass.php";
if (!isset($_POST['submit'])) {
    die(header("Location: ../register/"));
}


$_SESSION["formAttempt"] = true;
if (isset($_SESSION["error"])){
    unset($_SESSION["error"]);
}
$_SESSION["error"]= array ();


$con = connectPDO();
$ip = $_SERVER['REMOTE_ADDR'];

//Retrieve registered users from same IP address registered in the last 10 hours and block that IP address
// if there are more than 10 users registered.
$query = $con->prepare("SELECT id FROM users WHERE ip=? AND created_at >= DATE_SUB(NOW(), INTERVAL 10 HOUR)");
$query->execute(array($ip));
if($query->rowCount() > 4){
    $_SESSION['error'][] = "You have exceeded the number of registered users per day. Please 
        try to register after 10 hours.";

    $con = $query = $ip = null;
    unset($query, $ip);
    header("Location: ../register/");
    exit();
}





$required= array("email", "password1", "password2", "name");
foreach ($required as $requiredField) {
    if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
        $_SESSION["error"][] = $requiredField . " required.";
    }
}

//check if submitted token is valid...
if(!isset($_POST['init']) || $_POST['init'] !== $_SESSION['initToken']){
    $_SESSION['error'] = "Error in the submitted data, please try again.";
    unset($_SESSION['initToken']);
}

if (!preg_match("/^[a-zA-Z .]+$/", $_POST["name"])) {
    $_SESSION["error"][] = "Name and surname can contain only letters.";
}

if (isset($_POST["phone"]) && $_POST["phone"] != "") {
    if (!preg_match("/^[\+\d]+$/", $_POST["phone"])) {
        $_SESSION["error"][] = "Phone number can contain only numbers and '+'";
    } else if ((strlen($_POST["phone"]) < 8) || (strlen($_POST["phone"]) > 14)) {
        $_SESSION["error"][] = "Phone number need to be of at least 8 numbers";
    }
} 
if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"][] = "Invalid email address"; }

//check if the password is strong enough...
if(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,40})/", $_POST['password1'])){
    $_SESSION["error"][] = "Password must have at least 1 big letter, 1 small letter, 1 number and to be between 8 and 40 characters long.";
}


if ($_POST["password1"] != $_POST["password2"]) {
    $_SESSION["error"][] = "Passwords doesn't match"; }
  
if (count($_SESSION["error"]) > 0) {
    die(header("Location: ../register/"));
    } else {
        if(registerUser($_POST)) {
            unset($_SESSION["formAttempt"]);
            die ();
           // die(header("Location: success.php"));
        } else {
            error_log("Problem with the registration: {$_POST['email']}");
            $_SESSION["error"][] = "There was some problem with the registration, please try again.";
            die(header("Location: ../register"));
        }
    }

function registerUser($userData) {
    global $ip;
    $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DB);
    if ($mysqli->connect_errno) {
        error_log("Cannot connect to MySQL: " . $mysqli->connect_error);
        return false;
    } 
    $email = $mysqli->real_escape_string($_POST["email"]);
//check for an existing user - email
    $findUser = "SELECT id FROM users WHERE email = '{$email}'";
    $findResult = $mysqli->query($findUser);
    $findRow = $findResult->fetch_assoc();
    if (isset($findRow['id']) && $findRow['id'] != "") {
        $_SESSION["error"][] = "A user with the same e-mail address already exists.";
        return false;
    }
    $name = $mysqli->real_escape_string($_POST["name"]);
    //check for an existing user - name
    $findUser = "SELECT id FROM users WHERE name = '{$name}'";
    $findResult = $mysqli->query($findUser);
    $findRow = $findResult->fetch_assoc();
    if (isset($findRow['id']) && $findRow['id'] != "") {
        $_SESSION["error"][] = "A user with the same name and surname already exists.";
        return false;
    } 
   
    $cryptedPassword = password_hash($_POST["password1"], PASSWORD_DEFAULT);
    $password = $mysqli->real_escape_string($cryptedPassword);

    if (isset($_POST["phone"])) {
        $phone = $mysqli->real_escape_string($_POST["phone"]);
    } else {
        $phone = "";
    }

    if (isset($_POST["note"])) {
        $note = $mysqli->real_escape_string($_POST["note"]);
    } else {
        $note = "";
    }


    
    $query = "INSERT INTO users (id, email, password, name, phone, is_active, note, new_user, ip) 
              VALUES (Null, '{$email}', '{$password}', '{$name}', '{$phone}', 1, '{$note}', 1, '{$ip}')";



    if ($mysqli->query($query)) {
        $id = $mysqli->insert_id;
        error_log("Inserted {$email} as ID {$id}");

        //Send welcome message to this new user...
        //include (BASE_URI . "controllers/messages/Messages.php");
        //include (BASE_URI . "controllers/UserClass.php");
        //include (BASE_URI . "controllers/users/MenageUsers.php");
        //$msg = new Messages();
        //$msg->messageToAllAdmins("New User Registered", "<b>$name</b> registered on
       // Eastern Therapies with ID: <b>$id</b> on <span class='text-success'>"
         //   . date("d.m.Y") . "</span> at " . date("H:i") . " with IP address: $ip", $id);

        //$msg->newMessage("Welcome $name", "Thank you for registering to Eastern
        //Therapies.", $id, 0);

        //$msg = null;
        //unset($msg);

        //TODO set table for user photo...
        /*$mysqli->query("INSERT INTO user_photo (user_id, path)
                      VALUES ('$id', 'images/user_photo/default.png')");*/
        $_SESSION['message'] = "User $name is successfully registered. Please sign in to continue.";
        header('Location: ../login/');
        return true;
    } else {
        error_log("Problem inserting {$query} from user with IP: $ip");
        return false;
    } 
} //end function registerUser

