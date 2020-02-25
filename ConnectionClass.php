<?php
/**
 * Created by Aleksandar Ardjanliev.
 * User: Saso
 * Date: 11/3/2019
 * Time: 10:03 PM
 */

class Connection
{
    private static $_instance = null;
    private $con;
    private $host = DBHOST;
    private $dbName = DB;
    private $username = DBUSER;
    private $password = DBPASS;


    private function __construct(){}
    private function __clone(){}

    static function getInstance(){
        if(self::$_instance == null){
            self::$_instance = new Connection();
        }
        return self::$_instance;
    }

    public function getConn(){
        $this->con = null;
        try {
            $this->con = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->con->exec("set names utf8");
        }catch (PDOException $e){
            echo "Connection error: " . $e->getMessage();
        }
        return $this->con;
    }

    function __destruct()
    {
        $this->con = null;
    }
}