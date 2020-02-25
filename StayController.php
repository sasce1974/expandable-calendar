<?php
/**
 * Created by Aleksandar Ardjanliev.
 * User: Saso
 * Date: 11/6/2019
 * Time: 8:58 PM
 */
require "includes/config.inc.php";
require BASE_URI . "includes/functions.php";

require BASE_URI . "controllers/UserClass.php";
$user = new User();
if(!$user->isActive()){
    print "Please <a href='" . BASE_URL . "/login/'>log in</a> to use planner.";
    exit(404);
}

require BASE_URI . "ConnectionClass.php";
require_once BASE_URI . "DaysOfStayClass.php";

$user_id = $user->getAttribute('id');
$con = Connection::getInstance()->getConn();
$plan = new DaysOfStay($con, $user_id);

if (isset($_REQUEST['my_stay'])){
    if(isset($_REQUEST['period']) && $_REQUEST['period'] !=""){
        $period = filter_var($_REQUEST['period'], FILTER_SANITIZE_NUMBER_INT);
    }else{
        $period = 180;//180 days
    }


    $myStay = $plan->getPeriodDates($period);
    if(!$myStay){
        print "Data cannot be shown!";
    } else{
        print_r($myStay);
    }

}

if(isset($_POST['new_date'])){
    $date = filter_var($_POST['new_date'], FILTER_SANITIZE_STRING);
    $date = date("Y-m-d", strtotime($date));

    if($plan->insertDate($date, $note = null)){
        //print "Date: " . date("d.m.Y", strtotime($date)) . " successfully inserted!";
    }else{
        print "Date not entered!";
    }
}

if(isset($_POST['erase_date'])){
    $date = filter_var($_POST['erase_date'], FILTER_SANITIZE_STRING);
    $date = date("Y-m-d", strtotime($date));

    if($plan->deleteDate($date)){
        //print "Date: " . date("d.m.Y", strtotime($date)) . " removed from the schedule!";
    }else{
        print "Date not removed!";
    }
}

unset($con, $plan);