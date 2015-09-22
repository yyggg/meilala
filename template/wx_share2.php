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
        title: "<?php echo $share_data['title']?>", // 分享标题
        link:  "<?php echo $share_data['link']?>", // 分享链接
        //imgUrl: "http://m.meilala.net/plus/phpqrcode/img/ewm_<?php echo $partner_id; ?>.png", // 分享图标
        imgUrl: "<?php echo $share_data['imgUrl']?>", // 分享图标
        success: function () { 
            // 用户确认分享后执行的回调函数
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({
        title:  "<?php echo $share_data['title']?>", // 分享标题
        desc:   "<?php echo $share_data['desc']?>", // 分享描述
        link:   "<?php echo $share_data['link']?>", // 分享链接
        imgUrl: "<?php echo $share_data['imgUrl']?>", // 分享图标
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
        title:  "<?php echo $share_data['title']?>", // 分享标题
        desc:   "<?php echo $share_data['desc']?>", // 分享描述
        link:   "<?php echo $share_data['link']?>", // 分享链接
        imgUrl: "<?php echo $share_data['imgUrl']?>", // 分享图标
       success: function () { 
           // 用户确认分享后执行的回调函数
        },
        cancel: function () { 
           // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareWeibo({
        title:  "<?php echo $share_data['title']?>", // 分享标题
        desc:   "<?php echo $share_data['desc']?>", // 分享描述
        link:   "<?php echo $share_data['link']?>", // 分享链接
        imgUrl: "<?php echo $share_data['imgUrl']?>", // 分享图标
        success: function () { 
           // 用户确认分享后执行的回调函数
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });

});



</script>