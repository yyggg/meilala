
<?php
	require_once '../inc/common.php';
	$time = TIME;
	$pageSize = 5;
	$user = getUser('mid');
	
	$sql = "SELECT count(1) as count FROM m_atv_enroll WHERE mid = '$user[mid]'";
	$sth = $db->prepare($sql);
	$sth->execute();
	$pageTotal = ceil ($sth->fetchColumn()/$pageSize);
	
	
	if(isset($_REQUEST['ajax-goods']))
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  //获取请求的页数 
		$start = ($page-1)*$pageSize; 	
		
		$sql = "SELECT e.eid,a.aid,a.name,a.aimg,a.stime,a.etime,a.enroll,a.quota FROM m_atv_enroll as e
			LEFT JOIN m_activites as a ON e.aid = a.aid WHERE e.mid = '$user[mid]' ORDER BY e.eid DESC LIMIT $start,4";
	
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
<title>我的活动</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../css/common2.css" type="text/css" media="all">
	<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
    <script src="../js/mobile.js" type="text/javascript"></script>
    <script src="../js/scrollpagination.js" type="text/javascript"></script>
</head>

<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="space.php"></a></div>
    <h2>我的活动</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
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
		'contentPage': 'my_activites.php?ajax-goods', // the url you are fetching the results
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
					if(v.aid)
					{
						var atvTips = ''; //文字提示状态
						var cdTime = 0; //倒计时
						var cdTime2 = 0; //如果开始时间到转为结束倒计时用
						var time = <?php echo $time;?>;
						
						if(v.stime*1 > time) 
						{
							cdTime = (v.stime*1 - time);
							
							atvTips = '开始倒计时';
							cdTime2 = (v.etime*1 - v.stime*1) - (time-v.stime*1);
						}	
						else if(v.stime*1 < time && v.etime*1 > time && v.enroll*1 < v.quota)
						{
							cdTime = (v.etime*1 - v.stime*1) - (time-v.stime*1);
							
							atvTips = '结束倒计时';	
						}
						else if(v.etime*1 < time || v.enroll*1 >= v.quota)
						{
							atvTips = '活动已结束';
						}
						var html = '<div class="item_li act_li" id="atv_'+v.aid+'" style="opacity:0;-moz-opacity: 0;filter: alpha(opacity=0);">' +
								'<a href="/activites/activity_detail.php?aid='+v.aid+'"><div class="act_li_border white"></div>' +
								'<div class="li_img" style="background-image:url(<?php echo IMG_PATH;?>'+v.aimg+'); "></div>' +
								'<div class="title">'+ v.name +'</div>' +
								'<div class="comment"><b class="p1">'+atvTips+'</b><b class="p2" rel="'+cdTime+'">';
						if(cdTime) html += '<i>00</i>天<i>23</i>时<i>59</i>分<i>59</i>秒';
						html += '</b></div><b class="best"></b></a></div>';
						
						$('.item_ct').append(html);	
						if(cdTime) countDown(cdTime,cdTime2,v.aid);	
					}
				});
			}else{
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




/*时间倒计时
* time 倒计时秒数
* id 倒计时容器id
* day_elem 天数calss
* hour_elem 小时calss
* minute_elem 分钟calss
* second_elem 秒数calss
*/
function countDown(time,cdTime2,id){
	//var end_time = new Date(time).getTime(),//月份是实际月份-1
	//sys_second = (end_time-new Date().getTime())/1000;
	var timer = setInterval(function(){
		var idDom = $('#atv_'+id);
		if (time > 0) {
			time -= 1;
			var day = Math.floor((time / 3600) / 24);
			var hour = Math.floor((time / 3600) % 24);
			var minute = Math.floor((time / 60) % 60);
			var second = Math.floor(time % 60);
			
			idDom.find('i').eq(0).text(day);//计算天
			idDom.find('i').eq(1).text(hour<10?"0"+hour:hour);//计算小时
			idDom.find('i').eq(2).text(minute<10?"0"+minute:minute);//计算分
			idDom.find('i').eq(3).text(second<10?"0"+second:second);// 计算秒
		} else { 
			
			if(idDom.find('.p1').text() == '开始倒计时') 
			{
				idDom.find('.p1').text('结束倒计时');
				clearInterval(timer);
				countDown(cdTime2,0,id);
				
			}	
			else if(idDom.find('.p1').text() == '结束倒计时')
			{
				idDom.find('.p2').hide();
				idDom.find('.p1').text('活动已结束');
				clearInterval(timer);
					
				
			}
				
		}
	}, 1000);
}

</script>
</body>
</html>

