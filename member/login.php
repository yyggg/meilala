
<?php
    require_once '../inc/common.php';
    if (strpos($_SERVER['HTTP_USER_AGENT'], "MicroMessenger")){
        echo '本入口只对非微信版本开放';exit;
    }
    if($_POST){
        $res = array('flag'=>'0');
        $phone = htmlspecialchars($_POST['phone']);
        $password = md5(htmlspecialchars($_POST['pass']));
        $time   = time();

        $sql = "SELECT mid FROM m_member WHERE phone = '$phone' AND password =   '$password' ";        
        $sth = $db->prepare($sql);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $res['flag'] = '1';
            $_SESSION['mid']=$data['mid'];
        }else{
            $res['flag'] = '0';
        }
        die(json_encode($res));
    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户登陆</title>
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
    <h2>用户登录</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">

<div class="zc_bg">
	<div class="zc_block">
    	<input type="text" id="phone" class="zc_sjhm" value="" placeholder="请输入您的手机号"/>
    </div>
    <div class="zc_block" style="margin-top:1px; border-top:1px solid #EAEAEA;">
    	<input id="pass" type="password" class="zc_sjhm" value="" placeholder="请输入您的密码"/>
    </div>
   
    <div class="zc_block02">
       <a href="javascript:" class="zc_tijiao" onclick="login()" >登录</a>
    </div>
    
    <div class="zc_block03">
        <span class="xieyi"><a href="../member/regist.php">立即注册</a></span>
        
    </div>
   
    <div class="cl"></div> 
</div>
</div>
<!--content end-->
<script type="text/javascript">
    function login()
    {
        
        var phone = $('#phone').val();
        var pass = $('#pass').val();    
        if (validatemobile(phone) && validatepass(pass) ) {   
            $.post('login.php',{phone:phone,pass:pass}, function(res){
                    if(res.flag==1)
                    {
                        //alert('登录成功');
                        //window.location.href='person.php';
                        delay_alert('登录成功','person.php')
                    }
                    else 
                    {
                        alert('请检查手机或密码是否正确');  
                    }

            }, 'json');
        }
    }    


    function validatemobile(mobile) 
    { 
       if(mobile.length==0) 
       { 
          alert('请输入手机号码！'); 
          $('#phone').focus(); 
          return false; 
       }     
       if(mobile.length!=11) 
       { 
           alert('请输入有效的手机号码！'); 
           $('#phone').focus(); 
           return false; 
       } 
        
       var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/; 
       if(!myreg.test(mobile)) 
       { 
           alert('请输入有效的手机号码！'); 
           $('#phone').focus(); 
           return false; 
       } 
       else{
            return true; 
       }
    }

    function validatepass(pass) 
    { 
       if(pass.length==0) 
       { 
          alert('请输入密码'); 
          $('#pass').focus(); 
          return false; 
       }     
       if(pass.length<6) 
       { 
           alert('密码长度不能小于6位'); 
           $('#pass').focus(); 
           return false; 
       } 
        else{
            return true; 
       }       

    }    

</script>
</body>
</html>
