<?php
/**
 * Created by Aleksandar Ardjanliev.
 * User: Saso
 * Date: 11/6/2019
 * Time: 3:35 PM
 */

//require "../includes/config.inc.php";
//require BASE_URI . "controllers/users/UserClass.php";
//$user = new User();
//if(!$user->isActive()){
//    exit(400);
//}

class DaysOfStay
{

    private $user_id;
    private $con = null;

    function __construct($con, $user_id)
    {
        $this->con = $con;
        $this->user_id = $user_id;
    }

    /*public function insertPeriod($user, $start, $end, $note){
        $con = Connection::getInstance()->getConn();
        $query = $con->prepare("INSERT INTO stayPeriod (user_id, start_date, end_date, note) VALUES (?, ?, ?, ?)");
        $query->execute(array($user, $start, $end, $note));
        $con = null;
        if($query){
            return true;
        }else{
            return false;
        }
    }*/

    /*public function getOverallPeriod($user_id, $daysBack = 180){
        $con = Connection::getInstance()->getConn();
        $query = $con->prepare("SELECT * FROM stayPeriod WHERE user_id = ? AND
        (start_date BETWEEN CURRENT_DATE AND DATE_SUB(CURRENT_DATE, INTERVAL $daysBack DAY)
        OR end_date BETWEEN CURRENT_DATE AND DATE_SUB(CURRENT_DATE, INTERVAL $daysBack DAY)) ");
        $query->execute(array($user_id));
        $con = null;
        if($query){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }*/

    /** Insert one date in the date field in stayDates table for auth user */
    public function insertDate($date, $note=null){
        $con = $this->con;
        $query = $con->prepare("INSERT INTO stayDates (user_id, date, note) VALUES (?, ?, ?)");
        $query->execute(array($this->user_id, $date, $note));
        $con = null;
        if($query){
            return true;
        }else{
            return false;
        }
    }

    /** Get all rows for auth user */
    public function getFields(){
        $con = $this->con;
        $query = $con->prepare("SELECT * FROM stayDates WHERE user_id = ?");
        $query->execute(array($this->user_id));
        $con = null;
        if($query){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }

    /** Get all dates for auth user */
    public function getDates(){
        $con = $this->con;
        $query = $con->prepare("SELECT date FROM stayDates WHERE user_id = ?");
        $query->execute(array($this->user_id));
        $con = null;
        if($query){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }else{
            //return false;
        }
    }

    /** Get rows for auth user where dates are in the last 180 days from today */
    public function getPeriodDates($period = 180){
        $con = $this->con;
        $query = $con->prepare("SELECT * FROM stayDates WHERE user_id = ? AND 
                date BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL ? DAY) AND CURRENT_DATE");
        $query->execute(array($this->user_id, $period));
        $con = null;
        if($query){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }

    /** Delete row with some date for auth user */
    public function deleteDate($date){
        $con = $this->con;
        $query = $con->prepare("DELETE FROM stayDates WHERE date = ? AND user_id = ?");
        $query->execute(array($date, $this->user_id));
        $con = null;
        if($query){
            return true;
        }else{
            return false;
        }
    }

    function __destruct()
    {
        $con = $user_id = null;
        $this->con = null;
        $this->user_id = null;
    }
}

