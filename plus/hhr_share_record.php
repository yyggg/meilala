<?php

$res = array('flag' => 0);
if (strpos($_SERVER['HTTP_USER_AGENT'], "MicroMessenger") ){	
	if ($_POST) {
    	require_once '../inc/common_hhr.php';	
		$share_id	=	$_POST['share_id'];		
		$type 		= 	$_POST['type'];
		$partner_id = 	$_POST['partner_id'];
		$time=time();
		$share_url=cloud_get_field('redirect_link','partner_message_tips','id = '.$share_id);
		if (!empty($share_url)) {
			$have_record=cloud_get_field('id','partner_share_record',"partner_id = '$partner_id' AND share_id = '$share_id' AND  type = '$type'");			
			if(!empty($have_record)) {
				if(cloud_update('partner_share_record','share_num = share_num+1,time = '.$time,"id = '$have_record'")){
					$res = array('flag' => 1);
				}
			}else{
				$field ="share_id,type,partner_id,share_url,share_num,time";
				$value="'$share_id','$type','$partner_id','$share_url','1','$time'";
				if (cloud_insert('partner_share_record',$field,$value)) {
					$res = array('flag' => 1);
				}
			}
		}
	}

	//http://m.meilala.net/plus/hhr_share_record.php
	//cloud.net:8083/plus/hhr_share_record.php
	//share_id=298&partner_id=74&type=1
}
die(json_encode($res));






  	function get_query($item,$params){
       $paramsArr = explode('&',$params); 
   
       foreach($paramsArr as $k=>$v) 
       { 
          $a = explode('=',$v); 
          $arr[$a[0]] = $a[1]; 
       }
       //print_r($arr); 
       return $arr[$item];
  	}

	//生成随机数
	function randStr($len=4) {   
		$chars='1234567890'; // characters to build the password from   
		mt_srand((double)microtime()*1000000*getmypid()); // seed the random number generater (must be done)   
		$str='';   
		while(strlen($str)<$len)   
			$str.=substr($chars,(mt_rand()%strlen($chars)),1);   
		return $str;   
	} 

	function cloud_get_field($field='*',$table,$where=''){
		$data	=	cloud_get_row($field,$table,$where);
		return $data[$field];
	}

	function cloud_get_row($field='*',$table,$where=''){
		global $db;
		$where = empty($where)?'':' WHERE '.$where;echo
		$sql = "SELECT $field FROM $table $where";
		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data;
	}

	function cloud_insert($table,$field,$value){
		global $db;
		$sql = "INSERT INTO `$table` ($field) values ($value)";
		$sth = $db->prepare($sql);
		if($sth->execute()) {
			return true;
		}else{
			return false;
		}
	}
	function cloud_update($table,$value,$where){
		global $db;
		$where = empty($where)?'':' WHERE '.$where;echo
		$sql = "UPDATE `$table` SET $value $where";
		$sth = $db->prepare($sql);
		if($sth->execute()) {
			return true;
		}else{
			return false;
		}
	}


?>