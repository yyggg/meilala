<?php
require_once '../inc/common.php';


/* 分享增加砸蛋次数 */
if(isset($_GET['add_num']))
{
	$openid = (string)$_GET['openid'];
	$sql = "UPDATE m_beat_egg_restrict SET `num` = `num`+1 WHERE `num` < 2 AND `openid` = '$openid' LIMIT 1";
	$sth = $db->prepare($sql);
	$sth->execute();
	die(json_encode($sth->rowCount()));
}
