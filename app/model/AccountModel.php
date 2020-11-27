<?php
class AccountModel extends BaseModel{
    
    
    function get_all()
    {
        $sql='select * from account';
        return $this->query($sql);
    }

    function get_Account_By_Username($username){
        $sql = 'select * from account where username = ? ';
        $param = array('s', &$username);
        $data = $this->query_prepared($sql,$param);
        if (empty($data['data'])){
            return array('code'=> 3,'error' => 'The user has not been registered');
        }
        return $data;
    }
    function get_Email($email){
        $sql = 'select email from account where email = ?';
        $param = array('s', &$email);
        $data = $this->query_prepared($sql,$param);
        return $data;
    }
    function Insert_User($username,$password,$email,$token,$firstname,$lastname){
        $sql = "insert into account(username,password,email,token,activation,firstname,lastname) values(?,?,?,?,0,?,?) ";
        $param = array('ssssss',&$username,&$password,&$email,&$token,&$firstname,&$lastname);

        $data = $this->Update($sql,$param);
        return $data;
    }
    function get_Username_By_Email_Token($email,$token){
        $sql='select username from account where email = ? and token = ? and activation = 0';
        $param = array('ss',&$email,&$token);
        $data  = $this->query_prepared($sql,$param);
        return $data['data'];
    }
    function update_Activation($email){
        $sql="update account set activation = 1, token = '' where email = ?";
        $param = array('s',&$email);
        $data  = $this->Update($sql,$param);

        return $data;
    }

    function update_token_for_restPWD($email,$token){
        
        $sql='update reset_token set token = ? where email = ?';
        $param = array('ss',&$email,&$token);
        $data =$this->Exist($sql,$param);

        return $data;
    }
    function Insert_reset_token($email,$token,$exp){
        
        $sql='insert into reset_token values(?,?,?)';
        $param = array('ssi',&$email,&$token,&$exp);
        $data  = $this->Update($sql,$param);

        return $data;
    }
    function Change_Password($password,$email){
        $sql="update account set password = ? where email = ?  ";
        $param = array('ss',&$password,&$email);
        $data  = $this->Update($sql,$param);
        return $data;
    }
}