<?php
	require_once '../inc/common.php';    
	$aid = isset($_REQUEST['aid']) ? (int)$_REQUEST['aid'] : 0;
	if(!$aid){
		header("Location:activity.php");
		exit;	
	}
	$user = getUser('mid,name,phone');
	$is_enroll = 0;
	$enroll    = 0;
	//检查是否已报名
	$sql = "SELECT count(1) as count FROM m_atv_enroll WHERE mid = '$user[mid]' AND aid = '$aid'";
	if ($db->query($sql)->fetchColumn())
	{
		$is_enroll = 1;
	}
	else
	{
		//检查是否已经满额
		$sql = "SELECT aid FROM m_activites WHERE aid = '$aid' AND enroll >= quota";
		if ($db->query($sql)->fetchColumn())
		{
			$enroll = 1;
		}	
	}
		
	if($_POST)
	{
		$res = array('flag'=>0);
		$time = time();
		if($_POST['phone'] && ($_POST['phone'] != $user['phone']))	
		{
			$sql = "UPDATE m_member SET phone = :phone WHERE mid = :mid";
			$param = array(':phone' => $_POST['phone'], ':mid' => $user['mid']);
			$sth = $db->prepare($sql);
			$sth->execute($param);
		}
		
		//该活动报名人数更新
		$sql = "UPDATE m_activites SET enroll = enroll+1 WHERE aid = '$aid' AND enroll < quota";
		if($db->exec($sql))
		{
			$sql = "INSERT INTO m_atv_enroll SET mid = :mid, aid = :aid, addtime = '$time'";
			$param = array(':aid' => $_POST['aid'], ':mid' => $user['mid']);
			$sth = $db->prepare($sql);
			$sth->execute($param); 	
			if($db->lastInsertId()) $res['flag'] = 1;
		}
		die(json_encode($res));	
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>活动报名</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/common.css" type="text/css" media="all">
	<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
    <script src="../js/mobile.js" type="text/javascript"></script>
</head>

<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="/activites/activity.php"></a></div>
    <h2>活动报名</h2>
</header>
<!--header end-->
<!--content-->
<div class="content activity_applay mt10">
	<?php if($is_enroll):?>
    	<div class="wd90 white pt10 pb10">您已经报过名了，无需重复报名！</div>
    <?php elseif($enroll):?>
    	<div class="wd90 white pt10 pb10">名额已满！</div>
    <?php else:?>
    	<p><span>用户名：</span><?php echo $user['name'];?></p>
    	<p><span>手机号：</span><input type="text" name="phone" id="phone" value="<?php echo $user['phone'];?>" /></p>
        <p><button>确认报名</button></p>
    <?php endif;?>
    <?php if($is_enroll + $enroll)?>
    	
    
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
<script>
	var form = $('.activity_applay');
	form.find('button').click(function(){
		var aid = <?php echo $aid;?>;
		var phone = form.find('#phone');
		if(!phone.val())
		{
			alert('请填写手机号！');
			phone.focus();
			return false;
		}
		
		form.find('button').html('提交中...');
		form.find('button').attr("disabled",true);
		$.post('activity_apply.php', {aid:aid,phone:phone.val()}, function(res){
			if(res.flag == 1){
				form.find('button').html('恭喜成功报名');
				delay_alert('恭喜成功报名','activity_detail.php?aid=<?php echo $aid;?>');
				
				//window.location.href='activity_detail.php?aid=<?php echo $aid;?>';
					
			}else {
				alert('报名失败，请再次尝试！');	
				form.find('button').html('确认报名');	
				form.find('button').attr("disabled",false);
			}
		},'json');
	});
</script>
</body>
</html>
