<?php
	require_once '../inc/common.php';
	$user = getuser('mid,jifen,name,headimgurl');
	$user['headimgurl'] = empty($user['headimgurl'])?'../images/touxiang.png':$user['headimgurl'];
	$pageSize = 5;
	$sql = "SELECT count(1) as count FROM m_jifen_logs WHERE mid = '{$user["mid"]}'";
	$sth = $db->prepare($sql);
	$sth->execute();
	$pageTotal = ceil ($sth->fetchColumn()/$pageSize);
	
	if(isset($_GET['ajax_point']))
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  //获取请求的页数 
		$start = ($page-1)*$pageSize; 
		$sql = "SELECT point,income,remark,gtime FROM m_jifen_logs WHERE mid = '{$user["mid"]}' ORDER BY gtime DESC LIMIT $start, $pageSize";
		
		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($data as $k=>$v)
		{
			$data[$k]['gtime'] = date('Y-m-d', $v['gtime']);	
		}
		die(json_encode($data));  //转换为json数据输出
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>我的积分</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../css/common.css" type="text/css" media="all">
	<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
    <script src="../js/mobile.js" type="text/javascript"></script>
    <script src="../js/scrollpagination.js" type="text/javascript"></script>
</head>

<body class="my_points">
<!--header-->
<header class="header">
	<div class="header_back"><a href="#"></a></div>  
    <h2>我的积分</h2>
</header>
<!--header end-->
<!--content-->
<div class=" white clearfix">
	<section class="wd my_inform">
		<div class="my_header_img"><img src="<?php echo $user['headimgurl'];?>" alt="" /></div>
		<div class="my_name"><?php echo $user['name'];?></div>
		<div class="my_count">总积分：<?php echo number_format($user['jifen']);?></div>
	</section>
</div>
<div class="content white mt10">
	<section>
	<ul>
    	
	</ul>
    <div class="nodata"></div>
    </section>
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
	$('.content ul').scrollPagination({
		'contentPage': 'my_point.php?ajax_point', // the url you are fetching the results
		'contentData': {}, // these are the variables you can pass to the request, for example: children().size() to know which page you are
		'scrollTarget': $(window), // who gonna scroll? in this example, the full window
		'heightOffset': 10, // it gonna request when scroll is 10 pixels before the page ends
		'beforeLoad': function(){ // before load function, you can display a preloader div 预先加载
			//$('#loading').fadeIn();	
			
		}(),
		'afterLoad': function(elementsLoaded){ // after loading content, you can use this function to animate your new elements
			 //$('#loading').fadeOut();
			 var i = 0;
			 $(elementsLoaded).fadeInWithDelay();
			 if(this.page >= <?php echo $pageTotal;?>)
			 {
				$('#nomoreresults').html('没有更多了！').fadeIn();
				$('.content ul').stopScrollPagination();	 
			 }
		},
		
		'dataType': 'json',
		'loader': function (data){
			if(data.length)
			{
				$.each(data, function(k, v){
					if(v.income)
						var income = '+';
					else
						var income = '-';
					var html = '<li class="points_li">'+
								'<span class="title">'+v.remark+'</span>'+
								'<span class="time">'+v.gtime+'</span>'+
								'<span class="li_right">'+income+v.point +	'</span>'+
							    '</li>';
					$('.content ul').append(html);	
				});
			}
			else
			{
				$('#nomoreresults').html('没有更多了！').fadeIn();
			}
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





	
</script>
</body>
</html>
