<?php
	require_once '../inc/common.php';    
	$gid = isset($_REQUEST['gid']) ? (int)$_REQUEST['gid'] : 0;
	if(!$gid){
		header("Location:goods.php");
		exit;	
	}
	$user = getUser('mid,name,phone');
	$is_enroll = 0;
	$enroll    = 0;

	//获取活动支付价格
	$sql = "SELECT ispay, prepay, name ,gimg FROM m_goods WHERE gid = '$gid'";
	$sth = $db->prepare($sql);
	$sth->execute();
	$data = $sth->fetch(PDO::FETCH_ASSOC);
	$data['order_no']='MLL'.date('Ymdhis').randStr(4);
	//print_r($data);

	//生成随机数
	function randStr($len=4) {   
		$chars='ABDEFGHJKLMNPQRSTVWXY'; // characters to build the password from   
		mt_srand((double)microtime()*1000000*getmypid()); // seed the random number generater (must be done)   
		$str='';   
		while(strlen($str)<$len)   
			$str.=substr($chars,(mt_rand()%strlen($chars)),1);   
		return $str;   
	} 

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $data['name'] ?></title>
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
	<div class="header_back"><a href="../goods/goods.php"></a></div>
    <h2>提交订单</h2>
</header>
<!--header end-->
<!--content-->
<div class="content activity_applay mt10">
	<div class="wd activity_apply_ct" id="atv_<?php echo $gid;?>">
    	<div class="act_img"><img src="<?php echo IMG_PATH . $data['gimg'];?>" /></div>        
        <div class="ct">
        	<div class="title mt10"><?php echo $data['name'];?></div>    
			<p><span>用户：</span><?php echo $user['name'];?></p>
			<p><span>手机：</span><input type="text" name="phone" id="phone" value="<?php echo $user['phone'];?>" /></p>
		    <p class="applying_button"><button id="go_pay">确定订单</button></p>
        </div>
   </div>   
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
<script>
	$("#go_pay").click(function(){
		var tel=$("#phone").val();
		if(!tel)
		{
			alert('请填写手机号！');
			tel.focus();
			return false;
		}
		var pid 	=	<?php echo $gid; ?>;
		var price 	=	<?php echo $data['prepay'];?>;
		var mid 	=	<?php echo $user['mid'];?>;
		var order_no= 	"<?php echo $data['order_no'];?>";
		var order_name= "<?php echo $data['name'] ?>";
		//alert(order_no);
		$.post('../plus/place_order.php', {type:2,pid:pid,price:price,mid:mid,order_no:order_no,tel:tel,order_name:order_name}, function(res){
			if(res.flag == 1){
				alert('正在跳转');
				window.location.href='../plus/wx_pay.php';
			}else{
				alert('页面出错');	
			}

		},'json');		
	});

</script>
</body>
</html>
