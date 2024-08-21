<?php

session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>2FA</title>
</head>
<body>
   <div>An email have send to your mail box</div>
   <div>Remain time: <span id="countdown"></span></div>
    
 <?php
echo '
  <script>
    let beginTime = new Date( "'.$_SESSION['2FA_begin_time'].'");
    let timeoutTime=new Date("' .$_SESSION['2FA_timeout_time']. '");
    let remain_sec=120;
    let countdownTimer = setInterval(function() {
      document.getElementById("countdown").innerHTML = remain_sec + "s ";
      remain_sec--;
      if (remain_sec < 0 || timeoutTime.getTime()<beginTime.getTime()) {
        clearInterval(countdownTimer);
        document.getElementById(`countdown`).innerHTML = "Expired,Pls login again";
      }
    }, 1000);
  </script>
'

;?> 
  <form action="2FA.php" method="post">
    <label for="six-digit">Enter a six-digit number:</label>
    <input type="text" id="six-digit" name="six-digit" pattern="\d{6}" required>
    <br><br>
    <input type="submit" value="Submit">
  </form>
</body>
</html>



<?php
  //session_start();


  $current_time = new DateTime(); 
  $timeout_time = DateTime::createFromFormat('Y-m-d H:i:s', $_SESSION['2FA_timeout_time']);
  if($current_time<$timeout_time&&strval($_SESSION['2FA'])==strval($_POST['six-digit'])){
    $r=$_SESSION['r'];
    $adminFlag=$_SESSION['adminFlag'];
    $exp = time() + 3600 * 24 * 3;
    $token = array('em'=>$r['EMAIL'],'exp'=>$exp, 'k'=>hash_hmac('sha256', $exp.$r['HASHED_PASSWORD'], $r['SALT']),'admin_flag'=>$r['ADMIN_FLAG'],'userid'=>$r['USERID'] );
    setcookie('auth', json_encode($token), $exp, '','', true, true);
    $_SESSION['auth'] = $token;
    session_regenerate_id();
    if($adminFlag== 1)
    {
      $_SESSION['login'] = true;
      header('Location: admin.php', true, 302);
      exit();
    } else 
    {
        $_SESSION['login'] = true;
        header('Location: index.php', true, 302);
        exit();
    }
  }
    
    
?>