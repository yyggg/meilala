<?php 
	include_once '../inc/common.php';
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	
	$sql = "SELECT * FROM m_lushi WHERE id = '$id' LIMIT 1";
	$sth = $db->prepare($sql);
	$sth->execute();
	$data = $sth->fetch(PDO::FETCH_ASSOC);
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
	<div class="header_back"><a href="/know/lvshi.php"></a></div>
    <h2><?php echo $data['name'];?></h2>
</header>
<!--header end-->

<!--content-->
<div class="content">
	<div class="ls_bg">
    	<div class="ls_tx">
        	<div class="ls_tximg"><img src="<?php echo IMG_PATH . $data['photo'];?>" width="100%"/></div>
            <div class="ls_txcen">
                <p class="ls_textbt02"><?php echo $data['name'];?></p>
                <p><?php echo $data['suoshu'];?></p>
            </div>
        </div>

        <div class="lsxq_text">
            <p class="ls_texthff"><?php echo $data['info'];?></p>
        </div>
        
        <div class="lsxq_text">
        	<div class="ls_dianhua" style="background:url(../images/lsxq_07.jpg) no-repeat;"></div>
            <div class="ls_haoma">
            	<p>服务电话</p>
            	<p class="tshm"><em>4008-728-700</em></p>
            </div>
        </div>
        <Div class="cl"></Div>
    </div>
</div>
<!--content end-->

<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->




</body>
</html>
