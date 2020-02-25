<?php
/**
 * Created by Aleksandar Ardjanliev.
 * Date: 7/10/2019
 * Time: 1:30 PM
 */

//Session is initialized in the database sessionClassDB
require "sessionClassDB.php";

require_once ("config.inc.php");

/**
 * function @connectPDO is used as one option to do connection to database
 * Another option is by initializing the ConnectionClass
 */
try{
    function connectPDO ()
    {
        $con = new PDO("mysql:host=" . DBHOST . ";dbname=" . DB, DBUSER, DBPASS);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if($con){
            return $con;
        }else{
            throw new PDOException("There is no connection to database at the moment.");
        }
    }
}catch(PDOException $e){
    errorMessage($e);
}


function connectMysqli(){
    $con = new mysqli(DBHOST, DBUSER, DBPASS, DB);
    return $con;
}

// Object $session is used the whole application to store the session
$session = new sessionClassDB(connectPDO());


//function to create token...
function token($length = 64)
{
    $token = bin2hex(random_bytes($length));
    $_SESSION['token'] = $token;
    return $token;
}


//Timestamp to date...
function timestampToDate($timestamp, $format = "Y-m-d")
{
    if($timestamp == "" || $timestamp == null){
        return false;
    }else{
        return date($format, strtotime($timestamp));
    }

}
