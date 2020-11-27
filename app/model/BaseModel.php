<?php

abstract class BaseModel{
    private $db;
    public function __construct()
    {
        $this->db=Database::open();
    }
    abstract function get_all();
    
   
    public function query($sql){
        $result=$this->db->query($sql);
        if(!$result){
            return array('code' => 1, 'error' => $this->db->error);
        }
        $data=array();
        while($item = $result->fetch_assoc()){
            array_push($data,$item);
        }
        return array('code' => 0, 'data' => $data);
    }
    function query_prepared($sql, $param){
        $stm=$this->db->prepare($sql);
        call_user_func_array(array($stm,'bind_param'), $param);

        if(!$stm->execute()){
            return array('code' => 1, 'error' => $this->db->error);
        }
        $result = $stm->get_result();
        $item = $result->fetch_assoc();
        return array('code' => 0, 'data' => $item);
    }
    public function Update($sql,$param)
    {
        $stm=$this->db->prepare($sql);
        call_user_func_array(array($stm,'bind_param'), $param);
        if(!$stm->execute()){
            return false;
        }
        return true;
    }
    function Exist($sql,$param){
        $stm=$this->db->prepare($sql);
        call_user_func_array(array($stm,'bind_param'), $param);

        if(!$stm->execute()){
            return array('code' => 1, 'error' => $this->db->error);
        }
        if($stm->affected_rows == 0){
            return false;
        }
        return true;
    }
}