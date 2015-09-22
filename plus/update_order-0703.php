<?php
	require_once '../inc/common.php';    
	if(!empty($_SESSION['order_no'])){
		$order_no = $_SESSION['order_no'];
		$sql = "UPDATE m_order SET stat = 1 WHERE order_no ='".$order_no."'";
		if($db->exec($sql)){
			$sql = "SELECT * FROM m_order WHERE order_no = '$order_no'";
			$sth = $db->prepare($sql);
			$sth->execute();
			$data = $sth->fetch(PDO::FETCH_ASSOC);
			if($data['type']==1){
				header("Location:../activites/activity_detail.php?aid=".$data['pid']);
				exit;
			}
			
		}
	}
	header("Location:http://m.meilalal.net/");
	exit;		

?>