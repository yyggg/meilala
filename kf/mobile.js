var welive_auto = 0,
	welive_ttt = 0,
	welive_isIE6 = /msie 6/i.test(navigator.userAgent);

function $_$(id) {
	return document.getElementById(id)
}
function welive_setCookie(n, val, d) {
	var e = "";
	if (d) {
		var dt = new Date();
		dt.setTime(dt.getTime() + parseInt(d) * 24 * 60 * 60 * 1000);
		e = "; expires=" + dt.toGMTString()
	}
	document.cookie = n + "=" + val + e + "; path=/"
}
function welive_getCookie(n) {
	var a = document.cookie.match(new RegExp("(^| )" + n + "=([^;]*)(;|$)"));
	if (a != null) return a[2];
	return ""
}
function welive_drag(wraper) {
	var handler = $_$("welive_drag"),
		tracker = $_$("welive_mouse_tracker"),
		o = false,
		l = true,
		c = 0,
		u = 0;

	function mousexy(e) {
		var x, y;
		var e = e || window.event;
		return {
			x: e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft,
			y: e.clientY + document.body.scrollTop + document.documentElement.scrollTop
		}
	}
	handler.onmousedown = function(e) {
		o = true;
		l = true;
		c = mousexy(e).x;
		u = mousexy(e).y;
		tracker.style.display = "block"
	};

	function move(dx, dy) {
		var t = 360,
			h = wraper.offsetHeight,
			mW = document.documentElement.clientWidth - wraper.offsetWidth - 40,
			mH = document.documentElement.clientHeight - 30,
			i = wraper.offsetLeft + dx;
		h -= dy;
		if (h >= mH) h = mH;
		if (h <= t) h = t;
		if (i <= 30) i = 30;
		if (i >= mW) i = mW;
		wraper.style.height = h + "px";
		wraper.style.left = i + "px"
	}
	function f(e) {
		if (!o || !l) return;
		if (c != -1) {
			move(mousexy(e).x - c, mousexy(e).y - u);
			c = mousexy(e).x;
			u = mousexy(e).y
		}
		return false
	}
	function h(e) {
		o = false;
		l = false;
		tracker.style.display = "none"
	}
	tracker.onmouseup = function(e) {
		h(e)
	};
	tracker.onmousemove = function(e) {
		f(e)
	};
	handler.onmouseup = function(e) {
		h(e)
	};
	handler.onmousemove = function(e) {
		f(e)
	}
}
var welive_url = document.getElementsByTagName("script");
welive_url = welive_url[welive_url.length - 1].src;
welive_url = welive_url.substring(0, welive_url.indexOf("welive.js"));
var welive_css = document.createElement("link");
welive_css.setAttribute("rel", "stylesheet");
welive_css.setAttribute("href", welive_url + "public/welive.css");


document.getElementsByTagName("head")[0].appendChild(welive_css);
if (navigator.userAgent.toLowerCase().indexOf("msie") != -1) {
	var welive_lang = navigator.browserLanguage.toLowerCase()
} else {
	var welive_lang = navigator.language.toLowerCase()
}
var welive_chinese = (welive_lang == 'zh-cn' || welive_lang == 'zh-tw') ? 1 : 0;
var welive_c = '<div id="welive_online">' + '<div id="welive_info">' + (welive_chinese ? '在线客服' : 'Online Support') + '</div>' + '<div id="welive_tg" title="' + (welive_chinese ? '收拢' : 'Minimize') + '"></div>' + '<div id="welive_tg2" title="' + (welive_chinese ? '点击在线咨询' : 'Chat with us') + '"><img src="' + welive_url + 'public/img/ooo.gif"></div>' + '</div>' + '<div id="welive_wrap">' + '<div id="welive_drag"></div>' + '<div id="welive_mouse_tracker"></div>' + '<div id="welive_close"></div>' + '<div id="welive_close_btn"></div>' + '<div id="welive_iwrap">' + '<div id="welive_iholder"><iframe id="welive_iframe" src="" frameborder="0" scrolling="no" allowTransparency="true"></iframe></div>' + '</div>' + '</div>';
document.write(welive_c);
var welive_opened = 0,
	welive_loaded = 0,
	welive_wrap = $_$("welive_wrap"),
	welive_close_btn = $_$("welive_close_btn"),
	welive_tg = $_$("welive_tg"),
	welive_tg2 = $_$("welive_tg2"),
	welive_online = $_$("welive_online");
if (welive_getCookie("welive_min")) welive_online.className = "welive_min";
welive_wrap.onmouseover = function() {
	welive_close_btn.style.display = "block"
};
welive_wrap.onmouseout = function() {
	welive_close_btn.style.display = "none"
};
welive_tg.onmouseover = function() {
	this.className = "welive_tghv"
};
welive_tg.onmouseout = function() {
	this.className = ""
};
welive_tg.onclick = function() {
	welive_setCookie("welive_min", 1, 30);
	welive_online.className = "welive_min"
};
welive_tg2.onmouseover = function() {
	this.className = "welive_tghv2"
};
welive_tg2.onmouseout = function() {
	this.className = ""
};
welive_tg2.onclick = function() {
	if (welive_ttt) clearTimeout(welive_ttt);
	welive_opened = 1;
	if (!welive_loaded) {
		welive_loaded = 1;
		if (!welive_isIE6) welive_drag(welive_wrap);
		var url = window.location.href;
		$_$("welive_iframe").src = welive_url + "mobile.php?a=321456978&r=" + Math.random() + "&url=" + url.replace(/&/g, "||4||")
	}
	welive_wrap.style.display = "block";
	welive_online.style.display = "none"
};
$_$("welive_info").onclick = function() {
	welive_tg2.click()
};
welive_close_btn.onclick = function() {
	welive_opened = 0;
	welive_wrap.style.display = "none";
	if (welive_online.className == "welive_min") {
		welive_setCookie("welive_min", '', 30);
		welive_online.className = ''
	}
	welive_online.style.display = "block"
};
if (welive_auto) welive_ttt = setTimeout(function() {
	welive_tg2.click()
}, welive_auto * 1000);
if (welive_isIE6) {
	welive_online.style.cssText += ';position:absolute !important;top:expression(documentElement.scrollTop + documentElement.clientHeight - 30 + "px");left:expression(documentElement.scrollLeft + documentElement.clientWidth - 260 + "px");';
	welive_wrap.style.cssText += ';position:absolute !important;top:expression(documentElement.scrollTop + documentElement.clientHeight - 400 + "px");left:expression(documentElement.scrollLeft + documentElement.clientWidth - 400 + "px");';
	document.documentElement.style.cssText += ';background-image:url(about:blank);background-attachment:fixed;'
}

welive_opened=1;
if(!welive_loaded){
  welive_loaded=1;
  if(!welive_isIE6)welive_drag(welive_wrap);
  var url=window.location.href;
  $_$("welive_iframe").src=welive_url+"mobile.php?a=321456978&r="+Math.random()+"&url="+url.replace(/&/g,"||4||")
}
welive_wrap.style.display="block";
welive_online.style.display="none"
