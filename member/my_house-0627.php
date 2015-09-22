<?php
	require_once '../inc/common.php';
	$user = getUser('mid');
	
	$time = TIME;
	$pageSize = 5;
	$type = isset($_REQUEST['type']) ? (int)$_REQUEST['type'] : 1;
	
	$sql = "SELECT count(1) as count FROM m_house_zan WHERE type = '$type' AND acttype = 0 AND mid = '$user[mid]'";
	$sth = $db->prepare($sql);
	$sth->execute();
	$pageTotal = ceil ($sth->fetchColumn()/$pageSize);
	
	if(isset($_REQUEST['ajax_house']))
	{
		$data = ['where' => [$type], 'data' => []];
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  //获取请求的页数 
		$start = ($page-1)*$pageSize;
		if($type == 0)
		{
			$sql = "SELECT h.hid,h.pid,h.addtime,g.gid,g.name,g.market_price,g.price,g.gimg 
					FROM m_house_zan as h LEFT JOIN m_goods as g ON h.pid = g.gid WHERE h.type = '$type' 
					AND h.acttype = 0 AND h.mid = '$user[mid]' ORDER BY h.hid DESC  LIMIT $start, $pageSize";
			
		}
		else if($type == 1)
		{
			$sql = "SELECT h.hid,h.pid,h.addtime,a.aid,a.name,a.aimg,a.stime,a.etime,a.enroll,a.quota 
				FROM m_house_zan as h LEFT JOIN m_activites as a ON h.pid = a.aid WHERE h.type = '$type' 
				AND h.acttype = 0 AND h.mid = '$user[mid]' ORDER BY h.hid DESC  LIMIT $start, $pageSize";
		}
		
		$sth = $db->prepare($sql);
		$sth->execute();
		$sth->setFetchMode(PDO::FETCH_ASSOC);
		$data['data'] = $sth->fetchAll();
		
		die(json_encode($data));  //转换为json数据输出
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>项目列表</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../css/common.css" type="text/css" media="all">
	<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
    <script src="../js/mobile.js" type="text/javascript"></script>
    <script src="../js/scrollpagination.js" type="text/javascript"></script>
</head>

<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="space.php"></a></div>
    <h2>我的收藏</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
	<div class="item_top my_collect_top">
    	<button class="button2" onclick="window.location.href='my_house.php?type=0'">收藏的项目</button>
        <button class="button1" onclick="window.location.href='my_house.php?type=1'">收藏的活动</button>
    </div>
    <div class="item_ct">
    	
    </div>
    
</div>
<div class="item-loading">
	<div id="nomoreresults"><img src="../images/loading.gif" /></div>
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
<script>
$(function(){
	
	$('.item_ct').scrollPagination({
		'contentPage': 'my_house.php?ajax_house&type=<?php echo $type;?>', // the url you are fetching the results
		'contentData': {}, // these are the variables you can pass to the request, for example: children().size() to know which page you are
		'scrollTarget': $(window), // who gonna scroll? in this example, the full window
		'heightOffset': 10, // it gonna request when scroll is 10 pixels before the page ends
		'beforeLoad': function(){ // before load function, you can display a preloader div 预先加载
			//if('<?php echo $pageTotal;?>' > 0)
				//$('#loading').fadeIn();	
			
		}(),
		'afterLoad': function(elementsLoaded){ // after loading content, you can use this function to animate your new elements
			 //$('#loading').fadeOut();
			 
			 var i = 0;
			 $(elementsLoaded).fadeInWithDelay();
			 if(this.page >= <?php echo $pageTotal;?>)
			 {
				$('#nomoreresults').html('没有更多了！').fadeIn();
				$('.item_ct').stopScrollPagination();	 
			 }
		},
		
		'dataType': 'json',
		'loader': function (data){
			var html = '';
			if(data.where[0] == 0)
			{
				if(data['data'].length)
				{
					$.each(data['data'], function(k, v){
						if(v.gid)
						{
							html += '<a href="../goods/goods_detail.php?gid='+v.gid+'">'+
								'<div class="item_li">'+
									'<div class="item_li_border"></div>'+
									'<div class="li_img" style="background-image:url(<?php echo IMG_PATH;?>'+v.gimg+'); "></div>'+
									'<div class="title">'+v.name+'</div>'+
									'<div class="comment">'+
										'<b class="p1">&yen;'+commafy(v.market_price)+'</b>'+
										'<b class="p2">&yen;'+commafy(v.price)+'</b>'+
										'<button>查看详情</button></div>'+
									
								'</div>'+
							'</a>';	
						}
					});	
				}
				else
				{
					$('#nomoreresults').html('没有更多了！').fadeIn();	
				}
			}
			else if(data.where[0] == 1)
			{
				if(data['data'].length)
				{
					var time = <?php echo $time;?>;
					var atvTips = '';
					$.each(data['data'], function(k, v){
						if(v.aid)
						{
							
							if(v.stime*1 > time) 
							{
								atvTips = '活动未开始';
							}	
							else if(v.stime*1 < time && v.etime*1 > time && v.enroll*1 < v.quota)
							{
								atvTips = '活动进行中';	
							}
							else if(v.etime*1 < time || v.enroll*1 >= v.quota)
							{
								atvTips = '活动已结束';
							}
								
							html += '<a href="../activites/activity_detail.php?aid='+v.aid+'">'+
								'<div class="item_li act_li">'+
									'<div class="act_li_border"></div>'+
									'<div class="li_img" style="background-image:url(<?php echo IMG_PATH;?>'+v.aimg+'); "></div>'+
									'<div class="title">'+v.name+'</div>'+
									'<div class="comment">'+
										atvTips+
									'</div>'+
									'<b class="best"></b>'+
								'</div>'+
							'</a>';	
						}
					});	
				}
				else
				{
					$('#nomoreresults').html('没有更多了！').fadeIn();	
				}
			}
			
			$('.item_ct').append(html);	
		}
	});
	
	// code for fade in element by element
	$.fn.fadeInWithDelay = function(){
		var delay = 0;
		return this.each(function(){
			$(this).delay(delay).animate({opacity:1}, 200);
			delay += 100;
		});
	};
		   
});

function  commafy(num){ 
   num  =  num+""; 
   var  re=/(-?\d+)(\d{3})/ 
   while(re.test(num)){ 
			   num=num.replace(re,"$1,$2") 
   } 
   return  num; 
}  
</script>
</body>
</html>