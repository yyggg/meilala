<?php if(!defined('ROOT')) die('Access denied.');

error_reporting(E_ALL & ~E_NOTICE);

//自动加载函数(魔术函数)
function __autoload($class){
	require_once(ROOT. "includes/class.$class.php");
}

@include(ROOT . 'config/config.php');
require(ROOT . 'config/settings.php');

define('BASEURL', $_CFG['BaseUrl']);
define('COOKIE_USER', COOKIE_KEY.'user');  //前台用户的COOKIE名称

define('WS_HOST', gethostbyname($_SERVER['HTTP_HOST'])); //socket服务器IP地址
define('WS_PORT', $_CFG['SocketPort']); // socket服务器端口号

if($_CFG['Lang'] == 'Auto'){
	if (strstr(strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']), 'zh-cn') OR strstr(strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']), 'zh-tw')){
		$lang = 'Chinese';
	}else{
		$lang = 'English';
	}
}else{
	$lang = $_CFG['Lang'];
}

define('IS_CHINESE', Iif($lang == 'Chinese', 1, 0));
$langs = require(ROOT . "language/$lang.php"); //加载语言



//一些需要的函数
function header_nocache() {
	header("Expires: Mon, 18 Jul 1988 01:08:08 GMT"); // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache"); // HTTP/1.0
}

function encodeChar($str = ''){
	return str_replace('&', '||4||', $str); //将&转换成特殊字符串||4||
}

function Iif($expression, $x, $y = ''){
	return $expression ? $x : $y;
}

function PassGen($length = 8){
	$str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for ($i = 0, $passwd = ''; $i < $length; $i++)
		$passwd .= substr($str, mt_rand(0, strlen($str) - 1), 1);
	return $passwd;
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 600) {
	$ckey_length = 4;
	$key = md5($key ? $key : 'default_key');
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

function get_userAgent($userAgent){
	$r = "unknown";
	if(!$userAgent) return $r;
	$knownAgents = array("opera", "msie", "chrome", "safari", "firefox", "netscape", "mozilla");
	$userAgent = strtolower($userAgent);

	foreach ($knownAgents as $agent) {
		if (strstr($userAgent, $agent)) {
			if (preg_match("/" . $agent . "[\\s\/]?(\\d+(\\.\\d+(\\.\\d+(\\.\\d+)?)?)?)/", $userAgent, $matches)) {
				$ver = $matches[1];
				if ($agent == 'safari') {
					if (preg_match("/version\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
						$ver = $matches[1];
					} else {
						$ver = "1 or 2 (build " . $ver . ")";
					}
					if (preg_match("/mobile\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
						$r = "iPhone " . $matches[1] . " ($agent $ver)";
						break;
					}
				}

				$r = ucfirst($agent) . " " . $ver;
				break;
			}
		}
	}

	return $r;
}

?>