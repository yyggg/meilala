<?php

require_once '../../inc/common_hhr.php';
if (!empty($_GET['pid'])) {
    $pid=$_GET['pid'];
    
    $sql = "SELECT * FROM partner_wx_infos where openid = '$pid' ";
    $sth = $db->prepare($sql);
    $sth->execute();
    $data = $sth->fetch(PDO::FETCH_ASSOC);
    //echo
    $partner_id=$data['partner_id'];
    //print_r($data);
}
else{
    header('location:/');
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

<title>您的推荐二维码</title>
<style>
body{font-family:微软雅黑;background-color:#f3f3f3;}
#main{margin:0 auto;background-color:#fff;width:100%;margin-top:100px;text-align:center;}
.title{font-size:40px;text-align:center;}
#sea_area_left{margin:0 auto;border:3px solid #E56A6A;padding-left:5px;width:550px;margin-top:15px;height:40px;}
.searchtext{float:left;border:0px;width:350px;padding:5px;font-size:14.5pt;font-family:"Microsoft YaHei","微软雅黑",Verdana !important;}
.searchbtn{height:40px;border:0px;width:189px;font-family:"Microsoft YaHei","微软雅黑",Verdana !important;color:white;padding:3px;background-color:#E56A6A;font-size:15pt;}
* html input#searchbutton{margin-bottom:-1px;height:40px;}
*+html input#searchbutton{margin-bottom:-1px;height:40px;}
</style>
</head>



<body>

<div id="main">
<span class="title">您的推荐二维码</span>


<?php
if(!is_file('bigimg/ewm_'.$partner_id.'.png')){
    $_POST['keyword']='http://m.meilala.net/plus/pay_attention.php?s='.$partner_id;
    if(!empty($_POST['keyword'])){
    	//文件输出
        include('phpqrcode.php');
    	// 二维码数据
        $data = $_POST['keyword'];
    	// 生成的文件名
        $filename = 'bigimg/ewm_'.$partner_id.'.png';
    	// 纠错级别：L、M、Q、H
        $errorCorrectionLevel = 'H';
    	// 点的大小：1到10
        $matrixPointSize = 10;
        QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    }
}
?>
<br>
<img src="bigimg/ewm_<?php echo $partner_id ?>.png" />

</div> 
</body>
</html>