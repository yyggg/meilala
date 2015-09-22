<?php
	require_once '../inc/common.php';
	$gid = isset($_GET['gid']) ? (int)$_GET['gid'] : 0;
	if(!$gid) header("location:goods.php");
	
	$sql = "SELECT gid,gimg,doctor_hpt FROM m_goods WHERE gid = :gid";
	$sth = $db->prepare($sql);
	$sth->bindParam(':gid', $gid);
	$sth->execute();
	$data = $sth->fetch(PDO::FETCH_ASSOC);	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>项目详情</title>
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
	<div class="header_back"><a href="/goods/goods.php"></a></div>
    <h2>项目详情</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
	<?php include ('../template/item_detail_top.php');?> 
    
    <div class="detail">
    	<div class="big_img"><img src="<?php echo IMG_PATH . $data['gimg'];?>" /></div>
        <div class="cl"></div>
        <?php $doctor_hpt = explode('{|}', $data['doctor_hpt']);?>
        
        <?php foreach($doctor_hpt as $k=> $v):;?>
			<?php $arr = explode('{-}', $v);?>
            <div class="detail_ct mb10 pt10 detail_ct_bg">
                <?php if($arr[0]):;?>
                
                <div class="title  bd_bottom main_color pb10" ><?php echo $arr[0];?><i id="jiantou<?=$k?>"></i></div>
                <div class="detail_title_onclick" onclick="click_detail(<?=$k?>);"></div>
                <?php endif;?>
                <div id="detail<?=$k?>">
                <div class="ct mb10 mt10">
                    <?php echo $arr[1];?>
                </div>
                </div>
            </div>
      <?php endforeach;?> 
        
    </div>
    
</div>
<!--content end-->
<?php include_once ('../template/footer.php');?>
<!--footer-->

<!--footer end-->
</body>
</html>
