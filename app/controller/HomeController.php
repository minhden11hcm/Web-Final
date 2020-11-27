<?php
    class HomeController extends BaseController{
        function error(){
            echo "Mọi lỗi đều chuyển về HomeController";
        }
        public function index()
        {
            session_start();
            if(isset($_SESSION['username']) && isset($_SESSION['pwd'])){
                
                header('Location: /app/view/home/index.php');
            }
            else{
                header('Location: /app/view/account/login.php');
            } 
        }
        public function test(){
            $account = new AccountController();
            $a = $account->reset_pwd('huyn200028@gmail.com');
            if($a){
                echo "ok";
            }
            else{
                echo "no";
            }
        }
    }
?>