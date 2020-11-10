<?php
    session_start();
    if (isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }
    require_once('db.php');
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BootStrap</title>
    <!-- Latest compiled and minified CSS -->
    <link
      rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    />

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./css/register.css" />
  </head>
  <body>
    <?php
    $error = '';
    $first_name = '';
    $last_name = '';
    $email = '';
    $user = '';
    $pass = '';
    if (isset($_POST['First-name']) && isset($_POST['Last-name']) && isset($_POST['email'])
    && isset($_POST['username']) && isset($_POST['password'])){
        $first_name = $_POST['First-name'];
        $last_name = $_POST['Last-name'];
        $email = $_POST['email'];
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $result=register($first_name,$last_name,$email,$user,$pass);
            if($result['code']==0){
                // đăng ký thành công
                die("Succesful");
            }else{
                $error=$result['error'];
            }
    }
    ?>
    <div class="container">
      <div id="register" class="row justify-content-center">
        <div class="col-lg-6 col-sm-5">
          <h2>Register</h2>
          <form action="" method="POST" class="needs-validation" novalidate >
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="First-name">First name</label>
                <input
                  type="text"
                  class="form-control"
                  required
                  id="First-name"
                  name="First-name"
                  placeholder="First Name"
                />
                <div class="invalid-feedback">Enter First Name</div>
              </div>
              <div class="form-group col-md-6">
                <label for="Last-name">Last name</label>
                <input
                  type="text"
                  class="form-control"
                  required
                  id="Last-name"
                  name="Last-name"
                  placeholder="Last Name"
                />
                <div class="invalid-feedback">Enter Last Name</div>
              </div>
            </div>
            <div class="form-group">
              <label for="user-name">Username</label>
              <input
                type="text"
                class="form-control"
                required
                id="user-name"
                name="username"
                placeholder="Username"
              />
              <div class="invalid-feedback">Enter Username</div>
            </div>
            <div class="form-group">
              <label for="pwd">Password</label>
              <input
                type="password"
                class="form-control"
                required
                id="pwd"
                name="password"
                placeholder="Password"
              />
              <div class="invalid-feedback">Enter Password</div>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input
                type="email"
                class="form-control"
                required
                id="email"
                name="email"
                placeholder="Email"
              />
              <div class="invalid-feedback">Enter Email</div>
            </div>
            <div class="form-group">
                        <?php
                            if (!empty($error)) {
                                echo "<div class='alert alert-danger'>$error</div>";
                            }
                        ?>
            <div class="form-check">
              <button type="submit" class="btn btn-primary p-2 mr-3">
                Resgiter
              </button>
              <button type="reset" class="btn btn-outline-primary p-2">
                Reset
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
  </body>
  <script>
// Disable form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Get the forms we want to add validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
</script>
</html>
