<?php 
	/*个人资料修改*/
	require_once '../inc/common.php';
	$data = getUser();
	$mid = $data['mid'];
	$data['headimgurl']=empty($data['headimgurl'])?'../images/touxiang.png':$data['headimgurl'];
	if($_POST)
	{
		$res = array('flag' => 0);
		$name = (string)$_POST['name'];
		$phone = (int)$_POST['phone'];
		$email = (string)$_POST['email'];
		$qq = (int)$_POST['qq'];
		$sex = (string)$_POST['sex'];
		$birthday = strtotime((string)$_POST['birthday']);
		$hobby = (string)$_POST['hobby'];
		
		$sql = "UPDATE m_member SET name='$name', phone='$phone', sex='$sex', birthday='$birthday', email='$email', qq='$qq', hobby='$hobby' WHERE mid = '$mid'";
		$rows = $db->exec($sql);
		if($rows)
		{
			$res['flag'] = 1;
		}
		die(json_encode($res));
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>个人资料</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/common.css" type="text/css" media="all">
<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="../js/mobile.js" type="text/javascript"></script>
</head>

<body class="person_edit">
<!--header-->
<header class="header">
	<div class="header_back"><a href="/member/space.php"></a></div>
    <h2>资料修改</h2>
    <div class="header_right"><a href="javascript:personSave();"></a></div>
</header>
<!--header end-->
<!--content-->
<div class="content mb10">
	<ul>
    	<li class="my_li my_varify pt10 pb10"><span class="my_keywords"><b>*</b>头像</span><span class="my_value"><img src="<?php echo $data['headimgurl'];?>" /></span><input type="file" id="my_varify" unat="server" accept="image/*" capture="camera"/><canvas id="canvas"></canvas></li>
        <li class="my_li"><span class="my_keywords"><b>*</b>昵称</span><input class="my_value" id="name" value="<?php echo $data['name'];?>"/></li>
        <li class="my_li mt10"><span class="my_keywords"><b>*</b>电话</span><input type="tel" class="my_value" id="phone" value="<?php echo $data['phone'];?>"/></li>
        <li class="my_li"><span class="my_keywords"><b>*</b>邮箱</span><input type="email" class="my_value" id="email" value="<?php echo $data['email'];?>"/></li>
        <li class="my_li"><span class="my_keywords"><b>*</b> Q Q </span><input type="text" class="my_value" id="qq" value="<?php if($data['qq']) echo $data['qq'];?>"/></li>
        <li class="my_li mt10 ">
	        <span class="my_keywords"><b>*</b>性别</span>
	        <span class="my_value no_border ">
            	<div class="my_sex" >
		        <input type="radio" name="sex" class="sex_radio" value="1" <?php if($data['sex'] == '1') echo 'checked' ;?> /><span>男</span>
                </div>
                <div class="my_sex">
				<input type="radio" name="sex" class="sex_radio" value="0" <?php if($data['sex'] == '0') echo 'checked' ;?> /><span>女</span>
                </div>
			</span>
		</li>
        <li class="my_li"><span class="my_keywords"><b>*</b>生日</span><input class="my_value" id="birthday" type="date" value="<?php echo date('Y-m-d',$data['birthday']);?>"/></li>
        <!--<li class="my_li"><span class="my_keywords"><b>*</b>爱好</span><input type="text" class="my_value" id="hobby" value="<?php echo $data['hobby'];?>"/></li>-->
    </ul>
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
<script>
	function personSave()
	{
		var content = $('.content');
		var name = content.find('#name').val();
		var phone = content.find('#phone').val();
		var email = content.find('#email').val();
		var qq = content.find('#qq').val();
		var sex = $('input:radio[type="radio"]:checked').val();
		if(sex == undefined) sex = 2;
		
		var birthday = content.find('#birthday').val();
		var hobby = content.find('#hobby').val();
		
		$.post('person_edit.php',{name:name,phone:phone,email:email,qq:qq,sex:sex,birthday:birthday,hobby:hobby}, function(res){
			if(res.flag)
			{
				alert('修改成功');
				window.location.href='person.php';
			}
			else 
			{
				alert('修改失败');	
			}
		}, 'json');
	}
</script>
</body>
</html>
