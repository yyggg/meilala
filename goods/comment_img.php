<?php
require_once '../inc/common.php';
$cid = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;
$sql = "SELECT cid,imgs FROM m_comment WHERE cid = :cid";
$sth = $db->prepare($sql);
$sth->bindParam(':cid', $cid);
$sth->execute();
$data = $sth->fetch(PDO::FETCH_ASSOC);	
//print_r($data);
$imgs =  explode('|', $data['imgs']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>无标题文档</title>
<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../js/TouchSlide.js"></script>
<script src="http://siteapp.baidu.com/static/webappservice/uaredirect.js" type="text/javascript"></script>
<link href="../css/ectouch.css" rel="stylesheet" type="text/css" />
<style>
body{ background:#111; width:100%; height:100%;}
.slide_img{ overflow:hidden; vertical-align: middle !important; }
.slide_img img{ width:100% ;} 
.focus { width:100%; height:100%; position:absolute;}
.bd,.tempWrap,.tempWrap ul,.slide_img,.slide_img tr,.slide_img tr td		{width:100% ; height:100%; }

</style>
</head>

<body>
<!--播放器广告s-->
<div id="focus" class="focus region">
  <div class="hd">
    <ul>
    </ul>
  </div>
  
  <div class="bd">
  <ul>
  		<?php foreach($imgs as $vv):;?>
        <?php if(!empty($vv)) :?>
        <table align="center" class="slide_img"><tr><td><img src="<?php echo IMG_PATH. $vv;?>" /></td></tr></table>
        <?php endif; ?>
        <?php endforeach;?>
   </ul>
  </div>
</div>
<script type="text/javascript">
TouchSlide({ 
	slideCell:"#focus",
	titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
	mainCell:".bd ul", 
	effect:"leftLoop", 
	autoPlay:0,//自动播放
	autoPage:true //自动分页
});

h=$('#focus').height();
$('.slide_img').css({'height':h+'px',});

</script>
<!--播放器广告e-->


</body>
</html>
