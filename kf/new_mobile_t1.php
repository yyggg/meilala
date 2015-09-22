<?php  
//session_start();
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
//$a = intval($_GET['a']);
//if($a !== 321456978) die('Access denied.'); //简单地防止直接访问当前文件(并不重要)

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
//echo $_SESSION['wx_user']['headimgurl'];
//$headimgurl=$_SESSION['wx_user']['headimgurl']?$_SESSION['wx_user']['headimgurl']:'../images/touxiang.png';
//$openid=$_SESSION['wx_user']['openid']?$_SESSION['wx_user']['openid']:'12134254365476';
echo '<!DOCTYPE html>
<html>
<head>
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="public/guest.css?r=1212">
<link rel="stylesheet" type="text/css" href="public/jquery.tipTip.css">
<script type="text/javascript" src="public/jquery126.js"></script>
<script type="text/javascript" src="public/jquery.tipTip.js" ></script>
<script type="text/javascript" src="public/new_mobile.js" ></script>
<link rel="stylesheet" href="public/new_mobile.css?r=1212">
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
<header class="header">
	<div class="header_back"><a href="/"><img src="../images/header_icon1b.png" /></a></div>
    <h2>专家在线</h2>
</header>

<div class="history" id="history">
	<div class="viewport loading3 ">
		<div class="overview" >
			<div class="msg s">
				<div class="b">
					<div class="ico"></div>
					<div class="i">' . $langs['connecting'] . '</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="clear:both"></div>
<div class="enter">
	<div class="s_face"></div>
	<input name="msger" placeholder="Enter to send" type="text" class="msger" id= "msger">
    <form enctype="multipart/form-data" method="post" name="upform" id="upform" target="check_file_frame" >  
        <div class="btn_upfile"><input name="upfile" type="file" unat="server" accept="image/*" capture="camera" id="upfile" onchange="fileOnchage(this.files);">  </div>  
	    <iframe style="display: none;" id="check_file_frame" name="check_file_frame"></iframe>
        <canvas id="canvas" style="display:none;"></canvas> 
    </form>
    <a class="sender" title="' . $langs['send'] . '"><button>发送</button></a>
</div>
<div class="big_img_box" id="big_img_box" style="display:none;"></div>
<script type="text/javascript" src="public/new_guest_t1.js?r=1212"></script>

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