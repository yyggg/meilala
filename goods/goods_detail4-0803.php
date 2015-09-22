<?php
	require_once '../inc/common.php';
	$gid = isset($_GET['gid']) ? (int)$_GET['gid'] : 0;
	if(!$gid) {
		header("Location:goods.php");
		exit;
	}
	
	$pageSize = 5;
	$sql = "SELECT count(1) as count FROM m_comment WHERE pid = '$gid' AND type = 0";
	$sth = $db->prepare($sql);
	$sth->execute();
	$count = $sth->fetchColumn();
	$pageTotal = ceil ($count/$pageSize);
	
	if(isset($_GET['aj_comm']))
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  //获取请求的页数 
		$start = ($page-1)*$pageSize; 
		
		$sql = "SELECT c.cid, c.imgs, c.content, c.ctime, c.nice, m.headimgurl, m.name
			FROM m_comment as c LEFT JOIN m_member as m ON c.mid = m.mid 
			WHERE c.type = '0' AND c.pid = '$gid' ORDER BY c.cid DESC LIMIT $start, $pageSize";
	
		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($data as $k => $v)
		{
			$data[$k]['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
			$data[$k]['imgs'] = explode('|', $v['imgs']);
		}
		die(json_encode($data));
	}
	
	
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
    <script src="../js/scrollpagination.js" type="text/javascript"></script>
</head>

<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="/goods/goods.php"></a></div>
    <h2>项目详情</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
	<?php include_once ('../template/item_detail_top.php');?> 
    <div class="detail mt10 mb10">
		<ul class="detail_ul">
        	
        </ul>    
    </div>
    <div class="comment_button"><button onclick="window.location.href='act_comment.php?gid=<?php echo $gid;?>'">我要评论</button>
  </div>
</div>

<div class="item-loading">
	<div id="nomoreresults"><img src="../images/loading.gif" /></div>
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


/*分页*/
var count = <?php echo $count;?>;
if(count == 0) $('.detail_ul').html('<div class="detail_comment pt10 pb10 white">该项目暂无评论，快抢沙发！</div>');
$(function(){
	$('.detail_ul').scrollPagination({
		'contentPage': 'goods_detail4.php?gid=<?php echo $gid;?>&aj_comm', // the url you are fetching the results
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
				$('.detail_ul').stopScrollPagination();	 
			 }
		},
		
		'dataType': 'json',
		'loader': function (data){
			
			if(data.length)
			{
				var html = '';
				var imgs = '';
				var nice = '';
				$.each(data, function(k, v){
					
					if(v.nice == 1) nice = '<div class="best"></div>';	
					
					if(v.imgs.length > 0)
					{
						$.each(v.imgs, function(kk,vv){
							if(vv)
							{
								imgs += '<div class="img" style="width:32%;background-image:url(<?php echo IMG_PATH;?>'+vv+');" onclick="look_comment_img('+v.cid+');">';
							}	
						});
					}
					
					html += '<li class="detail_comment pt10 pb10 white">'+
							'<div class="person">'+
								'<div class="person_img"><img src="'+v.headimgurl+'" /></div>'+
								'<div class="person_name">'+v.name+'</div>'+
								'<div class="time">'+v.ctime+'</div>'+nice+'</div>'+
								'<div class="cm">'+
									'<div class="cm_img">'+imgs+'</div>'+
									'<div class="cl"></div>'+
									'<div class="cm_ct">'+v.content+'</div>'+
								'</div>'+
								'<div class="cl"></div>'+
							'</li>';
				});
				$('.detail_ul').append(html);
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
