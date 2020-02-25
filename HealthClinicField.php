<?php
/**
 * Created by PhpStorm.
 * User: Saso
 * Date: 11/10/2019
 * Time: 4:57 PM
 */

class HealthClinicField
{
    public $id = null;
    public $startTime = null;
    public $endTime = null;
    public $date = null;
    public $treatment;
    public $therapist;
    public $reserved = false;
//    public $patient;

    function __construct(array $fields)
    {
        $this->startTime = filter_var($fields['startTime'], FILTER_SANITIZE_STRING);
        $this->endTime = filter_var($fields['endTime'], FILTER_SANITIZE_STRING);
        $this->date = filter_var($fields['date'], FILTER_SANITIZE_STRING);
        $this->treatment = filter_var($fields['treatment'], FILTER_SANITIZE_STRING);
        $this->therapist = filter_var($fields['therapist'], FILTER_SANITIZE_STRING);
        $this->reserved = filter_var($fields['reserved'], FILTER_SANITIZE_NUMBER_INT);
        //$this->patient = filter_var($fields['patient'], FILTER_SANITIZE_STRING);
    }

    function getField($id){
        $this->id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $con = Connection::getInstance()->getConn();
        $query = $con->prepare("SELECT * FROM fieldTable Where id = ?");
        $query->execute(array($this->id));

        if($query){
            return $query->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }

    }

    function create (array $fields){
        $this->startTime = filter_var($fields['startTime'], FILTER_SANITIZE_STRING);
        $this->endTime = filter_var($fields['endTime'], FILTER_SANITIZE_STRING);
        $this->date = filter_var($fields['date'], FILTER_SANITIZE_STRING);
        $this->treatment = filter_var($fields['treatment'], FILTER_SANITIZE_STRING);
        $this->therapist = filter_var($fields['therapist'], FILTER_SANITIZE_STRING);
        $this->reserved = filter_var($fields['reserved'], FILTER_SANITIZE_NUMBER_INT);
//        $this->patient = filter_var($fields['patient'], FILTER_SANITIZE_STRING);

        $con = Connection::getInstance()->getConn();
        $query = $con->prepare("INSERT INTO fieldTable (date, start_time, end_time,
                treatment, therapist, reserved) VALUES (?, ?, ?, ?, ?, ?)");
        $query->execute(array($this->date, $this->startTime, $this->endTime, $this->treatment,
            $this->therapist, $this->reserved));
        $con = null;
        if($query){
            //TODO find why $query returns true even no table exists...
            return $query->errorInfo();
        }else{
            return $query->errorInfo();
        }
    }

    public function update (array $fields){
        $this->id = filter_var($fields['id'], FILTER_SANITIZE_NUMBER_INT);
        $this->startTime = filter_var($fields['startTime'], FILTER_SANITIZE_STRING);
        $this->endTime = filter_var($fields['endTime'], FILTER_SANITIZE_STRING);
        $this->date = filter_var($fields['date'], FILTER_SANITIZE_STRING);
        $this->treatment = filter_var($fields['treatment'], FILTER_SANITIZE_STRING);
        $this->therapist = filter_var($fields['therapist'], FILTER_SANITIZE_STRING);
        $this->reserved = filter_var($fields['reserved'], FILTER_SANITIZE_NUMBER_INT);

        $con = Connection::getInstance()->getConn();
        $query = $con->prepare("UPDATE fieldTable SET date = ?, start_time=?, end_time=?, 
            treatment=?, therapist=?, reserved=? WHERE id=?");
        $query->execute(array($this->date, $this->startTime, $this->endTime, $this->treatment,
            $this->therapist, $this->reserved, $this->id));
        $con = null;
        if($query){
            return true;
        }else{
            return false;
        }
    }

    public function delete ($id){
        $this->id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $con = Connection::getInstance()->getConn();
        $query = $con->prepare("DELETE FROM fieldTable WHERE id=?");
        $query->execute(array($id));
        $con = null;
        if($query){
            return true;
        }else{
            return false;
        }
    }


}