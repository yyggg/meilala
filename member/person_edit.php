<?php 
	/*个人资料修改*/
	require_once '../inc/common.php';
	$data = getUser();
	$mid = $data['mid'];
	$data['headimgurl']=empty($data['headimgurl'])?'../images/touxiang.png':$data['headimgurl'];
	$data['openid']=empty($data['openid'])?'oiITbswcXdpoX9Wx4t58hp3PDrdM':$data['openid'];
	$local_url=$_SERVER['PHP_SELF'];

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
	
	if($_POST)
	{
		$res = array('flag' => 0);
		$name = (string)$_POST['name'];
		$phone = $_POST['phone'];
		$email = (string)$_POST['email'];
		$qq = (int)$_POST['qq'];
		$sex = (string)$_POST['sex'];
		$birthday = strtotime((string)$_POST['birthday']);
		$smsCode = (int)$_POST['smscode'];
		
		$sData = isset($_SESSION['reg_code']) ? $_SESSION['reg_code'] : '';
		
		//验证短信验证码
		if(!$sData)
		{
			$res['flag'] = -1;
			die(json_encode($res));
		}
		if($time - $sData['stime'] > 60)
		{
			$res['flag'] = -2;
			die(json_encode($res));
		}
		if($sData['code'] != $smsCode)
		{
			$res['flag'] = -3;
			die(json_encode($res));
		}
		unset($_SESSION['reg_code']);
		
		
		$sql = "UPDATE m_member SET name='$name', phone='$phone', sex='$sex', birthday='$birthday', email='$email', qq='$qq' WHERE mid = '$mid'";
		$rows = $db->exec($sql);
		if($rows)
		{
			$res['flag'] = 1;
		}
		die(json_encode($res));
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>个人资料</title>
<meta name="keywords" content="美啦啦，整形美容" />
<meta name="description" content="美啦啦，整形美容" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/common.css" type="text/css" media="all">
<script src="../js/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="../js/mobile.js" type="text/javascript"></script>
</head>

<body class="person_edit">
<!--header-->
<header class="header">
	<div class="header_back"><a href="/member/space.php"></a></div>
    <h2>资料修改</h2>
    <div class="header_right"><a href="javascript:personSave();"></a></div>
</header>
<!--header end-->
<!--content-->
<div class="content mb10">
	<ul>
    	<li class="my_li my_varify pt10 pb10"><span class="my_keywords"><b>*</b>头像</span><span class="my_value"><img src="<?php echo $data['headimgurl'];?>" /></span><input type="file" id="my_varify" unat="server" accept="image/*" capture="camera"/><canvas id="canvas"></canvas></li>
        <li class="my_li"><span class="my_keywords"><b>*</b>昵称</span><input class="my_value" id="name" value="<?php echo $data['name'];?>"/></li>
        <li class="my_li"><span class="my_keywords"><b></b>邮箱</span><input type="email" class="my_value" id="email" value="<?php echo $data['email'];?>"/></li>
        <li class="my_li mt10"><span class="my_keywords"><b></b> Q Q </span><input type="tel" class="my_value" id="qq" value="<?php if($data['qq']) echo $data['qq'];?>"/></li>
        <li class="my_li"><span class="my_keywords"><b>*</b>手机</span><input type="tel" class="my_value" id="phone" value="<?php echo $data['phone'];?>"/></li>
        <li class="my_li">
        	<span class="my_keywords" style="float:left;"><b>*</b>验证码</span>
        	<input type="tel" class="my_value" id="smscode" placeholder="请输入手机验证码" style=" margin-left:28%;width:40%; position:static; "/>
        	<input type="button" class="yzm_button" value="获取验证码" style=" width:100px;float:right; margin-right:10px; margin-top:10px;">
        </li>
        <li class="my_li mt10 ">
	        <span class="my_keywords"><b>*</b>性别</span>
	        <span class="my_value no_border ">
            	<div class="my_sex" >
		        <input type="radio" name="sex" class="sex_radio" value="1" <?php if($data['sex'] == '1') echo 'checked' ;?> /><span>男</span>
                </div>
                <div class="my_sex">
				<input type="radio" name="sex" class="sex_radio" value="0" <?php if($data['sex'] == '0') echo 'checked' ;?> /><span>女</span>
                </div>
			</span>
		</li>
        <li class="my_li"><span class="my_keywords"><b>*</b>生日</span><input class="my_value" id="birthday" type="date" value="<?php echo date('Y-m-d',$data['birthday']);?>"/></li>
        <!--<li class="my_li"><span class="my_keywords"><b>*</b>爱好</span><input type="text" class="my_value" id="hobby" value="<?php echo $data['hobby'];?>"/></li>-->
    </ul>
</div>
<!--content end-->
<!--footer-->
<?php include_once ('../template/footer.php');?>
<!--footer end-->
<script>
	$('.yzm_button').on('click',function(){
		  var mobile = $('#phone').val();
		  var myreg = /^1[34578][0-9]{9}$/;
		  if(!myreg.test(mobile)) 
          { 
              alert('请输入正确的手机号码');
			  $('#phone').focus(); 
              return;
          }
		  $.post('person_edit.php?send_sms',{mobile:mobile}, function(data){
			 
		  });
		  time($(this)); 
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
		
		
		
	function personSave()
	{
		var content = $('.content');
		var name = content.find('#name').val();
		var phone = content.find('#phone').val();
		var email = content.find('#email').val();
		var qq = content.find('#qq').val();
		var sex = $('input:radio[type="radio"]:checked').val();
		var smscode = content.find('#smscode').val();
		if(sex == undefined) sex = 2;
		
		var birthday = content.find('#birthday').val();
		var hobby = content.find('#hobby').val();
		if(validatemobile(phone)){
			$.post('person_edit.php',{name:name,phone:phone,smscode:smscode,email:email,qq:qq,sex:sex,birthday:birthday,hobby:hobby}, function(res){
				if(res.flag == 1)
				{
					//alert('修改成功');
					//window.location.href='person.php';
					hhr_customers();
					delay_alert('修改成功','person.php');
					//return 1;
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
				else 
				{
					alert('修改失败');	
					//return 0;
				}
			}, 'json');
		}
	}

	function hhr_customers()
	{
		var openid ="<?php echo $data['openid'] ?>";
		var realname = $('#name').val();
		var mobile = $('#phone').val();
		var sex = $('input:radio[type="radio"]:checked').val();
		if(sex == undefined) sex = 2;		
		var back_url= "<?php echo $local_url ?>"
		$.post('../plus/hhr_customers.php',{openid:openid,realname:realname,sex:sex,mobile:mobile,back_url:back_url}, function(res){
			if(res.flag)
			{

				//delay_alert('修改成功','person.php');
				return 1;
			}
			else 
			{
				//alert('修改失败');	
				return 0;
			}
		}, 'json');
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


</script>
</body>
</html>
