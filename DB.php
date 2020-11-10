<?php

    // Import PHPMailer classes into the global namespace
    // These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // Load Composer's autoloader
    require 'vendor/autoload.php';
    
    // mở kết nối DB
    function OP_DB(){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $DB       = "classroom";
        $conn = new mysqli($servername,$username,$password,$DB);
        if($conn->connect_error){
            die('connet error: ' . $conn->connect_error);
        }
        return $conn;
    }

    // tạo chức năng đăng nhập
    function login($user,$password){
        $sql = "select * from account where username = ?";
        $conn = OP_DB();
        $stm  = $conn->prepare($sql);
        $stm->bind_param("s",$user);

        if(!$stm->execute()){
            return array('code' => 1,'error' => 'Can not command the execute');
        }
        $result = $stm->get_result();
        if($result->num_rows == 0){
            return array('code'=> 3,'error'=> 'The user has not been registered');
        }
        $data   = $result->fetch_assoc();
        
        $hashed_password = $data['password'];
        
        if(!password_verify($password,$hashed_password)){
            return array('code' => 2,'error' => 'Wrong password.');
        }else return array('code' => 0,'data' => $data);
    }
    //chức năng kiểm tra tồn tại email hay chưa
    function is_exist_email($email){
        $sql='select username from account where email=?';
        $conn=OP_DB();
        $stm=$conn->prepare($sql);
        $stm->bind_param('s',$email);
        if(!$stm->execute()){
            die("loi");
        }
        $result=$stm->get_result();
        //$count=$result->fetch_assoc();
        if($result->num_rows >0){
            return true;
        }
        else{
            return false;
        }
    }
    // chức năng đăng ý 
    function register($first_name,$last_name,$email,$user,$pass){
        if(is_exist_email($email)){
            return array('code'=>1,'error'=>'email has already been used');
        }
        // mã hóa password và tạo mã kích hoạt tài khoản
        $hash=password_hash($pass,PASSWORD_DEFAULT);
        $rand=random_int(0,1000);
        $token=md5($user."+".$rand);

        // chèn thông tin vào database

        $sql = "insert into account(username,password,email,token,activation,firstname,lastname) values(?,?,?,?,0,?,?) ";
        $conn = OP_DB();
        $stm = $conn->prepare($sql);
        $stm->bind_param("ssssss",$user,$hash,$email,$token,$first_name,$last_name);
        if(!$stm->execute()){
            return array('code'=>2,'error'=>'Can not execute command');
        }
        sendActivationEmail($email,$token);

        return array('code'=>0,'error'=>'Create account successful');
    }
    //chức năng gửi email để kích hoạt tại khoản
    function sendActivationEmail($email,$token){
        
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();  
            $mail->CharSet    = 'UTF-8';                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'huyn200028@gmail.com';                     // SMTP username
            $mail->Password   = 'Minhden123';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('huyn200028@gmail.com', 'Oga');
            $mail->addAddress($email, 'Người nhận ');     // Add a recipient
            /*$mail->addAddress('ellen@example.com');               // Name is optional
            $mail->addReplyTo('info@example.com', 'Information');
            $mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');*/

            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Authenticate your account';
            $mail->Body    = "Click <a href='http://localhost:8888/active.php?email=$email&token=$token'>here</a> to authenticate ";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return true ;
        } catch (Exception $e) {
            return $e;
        }
    }
    // kích hoạt tài khoản
    function activeAccount($email,$token){
        $sql='select username from account where email = ? and token = ? and activation = 0' ;
        $conn=OP_DB();
        $stm=$conn->prepare($sql);
        $stm->bind_param('ss',$email,$token);
        if(!$stm->execute()){
            return array('code' => 1, 'error' => 'This command can not execute');
        }
        $result=$stm->get_result();
        if($result->num_rows === 0){
            return array('code' => 2, 'error' => 'Email or Token is not found ');
        }
        //found

        $sql="update account set activation = 1, token = '' where email = ?";
        $stm=$conn->prepare($sql);
        $stm->bind_param('s',$email);
        if(!$stm->execute()){
            return array('code' => 1, 'error' => 'This command can not execute');
        }
        return array('code' => 0, 'mess' => 'Account is activated');
    }
    //chức năng quên mật khẩu
    function reset_pwd($email){
        if(!is_exist_email($email)){
            return array('code' => 1, 'error' => 'Email is not exist');
        }
        $token=md5($email."+".random_int(1000,2000));
        $sql='update reset_token set token = ? where email = ?';

        $conn=OP_DB();
        $stm=$conn->prepare($sql);
        $stm->bind_param('ss',$token,$email);
        
        if(!$stm->execute()){
            return array('code' => 2, 'error' => 'Can not execute the command');
        }
        if($stm->affected_rows == 0){
            //chưa có dòng nào của email này, ta sẽ thêm dòng mới
            $exp=time() + 3600 * 24;
            $sql='insert into reset_token values(?,?,?)';
            $stm=$conn->prepare($sql);
            $stm->bind_param('ssi',$email,$token,$exp);
            if(!$stm->execute()){
                return array('code' => 1, 'error' => 'không thể thực thi câu lệnh');
            }
        }
        $success=sendResetEmail($email,$token);
        return array('code' => 0, 'success' => $success);
    }
    // đổi pass
    function sendResetEmail($email,$token){
        
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();    
            $mail->CharSet    = 'UTF-8';                                        // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'huyn200028@gmail.com';                     // SMTP username
            $mail->Password   = 'Minhden123';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('huyn200028@gmail.com', 'Oga');
            $mail->addAddress($email, 'Người nhận ');     // Add a recipient
            /*$mail->addAddress('ellen@example.com');               // Name is optional
            $mail->addReplyTo('info@example.com', 'Information');
            $mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');*/

            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Khôi phục mật khẩu ';
            $mail->Body    = "Click <a href='http://localhost:8888/reset_password.php?email=$email&token=$token'>vào đây</a> khôi phục mật khẩu  ";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return true ;
        } catch (Exception $e) {
            return $e;
        }
    }
?>