<?php
/**
 * Created by Aleksandar Ardjanliev.
 * User: Saso
 * Date: 10/31/2019
 * Time: 9:31 PM
 */

session_start();
require "includes/config.inc.php";
include "PlannerClass.php";
include "DaysOfStayClass.php";
include "ConnectionClass.php";

$plan = firstPlanner\Planner::getInstance();

$dates = new DaysOfStay();

/** @var $period_of_days is how many days backwards we need to take in account
 to calculate the checked days (days of stay) */
$period_of_days = 180;

/** @var  $stayDates gets all the inserted dates for the given period
  by the $period_of_days variable */
$stayDates = $dates->getPeriodDates(1, $period_of_days);
$stayD = array();
//All dates are inserted in a array...
foreach($stayDates as $insertedDates){
    $stayD[] = $insertedDates['date'];
}
//We get the number of dates in the array - number of checked days within 180 days
$number_of_days_stayed = count($stayD);

/** @var $allStayDays contains all records for provided user_id */
$allStayDays = $dates->getDates(1);
$allStayD = array();
// All user checked dates are inserted in an array...
foreach ($allStayDays as $all_days){
    $allStayD[] = $all_days['date'];
}


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

/** @var  $sd and $ed get provided start and end dates for plan by period*/
$sd = $ed = null;
if(isset($_REQUEST['sd'])) $sd = filter_var($_REQUEST['sd'], FILTER_SANITIZE_STRING);

if(isset($_REQUEST['ed'])) $ed = filter_var($_REQUEST['ed'], FILTER_SANITIZE_STRING);

if(isset($_REQUEST['rows'])) $rows = filter_var($_REQUEST['rows'], FILTER_SANITIZE_NUMBER_INT);

if($page==="byPeriod" & ($sd=='' || $ed=='')){
    print "<h5>Please provide start date and end date</h5>";
    exit(400);
}

/** @var $rows is being set */
if(isset($rows) && $rows != null){
    //$rows = filter_var($rows, FILTER_SANITIZE_NUMBER_INT);
    if($rows < 1 || $rows > 24) $rows = null;
    $_SESSION['rows'] = $rows;
}else{
    if(isset($_SESSION['rows'])){
        $rows = $_SESSION['rows'];
    }else{
        $rows = 8;
    }
}


/** @var  $thisPlan  contains the plan */

$thisPlan = $plan->getPlan($page, $sd, $ed, $rows);

// Converting dates to unix time...
$start_date = strtotime($thisPlan['startDate']);
$end_date = strtotime($thisPlan['endDate']);
//$rows = $thisPlan['rows'];
$number_of_days = ceil(abs($end_date - $start_date) / 86400);

//if the plan is a month, there will be 42 fields/cells in a table for each month (starting from 0 to 41)
if (in_array($thisPlan['plan'],array('month', 'next_month', 'previous_month'))) $number_of_days = 41;

if($number_of_days_stayed > 80){
    $red_green = "#f77";
}elseif($number_of_days_stayed > 50){
    $red_green = "#f70";
}else{
    $red_green = "#afa";
}


print "<div style='background-color: $red_green; text-align: center'>
        You have overall $number_of_days_stayed days checked in during the 
        period of the last $period_of_days days!
        </div>";

$period_in_procentage = round($number_of_days_stayed/90 * 100);

print "<div class='bg-secondary border my-1 rounded'>
        <div style='text-align:center;background-color: $red_green; width:" .
        ($period_in_procentage <= 100 ? $period_in_procentage : 100) . "%'>
        $period_in_procentage%   
        </div>
        </div>";
print "<h4 class='text-uppercase text-center'>" . date('F Y', strtotime('-7 day', $end_date)) . "</h4>";
print <<<HERE
    <div class="px-1" style="overflow-x: auto">
    <table class="table table-striped text-center">
        <tr style="height: 5px;">
HERE;
    if(in_array($thisPlan['plan'],array('month', 'next_month', 'previous_month')))
        print '</tr>';
    $weekday = 0;
    print "<tr>";
    for ($d = 0; $d <= $number_of_days; $d++) {
        $next_day = date('Y-m-d', strtotime("+$d day", $start_date)); //current day
        $day = date('D', strtotime("+$d day", $start_date)); //current day in english

        /** PRINT MONTH CALENDAR */
        if (in_array($thisPlan['plan'],array('month', 'next_month', 'previous_month'))) {
            $current_mont = date('m', strtotime($next_day));
            $day_number = date('d', strtotime("+$d day", $start_date));

            if ($weekday == 0) print "<tr>";
            $weekday++;
            $fieldColor = ($next_day == date('Y-m-d', strtotime('today'))) ? 'lightgreen' : '#9af';
            if($current_mont != date('m', strtotime('-15 day', $end_date))) $fieldColor = '#ddd';
            $checked = '';
            if(in_array($next_day, $allStayD)) $checked = 'checked';
            if(in_array($next_day, $stayD)) $fieldColor = '#faa';

//            print "<td class='planner_field p-1 border text-left' style='background-color: $fieldColor' onclick='openDay(\"$next_day\")'\">

            /** @var $td  is a number of dates between current date in the field and first date of that period */
            $td = count(array_filter($allStayD, function ($array){
                global $next_day, $period_of_days;
                if(strtotime($array) <= strtotime($next_day) && strtotime($array) >= (strtotime("-$period_of_days day", strtotime($next_day)))){
                    return true;
                }else{
                    return false;
                }
            }));


            print "<td class='planner_field p-1 border text-left' style='background-color: $fieldColor')'\">
                       <input class='val' type='text' value='$next_day' disabled hidden>
                       <span class='rounded bg-dark text-light px-1'>" . $day_number . "</span>
                       <div class='text-center d-inline ml-1'>" . $day . "</div>
                       <span class='float-right'><input class='form-check m-1 get_check' style='transform: scale(2)' 
                       type='checkbox' value='$next_day' $checked></span>
                       <div class='bottom-right-corner font-weight-bold'>$td</div>
                   </td> \n";
            if ($weekday == 7) {
                $weekday = 0;
                print "</tr>";
            }

        } else {


            /** PRINT WEEK OR DAY PLAN */
            print "<td style='min-width:80px;width:auto !important;'>" . date_format(date_create($next_day), 'd M Y') . "<br><b>" . $day . "</b></td> \n";
        }
    }
    print "</tr>";
    if(!in_array($thisPlan['plan'], array('month', 'next_month', 'previous_month'))) {
        for ($r = 1; $r <= $rows; $r++) {
            print "<tr height='50'> \n";
            //print "<td style='width: 10px'>$r</td>";
            for ($d = 0; $d <= $number_of_days; $d++) {
                $next_day = date('Y-m-d', strtotime("+$d day", $start_date));
                print "<td class='border-right'></td>"; //Todo here would be all info for this cell
            }
            print "</tr>";
        }
    }
    print "</table>";
    print "</div>";

    unset($number_of_days_stayed, $number_of_days, $red_green, $r, $page, $p, $next_day,
    $allStayD, $period_of_days, $period_in_procentage, $stayD, $allStayDays, $all_days,
    $dates, $sd, $td, $d, $insertedDates, $plan, $stayDates, $start_date, $end_date,
    $thisPlan, $rows, $day_number, $day, $td, $current_mont, $fieldColor);
    ?>
