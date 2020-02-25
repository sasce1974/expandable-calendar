<?php
/**
 * Created by Aleksandar Ardjanliev.
 * User: Saso
 * Date: 11/3/2019
 * Time: 8:45 PM
 */

/** Class @Field can be recreated as abstract class with FACTORY pattern
 to make instance of different kind of Field classes, like a switch.
 * Abstract class @Field can contain one method to create/switch instance of different
 * Field class. That class can be the content of the cell in the Planner. Different
 * kind of fields can be use depending for requirements of the Planner/Application.
 *
 * Abstract class @Field can contain 2 properties, that would be common for each
 * possible applications, like @Start_time and @End_time.
 * Also other properties can be set in the abstract class, like @User ID,
 * @Application/company ID, @Department ID and other...
 */

class Field
{
    private static $_instance = null;
    private $id;
    private $userId;
    private $date;
    private $startTime; //TODO start and end time can be absolute position on table cells spreading over them...
    private $endTime;
    private $style = array(
        'background-color' => 'none',
        'text-color' => 'black'
    );
    private $inputs = array(
        //TODO here should be additional fields - eg. text/number inputs, select, checkbox...
        //TODO @$inputs can be formed in separate class...
    );

    private function __construct() {}
    private function __clone(){}

    static function getInstance(){
        if(self::$_instance == null){
            self::$_instance = new Field();
        }
        return self::$_instance;
    }

    function __destruct()
    {
        self::$_instance = null;
    }

    /** function @getFields returns all the fields from the table fields
     * that belongs to the selected user and are between given start and end date.
     */

    public function getFields($user_id, $start_date, $end_date){
        $user_id = filter_var($user_id, FILTER_SANITIZE_NUMBER_INT);
        //User_id can be auth user or Auth user can choose other user_id
        //TODO implement more user_id (maybe as array) to get more than one plan from different users
        $start_date = filter_var($start_date, FILTER_SANITIZE_STRING);
        $end_date = filter_var($end_date, FILTER_SANITIZE_STRING);

        $con = Connection::getInstance()->getConn();
        $q = "SELECT * FROM fields WHERE user_id = ? AND date BETWEEN ? AND ?";
        $query = $con->prepare($q);
        $query->execute(array($user_id, $start_date, $end_date));
        if($query){
            $r = $query->fetchAll(PDO::FETCH_ASSOC);
            return $r;
        }else{
            return false;
        }
    }

    /** function @getOneField returns only one field by given id */

    public function getOneField($id){

        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $con = Connection::getInstance()->getConn();
        $q = "SELECT * FROM fields WHERE id = ?";
        $query = $con->prepare($q);
        $query->execute(array($id));
        if($query){
            $r = $query->fetchAll(PDO::FETCH_ASSOC);
            return $r;
        }else{
            return false;
        }
    }

}