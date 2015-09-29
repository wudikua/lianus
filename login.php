<?php
require_once "lib.php";
if ($_REQUEST['email'] && $_REQUEST['password']) {
	$r = new Redis();
	$r->connect("localhost");
	$pwd = $r->hget($_REQUEST['email'], "password");
	if ($pwd == $_REQUEST['password']) {
		// success
		setcookie("email", aes_encode($_REQUEST['email']));
		header("location: /index.php");
	} else {
		$err = "用户名或密码错误";
	}
}
?>

<?php include_once("header.php");?>
<form class="am-form" action="/login.php" method="get">
  <fieldset>
    <legend>登录</legend>

    <?php if($err):?>
    <div class="am-form-group am-form-error">
      <label class="am-form-label" for="doc-ipt-error"><?php echo $err;?></label>
    </div>
    <?php endif;?>

    <?php if($_COOKIE['msg']):?>
    <div class="am-form-group am-form-success">
      <label class="am-form-label" for="doc-ipt-success"><?php echo $_COOKIE['msg'];?></label>
      <?php setcookie('msg', false);?>
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
    	<button type="submit" class="am-btn am-btn-default">登录</button>
		<a href="/reg.php" class="am-btn am-btn-default">注册</a>
    </p>
    
  </fieldset>
</form>

<script src="http://cdn.amazeui.org/amazeui/2.4.2/js/amazeui.min.js"></script>
</body>
</html>