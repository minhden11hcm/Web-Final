<?php
   //front controller
   require_once("vendor/autoload.php");

   
    if(empty($_GET['controller']) && empty($_GET['action'])){
        $Controller='home';
        $Action='index';
    }
    elseif(!empty($_GET['controller'])){
        $Controller=$_GET['controller'];
        if(!empty($_GET['action'])){
            $Action=$_GET['action'];
        }else{
            $Action='index';
        }
    }

    $Controller=ucfirst($Controller).'Controller';
    if(!class_exists($Controller)){
        $Controller="HomeController";
        $Action="error";
    }
    $obj=new $Controller();
    if(!method_exists($obj,$Action)){
        $obj=new HomeController();
        $Action="error";
    }
    $obj->$Action();
?>