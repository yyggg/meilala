
<?php

    require_once '../inc/common.php';
    include_once("../weixin/wxpay/WxPayPubHelper/WxPayPubHelper2.php");




    $sql = "SELECT * FROM m_red_packet where is_open =1";        
    $sth = $db->prepare($sql);
    $sth->execute();
    $red_packect = $sth->fetch(PDO::FETCH_ASSOC);
    print_r($red_packect);

    //$jsApi = new JsApi_pub();
    //echo $jsApi->getSign();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>获取红包</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/common2.css" type="text/css" media="all">
<link rel="stylesheet" href="../css/lvshi.css" type="text/css" media="all">
    <script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
    <script src="../js/mobile.js" type="text/javascript"></script>
</head>

<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="#"></a></div>
    <h2>获取红包</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">

<div class="zc_bg">

    <div class="zc_block02">
       <a href="javascript:" class="zc_tijiao" onclick="post_red_packect()" >获取红包</a>
    </div>

   
    <div class="cl"></div> 
</div>
</div>
<!--content end-->
<script type="text/javascript">
    function post_red_packect()
    {

        var submitData = {
            nonce_str   : "<?php echo $red_packect['nonce_str'] ;?>",
            sign        : "<?php echo $red_packect['sign'] ;?>",
            mch_billno  : "<?php echo $red_packect['mch_billno'] ;?>",
            mch_id      : "<?php echo $red_packect['mch_id'] ;?>",
            sub_mch_id  : "<?php echo $red_packect['sub_mch_id'] ;?>",
            wxappid     : "<?php echo $red_packect['wxappid'] ;?>",
            nick_name   : "<?php echo $red_packect['nick_name'] ;?>",
            send_name   : "<?php echo $red_packect['send_name'] ;?>",
            re_openid   : "<?php echo $red_packect['re_openid'] ;?>",
            total_amount: "<?php echo $red_packect['total_amount'] ;?>",
            min_value   : "<?php echo $red_packect['min_value'] ;?>",
            max_value   : "<?php echo $red_packect['max_value'] ;?>",
            total_num   : "<?php echo $red_packect['total_num'] ;?>",
            wishing     : "<?php echo $red_packect['wishing'] ;?>",
            client_ip   : "<?php echo $red_packect['client_ip'] ;?>",
            act_name    : "<?php echo $red_packect['act_name'] ;?>",
            remark      : "<?php echo $red_packect['remark'] ;?>",
            logo_imgurl : "<?php echo $red_packect['logo_imgurl'] ;?>",
            share_content:"<?php echo $red_packect['share_content'] ;?>",
            share_url   : "<?php echo $red_packect['share_url'] ;?>",
            share_imgurl: "<?php echo $red_packect['share_imgurl'] ;?>"

        };

        //document.write(submitData);

        if (1) {   
            $.post('https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack',submitData, function(res){
                    if(res)
                    {
                        //alert('登录成功');
                        //window.location.href='person.php';
                        delay_alert(res,'person.php')
                    }
                    else 
                    {
                        alert(res);  
                    }

            }, 'json');
        }
    }    



</script>
</body>
</html>
