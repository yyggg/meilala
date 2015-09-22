<?php
	require_once '../inc/db_config.php';
	$province = isset($_GET['ct'])&&$_GET['ct']  ? $_GET['ct'] : '';
	$data = array();
	$sql = "SELECT id,typename FROM `dede_arctype` WHERE reid = '275'";
	$res = $db->query($sql);
	$res->setFetchMode(PDO::FETCH_ASSOC);
	
	while ($row = $res->fetch())
	{
		$data[$row['id']] = $row['typename'];
	}
	//删除不属于项目的分类
	unset($data['386']); //网友体验
	unset($data['387']); //项目介绍
	unset($data['388']); //对比图案例
	unset($data['407']); //帮助中心
	unset($data['430']); //项目档案
	ksort($data);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>按分类查找</title>
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
	<div class="header_back"><a href="goods.php"></a></div>
    <h2>按分类查找</h2>
    
</header>
<!--header end-->
<!--content-->
<div class="content">
	<div class="item_cat">
      <ul>
      	<?php foreach($data as $k => $v):;?>	
          <li class="cat_li" onclick="window.location.href='goods.php?typeid=<?php echo $k;?>&ct=<?php echo $province;?>'">
              <i class="icon" style="background-image:url(../images/type_<?php echo $k;?>.png);"></i>
              <span class="cat_name"><?php echo $v;?></span>
              <i class="go"></i>
          </li>
         <?php endforeach;?>  
          
      </ul>
  </div>
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
</body>
</html>
