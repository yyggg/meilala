<?php
	require_once '../inc/common.php';
	$user = getuser('mid,headimgurl');
	
	$pageSize = 5;
	$sql = "SELECT count(1) as count FROM m_order WHERE mid = '{$user["mid"]}'";
	$sth = $db->prepare($sql);
	$sth->execute();
	$pageTotal = ceil ($sth->fetchColumn()/$pageSize);
	
	if(isset($_GET['ajax_order']))
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  //获取请求的页数 
		$start = ($page-1)*$pageSize; 
		$sql = "SELECT type,mid,pid,order_no,price,time,stat FROM m_order WHERE mid = '{$user["mid"]}' ORDER BY time DESC LIMIT $start, $pageSize";
		
		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($data as $k=>$v)
		{
			$data[$k]['time'] = date('Y-m-d H:i:s', $v['time']);
			$data[$k]['name'] =	get_activites_item( 'name',$v['pid']);
			$data[$k]['stat'] = $v['stat']==1?'<em class="stat1">支付成功</em>':'<em class="stat2">待支付</em>';
		}
		die(json_encode($data));  //转换为json数据输出
	}

	function get_activites_item($item='*',$aid)
	{
		global $db;
		$sql = "SELECT $item FROM m_activites WHERE aid = $aid ";		
		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return 	$data[$item];	
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>我的订单</title>
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
	<div class="header_back"><a href="space.php"></a></div>  
    <h2>我的订单</h2>
</header>
<!--header end-->
<!--content-->

<div class="content  mt10">
	<div>
	<ul>
    	
	</ul>
    <div class="nodata"></div>
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
	$('.content ul').scrollPagination({
		'contentPage': 'my_order.php?ajax_order', // the url you are fetching the results
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
				$('.item_ct').stopScrollPagination();	 
			 }
		},
		
		'dataType': 'json',
		'loader': function (data){
			if(data.length)
			{
				$.each(data, function(k, v){

					var html = '<li class="order_li mt10">'+
								'<span class="type ">活动支付订单</span>'+
								'<span class="time"><i class="left">下单日期：</i><i class="right">'+v.time+'</i></span>'+
								'<span class="name "><i class="left">活动名称：</i><i class="right ">'+v.name+'</i></span>'+
								'<span class="name "><i class="left">订单金额：</i><i class="right ">￥'+v.price+'</i></span>'+
								'<span class="stat"><i class="left">订单状态：</i><i class="right">'+v.stat +	'</i></span>'+
								'<span class="order_no"><i class="left">订单编号：</i><i class="right">'+v.order_no+'</i></span>'+
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
