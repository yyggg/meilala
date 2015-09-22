<?php
require_once '../inc/common.php';
require_once "../weixin/wx_js_sdk/jssdk.php";

$appid = 'wx7a5ba916e3074fd7';
$appsecret = '8bbef8aaca02345c838c31adcdf864f1';
$openid = $_SESSION['wx_user']['openid'];

$jssdk = new JSSDK($appid, $appsecret);
$signPackage = $jssdk->GetSignPackage();

$uInfo = getSubscribe($appid, $appsecret, $openid);
if($uInfo['subscribe'] == 0) header("Location: index.php");

$beat = 0;
$sql = "SELECT num,used FROM m_beat_egg_restrict WHERE `openid` = '$openid'";
$sth = $db->prepare($sql);
$sth->execute();
$beatInfo = $sth->fetch(PDO::FETCH_ASSOC);
if($beatInfo)
{
	$beat = $beatInfo['num'] - $beatInfo['used'];
}

/* 砸蛋动态 */
$sql = "SELECT username,headimgurl,money,addtime FROM m_beat_egg_logs ORDER BY id DESC LIMIT 8";
$sth = $db->prepare($sql);
$sth->execute();
$lists = $sth->fetchAll(PDO::FETCH_ASSOC);

function getSubscribe($appid, $appsecret, $openid)
{
	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
	$accessToken = https_request($url);
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
	<script src="./js/alert.js" type="text/javascript"></script>
	<script src="./js/mobile.js" type="text/javascript"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</head>
<body>
<section class="st1"></section>	
	<section class="st3"><img src="./images/egg_img2.png" alt="" /></section>
	
	<!--<section class="st2">
		<div class="st1_ct1">您还有 <font color="#f90"><?php echo $beat;?></font> 次砸蛋机会~</div>
		<div class="st1_ct2">分享朋友圈参与可获得砸蛋次数</div>
		<div class="st1_ct3"><button onclick="share();">分享朋友圈获得砸蛋机会</button></div>
		<div class="st1_ct3"><button onclick="window.location.href='index.php'">去砸蛋</button></div>
		<div class="st1_ct4"></div>
	</section>-->
	<section class="st5 st12 atv-end">
						<div class="st5_ct1 st12_ct1">
							本次砸现金蛋活动已结束了<br>
							敬请关注下次活动，别走开！随时开始哟~
						</div>
					</section>
	
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
				3、分享朋友圈与砸现金蛋活动，可获取一次砸蛋机会。</p><p>
				4、每人砸蛋机会共2次，数量有限，砸完即止。</p><p>
				5、本次活动最终解释权归美啦啦所有。</p>
			</div>
		</div>		
	</section>	
	

	

<script type="text/javascript">
	var w=$(window).width();
    $('.st1').css({'height':w/640*233+'px'});

    var openid = '<?php echo $openid;?>';
    
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
		
			$.get('ajax.php?add_num&openid='+openid, function(flag){
			
				if(flag == 1) $('.st1_ct1 font').text($('.st1_ct1 font').text()*1+1);
			 },'json');	
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
