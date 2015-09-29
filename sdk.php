<?php
if ($_REQUEST['a'] == "links") {
	$fr = $_REQUEST['fr'];
	$r = new Redis();	
	$r->connect("localhost");
	$links = $r->smembers("$fr:links");
	if ($_REQUEST['type']=='json') {
		$ls = [];
		foreach ($links as $link) {
			$v = json_decode($link, true);
			$ls[] = $v;
		}
		echo json_encode($ls);die;
	}
	foreach($links as $link) {
		$v = json_decode($link, true);
		if ($_REQUEST['type'] == 'jsonp') {
			echo "addLink(\"{$v['www']}\", \"{$v['name']}\");".PHP_EOL;
		}
	}
	die;
}
require_once "lib.php";
$email = aes_decode($_COOKIE['email']);
if (!$email) {
	header("location: /login.php");
}
?>
<?php include_once("header.php");?>

<?php
$r = new Redis();
$r->connect("localhost");

$sites = [];
$wwws = $r->zrange("email:$email:sites", 0, -1, true);
foreach ($wwws as $www=>$t) {
	$site = $r->hgetall("site:$www");
	$sites[] = $site;
}
?>
<div class="am-container">
<?php foreach($sites as $site):?>
<div class="am-g doc-am-g">
<div class="am-panel am-panel-success">
	<div class="am-panel-hd"><h3 class="am-panel-title"><?php echo $site['www'];?>嵌入代码</h3></div>

	<div class="am-panel-bd">
		<b>推荐使用php版本直接生成HTML，有利于SEO</b>
		<div data-am-widget="tabs"
       class="am-tabs am-tabs-d2"
        >
      <ul class="am-tabs-nav am-cf">
          <li class="am-active"><a href="[data-tab-panel-0]">PHP版本</a></li>
          <li class=""><a href="[data-tab-panel-1]">JS版本</a></li>
          <li class=""><a href="[data-tab-panel-2]">其他语言</a></li>
      </ul>
      <div class="am-tabs-bd">
          <div data-tab-panel-0 class="am-tab-panel am-active">
            <pre style="-webkit-user-select:auto;">
&lt?php
$handle = fopen("http://<?php echo WEBSITE;?>/sdk.php?type=json&a=links&fr=<?php echo $site['www'];?>", "rb");
$contents = ""; 
while (!feof($handle)) { 
    $contents .= fread($handle, 8192); 
}
fclose($handle);
$links = @json_decode($contents, true);
if ($links) {
    foreach($links as $link) {
        echo "&lta style=\"text-decoration: none;\" href=\"" . $link['www'] . "\" target=\"_blank\"&gt" . $link['name'] . "&lt/a&gt";		
    }
}
?&gt
    		</pre>
          </div>
          <div data-tab-panel-1 class="am-tab-panel ">
          	<pre style="-webkit-user-select:auto;">
			<?php $gen = md5($site['www']);?>
&ltscript id="<?php echo $gen;?>"&gt
function addLink(link, name) {
    $("#<?php echo $gen;?>").after("&lta style=\"text-decoration: none;\" href=\"" + link + "\" target=\"_blank\"&gt" + name + "&lt/a&gt");
}
var sc=document.createElement('script');
sc.src="http://<?php echo WEBSITE;?>/sdk.php?type=jsonp&a=links&fr=<?php echo $site['www'];?>";
document.body.appendChild(sc);
&lt/script&gt
			</pre>
          </div>
          <div data-tab-panel-2 class="am-tab-panel ">
            <pre>
其他语言系统请使用RESTFUL接口
地址：http://<?php echo WEBSITE;?>/sdk.php?type=jsonp&a=links&fr=<?php echo $site['www'];?>

返回数据格式：
[
    {
        www: "baidu.com", //友情链接的地址
        name: "baidu"	  //友情链接的名称
    }
]
        	</pre>
          </div>
      </div>
  </div>
	</div>
</div>
</div>
<?php endforeach;?>
</div>
<script src="http://cdn.amazeui.org/amazeui/2.4.2/js/amazeui.min.js"></script>
</body>
</html>