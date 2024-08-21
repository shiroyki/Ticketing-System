<?php
//namespace \MyNamespace;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
include_once('auth.php');
include_once('csrf-verify.php');

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
function send_mail(){

    try{
        $mail = new PHPMailer(true); 

        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true; 
        $mail->Username = 'senderjoe03221@gmail.com'; 
        $mail->Password = 'osvnzebufvfwguwr'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('senderjoe03221@gmail.com', 'Joe');
        $mail->addAddress($_POST['email'], 'Recipient Name');
        $mail->addReplyTo('senderjoe03221@gmail.com', 'Joe');

        $mail->Subject = 'Authentication email';
        $mail->Body = rand(100000, 999999);
        $_SESSION['2FA']=$mail->Body;
        $mail->send();
    }catch(Exception $e){

    }
   

}




function fetch_UserDB() {	
	$db = new PDO('sqlite:/var/www/tickets.db');
	$db->query('PRAGMA foreign_keys = ON;');
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $db;
}
function ierg4210_register() {
    if(empty($_POST['email']) || empty($_POST['pw']) 
    || !preg_match("/^[\w=+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['email'])
    || !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST["pw"])) {
        throw new Exception('Invalid email or password format!');
    }
    
    global $db;
    $db = fetch_UserDB();
    $email = $_POST['email'];
    $password = $_POST['pw'];
    $salt = bin2hex(openssl_random_pseudo_bytes(16));
    $hashed_password = hash_hmac('sha256', $password, $salt);
    $adminFlag = 0;
    
    $q=$db->prepare('INSERT INTO account (email, HASHED_PASSWORD, SALT, ADMIN_FLAG) VALUES (?, ?, ?, ?)'); 
    $q->bindParam(1, $email);
    $q->bindParam(2, $hashed_password);
    $q->bindParam(3, $salt);
    $q->bindParam(4, $adminFlag);
    
    if ($q->execute()) {
        header('Location: login.php', true, 302);
        exit();
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo '<head>
            <title>Register Page</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
            <link href="../css/styles.css" rel="stylesheet" />
        </head>
        <body style="background-color:white;">
            <div class="page-wrap d-flex flex-row align-items-center">
                <div class="container h-100 pt-5">
                        <div class="col-md-12 text-center">
                            <div class="mb-4 lead">Failed to register account, please try again.</div>
                            <a href="../register.php" class="btn btn-primary">Return Back to Register Page</a>
                        </div>
                </div>
            </div>
        </body>';
        exit();
    }
}
function ierg4210_login() {
    if(empty($_POST['email']) || empty($_POST['pw']) 
    || !preg_match("/^[\w=+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['email'])
    || !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST["pw"])) {
        throw new Exception('Wrong Credentials!');
    }

    global $db;
    $db = fetch_UserDB();
    $pwd = $_POST['pw'];
    $EMAIL = $_POST['email'];
    htmlspecialchars($pwd);
    htmlspecialchars($EMAIL);

    $q=$db->prepare('SELECT * FROM account WHERE email = (?);'); 

    $q->bindParam(1, $EMAIL);
    $q->execute();
    $r=$q->fetch();

    $pw = $r["HASHED_PASSWORD"];
    $salt = $r["SALT"];
    $adminFlag = $r["ADMIN_FLAG"];

    $hashed_pw = hash_hmac('sha256', $pwd, $salt);

    if($pw == $hashed_pw){
        send_mail();
        $_SESSION['adminFlag']=$adminFlag;
        $_SESSION['r']=$r;

        $current_time = new DateTime(); // create a new DateTime object for current time
        $current_time_formatted = $current_time->format('Y-m-d H:i:s'); // format the current time as a string

        $time_after_50_seconds = new DateTime(); // create a new DateTime object for the time after 50 seconds
        $time_after_50_seconds->modify('+50 seconds'); // add 50 seconds to the current time
        $time_after_50_seconds_formatted = $time_after_50_seconds->format('Y-m-d H:i:s'); // format the time after 50 seconds as a string
        $_SESSION['2FA_begin_time']=$current_time_formatted;
        $_SESSION['2FA_timeout_time'] =$time_after_50_seconds_formatted;
        header('Location:2FA.php');
    } else 
    {
        header('Content-Type: text/html; charset=utf-8');
        echo '<head>
 <title>Login Page</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
            <link href="../css/styles.css" rel="stylesheet" />
           
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
            
        </head>
        <body style="background-color:white;">
            <div class="page-wrap d-flex flex-row align-items-center">
                <div class="container h-100 pt-5">
                        <div class="col-md-12 text-center">
                            <div class="mb-4 lead">Either email or password is incorrect!</div>
                            <a href="../login.php" class="btn btn-primary">Return Back to Login Page</a>
                            <a href="../index.php" class="btn btn-dark">Continue as Guest</a>
                        </div>
                </div>
            </div>
        </body>'; 
//echo $pwd."salt: ".$salt ."current hashed pw: ". $hashed_pw ." db hashed pw:".$pw ;
        exit();
    }
}function ierg4210_logout() 
{
    setcookie('auth', '', time()- 3600 * 24 * 3, '/');
    session_destroy();
    header('Location: login.php', true, 302);
    exit();
}

function ierg4210_change_password() {
    global $db;
    $db = fetch_UserDB();
    $original_pwd = $_POST['pw'];
    htmlspecialchars($original_pwd);
    $EMAIL = $_POST['email'];
    htmlspecialchars($EMAIL);
  
    $q=$db->prepare('SELECT * FROM account WHERE email = (?);'); 
    $q->bindParam(1, $EMAIL);
    $q->execute();
    $r=$q->fetch();

    $original_db_pw = $r["HASHED_PASSWORD"];
    $original_salt = $r["SALT"];
    $DB_EMAIL = $r["EMAIL"];
    $adminFlag = $r["ADMIN_FLAG"];
    
    $original_hashed_pw = hash_hmac('sha256', $original_pwd, $original_salt);

    if ($EMAIL != $DB_EMAIL || $original_hashed_pw != $original_db_pw) {
        //prompt alert
        header('Content-Type: text/html; charset=utf-8');
        echo '<head>
            <title>Login Page</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
          
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
            <link href="../css/styles.css" rel="stylesheet" />
        </head>
        <body style="background-color:white;">
            <div class="page-wrap d-flex flex-row align-items-center">
                <div class="container h-100 pt-5">
                    <div class="col-md-12 text-center">
                        <div class="mb-4 lead">Either email or password is incorrect!</div>
                        <a href="changePassword.php" class="btn btn-primary">Return back to Password Reset Page</a>
                            <a href="index.php" class="btn btn-dark">Continue as Guest</a>
                    </div>
                </div>
        </div>
        </body>';
        exit();
    }
    else 
    {
        $new_pw = $_POST['updated_pwd'];
        htmlspecialchars($new_pw);
        $new_salt = mt_rand();

        $new_hashed_pw = hash_hmac('sha256', $new_pw, $new_salt);
        $q=$db->prepare('UPDATE account SET hashed_password = (?), salt =(?) WHERE (email)= (?);'); 
                

        $q->bindParam(1, $new_hashed_pw);
        $q->bindParam(2, $new_salt);
        $q->bindParam(3, $EMAIL);
        if ($q->execute()) 
        {
            if (isset($_COOKIE['auth'])) 
            {
                unset($_COOKIE['auth']);
                setcookie('auth', '', time() - 3600 * 24 * 3, '/'); 
            }
                     // echo $new_pw."new salt: ".$new_salt."new hashed pw: ". $new_hashed_pw."admin pw: ". $admin_pw ."user pw: ". $user_pw ."a4 pw: ". $a4_pw  ;

            header('Location: login.php', true, 302);
            exit();
        }
    }
}
?>