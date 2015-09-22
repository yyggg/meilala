function fileOnchage(files){

	//document.upform.action = "./plus/post.php";
	//document.upform.submit();
	var file = files[0];
	if (window.navigator.userAgent.indexOf("Chrome") >= 1 || window.navigator.userAgent.indexOf("Safari") >= 1) { 
		var url = webkitURL.createObjectURL(file);
	}
	else { 
		//imgObjPreview.src = window.URL.createObjectURL(docObj.files[0]); 
		var url = URL.createObjectURL(file);
	} 
	/* 生成图片
	* ---------------------- */
	var $img = new Image();
	$img.onload = function() {
		//生成比例
		var width = $img.width,
				height = $img.height,
				scale = width / height;
		width = parseInt(800);
		height = parseInt(width / scale);
		//生成canvas
		var $canvas = $('#canvas');
		var ctx = $canvas[0].getContext('2d');		
		$canvas.attr({width : width, height : height});
		ctx.drawImage($img, 0, 0, width, height);
		var base64 = $canvas[0].toDataURL('image/jpeg',0.5);
		
		//发送到服务端
		//alert(222);
		$.post('plus/upload.php',{formFile : base64.substr(22) },function(data){
			//$('#php').html(data);
			var send_str="[img:"+data+":]";
			var new_str=send_str.replace("../","")
			$('#msger').val(new_str);
			//window.parent.document.getElementById("msger").value="[img:"+data+":]";
			//showImg();
			welive_send();
			//alert(data);
		});
	}
	$img.src = url;
}

function look_big_img(url){
	
	$('#big_img_box').css({'display':'block'});
	var url_arr= new Array(); 
	var url_arr=url.split('&i=');	
	//alert(url);
	$('#big_img_box').html('<img class="big_img" src="'+url_arr[1]+'" /><div class="big_img_close" id="big_img_close" onclick="close_big_img();">关闭</div>');

}


function close_big_img(){
	$('#big_img_box').css({'display':'none'});
}



function showImg() {
   var newMask = document.createElement("div"); //加载一个蒙层，防止点击
   newMask.style.position = "absolute";
   newMask.style.zIndex = "1";
   newMask.style.width = document.body.scrollWidth + "px"; //适应文档宽度
   newMask.style.height = document.body.clientHeight+"px";//适应文档高度
   newMask.style.top = "0px";
   newMask.style.left = "0px";
   newMask.style.backgroundColor = "#fff";//背景色
   newMask.style.filter = "alpha(opacity=40)";
   newMask.style.opacity = "0.40";//透明度
   newMask.style.display = "block";
   newMask.style.textAlign = "center";
   newMask.style.paddingTop = document.body.clientHeight / 2 + "px";
   document .append(newMask);
   $("body").append("<img  src='../images/jiazai2.gif'/>");
   var imgd = document.getElementsByTagName("img")[0];
   imgd.style.position = "absolute";//图片定位
   imgd.style.zIndex = "9999";
   imgd.style.top = document.body.clientHeight / 2+ "px";
   imgd.style.left = document.body.scrollWidth/2-50 + "px";
}
