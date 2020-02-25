<?php
/**
 * Created by Aleksandar Ardjanliev.
 * User: Saso
 * Date: 10/31/2019
 * Time: 9:31 PM
 */

//session_start();
require "includes/config.inc.php";
require BASE_URI . "includes/functions.php";
include BASE_URI . "PlannerClass.php";
include BASE_URI . "DaysOfStayClass.php";
include BASE_URI . "ConnectionClass.php";
require BASE_URI . "controllers/UserClass.php";
$user = new User();

/** Creating instances of Plan and of DaysOfStay classes */
$plan = firstPlanner\Planner::getInstance();
$con = Connection::getInstance()->getConn();
$dates = new DaysOfStay($con, $user->getAttribute('id'));

/** @var $data
 * will contain all information that will be returned
 */
$data = array();

$data['error_message'] = array();
$data['message'] = array();

/** GET ERRORS OR MESSAGES FROM SESSION AND put into $data */
if(!empty($_SESSION['message'])){
    if(is_array($_SESSION['message'])){
        foreach ($_SESSION['message'] as $m){
            $data['message'][]= $m;
        }
    }else{
        $data['message'][] = $_SESSION['message'];
    }
    unset($_SESSION['message']);
}

if(!empty($_SESSION['error'])){
    if(is_array($_SESSION['error'])){
        foreach ($_SESSION['error'] as $e){
            $data['error_message'][] = $e;
        }
    }else{
        $data['error_message'][] = $_SESSION['error'];
    }
    unset($_SESSION['error']);
}


/** @var $allStayDays
 * contains all records for provided user_id
 * We gather only dates in the array
 */
$allStayDays = array();
if($user->isActive()){
    $allStayDays = $dates->getDates($user->getAttribute('id'));
}


$allStayD = array();
// All user checked dates are inserted in an array...
foreach ($allStayDays as $all_days){
    $allStayD[] = $all_days['date'];
}

/** Switching plan according the given $page in URL */
if(isset($_REQUEST['page'])){
$p = $_REQUEST['page'];
$p = filter_var($p, FILTER_SANITIZE_STRING);
}else{
    $p = "";
}

$page = "";
switch ($p){
    case 'home': $page = 'current'; break;
    case 'back': $page = 'back'; break;
    case 'next': $page = 'next'; break;
    case 'today': $page = 'today'; break;
    case 'next_day': $page = '+ 1 day'; break;
    case 'previous_day': $page = '- 1 day'; break;
    case 'byPeriod': $page = 'byPeriod'; break;
    case 'month': $page = 'month'; break;
    case 'next_month': $page = 'next_month'; break;
    case 'previous_month': $page = 'previous_month'; break;
    case 'previous_period': $page = 'previous_period'; break;
    case 'next_period': $page = 'next_period'; break;
    case 'last_one': $page = 'last_one'; break;
    default: $page = 'current';
}

/** switch back and next according to existing plan in session */

if(($page == 'next' || $page == 'back') && isset($_SESSION['plan'])){
    if(in_array($_SESSION['plan'], array('today', '- 1 day', '+ 1 day'))){
        $page == 'next' ? $page = '+ 1 day' : $page = '- 1 day';
    }elseif (in_array($_SESSION['plan'], array('current', 'next', 'back'))){
        $page == 'next' ? $page = 'next' : $page = 'back';
    }elseif (in_array($_SESSION['plan'], array('month', 'next_month', 'previous_month'))){
        $page == 'next' ? $page = 'next_month' : $page = 'previous_month';
    }elseif(in_array($_SESSION['plan'], array('byPeriod', 'next_period', 'previous_period'))) {
        $page == 'next' ? $page = "next_period" : $page = 'previous_period';
    }
}


/** $sd and $ed holds provided start and end dates for plan by period */
$sd = $ed = null;
if(isset($_REQUEST['sd'])) $sd = filter_var($_REQUEST['sd'], FILTER_SANITIZE_STRING);
if(isset($_REQUEST['ed'])) $ed = filter_var($_REQUEST['ed'], FILTER_SANITIZE_STRING);
if(isset($_REQUEST['rows'])) $rows = filter_var($_REQUEST['rows'], FILTER_SANITIZE_NUMBER_INT);


/** $rows is being set */
if(isset($rows) && $rows != null){
    $rows = filter_var($rows, FILTER_SANITIZE_NUMBER_INT);
    if($rows < 1 || $rows > 24) $rows = null;
    $_SESSION['rows'] = $rows;
}else{
    if(isset($_SESSION['rows'])){
        $rows = $_SESSION['rows'];
    }else{
        $rows = 8;
    }
}




if($page==="byPeriod" & ($sd=='' || $ed=='')){
    $data['error_message'] = '<h5>Please provide start date and end date</h5>';
    //print json_encode($data);
    //exit(400);
}


/** $thisPlan contains the plan */

$thisPlan = $plan->getPlan($page, $sd, $ed, $rows);








/** Get plan and days of stay in one array */

$data['plan'] = $thisPlan;
$data['stayDates'] = $allStayD;
$data['user_name'] = $user->getAttribute('name');

print json_encode($data);

unset($user, $thisPlan, $plan, $dates, $allStayDays, $allStayD, $all_days, $data, $p, $page);