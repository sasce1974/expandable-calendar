<?php
/**
 * Created by Aleksandar Ardjanliev.
 * User: Laravel
 * Date: 17-Mar-19
 * Time: 7:20 PM
 */


class sessionClassDB
{

    private $db;

    public function __construct($con)
    {
        $this->db  = $con;

        session_set_save_handler(
            array($this, "_open"),
            array($this, "_close"),
            array($this, "_read"),
            array($this, "_write"),
            array($this, "_destroy"),
            array($this, "_gc")
        );

        session_start();
    }

    public function _open(){
        if($this->db) return true;
        return false;
    }

    public function _close(){
        $this->db = null;
        return true;
    }

    public function _read($sid){
        $q=$this->db->prepare("SELECT data FROM sessions WHERE id = ?");
        if($q->execute(array($sid))){
            $data = $q->fetch(PDO::FETCH_ASSOC);
            if(is_null($data['data'])){
                return '';
            }
            return $data['data'];
        }
    }

    public function _write($sid, $data){
        $q=$this->db->prepare("REPLACE INTO sessions (id, data) VALUES (?, ?)");
        if($q->execute(array($sid, $data))){
            return true;
        }else{
            return false;
        }
    }

    public function _destroy($sid){
        $q=$this->db->prepare("DELETE FROM sessions WHERE id = ?");
        if($q->execute(array($sid))){
            $_SESSION = array();
            return true;
        }else{
            return false;
        }
    }

    public function _gc($max){
        $old = time() - $max;
        $q = $this->db->prepare("DELETE FROM sessions WHERE last_accesed < FROM_UNIXTIME(?)");
        if($q->execute(array($old))){
            return true;
        }
        return false;
    }

}