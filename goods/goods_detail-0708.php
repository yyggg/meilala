<?php
	error_reporting(E_ALL & ~E_NOTICE);
	require_once '../inc/common.php';    
	$gid = isset($_GET['gid']) ? (int)$_GET['gid'] : 0;
	$typeid = isset($_GET['typeid']) ? (int)$_GET['typeid'] : 0;
	$province = isset($_GET['ct'])&&$_GET['ct'] ? $_GET['ct'] : '';
	$user = getUser('mid');
	if(!$gid) {
		header("Location:goods.php");
		exit;
	}
	
	$sql = "SELECT gid,name,market_price,price,gimg,doctor_hpt,parameter,contens,zan,sc,pl FROM m_goods WHERE gid = '$gid'";
	$sth = $db->prepare($sql);
	$sth->execute();
	$data = $sth->fetch(PDO::FETCH_ASSOC);
	
	$is_best = 0; //是否已赞
	$is_collect = 0; //是否已收藏
	$sql = "SELECT acttype FROM m_house_zan WHERE pid = '$gid' AND type = 0 AND mid = '$user[mid]'";
	$sth = $db->prepare($sql);
	$sth->execute();
	while($row = $sth->fetch(PDO::FETCH_ASSOC))
	{
		if($row['acttype'] == 1) $is_best = 1;
		if($row['acttype'] == 0) $is_collect = 1;
	}
	
	$ckData = $data;
	unset($ckData['doctor_hpt']);
	unset($ckData['parameter']);
	unset($ckData['contens']);
	$cookieApi = new HistoryApi();
	$cookieApi->history_sign = 'g_' . $data['gid'];
	$cookieApi->add_history($ckData);
	
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
	<div class="header_back"><a href="/goods/goods.php?typeid=<?php echo $typeid;?>&ct=<?php echo $province;?>"></a></div>
    <h2>项目详情</h2>
</header>
<!--header end-->
<!--content-->
<div class="content mb10">
	<?php include ('../template/item_detail_top.php');?>  
    
    <?php if($_GET['type']==1 || empty($_GET['type'])):?>  
    <div class="detail" id="detail1">
    	<div class="big_img"><img src="<?php echo IMG_PATH . $data['gimg'];?>" /></div>
        <div class="cl"></div>
        <div class="detail_ct pb10 mb10">
        	<div class="title mt10"><?php echo $data['name'];?></div>
            <div class="price"><b class="p1">原价：&yen;<?php echo number_format($data['market_price']);?></b><b class="p2">美啦啦：<font>&yen;<?php echo number_format($data['price']);?></font></b></div>
            <div class="callus"><button onclick="window.location.href='/kf/'">咨询专家</button></div>
            <div class="operate">
            	
                <a href="goods_detail4.php?gid=<?php echo $gid?>" class="comment">评论<b><?php echo $data['pl'];?></b></a>
                <a href="javascript:my_operate(0,0,<?php echo $is_collect;?>,<?php echo $gid?>);" class="collect" id="0-0-<?php echo $gid;?>">收藏<b><?php echo $data['sc'];?></b></a>
                <a href="javascript:my_operate(1,0,<?php echo $is_best;?>,<?php echo $gid?>);" class="best" id="1-0-<?php echo $gid;?>">赞<b><?php echo $data['zan'];?></b></a>
            </div>
        </div>
    </div>
    <div class="detail" id="detail3">
        <div class="detail_ct detail3_ct mb10">
        	<div class="ct detail3_p">
            <table width="100%">
            	<?php $parameter = explode('{|}', $data['parameter']);?>
                <?php foreach($parameter as $v):;?>
                <?php $arr = explode('{-}', $v);?>
            	<tr><td class="k"><?php echo $arr[0];?></td><td class="v"><?php echo $arr[1];?></td></tr>
                <?php endforeach;?>
            </table>
            </div>
            <div class="cl"></div>
        </div>
        <?php $contens = explode('{|}', $data['contens']);?>
		<?php foreach($contens as $k=> $v):;?>
        <?php $arr = explode('{-}', $v);?>
        <div class="detail_ct mb10 pt10 detail_ct_bg">
        	<?php if($arr[0]):?>
            <div class="title pb10 bd_bottom main_color" >
				<?php echo $arr[0];?>
                <i id="jiantou<?='3'.$k?>"></i>
            </div>
            <div class="detail_title_onclick" onclick="click_detail(<?='3'.$k?>);"></div>
            <?php endif;?>
            <div id="detail<?='3'.$k?>" class="mb10 mt10">
            	<?php echo $arr[1];?>
            </div>
        </div>
        <?php endforeach;?>      
    </div>    
    <div class="detail" id="detail2">
        <?php $doctor_hpt = explode('{|}', $data['doctor_hpt']);?>
        
        <?php foreach($doctor_hpt as $k=> $v):;?>
			<?php $arr = explode('{-}', $v);?>
            <div class="detail_ct mb10 pt10 detail_ct_bg">
                <?php if($arr[0]):;?>
                <div class="title  bd_bottom main_color pb10" ><?php echo $arr[0];?><i id="jiantou<?='2'.$k?>"></i></div>
                <div class="detail_title_onclick" onclick="click_detail(<?='2'.$k?>);"></div>
                <?php endif;?>
                <div id="detail<?='2'.$k?>">
                <div class="ct mb10 mt10">
                    <?php echo $arr[1];?>
                </div>
                </div>
            </div>
      <?php endforeach;?> 
    </div>
    
    <?php elseif($_GET['type']==2):?> 
    <div class="detail" id="detail2">
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
    
    <?php elseif($_GET['type']==3):?> 
    <div class="detail" id="detail3">
    	<div class="big_img"><img src="<?php echo IMG_PATH . $data['gimg'];?>" /></div>
        <div class="cl"></div>
        <div class="detail_ct detail3_ct mb10">
        	<div class="ct detail3_p">
            <table width="100%">
            	<?php $parameter = explode('{|}', $data['parameter']);?>
                <?php foreach($parameter as $v):;?>
                <?php $arr = explode('{-}', $v);?>
            	<tr><td class="k"><?php echo $arr[0];?></td><td class="v"><?php echo $arr[1];?></td></tr>
                <?php endforeach;?>
            </table>
            </div>
            <div class="cl"></div>
        </div>
        <?php $contens = explode('{|}', $data['contens']);?>
		<?php foreach($contens as $k=> $v):;?>
        <?php $arr = explode('{-}', $v);?>
        <div class="detail_ct mb10 pt10 detail_ct_bg">
        	<?php if($arr[0]):?>
            <div class="title pb10 bd_bottom main_color" >
				<?php echo $arr[0];?>
                <i id="jiantou<?=$k?>"></i>
            </div>
            <div class="detail_title_onclick" onclick="click_detail(<?=$k?>);"></div>
            <?php endif;?>
            <div id="detail<?=$k?>" class="mb10 mt10">
            	<?php echo $arr[1];?>
            </div>
        </div>
        <?php endforeach;?>      
    </div>
    
    
    
    <?php else :?>
    <div class="detail" id="detail1">
    	<div class="big_img"><img src="<?php echo IMG_PATH . $data['gimg'];?>" /></div>
        <div class="cl"></div>
        <div class="detail_ct pb10 mb10">
        	<div class="title mt10"><?php echo $data['name'];?></div>
            <div class="price"><b class="p1">原价：&yen;<?php echo number_format($data['market_price']);?></b><b class="p2">美啦啦：<font>&yen;<?php echo number_format($data['price']);?></font></b></div>
            <div class="callus"><button onclick="window.location.href='/kf/'">咨询专家</button></div>
            <div class="operate">
            	
                <a href="goods_detail4.php?gid=<?php echo $gid?>" class="comment">评论<b><?php echo number_format($comment);?></b></a>
                <a href="javascript:my_operate(0,0,<?php echo $is_collect;?>,<?php echo $gid?>);" class="collect" id="0-0-<?php echo $gid;?>">收藏<b><?php echo number_format($house);?></b></a>
                <a href="javascript:my_operate(1,0,<?php echo $is_best;?>,<?php echo $gid?>);" class="best" id="1-0-<?php echo $gid;?>">赞<b><?php echo number_format($zan);?></b></a>
            </div>
        </div>
    </div>
    <div class="detail" id="detail2">
        <?php $doctor_hpt = explode('{|}', $data['doctor_hpt']);?>
        
        <?php foreach($doctor_hpt as $k=> $v):;?>
			<?php $arr = explode('{-}', $v);?>
            <div class="detail_ct mb10 pt10 detail_ct_bg">
                <?php if($arr[0]):;?>
                <div class="title  bd_bottom main_color pb10" ><?php echo $arr[0];?><i id="jiantou<?=$k?>"></i></div>
                <div class="detail_title_onclick" onclick="click_detail(<?='2'.$k?>);"></div>
                <?php endif;?>
                <div id="detail<?='2'.$k?>">
                <div class="ct mb10 mt10">
                    <?php echo $arr[1];?>
                </div>
                </div>
            </div>
      <?php endforeach;?> 
    </div>
    <div class="detail" id="detail3">
        <div class="detail_ct detail3_ct mb10">
        	<div class="ct detail3_p">
            	<?php $parameter = explode('{|}', $data['parameter']);?>
                <?php foreach($parameter as $v):;?>
                <?php $arr = explode('{-}', $v);?>
            	<p><span class="k"><?php echo $arr[0];?></span><span class="v"><?php echo $arr[1];?></span></p>
                <?php endforeach;?>
            </div>
            <div class="cl"></div>
        </div>
        <?php $contens = explode('{|}', $data['contens']);?>
		<?php foreach($contens as $k=> $v):;?>
        <?php $arr = explode('{-}', $v);?>
        <div class="detail_ct mb10 pt10 detail_ct_bg">
        	<?php if($arr[0]):?>
            <div class="title pb10 bd_bottom main_color" >
				<?php echo $arr[0];?>
                <i id="jiantou<?=$k?>"></i>
            </div>
            <div class="detail_title_onclick" onclick="click_detail(<?='3'.$k?>);"></div>
            <?php endif;?>
            <div id="detail<?='3'.$k?>" class="mb10 mt10">
            	<?php echo $arr[1];?>
            </div>
        </div>
        <?php endforeach;?>      
    </div>      
    <?php endif;?>
    
    
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
<script type="text/javascript"> 
window.onscroll = function(){ 
	var h=$(".header").height();
	var h1=$(".detail_top").height();
    var t = document.documentElement.scrollTop || document.body.scrollTop;  
	if(t<=h){
		$('.detail_top').css({"position":"relative","margin":"0.1rem"});
	}
    else { 
		$('.detail_top').css({"position":"fixed","width":"100%","top":"0px","max-width":"640px","margin-top":"0px","margin-left":"auto","margin-right":"auto"});
    }
} 

</script> 
</body>
</html>
