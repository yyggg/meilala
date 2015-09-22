<?php
	require_once '../inc/common.php';
	$user = getuser('mid');
	
	$sql = "SELECT point,income,remark,gtime FROM m_jifen_logs WHERE mid = '{$user["mid"]}' ORDER BY gtime DESC LIMIT 4";
	$res = $db->query($sql);
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$data = $res->fetchAll();
	
	if(isset($_GET['ajax_point']))
	{
		$page = intval($_GET['page']);  //获取请求的页数 
		$start = ($page-1)*4; 
		$res = $db->query("SELECT point,income,remark,gtime FROM m_jifen_logs WHERE mid = '{$user["mid"]}' ORDER BY gtime DESC LIMIT $start, 4");
		$res->setFetchMode(PDO::FETCH_ASSOC);
		$data = $res->fetchAll();
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
</head>

<body class="my_points">
<!--header-->
<header class="header">
	<div class="header_back"><a href="#"></a></div>  
    <h2>我的积分</h2>
    <div class="header_right"></div>
</header>
<!--header end-->
<!--content-->
<div class="content">
	<ul>
    	<?php foreach($data as $v):;?>
    	<li class="points_li">
        	<span class="title"><?php echo $v['remark'];?></span>
            <span class="time"><?php echo date('Y-m-d', $v['gtime']);?></span>
            <span class="li_right">
            	<?php
                	if($v['income'])
						echo '+';
					else
						echo '-';
					echo number_format($v['point']);
				?>
            </span>
        </li>
		<?php endforeach;?>
	</ul>
    <div class="nodata"></div>
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
<script>
	$(function(){ 
		var winH = $(window).height(); //页面可视区域高度 
		var i = 1; //设置当前页数
		var pageEnd = 1; //是否还有数据 
		$(window).scroll(function () {
			if(pageEnd) 
			{
				$(".nodata").show().html("拼命加载中。。。");	
			}
			else
			{
				$(".nodata").show().html("已经到底了。。。");
				return;
			}
			var pageH = $(document.body).height(); 
			var scrollT = $(window).scrollTop(); //滚动条top 
			var aa = (pageH-winH-scrollT)/winH; 
			if(aa<0.02 && pageEnd){
				 i++; 
				$.get("?ajax_point",{page:i},function(data){		console.log(data);
					if(data){ 
						var str = ""; 
						$.each(data,function(k,v){
							var income = '+';
							if(!v.income) income = '-';
							str += '<li class="points_li">' +
								'<span class="title">'+v.remark+'</span>' +
								'<span class="time">'+v.gtime+'</span>' +
								'<span class="li_right">'+income+v.point+'</span>' +
							'</li>';
		
							$(".content ul").append(str);
						});
						$(".nodata").hide(); 	 
					}
					else{
						pageEnd = 0;
						$(".nodata").show().html("已经到底了。。。"); 
						return; 
					} 
				}, 'json'); 
			} 
		}); 
	}); 
</script>
</body>
</html>
