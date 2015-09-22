<?php

if (strpos($_SERVER['HTTP_USER_AGENT'], "MicroMessenger")){	
	if (!empty($_GET['u']) && !empty($_GET['o'])) {
    	include_once '../weixin/weixin.auth.php';
    	require_once '../inc/common_hhr.php';	
		$back_url	=	$_GET['u'];
		$hrr_openid	=   $_GET['o'];
		$openid=!empty($_SESSION['wx_user']['openid'])?$_SESSION['wx_user']['openid']:'123456789';
		$partner_id=cloud_get_field('partner_id','partner_wx_infos'," openid ='$hrr_openid' AND is_delete = 1");
		$time=time();
		$url_data=parse_url($back_url);
		$type   =   dirname($url_data['path'])=='/goods'?2:1;//2对应是goods,1对应是活动
		$pid    =   $type==2?get_query('gid',$url_data['query']):get_query('aid',$url_data['query']);

		//判断是否浏览过
		$is_looked=cloud_get_field('id','partner_share_click_record','openid = \''.$openid.'\' AND pid = '.$pid.' AND type = '.$type);
		if(empty($is_looked)){
			//判断是否已经是其他人的粉丝
			if(!cloud_get_field('id','customers_from',"openid = '$openid'"))
			{
				//添加用户粉丝判断
				if(!cloud_insert('customers_from','openid,partner_id,type,time',"'$openid','$partner_id',2,'$time'")){
					echo "error";exti;
				}
			}
			//添加浏览记录

			if(!cloud_insert('partner_share_click_record','pid,type,openid,partner_id,back_url,time',"'$pid','$type','$openid','$partner_id','$back_url','$time'")){
				echo "error 2";exit;
			}
		}
		//echo "succese";
		header('location:'.$back_url);

	}
	else{
		echo "您访问的页面出错";exit;
	}

	//http://cloud.net:8083/plus/hhr_customers.php
	//http://cloud.net:8083/plus/hhr_share.php?o=oGZz6s3e_T0W0embYQ6zRH0ZiRXQ&u=http://m.meilala.net/goods/goods_detail.php?gid=151

}



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
		$where = empty($where)?'':' WHERE '.$where;
		echo
		$sql = "SELECT $field FROM $table $where";
		$sth = $db->prepare($sql);
		$sth->execute();
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		return $data;
	}

	function cloud_insert($table,$field,$value){
		global $db;
		//echo
		$sql = "INSERT INTO `$table` ($field) values ($value)";
		$sth = $db->prepare($sql);
		if($sth->execute()) {
			return true;
		}else{
			return false;
		}
	}

?>