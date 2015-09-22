<!-- 我的活动-->
<?php
	require_once '../inc/common.php';
	$time = time();
	$user = getUser('mid');
	if(isset($_REQUEST['ajax_enroll_logs']))
	{
		$page = intval($_GET['page']);  //获取请求的页数 
		$start = ($page-1)*4; 
		
		$sql = "SELECT e.eid,a.aid,a.name,a.aimg,a.stime,a.etime FROM m_atv_enroll as e
			LEFT JOIN m_activites as a ON e.aid = a.aid WHERE e.mid = '$user[mid]' ORDER BY e.eid DESC LIMIT $start,4";
		$res = $db->query($sql);
	
		while ($row = $res->fetch())
		{
			$data[$row['eid']] = $row;
		}
		
		die(json_encode($data));  //转换为json数据输出
	}
	else 
	{
		$sql = "SELECT e.eid,a.aid,a.name,a.aimg,a.stime,a.etime,a.enroll,a.quota FROM m_atv_enroll as e
			LEFT JOIN m_activites as a ON e.aid = a.aid WHERE e.mid = '$user[mid]' ORDER BY e.eid DESC LIMIT 4";
		$res = $db->query($sql);
		$res->setFetchMode(PDO::FETCH_ASSOC);
		$data = $res->fetchAll();
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>我的活动</title>
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
	<div class="header_back"><a href="#"></a></div>
    <h2>我的活动</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
    <div class="item_ct">
    	<?php foreach($data as $v):;?>
        <a href="../activites/activity_detail.php?aid=<?php echo $v['aid'];?>">
    	<div class="item_li act_li">
        	<div class="act_li_border"></div>
        	<div class="li_img" style="background-image:url(<?php echo IMG_PATH . $v['aimg'];?>); "></div>
            <div class="title"><?php echo $v['name'];?></div>
            <div class="comment">
            	<?php if($v['stime'] > $time):?>
            		<b class="p1">活动未开始</b>
                <?php elseif($v['stime'] < $time && $v['stime'] < $v['etime'] && $v['enroll'] < $v['quota']):?>
                	<b class="p1">活动进行中</b>
                <?php elseif($v['etime'] < $time || $v['enroll'] >= $v['quota']):?>
                	<b class="p1">活动已结束</b>
                <?php endif;?>
                <!--<b class="p2"><i>23</i>时<i>23</i>分<i>23</i>秒</b>-->
            </div>
            <b class="best"></b>
        </div>
        </a>
        <?php endforeach;?>
    	
    </div>

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
			if(pageEnd) $(".nodata").show().html("拼命加载中。。。");  
			var pageH = $(document.body).height(); 
			var scrollT = $(window).scrollTop(); //滚动条top 
			var aa = (pageH-winH-scrollT)/winH; 
			if(aa<0.02 && pageEnd){
				 i++; 
				$.getJSON("?ajax-goods",{page:i},function(data){		
					if(data){ 
						var str = ""; 
						$.each(data,function(index,array){
							
							str += '<a href="">'+
							'<div class="row-fluid margin-bootom20 show-grid box-radius5 box-shadow">'+
					            '<div class="span5 padding3">'+
					                '<img src="images/1.jpg">'+
					            '</div>'+
					            '<div class="span7">'+
					                '<div class="row-fluid good-name">'+
					                	'<div class="bottom-border">'+
					                    	array["name"]+
					                    '</div>'+
										
					                '</div>'+
					                '<div class="row-fluid r-bootom">'+
					                	'<div class="span6 good-price">'+
					                    	'<div><del>￥'+array["market_price"]+'</del></div>'+
					                        '<div>￥'+array["price"]+'</div>'+
					                    '</div>'+
					                    '<div class="span6">'+
					                    
					                    	'<div class="span10 good-btt">查看详情</div>'+
					                    '</div>'+
					                '</div>'+
					            '</div>'+
							'</div>'+
					        '</a>';
					        
							/* str = '<div class=single_item><div class=element_head>'; 
							str += '<div class=date>' + array["name"]+'</div>'; 
							str += '<div class=author>'+array["price"]+'</div>'; 
							str += '</div><div class=content>'+array["gid"]+'</div></div>';  */
							$("#container").append(str);
						});
						$(".nodata").hide(); 	 
					}
					else{
						pageEnd = 0;
						$(".nodata").show().html("已经到底了。。。"); 
						return; 
					} 
				}); 
			} 
		}); 
	}); 
</script>
</body>
</html>

