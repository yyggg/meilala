<?php
	require_once '../inc/common.php';
	$where = '';
	$status = isset($_GET['status']) ? (int)$_GET['status'] : 'all';
	if($status != 'all')
	{
		$time = time();
		if($status == 'no')
		{
			$where = " stime > '$time' ";		
		}
		elseif($status == 'ing')
		{
			$where = " stime < '$time' AND stime < etime AND enroll < quota ";		
		}
		elseif($status == 'end')
		{
			$where = " (etime < '$time') or (enroll >= quota) ";		
		}
	}
	
	$res = $db->query("SELECT aid,name,aimg FROM m_activites $where ORDER BY aid DESC LIMIT 4");
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$data = $res->fetchAll();
	
	if(isset($_GET['ajax-goods']))
	{
		$page = intval($_GET['page']);  //获取请求的页数 
		$start = ($page-1)*4; 	
		$res = $db->query("SELECT gid,name,market_price,price,gimg FROM m_activites ORDER BY gid DESC LIMIT $start, 4");
		$res->setFetchMode(PDO::FETCH_ASSOC);
		$data = $res->fetchAll();
		
		die(json_encode($data));  //转换为json数据输出
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>活动列表</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/common2.css" type="text/css" media="all">
	<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
    <script src="../js/mobile.js" type="text/javascript"></script>
</head>

<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="/"></a></div>
    <h2>活动列表</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
	<div class="activity_top mt10 mb10">
        <button class="button1" onclick="window.location.href='activity.php?status=all">全部</button>
        <button class="button2" onclick="window.location.href='activity.php?status=no'">未开始</button>
        <button class="button3" onclick="window.location.href='activity.php?status=ing'">进行中</button>
        <button class="button4" onclick="window.location.href='activity.php?status=end'">已结束</button>
    </div>
    <div class="item_ct">
    	<?php foreach($data as $v):;?>
        <a href="activity_detail.php?aid=<?php echo $v['aid'];?>">
    	<div class="item_li act_li" onclick="">
        	<div class="act_li_border white"></div>
            <b class="best"></b>
        	<div class="li_img" style="background-image:url(<?php echo IMG_PATH . $v['aimg'];?>); "></div>
            <div class="title"><?php echo $v['name'];?></div>
            <div class="comment"><b class="p1">活动结束</b><b class="p2"><i>23</i>时<i>23</i>分<i>23</i>秒</b></div>
        </div>
        </a>
        <?php endforeach;?>
    </div>

</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
</body>
</html>
