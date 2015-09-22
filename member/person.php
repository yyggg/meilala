<?php 
	/*个人资料*/
	require_once '../inc/common.php';
	$data = getUser();
	$data['headimgurl']=empty($data['headimgurl'])?'../images/touxiang.png':$data['headimgurl'];
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

<body class="person">
<!--header-->
<header class="header">
	<div class="header_back"><a href="/member/space.php"></a></div>
    <h2>个人资料</h2>
    <div class="header_right"><a href="/member/person_edit.php"></a></div>
</header>
<!--header end-->
<!--content-->
<div class="content mb10">
	<ul>
    	<li class="my_li my_varify pt10 pb10"><span class="my_keywords"><b>*</b>头像</span><span class="my_value"><img src="<?php echo $data['headimgurl'];?>" /></span></li>
        <li class="my_li"><span class="my_keywords"><b>*</b>昵称</span><span class="my_value"><?php echo $data['name'];?></span></li>
        <li class="my_li mt10"><span class="my_keywords"><b>*</b>电话</span><span class="my_value"><?php echo $data['phone'];?></span></li>
        <li class="my_li"><span class="my_keywords"><b>*</b>邮箱</span><span class="my_value"><?php echo $data['email'];?></span></li>
        <li class="my_li"><span class="my_keywords"><b>*</b> Q Q </span><span class="my_value"><?php if($data['qq']) echo $data['qq'];?></span></li>
        <li class="my_li mt10"><span class="my_keywords"><b>*</b>性别</span><span class="my_value"><?php if($data['sex']==0) echo '女'; elseif($data['sex']==1) echo '男'; else echo '保密';?></span></li>
        <li class="my_li"><span class="my_keywords"><b>*</b>生日</span><span class="my_value"><?php echo date('m月d日',$data['birthday']);?></span></li>
        <!--<li class="my_li"><span class="my_keywords"><b>*</b>爱好</span><span class="my_value"><?php echo $data['hobby'];?></span></li>-->
   </ul>
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
</body>
</html>
