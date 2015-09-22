<?php
if (!extension_loaded('php_sockets')||!extension_loaded('sockets')) {
	echo 111;
    if (!dl('sockets.so')) {
	echo 'success';
        exit;
    }
}else{
    echo '没有这个扩展';
}




?>

<?php 
$ip='http://meilala.taom.com.cn/';
$address = gethostbyname ($ip); 
        $command = "ping -c 1 " . $address;  
        $r = exec($command);  
          if ($r[0]=="r") 
          {        
            $socket = socket_create (AF_INET, SOCK_STREAM, 0); 
            if ($socket < 0) { 
                echo "socket_create() failed: reason: " . socket_strerror ($socket) . "n"; 
            } else { 
                echo "OK.n"; 
            } 
         }
?>