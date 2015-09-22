<?php

//еп╤он╒пе╩Ат╠
/*

*/

include_once ('../weixin/weixin.auth.php');

if(empty($_SESSION['wx_user'])){
	$_SESSION['wx_user']=array(
		'headimgurl'=>'http://wx.qlogo.cn/mmopen/UY7PGsIpavD3mExMUooWPadBcib3eACkZ6eq5Nu9fYX2M3R4icDlNqyYGqyPEErhLlsOv4JZGMiaMjviaJ4GT8SGTMOiaSIlZmbjl/0',
		'openid'=>'oiITbswcXdpoX9Wx4t58hp3PDrdM',
		
	);
}
include('new_mobile.php');



?>