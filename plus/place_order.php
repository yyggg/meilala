<?php
	require_once '../inc/common.php';    
	if ($_POST) {
			# code...
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$res = array('flag'=>0);
		$time = time();
		if (strpos($agent, "MicroMessenger")) { 
			$sql = "INSERT INTO m_order (type,pid,mid,order_no,tel,price,time,stat) value('$_POST[type]','$_POST[pid]','$_POST[mid]','$_POST[order_no]','$_POST[tel]','$_POST[price]',$time,0)";
			if($db->exec($sql)) 
			{
				$res['flag'] = 1;
				$_SESSION['wxpay_name']	=	$_POST['order_name']?$_POST['order_name']:'美啦啦订单';
				$_SESSION['wxpay_tel']	=	$_POST['tel'];
				$_SESSION['order_no']	=	$_POST['order_no'];
				$_SESSION['price']		=	$_POST['price'];
			}
		}
		die(json_encode($res));	
	}
	


?>