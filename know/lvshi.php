<?php 
	include_once '../inc/common.php';
	$sql = "SELECT * FROM m_lushi";
	$sth = $db->prepare($sql);
	$sth->execute();
	$data = $sth->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>美啦啦律师</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/common2.css" type="text/css" media="all">
<link rel="stylesheet" href="../css/lvshi.css" type="text/css" media="all">
<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="../js/mobile.js" type="text/javascript"></script>
</head>

<body>

<!--header-->
<header class="header">
	<div class="header_back"><a href="/know/"></a></div>
    <h2>律师保障</h2>
</header>
<!--header end-->


<!--content-->
<div class="content">
<div class="ls_bg">
	<?php foreach ($data as $v):;?>
	<div class="ls_block" onclick="window.location.href='/know/lvshi_info.php?id=<?php echo $v['id'];?>'">
    	<div class="ls_img"><img src="<?php echo IMG_PATH . $v['photo'];?>" width="100%" /></div>
        <div class="ls_text">
            <p class="ls_textbt"><strong><?php echo $v['name'];?></strong></p>
            <p class="ls_texth"><?php echo $v['zhaiyao'];?><p>
            <p class="ls_textd"><?php echo $v['suoshu'];?></p>
        </div>
        <div class="cl"></div>
    </div>
    <?php endforeach;?>
</div>
<div class="cl"></div>
</div>
<!--content end-->

<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->




</body>
</html>
