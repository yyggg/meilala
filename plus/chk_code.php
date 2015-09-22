<?php
session_start();

$res['stat']=0;

if($_POST['code']==$_SESSION["helloweba_num"]){
	$res['stat']=1;
}
die(json_encode($res));
exit;
?>