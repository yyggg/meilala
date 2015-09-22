<?php
	require_once '../inc/common.php';    
	if ($_GET['order_no']) {	
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$time = time();
		$order_no=$_GET['order_no'];
		if (strpos($agent, "MicroMessenger")) { 
			$user = getUser('mid,name,phone');
			if ($_GET['type']==2) {
				$sql = "SELECT o.*,a.name FROM m_order as o  left join m_goods as a on o.pid = a.gid WHERE o.order_no='$order_no'";
			}else{
				$sql = "SELECT o.*,a.name FROM m_order as o  left join m_activites as a on o.pid = a.aid WHERE o.order_no='$order_no'";
			}
			$sth = $db->prepare($sql);
			$sth->execute();
			$order = $sth->fetch(PDO::FETCH_ASSOC);
			$_SESSION['wxpay_name']	=	$order['name'];
			$_SESSION['order_no']	=	$order['order_no'];
			$_SESSION['price']		=	$order['price'];
			$_SESSION['wxpay_tel']	=	!empty($order['tel']) ?	$order['tel'] : $user['phone'];
			header("Location:../plus/wx_pay.php");
			//print_r($order);		
		}
		else{
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			echo "请用微信浏览器打开！";
		}

	}
	


?>