<?php
	require_once '../inc/common.php'; 

	if(!empty($_GET['order_no'])){
		$order_no = $_GET['order_no'];

		$sql = "SELECT * FROM m_order WHERE order_no = '$order_no'";
		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetch(PDO::FETCH_ASSOC);

		print_r($data);

		$sql = "UPDATE m_order SET stat = 1 WHERE order_no ='".$order_no."'";
		$sql2 = "UPDATE m_atv_enroll  SET pay_stat = 1 WHERE aid ='$data[pid]' AND mid = '$data[mid]'";	
		echo "<br>".$sql;
		echo "<br>".$sql2;
		if($db->exec($sql)){
				if($db->exec($sql2)){
				header("Location:../member/my_order.php?aid=".$data['pid']);
				exit;
				}
			
		}
	}
	//header("Location:http://m.meilala.net/");
	exit;	


	

?>