<?php 
ini_set("display_errors", "on");
error_reporting(E_ALL^E_NOTICE);
date_default_timezone_set("PRC");
header("Content-type: text/html; charset=utf-8");
require_once "lib.php";
?>
<!doctype html>
<html class="no-js">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>Hello Amaze UI</title>

  <!-- Set render engine for 360 browser -->
  <meta name="renderer" content="webkit">

  <!-- No Baidu Siteapp-->
  <meta http-equiv="Cache-Control" content="no-siteapp"/>

  <!-- Add to homescreen for Chrome on Android -->
  <meta name="mobile-web-app-capable" content="yes">

  <!-- Add to homescreen for Safari on iOS -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="Amaze UI"/>

  <!-- Tile icon for Win8 (144x144 + tile color) -->
  <meta name="msapplication-TileImage" content="assets/i/app-icon72x72@2x.png">
  <meta name="msapplication-TileColor" content="#0e90d2">

  <link rel="stylesheet" href="http://cdn.amazeui.org/amazeui/2.4.2/css/amazeui.min.css">
  <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
</head>
<body>

<!--在这里编写你的代码-->

<header data-am-widget="header" class="am-header am-header-default">
  <div class="am-header-left am-header-nav">
      <a href="/" class="">
            <i class="am-header-icon am-icon-home"></i>
      </a>
  </div>

  <h1 class="am-header-title">
    自动友情链接平台
  </h1>

  <div class="am-header-right am-header-nav">
    <?php if(!aes_decode($_COOKIE['email'])):?>
  	<a href="/login.php" class="">
  	    <i class="am-header-icon am-icon-user">登录</i>
  	</a>
    <?php else:?>
    <a href="/submit.php" class="">
        <i class="am-header-icon am-icon-edit">提交网站</i>
    </a>

    <a href="/sdk.php" class="">
        <i class="am-header-icon am-icon-code">获取嵌入SDK代码</i>
    </a>

    <a href="/admin.php" class="">
        <i class="am-header-icon am-icon-check">换链申请</i>
    </a>
    <?php endif;?>

    
  </div>
</header>