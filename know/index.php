<?php
require_once '../inc/common.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>知道</title>
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
    <h2>知道</h2>
</header>
<!--header end-->
<!--content-->
<div class="content space mb10">
    <ul>
		<li onclick="window.location.href='../know/about.html'" class="space_li know_li">
        	<i class="icon icon1"></i><h3>关于美啦啦</h3><i class="go"></i>
        </li>
		<li onclick="window.location.href='../know/about.html#fuwu'" class="space_li know_li">
        	<i class="icon icon2"></i><h3>服务</h3><i class="go"></i>
        </li>
		<li onclick="window.location.href='../know/about.html#baozhang'" class="space_li know_li">
        	<i class="icon icon3"></i><h3>保障<!--<span class="space_num">8</span>--></h3><i class="go"></i>
        </li>
        <li class="space_li" onclick="window.location.href='../know/lvshi.php'" class="space_li know_li">
        	<i class="icon icon1"></i><h3>律师保障<!--<span class="space_num">8</span>--></h3><i class="go"></i>
        </li> 
    </ul>           
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
</body>
</html>
