	<?php if ($_SERVER['PHP_SELF']=='goods/goods_detail.php'): ?>
	<div class="detail_top">
		<button class="button1"  onclick="window.location.href='#'">价格</button>
        <button class="button2"  onclick="window.location.href='#detail2'">医院医生</button>
        <button class="button3"  onclick="window.location.href='#detail3'">项目介绍</button>
        <button class="button4 <?php if($_SERVER['PHP_SELF']=='/goods/goods_detail4.php'||$_SERVER['PHP_SELF']=='/goods/act_comment.php')echo 'on';?>" onclick="window.location.href='goods_detail4.php?gid=<?php echo $gid?>'">评价</button>
    </div>
	<?php else: ?>
	<div class="detail_top">
		<button class="button1"  onclick="window.location.href='goods_detail.php?gid=<?php echo $gid?>#'">价格</button>
        <button class="button2"  onclick="window.location.href='goods_detail.php?gid=<?php echo $gid?>#detail2'">医院医生</button>
        <button class="button3"  onclick="window.location.href='goods_detail.php?gid=<?php echo $gid?>#detail3'">项目介绍</button>
        <button class="button4 <?php if($_SERVER['PHP_SELF']=='/goods/goods_detail4.php'||$_SERVER['PHP_SELF']=='/goods/act_comment.php')echo 'on';?>" onclick="window.location.href='goods_detail4.php?gid=<?php echo $gid?>'">评价</button>
    </div>		
	<?php endif ?>

    <script type="text/javascript">
    function scroll_to (top_h) {
  		var h=$(window.location.hash).offset().top;
    	//$("body").animate({scrollTop:$("body").height()},1000);   		
    	//var top_h=$('.detail_top').height()+$('.header').height();
    	$("body").animate({scrollTop:h-top_h},0);
    }

	function GetQueryString(name)
	{
		 var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
		 var r = window.location.search.substr(1).match(reg);
		 if(r!=null)return  unescape(r[2]); return null;
	}
	$(".detail_top button").click(function  () {
		$(".detail_top button").removeClass('on');
		$(this).addClass('on');	
		if (window.location.hash=='#detail1') {
			scroll_to ($('.detail_top').height()+$('.header').height());
		}else{
			scroll_to ($('.detail_top').height()+$('.header').height());
		}
	})

	$(function(){
		//alert(window.location.pathname);
		if (window.location.hash=='#detail1') {
			$(".detail_top button").removeClass('on');
			$(".button1").addClass('on');			
		}
		else if (window.location.hash=='#detail2') {
			$(".detail_top button").removeClass('on');
			$(".button2").addClass('on');			
		}
		else if (window.location.hash=='#detail3') {
			//alert(111);
			$(".detail_top button").removeClass('on');
			$(".button3").addClass('on');			
		}
		else{
			if(window.location.pathname!='/goods/goods_detail4.php'){
				$(".detail_top button").removeClass('on');
				$(".button1").addClass('on');			
			}
		}
		scroll_to ($('.detail_top').height()+$('.header').height());		
	})

	</script>