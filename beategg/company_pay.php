<?php
/* $mchid = '1248900901'; //商户号
$appid = 'wx7a5ba916e3074fd7';
$payurl = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers'; //接口请求URL
$no = substr(uniqid(),3); //随机字符串

//签名参与参数
$parameters = [
		'partner_trade_no' => $mchid . date('Ymd') . $no, //商户订单号
		'mchid' => $mchid, //商户号
		'mch_appid' => $appid, //公众账号appid
		'openid' => 'oiITbs6ygNTcRBjcyyaqb1CP-dsI', //用户id
		'check_name' => 'NO_CHECK', //校验用户姓名
		'amount' => '1', //付款金额
		'desc' => '抢到了红包', //
		'spbill_create_ip' => '124.172.237.174', //
		'nonce_str' => createNoncestr(), //随机字符串，小于32位
];

$sign = getSign($parameters);//获取签名
$parameters['sign'] = $sign;
$xml = arrayToXml($parameters); //生成请求XML */
//$res = postXmlSSLCurl($xml, $payurl);

/**
 * 	作用：生成签名
*/
function getSign($Obj)
{
	foreach ($Obj as $k => $v)
	{
		$Parameters[$k] = $v;
	}
	//签名步骤一：按字典序排序参数
	ksort($Parameters);
	$String = formatBizQueryParaMap($Parameters, false);
	//echo '【string1】'.$String.'</br>';
	//签名步骤二：在string后加入KEY
	$String = $String."&key=meilala123meilala123meilala12345"; //美啦啦KEY
	//echo "【string2】".$String."</br>";
	//签名步骤三：MD5加密
	$String = md5($String);
	//echo "【string3】 ".$String."</br>";
	//签名步骤四：所有字符转为大写
	$result_ = strtoupper($String);
	//echo "【result】 ".$result_."</br>";
	return $result_;
}

/**
* 	作用：格式化参数，签名过程需要使用
*/
function formatBizQueryParaMap($paraMap, $urlencode)
{
	$buff = "";
	ksort($paraMap);
	foreach ($paraMap as $k => $v)
	{
	if($urlencode)
	{
		$v = urlencode($v);
		}
		//$buff .= strtolower($k) . "=" . $v . "&";
		$buff .= $k . "=" . $v . "&";
	}
	$reqPar;
	if (strlen($buff) > 0)
	{
		$reqPar = substr($buff, 0, strlen($buff)-1);
	}
	return $reqPar;
}

/**
* 	作用：产生随机字符串，不长于32位
		*/
function createNoncestr( $length = 32 )
{
	$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
	$str ="";
	for ( $i = 0; $i < $length; $i++ )  {
		$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
	}
	return $str;
}

/**
* 	作用：array转xml
*/
function arrayToXml($arr)
{
	$xml = "<xml>";
	foreach ($arr as $k=>$val)
	{
		$xml.="<".$k.">".$val."</".$k.">";
	}
	$xml.="</xml>";
	return $xml;
}

/**
 * 	作用：将xml转为array
 */
function xmlToArray($xml)
{
	//将XML转为array
	$array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	return $array_data;
}


/**
 * 	作用：以post方式提交xml到对应的接口url
 */
function postXmlSSLCurl($xml,$url,$second=30)
{
	$ch = curl_init();
	//超时时间
	curl_setopt($ch,CURLOPT_TIMEOUT,$second);
	//这里设置代理，如果有的话
	//curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
	//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
	//设置header
	curl_setopt($ch,CURLOPT_HEADER,FALSE);
	//要求结果为字符串且输出到屏幕上
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
	//设置证书
	//使用证书：cert 与 key 分别属于两个.pem文件
	//默认格式为PEM，可以注释
	curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
	curl_setopt($ch,CURLOPT_SSLCERT, '../weixin/wxpay/WxPayPubHelper/cacert/apiclient_cert.pem');
	//默认格式为PEM，可以注释
	curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
	curl_setopt($ch,CURLOPT_SSLKEY, '../weixin/wxpay/WxPayPubHelper/cacert/apiclient_key.pem');
	//post提交方式
	curl_setopt($ch,CURLOPT_POST, true);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
	$data = curl_exec($ch);
	//返回结果
	if($data){
		curl_close($ch);
		return $data;
	}
	else {
		$error = curl_errno($ch);
		echo "curl出错，错误码:$error"."<br>";
		echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
		curl_close($ch);
		return false;
	}
}