	<div class="detail_top">
		<button class="button1 <?php if($_GET['type']==1||( empty($_GET['type'])&& $_SERVER['PHP_SELF']!='/goods/goods_detail4.php' &&$_SERVER['PHP_SELF']!='/goods/act_comment.php'))echo 'on' ;?>" onclick="window.location.href='goods_detail.php?gid=<?php echo $gid?>&type=1'">价格</button>
        <button class="button2 <?php if($_GET['type']==2)echo 'on';?>" onclick="window.location.href='goods_detail.php?gid=<?php echo $gid?>&type=2'">医院医生</button>
        <button class="button3 <?php if($_GET['type']==3)echo 'on';?>" onclick="window.location.href='goods_detail.php?gid=<?php echo $gid?>&type=3'">项目介绍</button>
        <button class="button4 <?php if($_SERVER['PHP_SELF']=='/goods/goods_detail4.php'||$_SERVER['PHP_SELF']=='/goods/act_comment.php')echo 'on';?>" onclick="window.location.href='goods_detail4.php?gid=<?php echo $gid?>'">评价</button>
    </div>
