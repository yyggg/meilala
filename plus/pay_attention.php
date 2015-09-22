<?php
    include_once '../weixin/weixin.auth_hhr.php';
    require_once '../inc/common_hhr.php';
    if (isset($_GET['s'])) {
        //echo $sid;
        $sid=$_GET['s'];
        $openid=!empty($_SESSION['openid'])?$_SESSION['openid']:'123456789';
        $time=time();
        $url="http://mp.weixin.qq.com/s?__biz=MzAxNzUwNzk3Ng==&mid=225623427&idx=1&sn=a69814e87637c29444dc3e3909992be0&scene=1&from=singlemessage&isappinstalled=0#rd";
        //echo 
        $sql = "SELECT * FROM partner_wx_infos where partner_id = '$sid' AND is_delete = 1 ";
        $sth = $db->prepare($sql);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        //print_r($data);


        $sql  = "SELECT * FROM customers_from where openid = '$openid' ";
        $sth = $db->prepare($sql);
        $sth->execute();
        $your_from = $sth->fetch(PDO::FETCH_ASSOC);
        //print_r($your_from);

        if(empty($your_from)){
            $sql  = "INSERT INTO customers_from (openid,partner_id,type,time) values('$openid','$data[partner_id]',1,'$time')";
            if($db->exec($sql)){
              echo('goods');

            }else{
              echo "error";
            }
        }
        else{
          echo '你已经注册过';
        }
        //exit;
        header('location:'.$url);
    }
?>





<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
