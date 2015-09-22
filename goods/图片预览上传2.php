<?php
    if($_POST)
	{
		echo $_POST['content'];	
	}
	if($_FILES)
	{
		echo "<pre>";
		print_r($_FILES);
		
		//echo $_GET['aaa'];	die;
	}
	
?>
<!DOCTYPE html>
<head>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
<title>无标题文档</title>
	
	<script type="text/javascript" src="../js/jquery-1.11.2.min.js"></script>

<style>
#img_view_list img {
	width:50px;
	height:50px;
}
</style>
</head>

<body>


 
 
 
 
	
<form method="post" id="uploadForm">
    <input type="file" name="files[]" id="files" multiple/>
    <br><br><br>
    <div id="img_view_list">
    	
    </div>
    <textarea id="content">5555</textarea>
    <input type="button" id="submit"  value="提交">
</form>
<script>	

 
var storedFiles = [];
$("body").on("click", ".selFile", removeFile);
$("#submit").on("click", handleForm);
/*$('#submit').click(function(){
	
	$('#uploadForm').ajaxSubmit(function(responseResult){
		console.log(responseResult);
	});
});*/

function handleForm(e) {
   e.preventDefault(); 
   var data = new FormData();
   for(var i=0, len=storedFiles.length; i<len; i++) {
		data.append('files[]', storedFiles[i]); 
   } 
   data.append('content', $('#content').val()); 
	var xhr = new XMLHttpRequest(); 
	xhr.open('POST', 'comment_goods.php', true); 
	xhr.onload = function(e) { 
		if(this.status == 200) { 
			console.log(e.currentTarget.responseText); 
			//alert(e.currentTarget.responseText + ' items uploaded.'); 
		} 
	} 
	//console.log(data);return;
	xhr.send(data);
} 
				
				

$("#files").change(function(){

	var allowFile = ['jpeg','gif','png'];
	//console.log(this.files);
	$.each(this.files, function(k){
		
		//console.log(this);
		
		if($.inArray(this.type.split('/')[1], allowFile))
		{
			alert('不是图片');	
		}
		else 
		{
			var objUrl = getObjectURL(this);
			if(objUrl)
			{
				storedFiles.push(this); 
				//$('#img_view_list').append('<img onclick="del('+k+')" src="'+objUrl+'">');	
				$('#img_view_list').append("<div><img src=\"" + objUrl + "\" data-file='"+this.name+"' class='selFile' title='Click to remove'>" + this.name + "<br clear=\"left\"/></div>"); 
			}
		}
		
	});
});
//建立一個可存取到該file的url
function getObjectURL(file) {
	var url = null ; 
	if (window.createObjectURL!=undefined) { // basic
		url = window.createObjectURL(file) ;
	} else if (window.URL!=undefined) { // mozilla(firefox)
		url = window.URL.createObjectURL(file) ;
	} else if (window.webkitURL!=undefined) { // webkit or chrome
		url = window.webkitURL.createObjectURL(file) ;
	}
	return url;
}


function removeFile(e) {
						  var file = $(this).data("file"); 
						  for(var i=0;i<storedFiles.length;i++) { 
						  	if(storedFiles[i].name === file) {
								
							  storedFiles.splice(i,1); 
							  break; 
						  	} 
						  } 
						  console.log(storedFiles);
						  $(this).parent().remove(); 
					} 
</script>
</body>
</html>
