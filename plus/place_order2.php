<?php
	require_once '../inc/common.php';    
	if ($_POST) {
			# code...
		$res = array('flag'=>0);
		$time = time();
		$sql = "INSERT INTO m_order (type,pid,mid,order_no,price,time,stat) value('$_POST[type]','$_POST[pid]','$_POST[mid]','$_POST[order_no]','$_POST[price]',$time,0)";
		if($db->exec($sql)) 
		{
			$res['flag'] = 1;
			$_SESSION['order_no']	=	$_POST['order_no'];
			$_SESSION['price']		=	$_POST['price'];
		}
		die(json_encode($res));	
	}
	


?>