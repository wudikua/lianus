<?php 
include_once("header.php");

$r = new Redis();
$r->connect("localhost");
$sites = [];
$email = aes_decode($_COOKIE['email']);
if (strlen($_REQUEST['q'])>0) {
	$names = $r->keys("search:*{$_REQUEST['q']}*");
	$wwws = [];
	foreach ($names as $name) {
		$wwws = array_merge(array_keys($r->hgetall("$name")), $wwws);
	}
	foreach ($wwws as $www) {
		$site = $r->hgetall("site:$www");
		$sites[] = $site;
	}
} else {
	$wwws = $r->zrevrange("sites", 0, -1, true);
	foreach ($wwws as $www=>$t) {
		$site = $r->hgetall("site:$www");
		$sites[] = $site;
	}
}

if ($email) {
	$mysites = $r->zrange("email:$email:sites", 0, -1);
}

?>



<?php if($_COOKIE['msg']):?>
<div class="am-panel am-panel-success">
	<div class="am-panel-hd">
		<h3 class="am-panel-title"><?php echo $_COOKIE['msg'];?></h3>
	</div>
</div>
<?php setcookie('msg', false);?>
<?php endif;?>

<div class="am-container">

	<div class="am-g doc-am-g">
		<form class="am-form-inline" role="form" method="post" action="/index.php">
		  <fieldset>
		  <legend>筛选</legend>
		  <div class="am-form-group">
		  	<input type="text" class="am-form-field" required="" name="q" placeholder="关键词" autocomplete="off">   
		  </div>
		  <button type="submit" class="am-btn am-btn-default">
		  	<span class="am-icon-search">搜索</span>
		  </button>
		  </fieldset>
		</form>
	</div>

	<div class="am-g doc-am-g">
		<table class="am-table">
		    <thead>
		        <tr>
		            <th>提交时间</th>
		            <th>网站名称</th>
		            <th>链接</th>
		            <th>alexa排名</th>
		            <th>baidu收录</th>
		            <th>类别</th>
		            <th>SDK</th>
		            <th>联系站长</th>
		            <th>申请换链</th>
		        </tr>
		    </thead>
		    <tbody>
		    	<?php foreach ($sites as $site):?>
		        <tr>
		            <td><?php echo $site['time'];?></td>
		            <td><?php echo $site['name'];?></td>
		            <td><a href="<?php echo "http://".$site['www'];?>"><?php echo $site['www'];?></a></td>
		            <td><?php echo intval($site['alexa']);?></td>
		            <td><?php echo intval($site['bdSearchCount']);?></td>
		            <td><?php echo $site['type'];?></td>
		            <td><?php if($site['sdk']):?>生效<?php else:?>未生效<?php endif;?></td>
		            <td>
		            	<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $site['qq'];?>&site=qq&menu=yes" targe="_blank" class="am-icon-qq"> QQ</a>
		            	<a href="mailto:<?php echo $site['email'];?>" class="am-icon-paper-plane"> 邮件</a>
		            </td>

		            <?php if($email):?>
		            <td>
		            	<div class="am-btn-group">
						  <button class="am-btn am-btn-primary">申请</button>
						  <div class="am-dropdown" data-am-dropdown>
						    <button class="am-btn am-btn-primary am-dropdown-toggle" data-am-dropdown-toggle> <span class="am-icon-caret-down"></span></button>
						    <ul class="am-dropdown-content">
						      <li class="am-dropdown-header">使用交换的网址</li>
						      <?php foreach($mysites as $t):?>
						      <li><a href="/admin.php?a=apply&www=<?php echo $site['www'];?>&use=<?php echo $t;?>"><?php echo $t;?></a></li>
						  	  <?php endforeach;?>
						    </ul>
						  </div>
						</div>
	            	</td>
	            	<?php else:?>
	            	<td><a href="/login.php" class="am-btn am-btn-primary">申请</a></td>
	            	<?php endif;?>
		            
		        </tr>
		    	<?php endforeach;?>
		    </tbody>
		</table>
	</div>
</div>

<script src="http://cdn.amazeui.org/amazeui/2.4.2/js/amazeui.min.js"></script>
</body>
</html>