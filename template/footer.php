<?php
$url_dir=dirname($_SERVER["SCRIPT_NAME"]);
?>
<?php if($_SERVER["SCRIPT_NAME"]!='/goods/act_comment.php' && $_SERVER["SCRIPT_NAME"]!='/activites/act_comment.php'):?>
<footer class="footer">
	<ul>
    	<li onclick="window.location.href='/activites/activity.php'" class="f_subbox <?php if($url_dir=='/activites')echo 'on'; ?>"><i class="footer_icon1"></i><span>活动</span></li>
		<li onclick="window.location.href='/goods/goods.php'" class="f_subbox <?php if($url_dir=='/goods')echo 'on'; ?>"><i class="footer_icon2"></i><span>项目</span></li>
    	<li onclick="window.location.href='/kf/index.php'" class="f_subbox"><i class="footer_icon3"></i><span>咨询</span></li>
		<li onclick="window.location.href='/know/'" class="f_subbox <?php if($url_dir=='/know')echo 'on'; ?>"><i class="footer_icon4"></i><span>知道</span></li>
    	<li onclick="window.location.href='/member/space.php'" class="f_subbox <?php if($url_dir=='/member')echo 'on'; ?>"><i class="footer_icon5"></i><span>我</span></li>
    </ul>
</footer>
<div class="footer_height"></div>
<?php endif?>