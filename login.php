<?php
 require_once('DB.php');
 session_start();
 if(isset($_SESSION['username']) && isset($_SESSION['pwd'])){
   header('location: index.php');
   die();
 }
 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="./css/login.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script
      type="text/javascript"
      src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.js"
    ></script>
  </head>
  <body>
  <?php
  $error = '';
  $check = '';
  $username = '';
  $password = '';
  if(isset($_POST['username']) && isset($_POST['pwd'])){
    $username = $_POST['username'];
    $password = $_POST['pwd'];
    $result   = login($username,$password);
    
    if($result['code'] == 0){
      $data = $result['data'];

      //set cookie để lưu người dùng
      if (isset($_POST['checkbox'])){
        
        $username = $_POST['username'];
        $password = $_POST['pwd'];
        setcookie('username',$username,time() + 3600 * 24);
        setcookie('pwd',$password,time() + 3600 * 24);
      }
      else{
        $username   = "";                     
        $password   = "";
        setcookie("username", "", time()- 3600*24);
        setcookie("pwd", "", time()- 3600*24);
      }

      //set session để giữ trạng thái đăng nhập
      $_SESSION['username'] = $username;
      $_SESSION['pwd']  = $password;
      header('location: index.php');
    }
    else{
      $error = $result['error'];
    }
    
  }
  if(isset($_COOKIE['username']) && isset($_COOKIE['pwd'])){
    $check    = "checked";
    $username = $_COOKIE['username'];
    $password = $_COOKIE['pwd'];
  }else{
    $check    = "";
    $username = '';
    $password = '';
  }
  
  ?>
    <div class="login">
      <h2>Login</h2>
      <br />
      <form id="login" action="" method="post">
        <label class="input" for=""> Username</label> <br />
        <input
          type="text"
          name="username"
          id="username"
          value="<?= $username ?>"
          placeholder="Enter Username"
        /><br />
        <label class="input" for="">Password</label><br />
        <input
          type="password"
          name="pwd"
          id="pwd"
          value="<?= $password ?>"
          placeholder="Enter Password"
        />
        <br />
        <div class="check">
          <input type="checkbox" name="checkbox" id="check" <?= $check ?>  />
          <label for="check">Remember me</label>
        </div>
        <br />
        <button>Login</button> <br />
        <a href="register.php"> Resgister</a>
        <a href="forgot.php"> Forgot password</a>
      </form>
      <p class="error"><?= $error?></p>
    </div>
  </body>
  <script src="/js/xuly.js"></script>
</html>
