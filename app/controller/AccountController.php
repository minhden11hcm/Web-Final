<?php
     use PHPMailer\PHPMailer\PHPMailer;
     use PHPMailer\PHPMailer\SMTP;
     use PHPMailer\PHPMailer\Exception;
    class AccountController extends BaseController{
        public function Login($username,$password){
            $account = new AccountModel();
            $result = $account->get_Account_By_Username($username);
            if ($result['code'] == 0){
                $data = $result['data'];
                $hashed_password = $data['password'];
                if(!password_verify($password,$hashed_password)){
                    return array('code' => 2,'error' => 'Wrong password.');
                }else return array('code' => 0,'data' => $data);
            }else{ return array('code' => 1,'error' => $result['error'] ) ;}
        }     
        
        public function is_exist_email($email){
            $account = new AccountModel();

            $result  =  $account->get_Email($email);

            $data    =  $result['data'];
            
            if(empty($data['email'])){
                return false;
            }
            return true;
        }
        public function register($first_name,$last_name,$email,$user,$pass){
            if($this->is_exist_email($email)){
                return array('code'=>1,'error'=>'email has already been used');
            }
            $hash=password_hash($pass,PASSWORD_DEFAULT);
            $rand=random_int(0,1000);
            $token=md5($user."+".$rand);
            $account = new AccountModel();

            $result = $account->Insert_User($user,$hash,$email,$token,$first_name,$last_name);
            if (!$result){
                return array('code'=>2,'error'=>'Can not execute command'); 
            }
            $this->sendActivationEmail($email,$token);

            return array('code'=>0,'error'=>'Create account successful');
        }

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
                $mail->addAddress($email, 'Customer ');     // Add a recipient
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
                $mail->Body    = "Click <a href='http://localhost:8888/app/view/account/active.php?email=$email&token=$token'>here</a> to authenticate ";
                //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
                $mail->send();
                return true ;
            } catch (Exception $e) {
                return $e;
            }
        }

        function activeAccount($email,$token){
            $account = new AccountModel();
            
            $data = $account->get_Username_By_Email_Token($email,$token);
            if(empty($data)){
                return array('code' => 2, 'error' => 'Email or Token is not found '); 
            }
            // found
            $result = $account->update_Activation($email);
            if(!$result){
                return array('code' => 1, 'error' => 'This command can not execute');
            }
            return array('code' => 0, 'mess' => 'Account is activated');
        }

        function reset_pwd($email){
            $account = new AccountModel();
            
            if(!$this->is_exist_email($email)){
                return array('code' => 1, 'error' => 'Email is not exist');
            }
            
            $token=md5($email."+".random_int(1000,2000));
            $data = $account->update_token_for_restPWD($email,$token);
            // email chua duoc dung lan nao thi ta add email vao rest_token
            if(!$data){
                $exp=time() + 3600 * 24;
                $account->Insert_reset_token($email,$token,$exp);        
            }

            $this->sendResetEmail($email,$token);
            return true;
        }

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
                $mail->addAddress($email, 'Customer ');     // Add a recipient
                /*$mail->addAddress('ellen@example.com');               // Name is optional
                $mail->addReplyTo('info@example.com', 'Information');
                $mail->addCC('cc@example.com');
                $mail->addBCC('bcc@example.com');*/
    
                // Attachments
                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    
                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Rest Password ';
                $mail->Body    = "Click <a href='http://localhost:8888/app/view/account/reset_password.php?email=$email&token=$token'>here</a> to reset password  ";
                //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
                $mail->send();
                return true ;
            } catch (Exception $e) {
                return $e;
            }
        }
    }
?>