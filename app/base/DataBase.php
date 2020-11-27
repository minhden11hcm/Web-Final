<?php
    class Database{
        private static $db;
        private function __construct(){

        }
        public static function open(){
            if(self::$db===null){
                self::$db=new mysqli('localhost','root','','classroom');
            }
            return self::$db;
        }
    }
?>