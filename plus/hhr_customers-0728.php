<?php
$res = array('flag' => 0);
if (strpos($_SERVER['HTTP_USER_AGENT'], "MicroMessenger") || 1){

    require_once '../inc/common_hhr.php';
    $openid		=	$_POST['openid'];
    $realname	=	$_POST['realname'];
    $sex		=	$_POST['sex'];
    //$province_txt=	$_POST['province_txt'];
    //$city_txt	=	$_POST['city_txt'];
    $mobile		=	$_POST['mobile'];
    $back_url 	=   $_POST['back_url'];
    $createtime	=	time();
    

	$sql = "SELECT partner_id FROM customers_from WHERE openid = '$openid'";
	$sth = $db->prepare($sql);
	$sth->execute();
	$data = $sth->fetch(PDO::FETCH_ASSOC);
	$partner_id=empty($data)?'0':$data['partner_id'];
	$number='M'.date('YmdHis').randStr(6);
    echo
    $sql = "INSERT INTO `customers` (`number`,`partner_id`,`realname`,`sex`,`createtime`,`mobile`) value ('$number','$partner_id','$realname','$sex','$createtime','$mobile')";
	//调用prepare方法准备查询
	$sth = $db->prepare($sql);

	if($sth->execute()) 
	{
		$res = array('flag' => 1);
	}else{
		$res = array('flag' => 0);
	}
}
die(json_encode($res));

	//生成随机数
	function randStr($len=4) {   
		$chars='1234567890'; // characters to build the password from   
		mt_srand((double)microtime()*1000000*getmypid()); // seed the random number generater (must be done)   
		$str='';   
		while(strlen($str)<$len)   
			$str.=substr($chars,(mt_rand()%strlen($chars)),1);   
		return $str;   
	} 


//openid=oiITbswcXdpoX9Wx4t58hp3PDrdM&realname=1&sex=1&mobile=19763763166&back_url=/member/person_edit.php
//partner_id=1&realname=asdf&sex=1&province_txt=1&city_txt=1&mobile=1
//oiITbswcXdpoX9Wx4t58hp3PDrdM
//http://m.meilala.net/plus/hhr_customers.php
?>