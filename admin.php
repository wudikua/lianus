<?php include_once("header.php");?>

<?php 
define("APPLY_WAIT", '待处理');
define("APPLY_NOT_PASSS", '残忍拒绝');
define("APPLY_PASSS", '同意');

$r = new Redis();
$r->connect("localhost");
$email = aes_decode($_COOKIE['email']);
if (!$email) {
	header("location: /login.php");
}
if ($_REQUEST['a'] == "apply") {
	$www = $_REQUEST['www'];
	$use = $_REQUEST['use'];
	$owner = $r->hget("site:$www", "email");
	if ($r->hget("$email:applyFrom:$www", $use)) {
		// 已经申请过
		setcookie("msg", "已经用$www申请过$use");
    	header("location: /index.php");
	}
	$d = json_encode(['fu'=>$email, 'tu'=>$owner, 
		'd'=>time(), 'fw'=>$use, 'tw'=> $www, 's'=>APPLY_WAIT]);
	// 我的申请状态
	$r->hset("$email:applyFrom:$use", $www, $d);
	// 别人向我申请
	$r->hset("$owner:handleApply", $use, $d);
} else if ($_REQUEST['a'] == "yes") {
	$fw = $_REQUEST['fw'];
	$tw = $_REQUEST['tw'];
	$v = json_decode($r->hget("$email:handleApply", $fw), true);
	$v['s'] = APPLY_PASSS;
	$d = json_encode($v);
	$r->hset("$email:handleApply", $fw, $d);
	$r->hset("{$v['fu']}:applyFrom:{$v['fw']}", $tw, $d);

	$r->sadd("{$v['tw']}:links", json_encode(['www'=>$v['fw'], 'name'=>$r->hget("site:{$v['fw']}", "name")]));
	$r->sadd("{$v['fw']}:links", json_encode(['www'=>$v['tw'], 'name'=>$r->hget("site:{$v['tw']}", "name")]));
} else if ($_REQUEST['a'] == "no") {
	$fw = $_REQUEST['fw'];
	$tw = $_REQUEST['tw'];
	$v = json_decode($r->hget("$email:handleApply", $fw), true);
	$v['s'] = APPLY_NOT_PASSS;
	$d = json_encode($v);
	$r->hset("$email:handleApply", $fw, $d);
	$r->hset("{$v['fu']}:applyFrom:{$v['fw']}", $tw, $d);
}

$sites = [];
$wwws = $r->zrange("email:$email:sites", 0, -1, true);
foreach ($wwws as $www=>$t) {
	$site = $r->hgetall("site:$www");
	$sites[] = $site;
}
$applys = [];
foreach ($sites as $site) {
	$t = $r->hgetall("$email:applyFrom:{$site['www']}");
	foreach ($t as $v) {
		$applys[] = $v;	
	}
}
$handle = $r->hgetall("$email:handleApply");

$both = [];
foreach ($sites as $site) {
	foreach ($r->smembers("{$site['www']}:links") as $v) {
		$both[$site['www']][] = json_decode($v, true)['www'];	
	}
}
?>

<div data-am-widget="tabs"
   class="am-tabs am-tabs-d2"
    >
  <ul class="am-tabs-nav am-cf">
      <li class="am-active"><a href="[data-tab-panel-0]">待处理换链申请</a></li>
      <li class=""><a href="[data-tab-panel-1]">我申请的换链状态</a></li>
      <li class=""><a href="[data-tab-panel-2]">互相换链接成功</a></li>
  </ul>
  <div class="am-tabs-bd">
      <div data-tab-panel-0 class="am-tab-panel am-active">
        <ul class="am-list am-list-static am-list-border">
        <?php foreach($handle as $vh):?>
        <?php $vv = json_decode($vh, true);?>
        	<?php if ($vv['s'] == APPLY_WAIT):?>
        	<li>
    		<div class="am-g">
    			<div class="am-u-sm-8">
    				<?php echo $vv['fu'];?>
	        		<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $r->hget("site:{$vv['fu']}", "qq");?>&site=qq&menu=yes" targe="_blank" class="am-icon-qq"></a>
	    			想使用
	    			<a href="<?php echo "http://".$vv['fw'];?>"><?php echo $vv['fw'];?></a>
	    			交换
	    			<a href="<?php echo "http://".$vv['tw'];?>"><?php echo $vv['tw'];?></a>
    			</div>
    			<div class="am-u-sm-4">
    				<a href="/admin.php?a=yes&fw=<?php echo $vv['fw'];?>&tw=<?php echo $vv['tw'];?>" class="am-btn am-btn-success">同意</a>
    				<a href="/admin.php?a=no&fw=<?php echo $vv['fw'];?>&tw=<?php echo $vv['tw'];?>" class="am-btn am-btn-danger">拒绝</a>
    			</div>
			</div>
        	</li>
        	<?php endif;?>
    	<?php endforeach;?>
    	</ul>
      </div>
      <div data-tab-panel-1 class="am-tab-panel ">
      	<ul class="am-list am-list-static am-list-border">
        <?php foreach($applys as $va):?>
        <?php $vv = json_decode($va, true);?>
        	<li>
			<div class="am-g">
				<div class="am-u-sm-8">
	    		<?php echo $vv['fu'];?>
	    		<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $r->hget("site:{$vv['fu']}", "qq");?>&site=qq&menu=yes" targe="_blank" class="am-icon-qq"></a>
				想使用
				<a href="<?php echo "http://".$vv['fw'];?>"><?php echo $vv['fw'];?></a>
				交换
				<a href="<?php echo "http://".$vv['tw'];?>"><?php echo $vv['tw'];?></a>
				</div>
    			<div class="am-u-sm-4">
    				<span class="am-alert">
    				<?php echo $vv['s'];?>
    				</span>
				</div>
    		</div>
    		</li>
    	<?php endforeach;?>
    	</ul>
      </div>
      <div data-tab-panel-2 class="am-tab-panel ">
       	<ul class="am-list am-list-static am-list-border">
        <?php foreach($both as $my=>$links):?>
        <?php foreach($links as $link):?>
        	<li>
			<div class="am-g">
				<div class="am-u-sm-8">
				<a href="<?php echo "http://".$my;?>"><?php echo $my;?></a>
				已经与互相交换链接
				<a href="<?php echo "http://".$link;?>"><?php echo $link;?></a>
				</div>
    		</div>
    		</li>
		<?php endforeach;?>
    	<?php endforeach;?>
    	</ul>
      </div>
  </div>
</div>


<script src="http://cdn.amazeui.org/amazeui/2.4.2/js/amazeui.min.js"></script>
</body>
</html>