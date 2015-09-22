<?php
	require_once '../inc/common.php';    
	if(!empty($_SESSION['order_no'])){
		$order_no = $_SESSION['order_no'];

		$sql = "SELECT * FROM m_order WHERE order_no = '$order_no'";
		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetch(PDO::FETCH_ASSOC);

		print_r($data);
		$sql = "UPDATE m_order SET stat = 1 WHERE order_no ='".$order_no."'";	
		$sql2 = "UPDATE m_atv_enroll  SET pay_stat = 1 WHERE aid ='$data[pid]' AND mid = '$data[mid]'";	


		if($db->exec($sql) && $db->exec($sql2)){

			if($data['type']==1){
				header("Location:../member/my_order.php?aid=".$data['pid']);
				exit;
			}
			
		}
	}
	header("Location:http://m.meilala.net/");
	exit;	


	

?>