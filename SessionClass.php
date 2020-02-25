<?php
/**
 * Created by Aleksandar Ardjanliev.
 * User: Saso
 * Date: 11/17/2019
 * Time: 6:30 PM
 *
 * SessionClass is used only to present the information from the session
 *
 * In Development...
 *
 */

class Session
{
    private $con = null;

    function __construct($con)
    {
        $this->con = $con;
    }



    function getAllSessions(){
        $query = "SELECT * FROM sessions";
        $q = $this->con->query($query);
        if($q){
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return $this->con->errorInfo()[2];
        }
    }

    function getUserNames(){
        $query = "SELECT date, last_accesed FROM sessions";
        $q = $this->con->query($query);
        if($q){
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return $this->con->errorInfo()[2];
        }
    }

    function __destruct()
    {
        $this->con = null;
    }
}