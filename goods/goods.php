<?php
	require_once '../inc/common.php';
	$user = getuser('province');
	$typeid = isset($_GET['typeid']) ? (int)$_GET['typeid'] : 0;
	//$province = !empty($_GET['ct']) ? $_GET['ct'] : $user['province']; //不自动定位，暂时不要的功能
	$province = !empty($_GET['ct']) ? $_GET['ct'] : '全国';
	
	$where = '';
	$pageSize = 5;
	if($typeid)
	{
		$where .= " WHERE g.typeid = '$typeid'";	
	}
	
	if($province && $typeid)
	{
		if($province != '全国') 
		{
			$where .= " AND m.city = '$province' ";	
		}
	}
	
	if(!$typeid && $province)
	{
		if($province != '全国') $where .= " WHERE m.city = '$province' ";
	}

	$sql = "SELECT count(1) as count FROM m_goods";
	$sth = $db->prepare($sql);
	$sth->execute();
	$pageTotal = ceil ($sth->fetchColumn()/$pageSize);
	
	if(isset($_GET['ajax-goods']))
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  //获取请求的页数 
		$start = ($page-1)*$pageSize; 	
		$sql = "SELECT g.gid,g.name,g.market_price,g.price,g.gimg,m.uname 
				FROM m_goods as g LEFT JOIN dede_member as m
				ON g.pmid = m.mid  
				$where ORDER BY g.gid DESC LIMIT $start, $pageSize";

		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		
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
<?php
	
	if(empty($_SESSION['user_location']) && empty($_GET['ct']))
	{
		include('../plus/my_location.php');
	}
	
	if(!empty($_GET['ct'])) $_SESSION['user_location'] = $_GET['ct'];
	
	//$user['province'] = !empty($_SESSION['user_location']) ? $_SESSION['user_location'] : $user['province'];//不自动定位，暂时不要的功能
	$user['province'] = !empty($_SESSION['user_location']) ? $_SESSION['user_location'] : '全国';
?>
<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="/"></a></div>
    <h2>项目列表</h2>
    <div class="header_right" id="city_2"><?php echo $user['province']; ?></div>
</header>
<!--header end-->
<!--content-->
<div class="content">
	<div class="item_top">
        <button class="button1" onclick="window.location.href='/goods/goods.php?typid=0&ct=全国'">全部</button>
        <button class="button2" onclick="window.location.href='cat_search.php?ct=<?php echo $user['province'];?>'">分类</button>
    </div>
    <div class="item_ct">
    	<!--<?php foreach($data as $v):;?>
        <a href="goods_detail.php?gid=<?php echo $v['gid'];?>">
    	<div class="item_li">
        	<div class="item_li_border"></div>
        	<div class="li_img" style="background-image:url(<?php echo IMG_PATH.$v['gimg'];?>); "></div>
            <div class="title"><?php echo $v['name'];?></div>
            <div class="comment"><b class="p1">&yen;<?php echo number_format($v['market_price']);?></b><b class="p2">&yen;<?php echo number_format($v['price']);?></b><button>查看详情</button></div>
        </div>
        </a>
        <?php endforeach;?> -->
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
		'contentPage': 'goods.php?ajax-goods&typeid=<?php echo $typeid;?>&ct=<?php echo $province;?>', // the url you are fetching the results
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
					var html = '<a href="goods_detail.php?gid='+v.gid+'&typeid=<?php echo $typeid;?>&ct=<?php echo $province;?>">'+
						'<div class="item_li">'+
							'<div class="item_li_border"></div>'+
							'<div class="li_img" style="background-image:url(<?php echo IMG_PATH;?>'+v.gimg+'); "></div>'+
							'<div class="title">'+v.name+'</div>'+
							'<div class="comment"><b class="p1">&yen;'+commafy(v.market_price)+'</b><b class="p2">&yen;'+commafy(v.price)+'</b><button>查看详情</button></div>'+
							'<div class="hospital">'+v.uname+'</div>'+
						'</div>'+
						'</a>';
						
					$('.item_ct').append(html);	
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

function  commafy(num){ 
   num  =  num+""; 
   var  re=/(-?\d+)(\d{3})/ 
   while(re.test(num)){ 
			   num=num.replace(re,"$1,$2") 
   } 
   return  num; 
} 
$('#city_2').click(function () {
	// body...
	window.location="../plus/city.php";
}); 
</script>
</body>
</html>
