<?php
	require_once '../inc/common.php';
	$type =  isset($_GET['type']) ? (int)$_GET['type'] : 0;
	$user = getuser('mid,headimgurl');
	$pageSize = 5;
	$sql = "SELECT count(1) as count FROM m_comment WHERE mid = '$user[mid]' AND type = 0";
	$sth = $db->prepare($sql);
	$sth->execute();
	$count = $sth->fetchColumn();
	$pageTotal = ceil ($count/$pageSize);
	
	if(isset($_GET['aj_comm']))
	{
		if($type == 0)
		{
			$table = 'm_goods';
		}
		elseif($type == 1)
		{
			$table = 'm_activites';
		}
		
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  //获取请求的页数 
		$start = ($page-1)*$pageSize; 
		$sql = "SELECT c.cid,c.pid,c.ctime,c.nice,c.imgs,c.content,g.name FROM m_comment as c
				LEFT JOIN $table as g ON c.pid = g.gid
				WHERE c.mid = '$user[mid]' AND c.type = '$type' ORDER BY c.ctime DESC LIMIT $start, $pageSize";
	
		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($data as $k=>$v)
		{
			$data[$k]['ctime'] = date('Y-m-d', $v['ctime']);
			$data[$k]['imgs'] = explode('|',$v['imgs']);
		}
		die(json_encode($data));  //转换为json数据输出
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>我的评论</title>
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
    <h2>我的评论</h2>
</header>
<!--header end-->
<!--content-->
<div class="content mb10">
	<div class="act_dtl_top  mb10">
        <button class="button1 <?php if($type == 0) echo 'on';?>" onclick="window.location.href='/member/my_comment.php?type=0'">项目评论</button>
        <button class="button2 <?php if($type == 1) echo 'on';?>" onclick="window.location.href='/member/my_comment.php?type=1'">活动评论</button>
    </div>
    <div class=" white clearfix">
    <section class="wd my_inform">
        <div class="my_header_img"><img src="<?php echo $user['headimgurl'];?>" alt="" /></div>
        <div class="my_name">美啦啦微整管家</div>
        <div class="my_count">总评论数：<?php echo number_format($count);?></div>
    </section>
</div>
    <div class="act_dtl_ct border mt10 act_comment">
		<ul>

        </ul>    
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
    var h1=$(".act_dtl_top").height();
    var t = document.documentElement.scrollTop || document.body.scrollTop;  
    if(t<=h){
        $('.act_dtl_top').css({"position":"relative"});
        $('.act_dtl_top button').css({"margin-top": "0.2rem"});
    }
    else { 
        $('.act_dtl_top').css({"position":"fixed","width":"100%","top":"0px","max-width":"640px","margin-top":"0px","margin-left":"auto","margin-right":"auto"});
        $('.act_dtl_top button').css({"margin-top": "0rem"});
    }
} 


/*分页*/
$(function(){
	$('.act_comment ul').scrollPagination({
		'contentPage': 'my_comment.php?aj_comm&type=<?php echo $type;?>', // the url you are fetching the results
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
				$('.act_comment ul').stopScrollPagination();	 
			 }
		},
		
		'dataType': 'json',
		'loader': function (data){
			if(data.length)
			{
				var html = '';
				var imgs = '';
				$.each(data, function(k, v){
					
					if(v.imgs.length > 0)
					{
						$.each(v.imgs, function(k,v){
							if(v)
							{
								imgs += '<div class="cm_img'+k+' half">'+
									'<img width="30%" src="<?php echo IMG_PATH;?>'+v+'" />'+
								'</div>';
							}	
						});
					}
					
					html += '<li class="detail_comment mt10 mb10 pt10 clearfix">'+
						'<div>'+v.ctime+' 评论了 <span onclick="window.location.href=/member/goods/goods?gid='+v.pid+'">'+v.name+'</span></div>'+
						'<div class="cm">'+
							'<div class="cm_img">'+imgs+'</div>'+
							'<div class="cm_ct">'+
								v.content+
							'</div>'+
						'</div>'+
						'<div class="cl"></div>'+
					'</li>';
				});
				$('.act_comment ul').html(html);
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
