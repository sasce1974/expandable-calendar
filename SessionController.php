<?php
/**
 * Created by Aleksandar Ardjanliev.
 * User: Saso
 * Date: 11/17/2019
 * Time: 6:28 PM
 */



/** THIS CONTROLLER IS ONLY FOR TESTING PURPOSE */



require "includes/config.inc.php";
require BASE_URI . "includes/functions.php";
require "SessionClass.php";
include BASE_URI . "controllers/UserClass.php";


if(isset($_REQUEST['get_sessions'])){
    $session = new Session(connectPDO());
    $ses = $session->getAllSessions();
    $data = array();
    foreach ($ses as $se){
        foreach ($se as $item=>$value){
            print "Session Key: " . $item . " - Value: " . $value . "<br>";
        }
    }
}