<?php
require_once "lib.php";
define("VALIDATE_MAIL", false);
global $err;
if ($_REQUEST['uuid'] && $_REQUEST['email']) {
  $r = new Redis();
  $r->connect("localhost");
  $pwd = $r->hget($_REQUEST['email'].":session", "password");
  if ($_REQUEST['uuid'] == $pwd) {
    $r->hset($_REQUEST['email'], "password", $r->hget($_REQUEST['email'], "regPassword"));
    $r->del($_REQUEST['email'].":session");
    // success
    setcookie("msg", "注册成功");
    header("location: /login.php");
  } else {
    setcookie("msg", "连接已经失效");
    header("location: /login.php");
  }
}
if ($_REQUEST['email'] && $_REQUEST['password']) {
	$r = new Redis();
	$r->connect("localhost");
	$pwd = $r->hget($_REQUEST['email'], "password");
	if (strlen($pwd) == 0) {
    if (VALIDATE_MAIL) {
      $uuid = md5(microtime());
      $rt = smtp_mail($_REQUEST['email'], "自动友情链接平台注册验证", "点击链接，完成注册
          http://".WEBSITE."/reg.php?uuid={$uuid}&email={$_REQUEST['email']}
        ");
      if ($rt) {
        $r->hset($_REQUEST['email'].":session", "password", $uuid);
        $r->hset($_REQUEST['email'].":session", "regPassword", 
          $_REQUEST['password']);
        $r->expire($_REQUEST['email'].":session", 3600);
        // success
        setcookie("msg", "请登录邮箱，查看注册邮件，如果没有收到请检查垃圾邮件");
        header("location: /login.php");
      }  
    } else {
      //直接注册
      $r->hset($_REQUEST['email'], "password", $_REQUEST['password']);
      // success
      setcookie("msg", "注册成功");
      header("location: /login.php");
    }
    
	} else {
		$err = "用户名已经被注册";
	}
}

function smtp_mail($to, $subject, $message, $headers = '')
{
  $recipients = explode(',', $to);
  $user = 'lianjie2015@2980.com';
  $pass = 'mjak123';
  // The server details that worked for you in the above step
  $smtp_host = 'smtp.2980.com';
  //The port that worked for you in the above step
  $smtp_port = 25;

  if (!($socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 3)))
  {
    header("Content-type: text/html; charset=utf-8");
    $err = "Error connecting to '$smtp_host' ($errno) ($errstr)";
    return false;
  }

  server_parse($socket, '220');

  fwrite($socket, 'EHLO '.$smtp_host."\r\n");
  server_parse($socket, '250');

  fwrite($socket, 'AUTH LOGIN'."\r\n");
  server_parse($socket, '334');

  fwrite($socket, base64_encode($user)."\r\n");
  server_parse($socket, '334');

  fwrite($socket, base64_encode($pass)."\r\n");
  server_parse($socket, '235');

  fwrite($socket, 'MAIL FROM: <'.$user.'>'."\r\n");
  server_parse($socket, '250');

  foreach ($recipients as $email)
  {
    fwrite($socket, 'RCPT TO: <'.$email.'>'."\r\n");
    server_parse($socket, '250');
  }

  fwrite($socket, 'DATA'."\r\n");
  server_parse($socket, '354');
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/plain; charset=utf-8\r\n";
  $headers .= "Content-Transfer-Encoding: 8bit";
  $subject = "=?UTF-8?B?".base64_encode($subject)."?=";
  fwrite($socket, 'From: admin<lianjie2015@2980.com>'."\r\n".'Subject: '
    .$subject."\r\n".'To: <'.implode('>, <', $recipients).'>'
    ."\r\n".$headers."\r\n\r\n".$message."\r\n");

  fwrite($socket, '.'."\r\n");
  server_parse($socket, '250');

  fwrite($socket, 'QUIT'."\r\n");
  fclose($socket);

  return true;
}

function server_parse($socket, $expected_response)
{
  $server_response = '';
  while (substr($server_response, 3, 1) != ' ')
  {
    if (!($server_response = fgets($socket, 256)))
    {
      $err = '服务器内部错误';
      return false;
    }
  }

  if (!(substr($server_response, 0, 3) == $expected_response))
  {
    $err = 'Unable to send e-mail."'.$server_response.'"';
    return false;
  }
}
?>

<?php include_once("header.php");?>

<form class="am-form" action="/reg.php" method="get">
  <fieldset>
    <legend>注册</legend>

    <?php if($err):?>
    <div class="am-form-group am-form-error">
      <label class="am-form-label" for="doc-ipt-error"><?php echo $err;?></label>
    </div>
    <?php endif;?>

    <div class="am-form-group">
      <label for="doc-ipt-email-1">邮件</label>
      <input type="email" name="email" class="" id="doc-ipt-email-1" placeholder="输入电子邮件">
    </div>

    <div class="am-form-group">
      <label for="doc-ipt-pwd-1">密码</label>
      <input type="password" name="password" class="" id="doc-ipt-pwd-1" placeholder="设置个密码吧">
    </div>

    <p>
		  <button type="submit" class="am-btn am-btn-default">注册</button>
    </p>
  </fieldset>
</form>

<script src="http://cdn.amazeui.org/amazeui/2.4.2/js/amazeui.min.js"></script>
</body>
</html>