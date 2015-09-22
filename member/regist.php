<?php
	header("Content-type: text/html; charset=utf-8");
    require_once '../inc/common.php';
    if (strpos($_SERVER['HTTP_USER_AGENT'], "MicroMessenger")){
        echo '本入口只对非微信版本开放';exit;
    }

	if(isset($_GET['send_sms']))
	{
		$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
		if(preg_match('/^1[34578][0-9]{9}$/',$mobile))
		{
			$code = rand('100000','999999');
			$url = "http://m.5c.com.cn/api/send/index.php?username=gzzsm&password_md5=".md5('asdf123')."&apikey=6285063b1cc5b110011ce7a74a5f4a01";
			$url .= "&mobile=$mobile&content=【美啦啦】您的验证码：" . $code . "，如非本人操作，请忽略";
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($curl);
			curl_close($curl);
			$_SESSION['reg_code'] = ['stime'=>time(),'code'=>$code];
		}
	}

    if($_POST){
        $res = array('flag'=>'0');
        $phone = htmlspecialchars($_POST['phone']);
		$smsCode = htmlspecialchars($_POST['smscode']);
        $password = md5(htmlspecialchars($_POST['pass']));
        $time = time();
		$sData = isset($_SESSION['reg_code']) ? $_SESSION['reg_code'] : '';
		
		//验证短信验证码
		if(!$sData)
		{
			$res['flag'] = -1;
			die(json_encode($res));
			//$this->flashSession->error('获取短信验证码失败，请重新获取');
		}
		if($time - $sData['stime'] > 60)
		{
			//$this->flashSession->error('短信验证码已失效，请重新获取');
			$res['flag'] = -2;
			die(json_encode($res));
		}
		if($sData['code'] != $smsCode)
		{
			//$this->flashSession->error('短信验证码不正确，请重新输入');
			$res['flag'] = -3;
			die(json_encode($res));
		}
		unset($_SESSION['reg_code']);
		
		

        $sql = "SELECT * FROM m_member WHERE phone = '$phone' ";        
        $sth = $db->prepare($sql);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        if (empty($data)) {
            $sql = "INSERT INTO m_member (name,phone,password,jifen) value('$phone','$phone','$password',5)";
            if($db->exec($sql)) 
            {               
                $res['flag'] = 1;
                $sql = "SELECT mid FROM m_member WHERE phone = '$phone'";
                $sth = $db->prepare($sql);
                $sth->execute();   
                $user=$sth->fetch(PDO::FETCH_ASSOC);   

                $sql = "INSERT INTO m_jifen_logs SET mid = '$user[mid]', gtime = '$time', point = '5', income='1', remark='注册获得积分', count = '5'";
                $sth = $db->prepare($sql);
                $sth->execute();
                $db->lastInsertId();

                $_SESSION['mid']=$user['mid'];
            }
            else
            {
                $res['flag'] = '0';        
            } 
        }else{
            $res['flag'] = '2';
        }
        die(json_encode($res));
    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户注册</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/common2.css" type="text/css" media="all">
<link rel="stylesheet" href="../css/lvshi.css" type="text/css" media="all">
    <script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
    <script src="../js/mobile.js" type="text/javascript"></script>
</head>
<style type="text/css">
.yzm {position: relative;}
  .yzm .yzm_input,.yzm  .yzm_input_code{   background-color: #EFEFEF; border: 0rem; width: 90%; margin:  0.5rem 5%; border-radius: 0.3rem; height: 2.5rem; line-height: 2.5rem; box-sizing:border-box; padding: 0 0.5rem;}
  .yzm .yzm_button,.yzm .yzm_button_code { position: absolute; width: 5rem; right: 5%; top: 0.5rem; height: 2.5rem; background: #673f88; color: #fff; border: 0rem;} 
  .yzm  .yzm_button_code img    { height: 2.5rem; width: 5rem;}
</style>
<body>
<!--header-->
<header class="header">
	<div class="header_back"><a href="#"></a></div>
    <h2>用户注册</h2>
</header>
<!--header end-->
<!--content-->
<div class="content">
<div class="zc_bg">
	<div class="zc_block">
    	<input id="phone" type="tel" class="zc_sjhm" value="" placeholder="请输入您的手机号码"/>
    </div>
    <div class="zc_block yzm">
      <input type="tel" placeholder="请输入图形验证码" value="" class="yzm_input_code" id="code_num" name="code_num" maxlength="4">
      <div class="yzm_button_code "><!--<img src="../images/aa_yzm.png" alt="" />--><img src="../plus/code_num.php" id="getcode_num" title="看不清，点击换一张" align="absmiddle"/></div>
    </div>       
    <div class="zc_block yzm">
      <input type="tel" placeholder="请输入手机验证码" value="" class="yzm_input">
      <input type="button" class="yzm_button" value="获取验证码">
    </div>   
    
    <div class="zc_block" style="margin-top:1px; border-top:1px solid #EAEAEA;">
    	<input id="pass" type="password" class="zc_sjhm" value="" placeholder="请输入您的密码"/>
    </div>
    <div class="zc_block" style="margin-top:1px; border-top:1px solid #EAEAEA;">
        <input id="repass" type="password" class="zc_sjhm" value="" placeholder="请再次输入您的密码"/>
    </div>    
     <div class="zc_block02">
    	<input id="checkbox" type="checkbox" class="zc_xuanze" checked="checked" />
        <span class="xieyi">我已阅读并同意<a href="#">《美啦啦用户协议》</a></span>
    </div>
    <div class="zc_block02">
       <a href="javascript:" class="zc_tijiao" onclick="regist()" >提交</a>
    </div>
    <div class="cl"></div>
</div>
</div>
<!--content end-->
<script type="text/javascript">

	$('.yzm_button').on('click',function(){
    
      var code_num = $("#code_num").val();
      var mobile = $('#phone').val();
      var myreg = /^1[34578][0-9]{9}$/; 


      $.post("../plus/chk_code.php", {code: code_num}, function(res) {
         if (res['stat'] == 1) {
            if(!myreg.test(mobile)) 
                { 
              alert('请输入正确的手机号码');
              return;
            }
            $.post('regist.php?send_sms',{mobile:mobile}, function(data){
              
            });
            time($(this)); 
        } else {
          alert('请输入正确的图形验证码');  
        }
      }, 'json');
   
	});
	
	
	
	var wait = 60;
	function time(o) {
		if (wait == 0) {
			o.attr("disabled",false);
			o.css('background','#673f88');
			o.val("重新发送");
			wait = 60;
		} else {
			o.css('background','#ccc');
			o.val('重发（' + wait+'）');
			o.attr("disabled", true);
			wait--;
			setTimeout(function() {
				time(o);
			}, 1000);
		};
	}



    function regist()
    {
        
        var phone = $('#phone').val();
        var pass = $('#pass').val();
		    var yzm_input =  $('.yzm_input').val();

        if (validatemobile(phone) && validatepass(pass) && check_pass() && agreement () ) {
            $.post('regist.php',{phone:phone,pass:pass,smscode:yzm_input}, function(res){
                if(res.flag==1)
                {
                    //alert('注册成功');
                    //window.location.href='person.php';
                    delay_alert('注册成功','person.php');
                }
                else if(res.flag==2){
                    alert('您的手机号已经注册了');
                }
                else if(res.flag == -1){
                    alert('获取短信验证码失败，请重新获取');  
                }
				else if(res.flag == -2){
                    alert('短信验证码已失效，请重新获取');  
                }
				else if(res.flag == -3){
                    alert('短信验证码不正确，请重新输入');  
                }
				else{
                    alert('注册失败');  
                }

            }, 'json');
        }
    }  

    function agreement () {
        if($("input[type='checkbox']").is(':checked')==true){
            return true; 
        }else{
            alert('请同意注册'); 
            return false; 
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
        
       var myreg = /^1[34578][0-9]{9}$/; 
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

    function check_pass(){
        if($('#pass').val() != $('#repass').val())
        {
           alert('两次输入的密码不一样'); 
           $('#pass').focus(); 
           return false;             
        }
        else{
            return true; 
       }       
       
    }


  $(function() {
    $("#getcode_num").click(function() { //数字验证
      $(this).attr("src", '../plus/code_num.php?' + Math.random());
    });
  });

</script>
</body>
</html>
