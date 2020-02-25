<?php
class User {
    private $id;
    private $email;
    private $name;
    private $phone;
    private $is_active;
    private $note;
    private $is_admin;
    private $isLoggedInPlanner = false;//change it for each program
    public $errorType = "fatal";
    public $created_at;
    public $modified_at;
    public $deleted_at;
    
    function __construct() {
        if (session_id() == "") {
            if(!headers_sent()) {
                session_start();
//                include ("includes/db_sessions.php");
            }
        }
        if (isset($_SESSION['isLoggedInPlanner']) && $_SESSION['isLoggedInPlanner'] == true) {
            $this->_initUser();
        }
    } //end __construct

    public function getAttribute($attr){
        return $this->$attr;
    }
    
    public function authenticate($user, $pass) {
        if (session_id() == "") {
            session_start();
        }
        $_SESSION['isLoggedInPortfolio'] = false;
        $this->isLoggedInPortfolio = false;
        
        $mysqli = connectMysqli(); //new mysqli(DBHOST, DBUSER, DBPASS, DB);
        if ($mysqli->connect_errno) {
            error_log("Cannot connect to database: " . $mysqli->connect_error);
            $_SESSION['error'][] = "Cannot establish connection to database. Please try later again.";
            return false;
        }
        $safeUser = $mysqli->real_escape_string($user);
        $incomingPassword = $mysqli->real_escape_string($pass);
        $query = "SELECT * from users WHERE email = '{$safeUser}'";
        if (!$result = $mysqli->query($query)) {
            error_log("Cannot retrieve account for {$user}");
            $_SESSION['error'][] = "Account not found.";
            return false;
        }
        // Will be only one row, so no while() loop needed
        $row = $result->fetch_assoc();
        $dbPassword = $row['password'];
//        if (crypt($incomingPassword,$dbPassword) !=$dbPassword) {
        if (password_verify($incomingPassword,$dbPassword) !=$dbPassword) {
            error_log("Passwords for {$user} don't match");
            return false;
        }
        $this->id = $row['id'];
        $this->email = $row['email'];
        $this->name = $row['name'];
        $this->phone = $row['phone'];
        $this->is_active = $row['is_active'];
        $this->note = $row['note'];
        $this->is_admin = $row['is_admin'];
        $this->created_at = $row['created_at'];
        $this->modified_at = $row['modified_at'];
        $this->deleted_at = $row['deleted_at'];
        $this->isLoggedInPlanner = true;
    
        $this->_setSession();
        return true;
    } //end function authenticate
    
     private function _setSession() {
        if (session_id() == '') {
            session_start();
        }
        $_SESSION['id'] = $this->id;
        $_SESSION['email'] = $this->email;
        $_SESSION['name'] = $this->name;
        $_SESSION['phone'] = $this->phone;
        $_SESSION['is_active'] = $this->is_active;
        $_SESSION['note'] = $this->note;
        $_SESSION['is_admin'] = $this->is_admin;
        $_SESSION['created_at'] = $this->created_at;
        $_SESSION['modified_at'] = $this->modified_at;
        $_SESSION['deleted_at'] = $this->deleted_at;
        $_SESSION['isLoggedInPlanner'] = $this->isLoggedInPlanner;
        $_SESSION['session_time_created'] = time(); //time the session is created
        
        $_SESSION['next_monday']="";
        $_SESSION['start_date']="";
    } //end function setSession

    
    private function _initUser() {
        if (session_id() == '') {
            session_start();
        }
        $this->id = $_SESSION['id'];
        $this->email = $_SESSION['email'];
        $this->name = $_SESSION['name'];
        $this->phone = $_SESSION['phone'];
        $this->is_active = $_SESSION['is_active'];
        $this->note = $_SESSION['note'];
        $this->is_admin = $_SESSION['is_admin'];
        $this->created_at = $_SESSION['created_at'];
        $this->modified_at = $_SESSION['modified_at'];
        $this->deleted_at = $_SESSION['deleted_at'];
        $this->isLoggedInPlanner = $_SESSION['isLoggedInPlanner'];
    } //end function initUser
    
    public function logout() {
        $this->isLoggedInPlanner = false;
        if (session_id() == "") {
            session_start();
//            include ("includes/db_sessions.php");
        }
        $_SESSION['isLoggedInPlanner'] = false;
        foreach ($_SESSION as $key => $value) {
            $_SESSION[$key] = "";
            unset($_SESSION[$key]);
        }
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $cookieParameters = session_get_cookie_params();
            setcookie(session_name(), '', time() - 28800,
            $cookieParameters['path'], $cookieParameters['domain'],
            $cookieParameters['secure'], $cookieParameters['httponly']);
        } //end if
        session_destroy();
    } //end function logout
    
    public function sessionExpired($time = 31536000){ //1 year
        if(isset($_SESSION['session_time_created']) && time() - $_SESSION['session_time_created'] > $time){
            $this->logout();
        }
    }
    
    public function emailPass($user) {
        $mysqli = connectMysqli();
        if ($mysqli->connect_errno) {
                error_log("Cannot connect to MySQL: " . $mysqli->connect_error);
                $_SESSION['error'][] = "No connection to database. Please try later again.";
                return false;
        }
        // first, lookup the user to see if they exist.
        $safeUser = $mysqli->real_escape_string($user);
        $query = "SELECT id, email FROM users WHERE email = '{$safeUser}'";
        if (!$result = $mysqli->query($query)) {
            $_SESSION['error'][] = "Unknown Error";
            return false;
        }
        if ($result->num_rows == 0) {
            $_SESSION['error'][] = "User not found";
            return false;
        }
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $hash = uniqid("",TRUE);
        $safeHash = $mysqli->real_escape_string($hash);
        $insertQuery = "INSERT INTO resetpassword (email_id, pass_key, date_created, status) VALUES ('{$id}', '{$safeHash}', NOW(), 'A')";
        if (!$mysqli->query($insertQuery)) {
            error_log("Problem inserting resetPassword row for " . $id);
            $_SESSION['error'][] = "Unknown problem";
            return false;
        }
        $urlHash = urlencode($hash);
        //$site = "www.3delacto.com/projects/E-MEDIS/reset";
        //$resetPage = "/index.php";
        //$fullURL = "<a href='$site $resetPage?user=$urlHash'>Click here to reset your password</a><br>";
        $fullURL = "<a href='www.3delacto.com/projects/planner/reset/index.php?user=$urlHash'><b>Please click here to reset your password</b></a><br><br>\r\n";
        //set up things related to the e-mail
        $to = $row['email'];
        $subject = "Password Reset for Planner";
        $message = "<html><head><title>Planner Password Request</title></head><body><div style='text-align: center'>\r\n";
        $message .= "<img style='vertical-align: middle;margin:auto;' src='https://www.3delacto.com/portfolio/images/logo.png' alt='logo' width=100 height='auto'>\r\n";
        $message .= "<h3 style='color: darkblue;padding:1em;vertical-align: middle;border-radius: 6px'>Password reset requested for Eastern Therapies.</h3><br>\r\n";
        $message .= "<h4 style='border:1px solid darkblue;color:darkblue;border-radius:6px'>" . $fullURL . "</h4> \r\n";
        $message .= "<p>Or if the link above is not working then use the following url: 
        <i>www.3delacto.com/projects/planner/reset/index.php?user=$urlHash</i></p>
        <hr><small>If you didn't requested the link from this message, please ignore it.</small><br><div style='background-color: darkblue;height: 2em'></div>\r\n";
        $message .= "</div></body></html>";
        $headers = "From: 3Delacto Planner <no-reply@3delacto.com>" . "\r\n";
        $headers .="Reply-To: planner@3delacto.com" . "\r\n";
        $headers .="MIME-Version: 1.0" . "\r\n";
        $headers .="Content-Type: text/html; charset=UTF-8";
        mail($to,$subject,$message,$headers);
        return true;
    } //end function emailPass
    
    public function validateReset($formInfo) {
        $pass1 = $formInfo['password1'];
        $pass2 = $formInfo['password2'];
        if ($pass1 != $pass2) {
            $this->errorType = "nonfatal";
            $_SESSION['error'][] = "Passwords don't match";
            return false;
        }
        $mysqli = connectMysqli();// new mysqli(DBHOST, DBUSER, DBPASS, DB);
        if ($mysqli->connect_errno) {
                error_log("Cannot connect to MySQL: " . $mysqli->connect_error);
                return false;
        }
        $decodedHash = urldecode($formInfo['hash']);
        $safeEmail = $mysqli->real_escape_string($formInfo['email']);
        $safeHash = $mysqli->real_escape_string($decodedHash);
        $query = "SELECT k.id as id, k.email as email FROM users k, resetpassword r WHERE " .
            "r.status = 'A' AND r.pass_key = '{$safeHash}' " .
            " AND k.email = '{$safeEmail}' " .
            " AND k.id = r.email_id";
        if (!$result = $mysqli->query($query)) {
            $_SESSION['error'][] = "Unknown Error";
            $this->errorType = "fatal";
            error_log("database error: " . $formInfo['email'] . " - " . $formInfo['hash']);
            return false;
        } else if ($result->num_rows == 0) {
            $_SESSION['error'][] = "Link not active or user not found";
            $this->errorType = "fatal";
            error_log("Link not active: " . $formInfo['email'] . " - " . $formInfo['hash']);
            return false;
        } else {
            $row = $result->fetch_assoc();
            $id = $row['id'];
            if ($this->_resetPass($id, $pass1)) {
                return true;
            } else {
                $this->errorType = "nonfatal";
                $_SESSION['error'][] = "Error resetting password";
                error_log("Error resetting password: " . $id);
                return false;
            }
        }
    } //end function validateReset
    
    private function _resetPass($id, $pass) {
        $mysqli = connectMysqli();
        if ($mysqli->connect_errno) {
            error_log("Cannot connect to MySQL: " . $mysqli->connect_error);
            return false;
        }
        $safeUser = $mysqli->real_escape_string($id);
        $newPass = password_hash($pass, PASSWORD_DEFAULT);
        $safePass = $mysqli->real_escape_string($newPass);
        $query = "UPDATE users SET password = '{$safePass}' WHERE id = '{$safeUser}'";
        if (!$mysqli->query($query)) {
            return false;
        } else {
            return true;
        }
    } //end function _resetPass


    //returns profile photo path if the photo is approved...
    /*public function photo(){
        $photo_query = "SELECT path, approved FROM user_photo WHERE user_id = $this->id";
        $con = connectPDO();
        $res = $con->query($photo_query);
        $path = $res->fetch(PDO::FETCH_ASSOC);
        $con = $photo_query = $res = null;
        if($path and $path['approved']==1){
            return $path['path'];
        }else{
            return "user_photo/default.png";
        }
    }*/


    //Returns the number of new (not viewed) messages to authenticated user...
    /*public function countMessages(){
        $con = connectPDO();
        $number = $con->query("SELECT COUNT(id) FROM messages WHERE sent_to = $this->id AND viewed IS NULL AND deleted_at IS NULL");
        $number = $number->fetchColumn();
        $con = null;
        return $number;

    }*/

    //return user if is admin...
    public function isAdmin (){
        if($this->is_active == 1 && $this->is_admin == 1) {
            return true;
        }else{
            return false;
        }
    }

    //return user if is active...
    public function isActive (){
        if($this->is_active == 1) {
            return true;
        }else{
            return false;
        }
    }

    //Check if email already exists in database...
    public function emailExists ($email){
        $con = connectPDO();
        $query = $con->prepare("SELECT COUNT(id) FROM users WHERE email = ?");
        $query->execute(array($email));
        $rowcount = $query->fetchColumn();
        if ($rowcount > 0) {
            return true;
        }else{
            return false;
        }
    }

} //end class User

