<?php
require_once '../inc/common.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>知道</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/slide.css" type="text/css" media="all">
<script type="text/javascript" src="../js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="../js/jquery.touchSwipe.min.js"></script>
<style type="text/css">
</style>
<script type="text/javascript">
  $(document).ready(
	  function() {
		  var nowpage = 0;
		  var max_page=3;
		  //给最大的盒子增加事件监听
		  $(".container").swipe(
			  {
				  swipe:function(event, direction, distance, duration, fingerCount) {
					   if(direction == "up"){
						  nowpage = nowpage + 1;
					   }else if(direction == "down"){
						  nowpage = nowpage - 1;
					   }

					   if(nowpage > max_page){
						  nowpage = 0;
					   }

					   if(nowpage < 0){
						  nowpage = max_page;
					   }

					  $(".container").animate({"top":nowpage * -100 + "%"},300);

					  $(".page").eq(nowpage).addClass("cur").siblings().removeClass("cur");
				  }
			  }
		  );
	  }
  );
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>


<!--播放器广告s
<div id="focus" class="focus region">
  <div class="hd">
    <ul>
    </ul>
  </div>
  
  <div class="bd">
  <ul>
        <li><img src="../images/02.jpg"/></li>
        <li><img src="../images/04.jpg"/></li>
        <li><img src="../images/06.jpg"/></li>
   </ul>
  </div>
</div>
<script type="text/javascript">
TouchSlide({ 
	slideCell:"#focus",
	titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
	mainCell:".bd ul", 
	effect:"leftLoop", 
	autoPlay:true,//自动播放
	autoPage:true //自动分页
});
</script>
<!--播放器广告e-->

<body onmousewheel="return false;">
	<div class="container">
		<div class="page page0 cur">
        	<div class="know_img" style="background-image:url(../images/03.jpg);" ></div>
		</div>
		<div class="page page1">
			<div class="know_img" style="background-image:url(../images/05.jpg);" ></div>
		</div>
		<div class="page page2">
        	<div class="know_img" style="background-image:url(../images/07.jpg);" ></div>
        </div>
		<div class="page page3">
        	<div class="know_img" style="background-image:url(../images/10.jpg);" ></div>
        </div>        
	</div>

	<img  class="xiangxiatishi" src="../images/prompt.png" />



</body>
</html>
