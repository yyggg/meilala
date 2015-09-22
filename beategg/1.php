<?php
require_once '../inc/common.php';
require_once 'company_pay.php';
require_once "../weixin/wx_js_sdk/jssdk.php";

//缓存 获取砸蛋配置信息
$m = new Memcache();
$m->connect('127.0.0.1', 11211);
//$m->delete('egg_cfg',0);die;
$beatEggCfg = $m->get('egg_cfg');
if(!$beatEggCfg)
{
	$sql = "SELECT * FROM m_beat_egg_cfg";
	$sth = $db->prepare($sql);
	$sth->execute();
	$beatEggCfg = $sth->fetch(PDO::FETCH_ASSOC);
	$beatEggCfg['yue'] = $beatEggCfg['tmoney'] - $beatEggCfg['umoney'];
	$m->set('egg_cfg', $beatEggCfg, 0, 86400*30);
}
//print_r($beatEggCfg);die;
$beatEggCfg['status'] = 0; //活动结束

$mchid = '1248900901'; //商户号
$no = substr(uniqid(),3); //随机字符串
$appid = 'wx7a5ba916e3074fd7';
$appsecret = '8bbef8aaca02345c838c31adcdf864f1';
$payurl = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers'; //企业付款接口请求URL
$openid = $_SESSION['wx_user']['openid']; 
$username = $_SESSION['wx_user']['nickname'];
$headimgurl = $_SESSION['wx_user']['headimgurl'];

$jssdk = new JSSDK($appid, $appsecret);
$signPackage = $jssdk->GetSignPackage();

$time = time();
/* 1块到1块五（单位分） */
$rands = [
		1,1.01,1.02,1.03,1.04,1.05,1.06,1.07,1.08,1.09,1.1,1.11,1.12,1.13,1.14,1.15,1.16,1.17,1.18,1.19,1.2,
		//1.21,1.22,1.23,1.24,1.25,1.26,1.27,1.28,1.29,1.3,1.31,1.32,1.33,1.34,1.35,1.36,1.37,1.38,1.39,1.4
];

/* 砸蛋动态 */
$sql = "SELECT username,headimgurl,money,addtime FROM m_beat_egg_logs ORDER BY id DESC LIMIT 8";
$sth = $db->prepare($sql);
$sth->execute();
$lists = $sth->fetchAll(PDO::FETCH_ASSOC);
	

$uInfo = getSubscribe($appid, $appsecret, $openid);

function getSubscribe($appid, $appsecret, $openid)
{
	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
	$accessToken = https_request($url);
	var_dump($accessToken);die;
    $accessToken = json_decode($accessToken, true);
	$token = $accessToken['access_token'];

	$dingyue_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid&lang=zh_CN";
	$dingyue_json = https_request($dingyue_url);
    return json_decode($dingyue_json, true);
}

function https_request($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {return 'ERROR '.curl_error($curl);}
    curl_close($curl);
    return $data;
}


/* 付款 */
function pay($mchid,$appid,$no,$payurl,$openid,$money,$desc='美啦啦砸金蛋活动')
{
	//签名参与参数
	$parameters = [
			'partner_trade_no' => $mchid . date('Ymd') . $no, //商户订单号
			'mchid' => $mchid, //商户号
			'mch_appid' => $appid, //公众账号appid
			'openid' => $openid, //用户id
			'check_name' => 'NO_CHECK', //校验用户姓名
			'amount' => $money, //付款金额
			'desc' => $desc, 
			'spbill_create_ip' => '124.172.237.174', //
			'nonce_str' => createNoncestr(), //随机字符串，小于32位
	];
	$sign = getSign($parameters);//获取签名
	$parameters['sign'] = $sign;
	$xml = arrayToXml($parameters); //生成请求XML
	return postXmlSSLCurl($xml, $payurl);
}


/* 砸蛋操作 */
if(isset($_GET['get_num']))
{
	
	$flag = ['flag'=>0, 'money'=>0];
	if($beatEggCfg['status'] == 0 || $beatEggCfg['yue']<2)
	{
		$flag['flag'] = -5; //活动结束
		die(json_encode($flag));
	}
	
	$sql = "SELECT num,used FROM m_beat_egg_restrict WHERE openid = '$openid'";
	$sth = $db->prepare($sql);
	$sth->execute();
	$data = $sth->fetch(PDO::FETCH_ASSOC);
	
	if(!$data) //第一次砸，有一次免费机会
	{
		$money = $rands[mt_rand(0,20)];
		$flag['money'] = $money;
		$sql = "INSERT INTO m_beat_egg_logs (`openid`,`username`,`headimgurl`,`money`,`addtime`)
				VALUES ('$openid','$username','$headimgurl','$money','$time')";
		$sth = $db->prepare($sql);
		$sth->execute();
		$flag['flag'] = 1; //砸蛋成功
		$payMoney = $money * 100;
		if($payMoney > 120 ) $payMoney = 120;
		if($flag['flag']) pay($mchid,$appid,$no,$payurl,$openid,$payMoney); //付款
		
		$sql = "INSERT INTO m_beat_egg_restrict (`openid`,`num`,`used`) VALUES ('$openid','1','1')";
		$sth = $db->prepare($sql);
		$sth->execute();
		
		$beatEggCfg['yue'] -= $money;
		$m->replace('egg_cfg',$beatEggCfg);
		
		$sql = "UPDATE m_beat_egg_cfg SET `umoney` = `umoney` + $money";
		$sth = $db->prepare($sql);
		$sth->execute();
	}
	elseif ($data['num'] == 0 && $data['used'] == 1) //免费一次机会已用完，需要分享获取更多次数
	{
		$flag['flag'] = -1; //砸蛋失败，
	}
	elseif ($data['num'] > 0 && $data['used'] < 2 && $data['used'] < $data['num']) //最多有2次砸蛋
	{
		$money = $rands[mt_rand(0,20)];
		$flag['money'] = $money;
		$sql = "INSERT INTO m_beat_egg_logs (`openid`,`username`,`headimgurl`,`money`,`addtime`)
		VALUES ('$openid','$username','$headimgurl','$money','$time')";
		$sth = $db->prepare($sql);
		$sth->execute();
		$flag['flag'] = 1; //砸蛋成功，
		$payMoney = $money * 100;
		if($payMoney > 120 ) $payMoney = 120;
		if($flag['flag']) pay($mchid,$appid,$no,$payurl,$openid,$payMoney); //付款
		
		$sql = "UPDATE m_beat_egg_restrict SET `used` = `used` + 1 WHERE `openid` = '$openid' LIMIT 1";
		$sth = $db->prepare($sql);
		$sth->execute();
		
		$beatEggCfg['yue'] -= $money;
		$m->replace('egg_cfg',$beatEggCfg);
		
		$sql = "UPDATE m_beat_egg_cfg SET `umoney` = `umoney` + $money";
		$sth = $db->prepare($sql);
		$sth->execute();
	}
	elseif ($data['used'] == $data['num'] && $data['used'] < 3)
	{
		$flag['flag'] = -2; //砸蛋失败，次数全部用完
	}
	else
	{
		$flag['flag'] = -3; //砸蛋失败，需要分享获取更多次数
	}
	die(json_encode($flag));
}

/* 分享增加砸蛋次数 */
if(isset($_GET['add_num']))
{
	$flag = ['flag'=>0];
	$sql = "UPDATE m_beat_egg_restrict SET `num` = `num`+1 WHERE `num` < 10 AND `openid` = '$openid' LIMIT 1";
	$sth = $db->prepare($sql);
	$sth->execute();
	if($sth->rowCount()) $flag['flag'] = 1;
	die(json_encode($flag));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>美啦啦砸金蛋活动</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="./css/app.css" type="text/css" media="all">
	<link rel="stylesheet" href="./css/style.css" type="text/css" media="all">
	<script src="./js/jquery-1.11.2.min.js" type="text/javascript"></script>
	<script src="./js/mobile.js" type="text/javascript"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</head>
<body>
<section class="st1"></section>	
	<section class="st3"><img src="./images/egg_img2.png" alt="" /></section>
	<div id="egg-area">
		
			<?php if($uInfo['subscribe'] == 1):?> 
				
					
				<?php if($beatEggCfg['status'] && $beatEggCfg['yue'] > 2):?>		
					
					
					<section class="st5 st7">
						<div class="st5_ct1 st7_ct1" id="egg_area" >
							<img src="./images/egg.png" alt="" id="egg_img" />
							<img src="./images/egg_img6.png" alt="" id="cuizi" />
						</div>			
					</section>	
					<section class="st2 st8"><div class="st1_ct4"></div></section>
					<?php else:?>
					<section class="st5 st12 atv-end">
						<div class="st5_ct1 st12_ct1">
							本次砸现金蛋活动已结束了<br>
							敬请关注下次活动，别走开！随时开始哟~
						</div>
					</section>
				<?php endif;?>
			<?php elseif ($uInfo['subscribe'] == 0):?>
			
			<section class="st5">
				<div class="st5_ct1">
					<div class="st5_ct1_p1">从美啦啦公众号回复“美啦啦”参与活动</div>
					<div class="st5_ct1_p2"><img src="./images/egg_img5.png" alt="" /></div>
					<div class="st5_ct1_p3">长按美啦啦二维码关注/进入</div>
					<div class="st5_ct1_p4 st1_ct3">
						<button onclick="window.location.href='http://mp.weixin.qq.com/s?__biz=MzAxNzUwNzk3Ng==&mid=223944016&idx=1&sn=48d3e60ba805ce2bff8b116849c39625&scene=1&key=0acd51d81cb052bc39273a9f5ad9fc8b052cd654299298f13fd0ca7007e5d9be23fb48c5c86910515f44390d839863dc&ascene=1&uin=MTc5MjE1NjgyNg%3D%3D&devicetype=Windows-QQBrowser&version=61001201&pass_ticket=6uTUkFDFEggtVWfkMP6gjI6MtWlyvvWXcLMjz6pSsnNB2oiDiX0hzrP3ph3XSxiW'">如无法识别可点此进入后关注</button>
					</div>
				</div>
				
			</section>
			<section class="st2 st6"><div class="st1_ct4"></div></section>
			<?php endif;?>
		
	</div>
		<?php if(!$beatEggCfg['status'] || $uInfo['subscribe'] == 0):?>
			<section class="st9 st11">
			<div class="st9_ct1">
				<div class="st9_ct1_p1">砸蛋结果动态</div>
				<div class="st9_ct1_p2">
					<?php foreach($lists as $v):;?>
					<div class="st9_ct1_p2_li">
						<div class="st9_ct1_p2_li_c1"><img src="<?php echo $v['headimgurl'];?>" alt="" /></div> 
						<div class="st9_ct1_p2_li_c2 text_overflow"><?php echo $v['username'];?></div>                          
						<div class="st9_ct1_p2_li_c3"><?php echo $v['money'];?>元</div> 
						<div class="st9_ct1_p2_li_c4"><?php echo date('m-d H:i:s',$v['addtime']);?></div> 
					</div>	
					<?php endforeach;?>
				</div>			
			</div>
			<div class="st9_ct2">
				<div class="st9_ct2_p1">活动规则：</div>
				<div class="st9_ct2_p2">
				<p>
					1、通过美啦啦公众号提示消息进入砸现金蛋页面。</p><p>			
					2、每砸蛋一次，都可砸出现金，最高1000元，现金自动存入您的微信零钱。</p><p>
					3、邀请朋友一起参与砸现金蛋活动，可获取一次砸蛋机会。</p><p>
					4、每人砸蛋机会共2次，数量有限，砸完即止。</p><p>
					5、本次活动最终解释权归美啦啦所有。</p>
				</div>
			</div>		
			</section>	
		<?php else:?>
			<section class="st9 st13">
			<div class="st9_ct1">
				<div class="st9_ct1_p1">砸蛋结果动态</div>
				<div class="st9_ct1_p2">
					<?php foreach($lists as $v):;?>
					<div class="st9_ct1_p2_li">
						<div class="st9_ct1_p2_li_c1"><img src="<?php echo $v['headimgurl'];?>" alt="" /></div> 
						<div class="st9_ct1_p2_li_c2 text_overflow"><?php echo $v['username'];?></div>                          
						<div class="st9_ct1_p2_li_c3"><?php echo $v['money'];?>元</div> 
						<div class="st9_ct1_p2_li_c4"><?php echo date('m-d H:i:s',$v['addtime']);?></div> 
					</div>	
					<?php endforeach;?>
				</div>			
			</div>
			<div class="st9_ct2">
				<div class="st9_ct2_p1">活动规则：</div>
				<div class="st9_ct2_p2">
				<p>
					1、通过美啦啦公众号提示消息进入砸现金蛋页面。</p><p>			
					2、每砸蛋一次，都可砸出现金，最高1000元，现金自动存入您的微信零钱。</p><p>
					3、邀请朋友一起参与砸现金蛋活动，可获取一次砸蛋机会。</p><p>
					4、每人砸蛋机会共2次，数量有限，砸完即止。</p><p>
					5、本次活动最终解释权归美啦啦所有。</p>
				</div>
			</div>		
			</section>				
		<?php endif;?>

<script>

var w=$(window).width();
$('.st1').css({'height':w/640*233+'px'});

/* 砸蛋特效 */
	$("#egg_area").click(function() {
		$.get('index.php?get_num', function(data){
		if(data.flag == 1)
		{
			go(data.money);
		}
		else if(data.flag == -1 || data.flag == -2)
		{
			//alert('您的次数已做用完，请先分享到朋友圈或邀请好友获取更多机会 ');
			//window.location.href="share.php";
			empty_num ();
		}
		else if(data.flag == -5)
		{
			var html =  '<section class="st5 st12 atv-end">'+
						'<div class="st5_ct1 st12_ct1">'+
						'本次砸现金蛋活动已结束了<br>'+
						'敬请关注下次活动，别走开！随时开始哟~'+
						'</div>'+
						'</section>';
			$('#egg-area').html(html);
		}
		else
		{
			//alert('您的次数已全部用完~，敬请期待下期活动 ');
			empty_num ();
		}
  },'json');
	})
 function go (money) {
    	var cuizi_t=$("#cuizi").position().top;
    	var cuizi_l=$("#cuizi").position().left;
    	//alert(cuizi_l);alert(cuizi_t);
    	$("#cuizi").css({"top":cuizi_t,"left":cuizi_l,"position":""});
    	$('.st7_ct1').attr('id','egg_area_end');
		$("#cuizi").animate({
			"top":cuizi_t  +20,
			"left":cuizi_l -40,
			},100,function(){
				$("#cuizi").hide();
				$(".st7").addClass("st10");  	
		    	$("#egg_img").attr("src","./images/egg_2.png");	
		    	var html=	'<div class="money">恭喜你砸出<br><i>'+money+'元</i></div>'+
							'<div class="st1_ct3 re_go"><button>再砸一次</button></div>';				
		    	$(".st10 .st7_ct1").append(html);
		    	$(".money").css({"opacity":"0","fontSize":"0","top":"0"});
		    	$(".money").animate({"opacity":"1.0","fontSize":"1.3rem","top":"-3rem"},1000);
			}
		);  

    }
 	function empty_num () {
    	var cuizi_t=$("#cuizi").position().top;
    	var cuizi_l=$("#cuizi").position().left;
    	//alert(cuizi_l);alert(cuizi_t);
    	$("#cuizi").css({"top":cuizi_t,"left":cuizi_l,"position":""});
    	$('.st7_ct1').attr('id','egg_area_end');
		$("#cuizi").animate({
			"top":cuizi_t  +20,
			"left":cuizi_l -40,
			},100,function(){
				$("#cuizi").hide();
				$(".st7").addClass("st10");  	
		    	$("#egg_img").attr("src","./images/egg_2.png");	
		    	var html=	'<div class="money">砸蛋机会用完啦~<br>分享朋友圈可获取砸蛋机会！</div>';
		    	$(".st10 .st7_ct1").append(html);
		    	$(".money").css({"opacity":"0","fontSize":"0","top":"0"});
		    	$(".money").animate({"opacity":"1.0","fontSize":"1.3rem","top":"-3rem"},1000);
		    	window.setTimeout("window.location='share.php',10000");
			}
		);
		//

    } 



 	wx.config({
 	    debug: false,
 	    appId: '<?php echo $signPackage["appId"];?>',
 	    timestamp: '<?php echo $signPackage["timestamp"];?>',
 	    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
 	    signature: '<?php echo $signPackage["signature"];?>',
 	    jsApiList: [
 			// 所有要调用的 API 都要加到这个列表中
 			'checkJsApi',    
 			'onMenuShareTimeline',    
 			'onMenuShareAppMessage',    
 			'onMenuShareQQ',    
 			'onMenuShareWeibo',   
 	    ]
 	  });
 	  
 	  wx.ready(function () {
 	    // 在这里调用 API
 		wx.onMenuShareTimeline({
 			title: '美啦啦砸现金蛋活动，最高1000元现金', // 分享标题
 			desc: '美啦啦不玩礼品，美啦啦只玩现金！蛋不虚砸，每次必中！最高1000元现金，还等什么呢？进去开砸吧~', // 分享描述
 			link: 'http://m.meilala.net/beategg/index.php', // 分享链接
 			imgUrl: 'http://m.meilala.net/beategg/images/egg_atv.jpg', // 分享图标
 			type: 'link', // 分享类型,music、video或link，不填默认为link
 			dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
 			success: function () {

 			},
 			cancel: function () { 
 				// 用户取消分享后执行的回调函数
 			}
 		});

 		wx.onMenuShareAppMessage({
 			title: '美啦啦砸现金蛋活动，最高1000元现金', // 分享标题
 			desc: '美啦啦不玩礼品，美啦啦只玩现金！蛋不虚砸，每次必中！最高1000元现金，还等什么呢？进去开砸吧~', // 分享描述
 			link: 'http://m.meilala.net/beategg/index.php', // 分享链接
 			imgUrl: 'http://m.meilala.net/beategg/images/egg_atv.jpg', // 分享图标
 			type: 'link', // 分享类型,music、video或link，不填默认为link
 			dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
 			success: function () {
 			},
 			cancel: function () { 
 				// 用户取消分享后执行的回调函数
 			}
 		});
 		
 		wx.onMenuShareQQ({
 			title: '美啦啦砸现金蛋活动，最高1000元现金', // 分享标题
 			desc: '美啦啦不玩礼品，美啦啦只玩现金！蛋不虚砸，每次必中！最高1000元现金，还等什么呢？进去开砸吧~', // 分享描述
 			link: 'http://m.meilala.net/beategg/index.php', // 分享链接
 			imgUrl: 'http://m.meilala.net/beategg/images/egg_atv.jpg', // 分享图标
 			type: 'link', // 分享类型,music、video或link，不填默认为link
 			dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
 			success: function () {
 			},
 			cancel: function () { 
 			}
 		});
 		wx.onMenuShareWeibo({
 			title: '美啦啦砸现金蛋活动，最高1000元现金', // 分享标题
 			desc: '美啦啦不玩礼品，美啦啦只玩现金！蛋不虚砸，每次必中！最高1000元现金，还等什么呢？进去开砸吧~', // 分享描述
 			link: 'http://m.meilala.net/beategg/index.php', // 分享链接
 			imgUrl: 'http://m.meilala.net/beategg/images/egg_atv.jpg', // 分享图标
 			type: 'link', // 分享类型,music、video或link，不填默认为link
 			dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
 			success: function () {
 			},
 			cancel: function () { 
 			}
 		});
 		
 	  });
</script>
</html>
