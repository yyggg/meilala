<?php
	require_once '../inc/common.php';
	$time = time();
	$aid = isset($_GET['aid']) ? (int) $_GET['aid'] : 0;
	if(!$aid)
	{
		header("Location:activity.php");
		exit;	
	} 
	
	$sql = "SELECT aid,name,aimg,stime,etime,quota,enroll,contens FROM m_activites WHERE aid = :aid";
	$sth = $db->prepare($sql);
	$sth->bindParam(':aid', $aid);
	$sth->execute();
	$data = $sth->fetch(PDO::FETCH_ASSOC);
	
	//赞，收藏，评价
	$sql = "SELECT count(1) as count FROM m_house_zan WHERE `pid` = :gid AND `type` = '1' AND `acttype` = '0'";
	$sth = $db->prepare($sql);
	$sth->bindParam(':gid', $gid);
	$sth->execute();
	$house = $sth->rowCount();
	

	$sql = "SELECT count(1) as count FROM m_house_zan WHERE pid = :gid AND type = '1' AND acttype = '1'";
	$sth = $db->prepare($sql);
	$sth->bindParam(':gid', $gid);
	$sth->execute();
	$zan = $sth->rowCount();
	
	$sql = "SELECT count(1) as count FROM m_comment WHERE pid = :gid AND type = '1'";
	$sth = $db->prepare($sql);
	$sth->bindParam(':gid', $gid);
	$sth->execute();
	$comment = $sth->rowCount();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>活动详情</title>
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
	<div class="header_back"><a href="#"></a></div>
    <h2>活动详情</h2>
</header>
<!--header end-->
<!--content-->
<div class="content mb10">
	<div class="act_dtl_top  mb10">
        <button class="button1 on">活动介绍</button>
        <button class="button2 " onclick="window.location.href='comment_list.php?aid=<?php echo $data['aid'];?>'">活动评价</button>
    </div>
    
    <div class="act_dtl_ct">
    	<div class="act_img"><img src="<?php echo IMG_PATH . $data['aimg'];?>" /></div>
        <?php if($data['stime'] > $time):?>
        <div class="ct">
        	<div class="title mt10"><?php echo $data['name'];?></div>
            <div class="time mt10 mb10">活动**年**月**日开启</div>
            <div class="consult"><button>活动还未开始</button></div>
            <div class="operate">
                <a href="java:" class="comment">评论<b><?php echo number_format($comment);?></b></a>
                <a href="java:" class="collect">收藏<b><?php echo number_format($house);?></b></a>
                <a href="java:" class="best">赞<b><?php echo number_format($zan);?></b></a>
            </div>
        </div>
        <?php elseif($data['stime'] < $time && $data['stime'] < $data['etime'] && $data['enroll'] < $data['quota']):?>
        <div class="ct">
        	<div class="title mt10 mb10"><?php echo $data['name'];?></div>
            <div class="time_ing mb10">活动结束<b>23</b>时<b>23</b>分<b>23</b>秒</div>
            <div class="apply mb10">活动已报名<b><?php echo number_format($data['enroll']);?></b>名，还有<b><?php echo number_format($data['quota']-$data['enroll']);?></b>名</div>
            <div class="consult consult_ing"><button onclick="window.location.href='activity_apply.php?aid=<?php echo $data['aid'];?>'">立即报名</button></div>
            <div class="operate">
                <a href="java:" class="comment">评论<b><?php echo number_format($comment['count']);?></b></a>
                <a href="java:" class="collect">收藏<b><?php echo number_format($house['count']);?></b></a>
                <a href="java:" class="best">赞<b><?php echo number_format($zan['count']);?></b></a>
            </div>
        </div>
        <?php elseif($data['etime'] < $time || $data['enroll'] >= $data['quota']):?>
        <div class="ct">
        	<div class="title mt10 mb10"><?php echo $data['name'];?></div>
            <!--<div class="time mt10 mb10">活动**年**月**日开启</div>-->
            <div class="consult"><button onclick="alert('活动已结束')">活动已结束</button></div>
            <div class="operate">
                <a href="java:" class="comment">评论<b><?php echo number_format($comment['count']);?></b></a>
                <a href="java:" class="collect">收藏<b><?php echo number_format($house['count']);?></b></a>
                <a href="java:" class="best">赞<b><?php echo number_format($zan['count']);?></b></a>
            </div>
        </div>
        <?php endif;?>
    </div>
    <div class="act_dtl_ct border mt10">
    	<?php $contens = explode('{|}', $data['contens']);?>
		<?php foreach($contens as $v):;?>
			<?php $arr = explode('{-}', $v);?>
            <?php if($arr[0]):?>
                <div class="title main_color"><?php echo $arr[0];?></div>
            <?php endif;?>
            <div class="act_ct"><?php echo $arr[1];?></div>
        <?php endforeach;?>
    </div>
    
</div>

<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
</body>
</html>
