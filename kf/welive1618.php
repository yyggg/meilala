<?php  

define('ROOT', dirname(__FILE__).'/');  //系统程序根路径, 必须定义, 用于防翻墙
require(ROOT . 'includes/core.guest.php');  //加载核心文件

if(!$_CFG['Actived']) shut_down($langs['shutdown']);

//ajax留言操作
$ajax =  intval($_GET['ajax']);
if($ajax == 1){
	require(ROOT . 'includes/functions.comment.php');  //加载留言需要的函数
	$DB = new Mysql($dbusername, $dbpassword, $dbname,  $servername, false, false); //不显示mysql错误
	$ajax = array('s' => 0, 'i' => 0);
	$json = new JSON;

	$act = ForceStringFrom('act');
	if($act == 'vvc'){
		$ajax['s'] = createVVC();
		die($json->encode($ajax));
	}elseif($act == 'get'){
		getVVC();
		die();
	}

	$key = ForceStringFrom('key');
	$code = ForceStringFrom('code');
	$decode = authcode($code, 'DECODE', $key);
	if($decode != md5(WEBSITE_KEY . $_CFG['KillRobotCode'])){
		die($json->encode($ajax)); //验证码过期
	}

	$fullname = ForceStringFrom('fullname');
	$email = ForceStringFrom('email');
	$phone = ForceStringFrom('phone');
	$content = ForceStringFrom('content');
	$vid = ForceIntFrom('vid');
	$vvc = ForceIntFrom('vvc');

	if(!$fullname OR strlen($fullname) > 90){
		$ajax['s'] = 2;
		die($json->encode($ajax));
	}elseif(!IsEmail($email)){
		$ajax['s'] = 3;
		die($json->encode($ajax));
	}elseif(!$content OR strlen($content) > 1800){
		$ajax['s'] = 4;
		die($json->encode($ajax));
	}elseif(!checkVVC($vid, $vvc)){
		$ajax['s'] = 5;
		die($json->encode($ajax));
	}

	$gid = ForceIntFrom('gid');
	$ip = GetIP();

	$DB->exe("INSERT INTO " . TABLE_PREFIX . "comment (gid, fullname, ip, phone, email, content, time) VALUES ('$gid', '$fullname', '$ip', '$phone', '$email', '$content', '".time()."')");
	$ajax['s'] =1;
	die($json->encode($ajax));
}

//正式开始
$a = intval($_GET['a']);
if($a !== 321456978) die('Access denied.'); //简单地防止直接访问当前文件(并不重要)

$fromurl = trim($_GET['url']);
$json = new JSON; //将语言转换成js对象

$smilies = ''; //表情图标
for($i = 0; $i < 24; $i++){
	$smilies .= '<img src="' . SYSDIR . 'public/smilies/' . $i . '.png" onclick="insertSmilie(' . $i . ');">';
}

$agent = encodeChar(get_userAgent($_SERVER['HTTP_USER_AGENT']));

$key = PassGen(8);
$code = authcode(md5(WEBSITE_KEY . $_CFG['KillRobotCode']), 'ENCODE', $key, 3600); //60分钟过期(60分钟后断线重连将失败)
$code = encodeChar($code); //先将&转换成特殊字符串||4||

header_nocache(); //不缓存
header('P3P: CP=CAO PSA OUR'); //解决IE下iframe cookie问题

echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="public/guest.css?r=1212">
<link rel="stylesheet" type="text/css" href="public/jquery.tipTip.css">
<script type="text/javascript" src="public/jquery126.js"></script>
<script type="text/javascript" src="public/jquery.tipTip.js" ></script>
<script type="text/javascript" src="public/jquery.scrollbar.js" ></script>
<script type="text/javascript">
SYSDIR = "' . SYSDIR . '",
COOKIE_USER = "' . COOKIE_USER . '",
SYSKEY = "' . $key . '",
SYSCODE = "' . $code . '",
WS_HOST = "' . WS_HOST . '",
WS_PORT = "' . WS_PORT . '",
update_time = ' . intval($_CFG['Update']) * 1000 . ',
offline_time = ' . intval($_CFG['AutoOffline']) * 60000 . ',
guest = {gid: 0, fn: "", aid: 0, an: "", lang: ' . IS_CHINESE . ', agent: "' . $agent . '", fromurl: "' . $fromurl . '"},
welcome = "' . encodeChar(Iif(IS_CHINESE, $_CFG['Welcome'], $_CFG['Welcome_en'])) . '",
langs = ' . $json->encode($langs) . ';
</script>
</head>
<body>
<div id="welive_operator">
	<img src="' . SYSDIR . 'public/img/welive.png" id="welive_avatar" style="padding:2px;">
	<div id="welive_name">' . $langs['welive'] . '</div>
	<div id="welive_duty">Connecting ...</div>
	<div id="welive_copyright" class=""><a href="http://www.weentech.com" target="_blank">&copy; WeLive</a></div>
</div>
<div class="history">
	<div class="scb_scrollbar scb_radius"><div class="scb_tracker"><div class="scb_mover scb_radius"></div></div></div>
	<div class="viewport loading3">
		<div class="overview">
			<div class="msg s">
				<div class="b">
					<div class="ico"></div>
					<div class="i">' . $langs['connecting'] . '</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="enter">
	<div class="s_face"></div>
	<input name="msger" placeholder="Enter to send" type="text" class="msger">
	<a class="sender" title="' . $langs['send'] . '"></a>
</div>
<div id="wl_sounder" style="width:0;height:0;display:block;overflow:hidden;"></div>
<div class="smilies_div" style="display:none"><div class="smilies_wrap">' . $smilies . '</div></div>
<script type="text/javascript" src="public/guest.js?r=1212"></script>
</body>
</html>';


function shut_down($info){
	echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body style="background:#fff url(public/img/w_ibg.png);margin:0;padding:0;">
<div style="font-size:14px;color:red;text-align:center;border:1px solid #b7b7b7;margin:0;padding:0;height:400px;padding-top:100px;">
' . $info . '
</div>
</body>
</html>';

	die();
}


?>