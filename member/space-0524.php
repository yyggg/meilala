<?php 
	require_once '../inc/common.php';
	$user = getuser('jifen');
	//我的积分
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> 个人中心</title>
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
	<div class="header_back"><a href="/"></a></div>
    <h2>个人中心</h2>
    <div class="header_right"></div>
</header>
<!--header end-->
<!--content-->
<div class="content space">
	<ul>
		<li onclick="window.location.href='/member/person.php'" class="space_li">
        	<i class="icon icon1"></i><h3>个人资料</h3><i class="go"></i>
        </li>
		<li onclick="window.location.href='/member/my_house.php'" class="space_li">
        	<i class="icon icon2"></i><h3>我的收藏</h3><i class="go"></i>
        </li>
		<li onclick="window.location.href='#'" class="space_li">
        	<i class="icon icon3"></i><h3>顾问推荐<!--<span class="space_num">8</span>--></h3><i class="go"></i>
        </li>        
 		<li onclick="window.location.href='/member/my_point.php'" class="space_li">
        	<i class="icon icon4"></i><h3>我的积分<span class="space_num"><?php echo $user['jifen'];?></span></h3><i class="go"></i>
        </li>       
	</ul>
    <ul>
		<li onclick="window.location.href='#'" class="space_li">
        	<i class="icon icon5"></i><h3>抽奖活动</h3><i class="go"></i>
        </li>
		<li onclick="window.location.href='#'" class="space_li">
        	<i class="icon icon6"></i><h3>我的评论</h3><i class="go"></i>
        </li>
		<li onclick="window.location.href='/member/my_activites.php'" class="space_li">
        	<i class="icon icon7"></i><h3>我的活动</h3><i class="go"></i>
        </li>        
	</ul>
    <ul>
		<li onclick="window.location.href='/member/history.php'" class="space_li">
        	<i class="icon icon8"></i><h3>浏览记录</h3><i class="go"></i>
        </li>
		<li onclick="window.location.href='#'" class="space_li">
        	<i class="icon icon9"></i><h3>咨询记录</h3><i class="go"></i>
        </li>
		<li onclick="window.location.href='#'" class="space_li">
        	<i class="icon icon10"></i><h3>邀请好友</h3><i class="go"></i>
        </li>        
	</ul>    
    
</div>
<!--content end-->

<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->

</body>
</html>
