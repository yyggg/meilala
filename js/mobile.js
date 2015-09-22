document.write('<link rel="stylesheet" href="../css/alert.css" type="text/css" media="all">');
document.write('<script src="http://s4.cnzz.com/stat.php?id=1255877391&web_id=1255877391" language="JavaScript"></script>');
document.write('<script language=javascript src="../js/zDrag.js"></script>');
document.write('<script language=javascript src="../js/zDialog.js"></script>');
function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}

function delayURL(url) {
	var delay = document.getElementById("time").innerHTML;
	if(delay > 0) {
		 delay--;
		 document.getElementById("time").innerHTML = delay;
	} else {
		 window.top.location.href = url;
	}
	setTimeout("delayURL('" + url + "')", 1000);
}

function show_seach(){
	//alert(123);
	var boardDiv = '<div id="search" class="item_search"><form action="" method="post" name="search" class="search_form" ><input name="keys"  id="keys" class="keys" value="请输入文字！" onclick="this.value=\'\'" /><button class="seach_button">搜索</button></form></div>';
	$(document.body).append(boardDiv); 
	
}
function click_detail(num){
	var ds = $('#detail'+num).css('display');
	if(ds=='none'){
		$('#detail'+num).css({'display':'block'});
		$('#jiantou'+num).css({'background-image':'url(../images/detail_icon5.png)'});
	}else{
		$('#detail'+num).css({'display':'none'});
		$('#jiantou'+num).css({'background-image':'url(../images/detail_icon7.png)'});
	}
}

function comment_post(){
	$.post('test.php',{input1: input1,input2:input2},function(data){
		if(data==1){
			var boardDiv = '<div id="comment_alert" class="comment_alert"><button>操作成功！</button></div>';
		}
		else{
			var boardDiv = '<div id="comment_alert" class="comment_alert"><button>操作失败！</button></div>';
		}
		$(document.body).append(boardDiv); 
	});
	var boardDiv = '<div id="comment_alert" class="comment_alert"><button>操作失败！</button></div>';
	$(document.body).append(boardDiv); 
	alert(456);
}

function my_operate(act,type,status,pid){
	var msg = {0:['收藏成功','点赞成功'],1:['您已经收藏过了','您已经点过赞了']};
	alert(msg[status][act]);
	if(status) return;	

	$('#'+act+'-'+type+'-'+pid+' b').text($('#'+act+'-'+type+'-'+pid+' b').text()*1+1);

	$.get('../inc/ajax_fun.php?action=house_zan&act='+act+'&type='+type+'&pid='+pid, function(data){
	 	//console.log(data);
		$('#'+act+'-'+type+'-'+pid).attr('href','javascript:;');
	});
}

function focus_input(){
	var w=$(window).width();
	if(w<480){
		$('#content').css({'height':'4rem'});
		$('.comment_operate3').css({  'position': 'absolute','width':'30%','right':'0rem','top':'4rem','margin-top':'0.5rem'});
	}
	else if(w<640){
		$('#content').css({'height':'6rem'});
		$('.comment_operate3').css({  'position': 'absolute','width':'30%','right':'0rem','top':'6rem','margin-top':'0.75rem'});
	}
	else {
		$('#content').css({'height':'8rem'});
		$('.comment_operate3').css({  'position': 'absolute','width':'30%','right':'0rem','top':'8rem','margin-top':'1rem'});
	}
}
function onblur_input(){
	var w=$(window).width();
	if(w<480){
		$('#content').css({'height':'8.25rem'});
		$('.comment_operate3').css({ 'position': 'relative','width':'100%','right':'auto','top':'auto','margin':'0 auto'});
	}
	else if(w<640){
		$('#content').css({'height':'12rem'});
		$('.comment_operate3').css({ 'position': 'relative','width':'100%','right':'auto','top':'auto','margin':'0 auto'});
	}
	else {
		$('#content').css({'height':'16rem'});
		$('.comment_operate3').css({ 'position': 'relative','width':'100%','right':'auto','top':'auto','margin':'0 auto'});
	}
}


function look_comment_img(cid){
	window.location.href='comment_img.php?cid='+cid;
	exit;
}


function inform(inform,url,title){
	//Dialog.title=title?title:'美啦啦温馨提醒';
	//Dialog.alert("提示：你点击了一个按钮");
	var diag = new Dialog();
	diag.Width = 300;
	diag.Height = 100;
	diag.Title = title?title:'美啦啦温馨提醒';
	url=url?url:'#';
	diag.InnerHtml='<div class="inform" style="text-align:center;color:red;font-size:14px;">'+inform+'</div>'
	diag.OKEvent = function(){diag.close();window.location.href=url};//点击确定后调用的方法
	diag.show();
}

function alert(val){
	//Dialog.alert(inform);
	var diag = new Dialog();
	diag.AutoClose=2;
	diag.ShowCloseButton=false;
	diag.URL = "javascript:void(document.write(\'"+val+"\'))";
	diag.show();
	$('.Dialogtable').css({"background":"rgba(0,0,0,0.5)"});
	$('.Draghandle2 div').css({  "width":"90%"});
	$('.Draghandle2 span').css({"color":"#fff","opacity":"0"});
	$('.DragContent2 table').css({"background":"none"});
	$('.DragContent2 iframe').css({"margin-top":"-12px"});
	$(window.frames["iframe"].document).find("body").css({"color":"#fff","text-align":"center"});
	//window.setTimeout("window.location=\'"+url+"\'",40000000);
	
}


function delay_alert(val,url)
{
	var diag = new Dialog();
	diag.AutoClose=3;
	diag.ShowCloseButton=false;
	diag.URL = "javascript:void(document.write(\'"+val+"\'))";
	diag.show();
	$('.Dialogtable').css({"background":"rgba(0,0,0,0.5)"});
	$('.Draghandle2 div').css({  "width":"90%"});
	$('.Draghandle2 span').css({"color":"#fff"});
	$('.DragContent2 table').css({"background":"none"});
	$(window.frames["iframe"].document).find("body").css({"color":"#fff","text-align":"center"});
	window.setTimeout("window.location=\'"+url+"\'",2500);
}
