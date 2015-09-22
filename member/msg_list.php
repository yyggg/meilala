<?php 
	require_once '../inc/common.php';
	
	$user = getUser('mid');
	
	$pageSize = 5;
	$sql = "SELECT count(1) as count FROM m_message WHERE mid IN ('0','{$user["mid"]}')";
	$sth = $db->prepare($sql);
	$sth->execute();
	$pageTotal = ceil ($sth->fetchColumn()/$pageSize);
	
	if(isset($_GET['ajax_msg']))
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;  //获取请求的页数 
		$start = ($page-1)*$pageSize; 
		$sql = "SELECT * FROM m_message WHERE mid IN ('0','{$user["mid"]}') ORDER BY id DESC LIMIT $start, $pageSize";
		
		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($data as $k=>$v)
		{
			$data[$k]['addtime'] = date('Y-m-d', $v['addtime']);	
		}
		die(json_encode($data));  //转换为json数据输出
	}
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8" />
<title>消息列表</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

<link rel="stylesheet" href="../css/common2.css" type="text/css" media="all">
<link rel="stylesheet" href="../css/lvshi.css" type="text/css" media="all">
<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="../js/mobile.js" type="text/javascript"></script>
<script src="../js/scrollpagination.js" type="text/javascript"></script>
</head>

<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="/member/space.php"></a></div>
    <h2>我的消息</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
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
	$('.content').scrollPagination({
		'contentPage': 'msg_list.php?ajax_msg', // the url you are fetching the results
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
				$('.content').stopScrollPagination();	 
			 }
		},
		
		'dataType': 'json',
		'loader': function (data){
			if(data.length)
			{
				$.each(data, function(k, v){
					if(v.type == 1)
					{
						var html = '<div class="xx_block" onclick="linkMsg('+v.read+','+"'"+v.url+"'"+','+v.mid+','+v.id+')">'+
							'<div class="xx_nr">'+
							'<p class="xx_bt">'+v.title+'</p>'+
							'<p class="xx_sj">'+v.addtime+'</p>'+
							'<div class="xx_tup"><img src="<?php echo IMG_PATH;?>'+v.img+'" /></div>'+
							'<div class="xx_text">'+v.zhaiyao+'</div>'+
            				'<div class="xx_chakan"><a href="javascript:;">查看消息>></a></div>'+
							'</div>'+
							'<div class="cl"></div>'+
							'</div>';
					}
					else
					{
						var html = '<div class="xx_block">'+
							'<div class="xx_nr">'+
							'<p class="xx_bt">'+v.title+'</p>'+
							'<p class="xx_sj">'+v.addtime+'</p>'+
							'<div class="xx_text">'+v.content+'</div>'+
							'</div>'+
							'<div class="cl"></div>'+
							'</div>';	
					}
					$('.content').append(html);	
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
function linkMsg(read,url,mid,id){
		if(read == 0 && mid)
		{
			var postUrl = '/inc/ajax_fun.php?action=msg&id=' + id;
			$.get(postUrl,function(){});
		}
		window.location.href = url;
	}	
</script>
</body>
</html>
