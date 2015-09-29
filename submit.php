<?php 
require_once "lib.php";

$name = $_REQUEST['name'];
$www = $_REQUEST['www'];
$type = $_REQUEST['type'];
$qq = $_REQUEST['qq'];
$email = aes_decode($_COOKIE['email']);
if ($name && $www && $type && $qq && $email) {
  $r = new Redis();
  $r->connect("localhost");
  $site = $r->hgetall("site:$www");
  $createTime = $r->zscore("sites", $www);
  if ($site || $createTime) {
    $err = "$www 已经被提交";
  } else {
    $r->hset("site:$www", "name", $name);
    $r->hset("site:$www", "type", $type);
    $r->hset("site:$www", "qq", $qq);
    $r->hset("site:$www", "email", $email);
    $r->hset("site:$www", "alexa", getAlexaRank($www));
    $r->hset("site:$www", "bdSearchCount", getBdSearchCount($www));
    $r->hset("site:$www", "time", date("Y-m-d H:i", time()));
    $r->hset("site:$www", "www", $www);
    $r->zadd("sites", time(), $www);
    $r->zadd("type:".base64_encode($type), time(), $www);
    $r->zadd("email:$email:sites", time(), $www);
    $r->hset("search:$name", $www, 1);
    setcookie("msg", "提交成功");
    header("location: /index.php");
  }
}

?>
<?php include_once("header.php");?>
<form class="am-form am-form-horizontal" action="/submit.php" method="post">
  <fieldset>
    <legend>提交网站</legend>

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
      <label for="doc-ipt-name-1">网站名称</label>
      <input type="text" name="name" value="<?php echo $name;?>" class="" id="doc-ipt-name-1" placeholder="输入网站名称">
    </div>

    <div class="am-form-group">
      <label for="doc-ipt-www-1">网址(例:sina.com)</label>
      <input type="text" name="www" value="<?php echo $www;?>" id="doc-ipt-www-1" placeholder="输入网站地址">
    </div>

    <div class="am-form-group">
      <label for="doc-select-1">行业分类</label>
      <select name="type" id="doc-select-1">
        <option value="女性时尚" <?php if($type=='女性时尚') echo "selected";?>>女性时尚</option>
        <option value="文学艺术" <?php if($type=='文学艺术') echo "selected";?>>文学艺术</option>
        <option value="企业商务" <?php if($type=='企业商务') echo "selected";?>>企业商务</option>
        <option value="彩票博彩" <?php if($type=='彩票博彩') echo "selected";?>>彩票博彩</option>
        <option value="体育运动" <?php if($type=='体育运动') echo "selected";?>>体育运动</option>
        <option value="影视宽带" <?php if($type=='影视宽带') echo "selected";?>>影视宽带</option>
        <option value="星相命理" <?php if($type=='星相命理') echo "selected";?>>星相命理</option>
        <option value="娱乐八卦" <?php if($type=='娱乐八卦') echo "selected";?>>娱乐八卦</option>
        <option value="明星美女" <?php if($type=='明星美女') echo "selected";?>>明星美女</option>
        <option value="爱情交友" <?php if($type=='爱情交友') echo "selected";?>>爱情交友</option>
        <option value="游戏网游" <?php if($type=='游戏网游') echo "selected";?>>游戏网游</option>
        <option value="博客论坛" <?php if($type=='博客论坛') echo "selected";?>>博客论坛</option>
        <option value="音乐Mp3" <?php if($type=='音乐Mp3') echo "selected";?>>音乐Mp3</option>
        <option value="个人网站" <?php if($type=='个人网站') echo "selected";?>>个人网站</option>
        <option value="门户网站" <?php if($type=='门户网站') echo "selected";?>>门户网站</option>
        <option value="网上购物" <?php if($type=='网上购物') echo "selected";?>>网上购物</option>
        <option value="旅游票务" <?php if($type=='旅游票务') echo "selected";?>>旅游票务</option>
        <option value="电脑网络" <?php if($type=='电脑网络') echo "selected";?>>电脑网络</option>
        <option value="人才培训" <?php if($type=='人才培训') echo "selected";?>>人才培训</option>
        <option value="娱乐社区" <?php if($type=='娱乐社区') echo "selected";?>>娱乐社区</option>
        <option value="聊天QQ" <?php if($type=='聊天QQ') echo "selected";?>>聊天QQ</option>
        <option value="动漫卡通" <?php if($type=='动漫卡通') echo "selected";?>>动漫卡通</option>
        <option value="文学小说" <?php if($type=='文学小说') echo "selected";?>>文学小说</option>
        <option value="幽默笑话" <?php if($type=='幽默笑话') echo "selected";?>>幽默笑话</option>
        <option value="医疗健康" <?php if($type=='医疗健康') echo "selected";?>>医疗健康</option>
        <option value="军事国防" <?php if($type=='军事国防') echo "selected";?>>军事国防</option>
        <option value="法律律师" <?php if($type=='法律律师') echo "selected";?>>法律律师</option>
        <option value="软件下载" <?php if($type=='软件下载') echo "selected";?>>软件下载</option>
        <option value="政府组织" <?php if($type=='政府组织') echo "selected";?>>政府组织</option>
        <option value="财经股票" <?php if($type=='财经股票') echo "selected";?>>财经股票</option>
        <option value="新闻报刊" <?php if($type=='新闻报刊') echo "selected";?>>新闻报刊</option>
        <option value="站长资源" <?php if($type=='站长资源') echo "selected";?>>站长资源</option>
        <option value="电子数码" <?php if($type=='电子数码') echo "selected";?>>电子数码</option>
        <option value="英文网站" <?php if($type=='英文网站') echo "selected";?>>英文网站</option>
        <option value="教育培训" <?php if($type=='教育培训') echo "selected";?>>教育培训</option>
        <option value="家居装饰" <?php if($type=='家居装饰') echo "selected";?>>家居装饰</option>
        <option value="其它网站" <?php if($type=='其它网站') echo "selected";?>>其它网站</option>
      </select>
      <span class="am-form-caret"></span>
    </div>


    <div class="am-form-group">
      <label for="doc-ipt-qq-1">QQ</label>
      <input type="number" name="qq" value="<?php echo $qq;?>" class="" id="doc-ipt-qq-1" placeholder="联系QQ">
    </div>

    <p>
    	<button type="submit" class="am-btn am-btn-default">提交</button>
    </p>
    
  </fieldset>
</form>

<script src="http://cdn.amazeui.org/amazeui/2.4.2/js/amazeui.min.js"></script>
</body>
</html>