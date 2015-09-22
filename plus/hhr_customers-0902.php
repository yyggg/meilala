<?php
$res = array('flag' => 0);
if (strpos($_SERVER['HTTP_USER_AGENT'], "MicroMessenger")){
	require_once '../inc/common_hhr.php';
	$is_reged=cloud_get_field('mobile','customers'," mobile = $_POST[mobile]");
	if(empty($is_reged)){
	    $openid		=	$_POST['openid'];
	    $realname	=	$_POST['realname'];
	    $sex 		=	$_POST['sex']==0?2:$_POST['sex'];

	    //$province_txt=	$_POST['province_txt'];
	    //$city_txt	=	$_POST['city_txt'];
	    $mobile		=	$_POST['mobile'];
	    $back_url 	=   $_POST['back_url'];
	    $createtime	=	time();    
		$partner_id =	cloud_get_field('partner_id','customers_from',"openid = '$openid'");
		$number='M'.date('YmdHis').randStr(6);
		//echo

		$field = "`number`,`partner_id`,`realname`,`sex`,`createtime`,`mobile`";
		$value = "'$number','$partner_id','$realname','$sex','$createtime','$mobile'";	
		if(cloud_insert('customers',$field,$value)) 
		{
			$res = array('flag' => 1);
		}
	}

	//http://cloud.net:8083/plus/hhr_customers.php

}
die(json_encode($res));




	//生成随机�?
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
		$where = empty($where)?'':' WHERE '.$where;
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
//openid=oiITbswcXdpoX9Wx4t58hp3PDrdM&realname=1&sex=1&mobile=19763763166&back_url=/member/person_edit.php
//partner_id=1&realname=asdf&sex=1&province_txt=1&city_txt=1&mobile=1
//oiITbswcXdpoX9Wx4t58hp3PDrdM
//http://m.meilala.net/plus/hhr_customers.php
?>