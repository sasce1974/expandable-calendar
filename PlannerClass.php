<?php
/**
 * Created by Aleksandar Ardzanliev.
 * User: Saso
 * Date: 10/14/2019
 * Time: 4:59 PM
 */


namespace firstPlanner;
class Planner
{
    static private $_instance = null;
    private $plan;
    private $posiblePlans = array(
        "back", // gives previous week (7 days) starting previous monday
        "current", // this is for the current week starting monday to sunday
        "next", // gives next week starting next monday
        "chosen", // gives recent plant TODO to implement session for start and end date
        "today", // gives only today date
        "- 1 day", // previous day
        "+ 1 day", // next day
        "byPeriod", // by given start and end date
        "last_one", // the last 'chosen' day Todo see if can be merged with 'chosen'
        "month",
        "next_month",
        "previous_month",
        "next_period",
        "previous_period",
    );

    private $startDate;
    private $endDate;
    private $startMonthDate;
    private $endMonthDate;
    private $rows; // each row can represent hour or defined time within the column...
    //private $column; //Can be day, week, month...

    /** Using Singleton pattern to create instance from Planner class */
    private function __construct(){}
    private function __clone(){}

    static function getInstance(){
        if(self::$_instance == null){
            self::$_instance = new Planner();
        }
        return self::$_instance;
    }


    /** This is the main method! Here the plan is set by receiving 4 arguments
     * Argument Plan is required to initiate plan. If no recognized plan is given,
     * the 'current' plan is used (Which means the current week will be shown - start
     * date will be set to this week Monday, and end date will be set to incoming sunday.
     * Weeks starts from monday.
     */

    private function _setPlan($plan = null, $start = null, $end = null, $rows = null){
        /** Arguments will be saved in a session in order to be used on following calls
         * even without further provided arguments.
         */
        if(session_id() == ""){
            session_start();
        }

        /** Arguments should be sanitized from unwanted characters */
        $plan = filter_var($plan, FILTER_SANITIZE_STRING);
        $start = filter_var($start, FILTER_SANITIZE_STRING);
        $end = filter_var($end, FILTER_SANITIZE_STRING);

        /** @var $rows is being set */
        if($rows != null){
            $rows = filter_var($rows, FILTER_SANITIZE_NUMBER_INT);
            if($rows < 1 || $rows > 24) $rows = null;
            $this->rows = $_SESSION['rows'] = $rows;
        }else{
            if(isset($_SESSION['rows'])){
                $this->rows = $_SESSION['rows'];
            }else{
                $this->rows = 8;//TODO later this from options
            }
        }

        /** setting plan attribute */
        if(in_array($plan, $this->posiblePlans)) {
            $this->plan = $plan;
        }else{
            $this->plan = 'current';
        }

        /** Setting last_one plan */
        if($this->plan == 'last_one'){
            if(isset($_SESSION['plan']) && !in_array($_SESSION['plan'], array('back', 'next', '- 1 day', '+ 1 day', 'previous_period', 'next_period', 'previous_month', 'next_month'))){
                $this->plan = $_SESSION['plan'];
                $this->startDate = $_SESSION['startDate'];
                $this->endDate = $_SESSION['endDate'];
            }else{
                $this->plan = 'month';
            }
        }

        /** @var $next_monday is used to set the start date - starting monday */
        if(isset($_SESSION['next_monday'])){
            $next_monday = $_SESSION['next_monday'];
        }else{
            $next_monday = "";
        }

        $today = strtotime("today");

        /** setting the starting date from a session, or as today's date */
        if(isset($_SESSION['startDate'])) {
            $this->startDate = date('Y-m-d',strtotime($_SESSION['startDate']));
        }else{
            $this->startDate = date('Y-m-d', $today);
        }

        /** WEEK PLAN ($start_date + 6 days, Monday to Sunday) */
        if($this->plan == 'current'){
            $_SESSION['next_monday'] = "";
            if(date("l", $today) == "Monday"){ //we check if today is monday
                $next_monday = strtotime("monday");
            } else{
                $next_monday = strtotime('last monday');
            }

            /** method setDates sets starting day as given monday and end day as
             * the sunday following that monday
             */
            $this->setDates($next_monday);

        }else if($this->plan == 'next'){
            //TODO new statDate should be set to 1 day after old endDate...
            if($next_monday == ""){
                $next_monday = strtotime("next monday");
            } else{
                $next_monday = strtotime("+7 day", $next_monday); //adding + week on every "NEXT" press
            }
            $this->setDates($next_monday);

        }else if($this->plan == 'back'){
            if($next_monday == ""){
                $next_monday = strtotime("last monday - 7 day");
            } else{
                $next_monday = strtotime("-7 day", $next_monday); //- week on every "BACK" press
            }
            $this->setDates($next_monday);

        }else if($this->plan == 'chosen'){
            //do not change $next_monday

        /** ONE DAY PLAN initiated by 'today' */
        }elseif($this->plan == 'today'){
            $this->startDate = date("Y-m-d", $today);
            $this->endDate = $this->startDate;
            $_SESSION['startDate'] = $_SESSION['endDate'] = $this->startDate;
        }elseif($this->plan == '- 1 day'){
            $this->startDate = strtotime("-1 day", strtotime($this->startDate));
            $this->startDate = date("Y-m-d", $this->startDate);
            $this->endDate = $this->startDate;
            $_SESSION['startDate'] = $_SESSION['endDate'] = $this->endDate;
        }elseif($this->plan == '+ 1 day'){
            $this->startDate = date("Y-m-d", strtotime("+1 day", strtotime($this->startDate)));
            $this->endDate = $this->startDate;
            $_SESSION['startDate'] = $_SESSION['endDate'] = $this->startDate;
        }elseif($this->plan == 'last_one'){
            if(isset($_SESSION['startDate'])) $this->startDate = $_SESSION['startDate'];
            if(isset($_SESSION['endDate'])) $this->startDate = $_SESSION['endDate'];
            //$this->startDate = date("Y-m-d", $this->startDate);
            //$this->endDate = $this->startDate;
            if(in_array($_SESSION['plan'], array('current', 'today', 'month', 'byPeriod'))){
                $this->plan = $_SESSION['plan'];
            }elseif(in_array($_SESSION['plan'], array('back', 'next'))){
                $this->plan = 'current';
            }elseif (in_array($_SESSION['plan'], array('- 1 day', '+ 1 day')))   {
                $this->plan = 'today';
            }elseif (in_array($_SESSION['plan'], array('next_month', 'previous_month'))) {
                $this->plan = 'month';
            }elseif (in_array($_SESSION['plan'], array('next_period', 'previous_period'))) {
                $this->plan = 'byPeriod';
            }else{
                $this->plan = 'month';
            }

        }

        /** PLAN BY PERIOD give specific period */
        //If the dates are passed to _setPlan function, they are initialized here


        if ($this->plan == 'byPeriod'){
            if($start != null && $end != null){
                $this->startDate = $_SESSION['startDate'] = $start;
                $this->endDate = $_SESSION['endDate'] = $end;
            /*}elseif($_SESSION['startDate']!='' && $_SESSION['endDate']!=''){
                $this->startDate = $_SESSION['startDate'];
                $this->endDate = $_SESSION['endDate'];*/
            }else{
                $this->getPlan('current');
            }
        }elseif($this->plan == 'previous_period'){
            if($_SESSION['startDate'] !='' & $_SESSION['endDate']!='') {
                $days = $this->numberOfDays($_SESSION['startDate'], $_SESSION['endDate']) + 1;
                $_SESSION['startDate'] = $this->startDate = date("Y-m-d", strtotime("-$days day", strtotime($_SESSION['startDate'])));
                $_SESSION['endDate'] = $this->endDate = date("Y-m-d", strtotime("-$days day", strtotime($_SESSION['endDate'])));
            }
        }elseif($this->plan == 'next_period') {
            if ($_SESSION['startDate'] != '' & $_SESSION['endDate'] != '') {
                $days = $this->numberOfDays($_SESSION['startDate'], $_SESSION['endDate']) + 1;
                $_SESSION['startDate'] = $this->startDate = date("Y-m-d", strtotime("+$days day", strtotime($_SESSION['startDate'])));
                $_SESSION['endDate'] = $this->endDate = date("Y-m-d", strtotime("+$days day", strtotime($_SESSION['endDate'])));
            }
        }

        /** MONTH PLAN */

        if (in_array($this->plan, array('month', 'next_month', 'previous_month'))){

            if($this->plan == 'month' && isset($_SESSION['plan']) && in_array($_SESSION['plan'], array('month', 'next_month', 'previous_month')) && isset($_SESSION['timer']) && (time() - $_SESSION['timer']) < 20 ){
                $this->startDate = $_SESSION['startDate'];
                $this->endDate = $_SESSION['endDate'];
            }elseif($this->plan == 'month' && isset($_SESSION['plan']) && $_SESSION['plan'] == 'month' && isset($_SESSION['startDate'])){
                $this->startDate = $_SESSION['startDate'];
                $this->endDate = $_SESSION['endDate'];
            }else{
                if($this->plan == 'month') $this->startMonthDate = strtotime('first day of this month');
                if($this->plan == 'next_month') $this->startMonthDate = strtotime('+1 month', $_SESSION['startMonthDate']);
                if($this->plan == 'previous_month') $this->startMonthDate = strtotime('-1 month', $_SESSION['startMonthDate']);
                $_SESSION['startMonthDate'] = $this->startMonthDate;

                $this->startDate = date('Y-m-d', $this->startMonthDate);
                if($this->startDate != 'Monday') {
                    $this->startDate = date('Y-m-d', strtotime('Monday this week', $this->startMonthDate));
                }
                $_SESSION['startDate'] = $this->startDate;

                if($this->plan == 'month') $this->endMonthDate = strtotime('last day of this month');
                if($this->plan == 'next_month') $this->endMonthDate = strtotime('+1 month', $_SESSION['endMonthDate']);
                if($this->plan == 'previous_month') $this->endMonthDate = strtotime('-1 month', $_SESSION['endMonthDate']);
                $_SESSION['endMonthDate'] = $this->endMonthDate;
                $this->endDate = date('Y-m-d', $this->endMonthDate);
                if($this->endDate != 'Sunday'){
                    $this->endDate = date('Y-m-d', strtotime('Sunday this week', $this->endMonthDate));
                }
                $_SESSION['endDate'] = $this->endDate;
            }

        }
        $_SESSION['plan'] = $this->plan;
        $_SESSION['timer'] = time();

    } //End of _setPlan method

    /** method setDates sets start and end date by given $next_monday value */
    private function setDates($next_monday){
        $next_sunday = strtotime("+ 6 day", $next_monday);
        $this->startDate = date("Y-m-d", $next_monday);
        $_SESSION['startDate'] = $this->startDate;
        $this->endDate = date("Y-m-d", $next_sunday);
        $_SESSION['endDate'] = $this->endDate;
        $_SESSION['next_monday'] = $next_monday;
    }

    //calculate number of days between start and end date
    private function numberOfDays($startDate, $endDate){
        return ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400);
    }

    function getPlan($plan, $start=null, $end=null, $rows = null){
        $this->_setPlan($plan, $start, $end, $rows);
        $data = array();
        $data['plan'] = $this->plan;
        $data['startDate'] = $this->startDate;
        $data['endDate'] = $this->endDate;
        $data['rows'] = $this->rows;

        return $data;
    }

    function __destruct()
    {
        self::$_instance = null;
    }

}