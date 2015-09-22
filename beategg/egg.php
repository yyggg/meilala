<?php
$stat=isset($_GET['stat'])?$_GET['stat']:1;
$title='砸金蛋';
if ($stat==1) {
	include './temp/empty_num.php';
}
elseif ($stat==2) {
	include './temp/pay_attention.php';
}
elseif ($stat==3) {
	include './temp/going.php';
}
elseif ($stat==4) {
	include './temp/gif.php';
}
else{
	include './temp/empty_num.php';
}


?>