<?php
    include_once '../weixin/weixin.auth_hhr.php';
    require_once '../inc/common_hhr.php';
    if (isset($_GET['s'])) {
        //echo $sid;
        $sid=$_GET['s'];
        $openid=!empty($_SESSION['openid'])?$_SESSION['openid']:'123456789';
        $time=time();
        $url="http://mp.weixin.qq.com/s?__biz=MzAxNzUwNzk3Ng==&mid=223944016&idx=1&sn=48d3e60ba805ce2bff8b116849c39625&scene=1&key=0acd51d81cb052bc39273a9f5ad9fc8b052cd654299298f13fd0ca7007e5d9be23fb48c5c86910515f44390d839863dc&ascene=1&uin=MTc5MjE1NjgyNg%3D%3D&devicetype=Windows-QQBrowser&version=61001201&pass_ticket=6uTUkFDFEggtVWfkMP6gjI6MtWlyvvWXcLMjz6pSsnNB2oiDiX0hzrP3ph3XSxiW";
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
