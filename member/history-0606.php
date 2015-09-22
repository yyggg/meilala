<?php
	require_once '../inc/common.php';
	$cookieApi = new HistoryApi();
	$data = ['g' => [],'a' => []];
	foreach((array)$_COOKIE as $k => $v)
	{
		$arr = explode('_', $k);
		if($arr[0] == 'g')
		{
			$data['g'][] = unserialize($cookieApi->gzdecode(stripslashes($v)));	
		}
		elseif($arr[0] == 'a')
		{
			$data['a'][] = unserialize($cookieApi->gzdecode(stripslashes($v)));	;		
		}
	}	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>我的浏览记录</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/common.css" type="text/css" media="all">
	<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
    <script src="../js/mobile.js" type="text/javascript"></script>
</head>

<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="/member/space.php"></a></div>
    <h2>浏览记录</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
	<ul>
    	<?php foreach($data['g'] as $v):;?>
            <li class="history_li" onclick="window.location.href='/goods/goods_detail.php?gid=<?php echo $v['gid'];?>'">
                <span class="title"><?php echo $v['name'];?></span>
                <span class="markey_price">原价<b>￥<?php echo number_format($v['market_price']);?></b> </span>
                <span class="shop_price">美啦啦<b>￥<?php echo number_format($v['price']);?></b> </span>
                <span class="go"></span>
            </li>
    	<?php endforeach;?>
    </ul>
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
</body>
</html>
