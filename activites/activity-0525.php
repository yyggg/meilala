<?php
	require_once '../inc/common.php';
	$where = '';
	$status = isset($_GET['status']) ? (int)$_GET['status'] : 'all';
	$time = time();
	if($status != 'all')
	{
		if($status == 'no')
		{
			$where = " stime > '$time' ";		
		}
		elseif($status == 'ing')
		{
			$where = " stime < '$time' AND stime < etime AND enroll < quota ";		
		}
		elseif($status == 'end')
		{
			$where = " (etime < '$time') or (enroll >= quota) ";		
		}
	}
	
	$res = $db->query("SELECT aid,name,aimg,enroll,quota,stime,etime FROM m_activites $where ORDER BY aid DESC LIMIT 0,4");
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$data = $res->fetchAll();
	
	$sql = "SELECT count(1) as count FROM m_activites";
	$sth = $db->prepare($sql);
	$sth->execute();
	$pageTotal = $sth->fetchColumn();
	
	
	if(isset($_GET['ajax-goods']))
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  //获取请求的页数 
		$start = ($page-1)*4; 	
		$sql = "SELECT aid,name,aimg,enroll,quota,stime,etime FROM m_activites ORDER BY aid DESC LIMIT $start, 4";
		$sth = $db->prepare($sql);
		//$sth->bindParam(':aid', $aid);
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		die(json_encode($data));  //转换为json数据输出
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>活动列表</title>
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
	<div class="header_back"><a href="/"></a></div>
    <h2>活动列表</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
	<div class="activity_top mt10 mb10">
        <button class="button1" onclick="window.location.href='activity.php?status=all">全部</button>
        <button class="button2" onclick="window.location.href='activity.php?status=no'">未开始</button>
        <button class="button3" onclick="window.location.href='activity.php?status=ing'">进行中</button>
        <button class="button4" onclick="window.location.href='activity.php?status=end'">已结束</button>
    </div>
    <div class="item_ct">
    	<?php foreach($data as $v):;?>
        <a href="activity_detail.php?aid=<?php echo $v['aid'];?>">
    	<div class="item_li act_li" onclick="">
        	<div class="act_li_border white"></div>
            <b class="best"></b>
        	<div class="li_img" style="background-image:url(<?php echo IMG_PATH . $v['aimg'];?>); "></div>
            <div class="title"><?php echo $v['name'];?></div>
            <div class="comment">
            	
            	<b class="p1">
                	<?php if($v['stime'] > $time):?>
                		开始倒计时
                    <?php elseif($v['stime'] < $time && $v['stime'] < $v['etime'] && $v['enroll'] < $v['quota']):?>
                    	进行中...
                    <?php elseif($v['etime'] < $time || $v['enroll'] >= $v['quota']):?>
                    	活动已结束
                    <?php endif;?>
                </b>
                <b class="p2">
                	<i>23</i>时<i>23</i>分<i>23</i>秒
                </b>
            </div>
            <input type="hidden" id="<?php echo $v['aid'];?>" name="group" value="<?php echo $v['etime']-$time;?>">
        </div>
        </a>
        <?php endforeach;?>
    </div>
    <style>
    .loading {
	background:#c1c39a;
	color:#303030;
	font-size:20px;
	padding:5px 10px;
	text-align:center;
	width:100%;
	margin:0px auto;
	display:none;
	border-radius: 5px;
}
    </style>
	<div class="loading" id="loading">正在努力加载...</div>
    <div class="loading" id="nomoreresults">没有更多的数据了...</div>
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->

<script>
$(function(){
	$('.item_ct').scrollPagination({
		'contentPage': 'activity.php?ajax-goods', // the url you are fetching the results
		'contentData': {}, // these are the variables you can pass to the request, for example: children().size() to know which page you are
		'scrollTarget': $(window), // who gonna scroll? in this example, the full window
		'heightOffset': 10, // it gonna request when scroll is 10 pixels before the page ends
		'beforeLoad': function(){ // before load function, you can display a preloader div
			$('#loading').fadeIn();	
		},
		'afterLoad': function(elementsLoaded){ // after loading content, you can use this function to animate your new elements
			 $('#loading').fadeOut();
			 var i = 0;
			 $(elementsLoaded).fadeInWithDelay();
			 if(this.page >= <?php echo $pageTotal;?>)
			 {
				$('#nomoreresults').fadeIn();
				$('.item_ct').stopScrollPagination();	 
			 }
			 /*if($('.item_ct').children().size() > 100){ // if more than 100 results already loaded, then stop pagination (only for testing)
			 	$('#nomoreresults').fadeIn();
				$('.item_ct').stopScrollPagination();
			 }*/
		},
		
		'dataType': 'json',
		'loader': function (data){
			$.each(data, function(k, v){
				$('.item_ct').append(
					'<div class="item_li act_li" style="opacity:0;-moz-opacity: 0;filter: alpha(opacity=0);">' +
					'<div class="act_li_border"></div>' +
					'<div class="li_img" style="background-image:url(<?php echo IMG_PATH;?>'+v.aimg+'); "></div>' +
					'<div class="title">'+ v.name +'</div>' +
					'<div class="comment"><b class="p1">活动结束</b><b class="p2"><i>23</i>时<i>23</i>分<i>23</i>秒</b></div>' +
					'<b class="best"></b>' +
					'</div>'
				);	
			});
			
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
function countDown(type,time,id,day_elem,hour_elem,minute_elem,second_elem){
	
	//var end_time = new Date(time).getTime(),//月份是实际月份-1
	//sys_second = (end_time-new Date().getTime())/1000;
	
	var timer = setInterval(function(){
		if (time > 0) {
			time -= 1;
			var day = Math.floor((time / 3600) / 24);
			var hour = Math.floor((time / 3600) % 24);
			var minute = Math.floor((time / 60) % 60);
			var second = Math.floor(time % 60);
			day_elem && $('#'+id+' .'+day_elem).text(day);//计算天
			$('#'+id+' .'+hour_elem).text(hour<10?"0"+hour:hour);//计算小时
			$('#'+id+' .'+minute_elem).text(minute<10?"0"+minute:minute);//计算分
			$('#'+id+' .'+second_elem).text(second<10?"0"+second:second);// 计算秒
		} else { 
			clearInterval(timer);
			if(type == 'group') {
				$('.ymms_right_jx').html('团购已结束');
				$('#'+id+' .zt6 span').show();
				$('#'+id+' .zt6 a').hide();
			}else{
				
				$('#'+id+' .son_djs').html('秒杀进行中...');
				$('#'+id+' .son_gn span').hide();
				$('#'+id+' .son_gn a').show();
				
				$('#'+id+' .nostart').hide();
				$('#'+id+' .son_djssz').show();
			}
			
		}
	}, 1000);
}
$('.ymms_fxs input').each(function(){
	
	var id = 'countDown_' + $(this).attr('id');
	var type = $(this).attr('name');
	var time = $(this).val();

	countDown(type,time,id,"day","hour","minute","second");
});
</script>
</body>
</html>
