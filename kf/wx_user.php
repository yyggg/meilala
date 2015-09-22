<?php
error_reporting(E_ALL & ~E_NOTICE);
header("Content-type: text/html; charset=utf-8"); 
if($_POST){
    $openid=$_POST['openid'];
    $gid=$_POST['gid'];
    include('../weixin/Connect.class.php');
    $db =  new Connect();
    $openid=empty($openid)?'123456789':$openid;
    $gid=empty($gid)?'1':$gid;
    //echo
    $sql="update mkf_guest set openid = '$openid' where gid = '$gid'";
    $db->query($sql);
}

?>