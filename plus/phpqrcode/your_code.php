<?php

require_once '../../inc/common_hhr.php';
include '../../weixin/weixin_config.php';
include  '../jssdk.php';
$jssdk = new JSSDK($appid, $appsecret);
$signPackage = $jssdk->GetSignPackage();     


if (!empty($_GET['pid'])) {
    $pid=$_GET['pid'];

    $sql = "SELECT a.partner_id ,a.openid FROM partner_wx_infos as a left join partners as b on a.partner_id=b.id where a.openid = '$pid' AND b.is_delete = 1 ";
    $sth = $db->prepare($sql);
    $sth->execute();
    $data = $sth->fetch(PDO::FETCH_ASSOC);
    //echo
    $partner_id=$data['partner_id'];
    //print_r($data);
    if (empty($partner_id)){
        echo "您访问的网址出错";exit;
    }
}
else{
    header('location:/');
}



if(!is_file('img/ewm_'.$partner_id.'.png')){

        //文件输出
        include('phpqrcode.php');
        // 二维码数据
        $data = 'http://m.meilala.net/plus/pay_attention.php?s='.$partner_id;
        // 生成的文件名
        $filename = 'ewm_'.$partner_id.'.png';
        $logo = 'img/logo.png';//准备好的logo图片        
        // 纠错级别：L、M、Q、H
        $errorCorrectionLevel = 'H';

        // 点的大小：1到10
        $matrixPointSize = 4;
        QRcode::png($data, 'img/'.$filename, $errorCorrectionLevel, $matrixPointSize, 2);

        $QR='img/'.$filename;
        if ($logo !== FALSE) {
            
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
             $from_width = ($QR_width - $logo_qr_width) / 2;
                            
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
            $logo_qr_height, $logo_width, $logo_height);
        }
        imagepng($QR, 'img/'.'ewm_'.$partner_id.'.png');
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<title>您的推荐二维码</title>
<script src="../../js/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="http://s4.cnzz.com/stat.php?id=1255877391&web_id=1255877391" language="JavaScript"></script>
<link rel="stylesheet" href="../../css/common.css" type="text/css" media="all">
<style>
body{font-family:微软雅黑;background-color:#fff; width: 100%;}
section     { position: relative; border: 0rem; }
.content    { max-width: 640px; padding: 0; border: 0; margin: 0rem;}
.st1        { height: 2.5rem; line-height: 2.5rem; margin-top: 3rem; }
.st1_span   { color: #3a3a3a; width: 100%; text-align: center; position: relative;z-index: 10; }
.st1_span span{ background: #fff; padding: 0rem 0.5rem;}
.st1_line   { width: 95%; position: absolute; left: 2.5%; height: 1px ; background: #e5e5e5; top: 1.25rem; }
.st2_ct1    { color: #999; position: relative; margin-top: 0.5rem;}
.st2_ct2    { position: relative; margin-top: 2rem; text-align: center;}
.st2_ct2    { position: relative;}
.st2_ct2_img{}
.st2_ct2_logo{ display: none; position: absolute; left: 50%; width: 30px;  top: 50%; margin-left: -15px; margin-top: -15px; border: 1px solid #999; border-radius: 5px;}
.st3        { margin-top: 1.5rem;}
.st4        { position: relative; margin-top: 1rem;}
.st4 button { height: 2.5rem; line-height: 2.5rem; width: 90%;  margin: 0 5%; background: #1e9c0d;  border-radius: 0.4rem; border: 0rem; font-size: 1rem; color: #fff; font-weight: bolder; }
.share_box  { width: 100%; height: 100%; position: fixed; top: 0rem; left: 0rem; background: rgba(0,0,0,0.7); z-index: 100; text-align: center;}
.share      { color: #fff; margin-top: 50%; font-size: 1rem; font-weight: bolder;}
#loadingDiv {position:fixed;left:0;width:100%;height:100%;top:0;background:rgba(0,0,0,0.1);z-index:10000;}
.loadingDiv_box{ text-align: center; position: absolute; background:rgba(0,0,0,0.4);width: 200px; border-radius: 6px; height: 60px; margin-left:-100px;margin-top:-30px; line-height: 60px; cursor1: wait; left:50%; top:50%; color: #fff; font-family:'Microsoft YaHei';}

a[href='http://www.cnzz.com/stat/website.php?web_id=1255877391']{display: none;}
</style>
</head>



<body>
<div class="content">
    <section class="st1">
        <div class="st1_span"><span>推荐专属二维码</span></div>
        <div class="st1_line"></div>
    </section>
    <section class="st2"> 
        <div class="st2_ct1 st1_span">被推荐朋友通过微信扫码，即可成为您的粉丝团</div>
        <div class="st2_ct2">
            <div class="st2_ct2_img"><img src="img/ewm_<?php echo $partner_id ?>.png" /><img src="img/logo.png" class="st2_ct2_logo" /></div>
        </div>
    </section>
    <section class="st1 st3">
        <div class="st1_span"><span>社交分享</span></div>
        <div class="st1_line"></div>
    </section>
    <section class="st4">
        <button onclick="share_your();" id="onMenuShareAppMessage">分享给好友</button>
    </section>


</div>
<script type="text/javascript">
    function share_your () {
        var html=   '<div class="share_box bxz" id="share_box" onclick="share_cancel()">'+
                        '<div class="share  bxz">'+
                            '点击右上角分享给朋友'
                        '</div>'+
                    '</div>'
        $("body").append(html);
    }
    function share_cancel () {
        $("#share_box").remove();       
    }




</script>


<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script language="javascript" type="text/javascript">
wx.config({
    debug: false,//这里是开启测试，如果设置为true，则打开每个步骤，都会有提示，是否成功或者失败
    appId:      '<?php echo $signPackage["appId"];?>',
    timestamp:   <?php echo $signPackage["timestamp"];?>,//这个一定要与上面的php代码里的一样。
    nonceStr:   '<?php echo $signPackage["nonceStr"];?>',//这个一定要与上面的php代码里的一样。
    signature:  '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo'
    ]
});
wx.ready(function () {
    wx.onMenuShareTimeline({
        title: "美啦啦，您的微整管家！", // 分享标题
        link: "<?php echo 'http://m.meilala.net/plus/pay_attention.php?s='.$partner_id; ?>", // 分享链接
        //imgUrl: "http://m.meilala.net/plus/phpqrcode/img/ewm_<?php echo $partner_id; ?>.png", // 分享图标
        imgUrl: "http://m.meilala.net/plus/phpqrcode/logo.jpg", // 分享图标
        success: function () { 
            // 用户确认分享后执行的回调函数
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({
        title: "美啦啦，您的微整管家！", // 分享标题
        desc: "美啦啦，您的微整管家，让您安全、便捷、无忧的变美。想美哪里美哪里，美你所想！", // 分享描述
        link: "<?php echo 'http://m.meilala.net/plus/pay_attention.php?s='.$partner_id; ?>", // 分享链接
        imgUrl: "http://m.meilala.net/plus/phpqrcode/logo.jpg", // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () { 
            // 用户确认分享后执行的回调函数
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareQQ({
        title: "美啦啦，您的微整管家！", // 分享标题
        desc: "美啦啦，您的微整管家，让您安全、便捷、无忧的变美。想美哪里美哪里，美你所想！", // 分享描述
        link: "<?php echo 'http://m.meilala.net/plus/pay_attention.php?s='.$partner_id; ?>", // 分享链接
        imgUrl: "http://m.meilala.net/plus/phpqrcode/logo.jpg", // 分享图标
       success: function () { 
           // 用户确认分享后执行的回调函数
        },
        cancel: function () { 
           // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareWeibo({
        title: "美啦啦，您的微整管家！", // 分享标题
        desc: "美啦啦，您的微整管家，让您安全、便捷、无忧的变美。想美哪里美哪里，美你所想！", // 分享描述
        link: "<?php echo 'http://m.meilala.net/plus/pay_attention.php?s='.$partner_id; ?>", // 分享链接
        imgUrl: "http://m.meilala.net/plus/phpqrcode/logo.jpg", // 分享图标
        success: function () { 
           // 用户确认分享后执行的回调函数
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });
});
</script>

</body>
</html>


