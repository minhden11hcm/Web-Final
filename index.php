<?php
  session_start();
  session_destroy();
  header('')
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
      a{
        text-decoration: none;
        color: blue;
      }
    </style>
  </head>
  <body>
    <h2>đăng nhập thành công</h2>

    <h1><a href="logout.php"> Đăng Xuất</a></h1>
  </body>
</html>
