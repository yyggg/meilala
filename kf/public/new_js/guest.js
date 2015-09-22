var isIE6 = /msie 6/i.test(navigator.userAgent);

function shake(ele, cls, times) {
	var i = 0,
		t = false,
		o = ele.attr("class") + " ",
		c = "",
		times = times || 3;
	if (t) return;
	t = setInterval(function() {
		i++;
		c = i % 2 ? o + cls : o;
		ele.attr("class", c);
		if (i == 2 * times) {
			clearInterval(t);
			ele.removeClass(cls)
		}
	}, 200)
}
function validate_input(value, name) {
	value = $.trim(value);
	if (!value) return false;
	switch (name) {
	case "fullname":
		var pattern = /^[\w\.\-Α-￥]{2,30}$/;
		break;
	case "email":
		var pattern = /^\w+([-+.]\w+)*@\w+([-.]\w+)+$/i;
		break;
	case "vvc":
		var pattern = /^[\d+]{1,4}$/;
		break;
	case "content":
		var len = value.length;
		if (len < 6 || len > 600) return false;
		break
	}
	if (name && pattern) {
		return pattern.test(value)
	} else {
		return true
	}
}
var ajax_isOk = 1;

function ajax(url, send_data, callback) {
	if (!ajax_isOk) return false;
	$.ajax({
		url: url,
		data: send_data,
		type: "post",
		cache: false,
		dataType: "json",
		beforeSend: function() {
			ajax_isOk = 0;
			$("#ajax-loader").addClass('loading2')
		},
		complete: function() {
			ajax_isOk = 1;
			$("#ajax-loader").removeClass('loading2')
		},
		success: function(data) {
			if (callback) callback(data)
		},
		error: function(XHR, Status, Error) {
			alert("Data: " + XHR.responseText + "\r\nStatus: " + Status + "\r\nError: " + Error)
		}
	})
}
function setCookie(n, val, d) {
	var e = "";
	if (d) {
		var dt = new Date();
		dt.setTime(dt.getTime() + parseInt(d) * 24 * 60 * 60 * 1000);
		e = "; expires=" + dt.toGMTString()
	}
	document.cookie = n + "=" + val + e + "; path=/"
}
function getCookie(n) {
	var a = document.cookie.match(new RegExp("(^| )" + n + "=([^;]*)(;|$)"));
	if (a != null) return a[2];
	return ''
}
function parseJSON(data) {
	if (window.JSON && window.JSON.parse) return window.JSON.parse(data);
	if (data === null) return data;
	if (typeof data === "string") {
		data = $.trim(data);
		if (data) {
			var rvalidchars = /^[\],:{}\s]*$/,
				rvalidbraces = /(?:^|:|,)(?:\s*\[)+/g,
				rvalidescape = /\\(?:["\\\/bfnrt]|u[\da-fA-F]{4})/g,
				rvalidtokens = /"[^"\\\r\n]*"|true|false|null|-?(?:\d+\.|)\d+(?:[eE][+-]?\d+|)/g;
			if (rvalidchars.test(data.replace(rvalidescape, "@").replace(rvalidtokens, "]").replace(rvalidbraces, ""))) {
				return (new Function("return " + data))()
			}
		}
	}
	return false
}
function flashTitle() {
	clearInterval(tttt);
	flashtitle_step = 1;
	tttt = setInterval(function() {
		if (flashtitle_step == 1) {
			welive_cprt.addClass("hover");
			flashtitle_step = 2
		} else {
			welive_cprt.removeClass("hover");
			flashtitle_step = 1
		}
	}, 200)
}
function stopFlashTitle() {
	if (flashtitle_step != 0) {
		flashtitle_step = 0;
		clearInterval(tttt);
		welive_cprt.removeClass("hover")
	}
}
function getLocalTime() {
	var date = new Date();

	function addZeros(value, len) {
		var i;
		value = "" + value;
		if (value.length < len) {
			for (i = 0; i < (len - value.length); i++) value = "0" + value
		}
		return value
	}
	return addZeros(date.getHours(), 2) + ':' + addZeros(date.getMinutes(), 2) + ':' + addZeros(date.getSeconds(), 2)
}
var WebSocket = window.WebSocket || window.MozWebSocket;
if (!WebSocket) {
	var ws_obj = {};
	var ws_ready = false;
	$(window).load(function() {
		ws_ready = true
	});
	$(function() {
		$.ajax({
			url: SYSDIR + 'public/jquery.swfobject.js',
			dataType: 'script',
			async: false,
			cache: true
		});
		window.WebSocket = function(a) {
			a = a.match(/wss{0,1}\:\/\/([0-9a-z_.-]+)(?:\:(\d+)){0,1}/i);
			this.host = a[1];
			this.port = a[2] || 843;
			this.onopen = function() {};
			this.onclose = function() {};
			this.onmessage = function() {};
			this.onerror = function() {};
			this.ready = function(b) {
				return true
			};
			this.send = function(b) {
				return ws_obj.call.Send(b)
			};
			this.close = function() {
				return ws_obj.call.Close()
			};
			this.ping = function() {
				return ws_obj.call.Ping()
			};
			this.connect = function() {
				ws_obj.call = $('#flash_websocket')[0];
				ws_obj.call.Connect(this.host, this.port)
			};
			this.onmessage_escape = function(d) {
				this.onmessage({
					data: unescape(d)
				})
			};
			if ($('#websocket1212').size()) {
				this.connect()
			} else {
				var div = $('<div id="websocket1212"></div>').css({
					position: 'absolute',
					top: -999,
					left: -999
				});
				div.flash({
					swf: SYSDIR + 'public/websocket.swf?r=' + Math.random(),
					wmode: "window",
					scale: "showall",
					allowFullscreen: true,
					allowScriptAccess: 'always',
					id: 'flash_websocket',
					width: 1,
					height: 1,
					flashvars: {
						call: 'ws_obj._this'
					}
				});
				$('body').append(div)
			}
			ws_obj._this = this
		}
	})
}
function format_output(data) {
	data = data.replace(/((href=\"|\')?(((https?|ftp):\/\/)|www\.)([\w\-]+\.)+[\w\.\/=\?%\-&~\':+!#;]*)/ig, function($1) {
		return getURL($1)
	});
	data = data.replace(/([\-\.\w]+@[\.\-\w]+(\.\w+)+)/ig, '<a href="mailto:$1" target="_blank">$1</a>');
	data = data.replace(/\[:(\d*):\]/g, '<img src="' + SYSDIR + 'public/smilies/$1.png">').replace(/\\/g, '');
	return data
}
function getURL(url, limit) {
	if (url.substr(0, 5).toLowerCase() == 'href=') return url;
	if (!limit) limit = 60;
	var urllink = '<a href="' + (url.substr(0, 4).toLowerCase() == 'www.' ? 'http://' + url : url) + '" target="_blank" title="' + url + '">';
	if (url.length > limit) {
		url = url.substr(0, 30) + ' ... ' + url.substr(url.length - 18)
	}
	urllink += url + '</a>';
	return urllink
}
function insertSmilie(code) {
	code = '[:' + code + ':]';
	var obj = msger[0];
	var selection = document.selection;
	obj.focus();
	if (typeof obj.selectionStart != 'undefined') {
		var opn = obj.selectionStart + 0;
		obj.value = obj.value.substr(0, obj.selectionStart) + code + obj.value.substr(obj.selectionEnd)
	} else if (selection && selection.createRange) {
		var sel = selection.createRange();
		sel.text = code;
		sel.moveStart('character', -code.length)
	} else {
		obj.value += code
	}
}
function welive_link() {
	welive.ws = new WebSocket('ws://' + WS_HOST + ':' + WS_PORT + '/');
	welive.ws.onopen = function() {
		setTimeout(welive_verify, 100)
	};
	welive.ws.onclose = function() {
		welive_close()
	};
	welive.ws.onmessage = function(get) {
		welive_parseOut(get)
	}
}
function welive_parseOut(get) {
	var d = false,
		type = 0,
		data = parseJSON(get.data);
	if (!data) return;
	switch (data.x) {
	case 5:
		if (data.a == 1) {
			welive.flashTitle = 1;
			type = 1;
			d = data.i
		} else {
			type = 2;
			d = welive.msg.replace(/</g, "&lt;").replace(/>/g, "&gt;");
			welive.status = 1
		}
		break;
	case 6:
		switch (data.a) {
		case 8:
			welive.status = 1;
			welive.autolink = 1;
			guest.gid = data.gid;
			guest.fn = data.fn;
			guest.aid = data.aid;
			guest.an = data.an;
			setCookie(COOKIE_USER, data.gid, 365);
			welive_op.find("#welive_avatar").attr("src", SYSDIR + "avatar/" + data.av);
			welive_op.find("#welive_name").html(data.an);
			welive_op.find("#welive_duty").html(data.p);
			historyViewport.removeClass('loading3');
			var recs = '';
			$.each(data.re, function(i, rec) {
				if (rec.t == 1) {
					recs += '<div class="msg r"><b></b><div class="b"><div class="i">' + format_output(rec.m) + '</div></div><i>' + rec.d + '</i></div>'
				} else {
					recs += '<div class="msg l"><b></b><div class="b"><div class="i">' + format_output(rec.m) + '</div></div><i>' + rec.d + '</i></div>'
				}
			});
			if (recs != '') {
				recs += '<div class="msg s"><div class="b"><div class="ico"></div><div class="i">' + langs.records + '</div></div></div>';
				historier.append(recs)
			}
			msger.focus();
			welive.flashTitle = 1;
			type = 1;
			d = welcome;
			autoOffline();
			welive_runtime();
			break;
		case 1:
			welive.status = 1;
			welive.flashTitle = 1;
			type = 3;
			d = guest.an + langs.aback;
			autoOffline();
			break;
		case 2:
			welive.flashTitle = 1;
			welive.status = 0;
			type = 4;
			d = guest.an + langs.offline;
			break;
		case 4:
			welive.status = 0;
			welive.autolink = 0;
			type = 4;
			d = langs.relinked + '<br><a onclick="welive_link();$(this).parents(\'.msg\').hide();return false;" class="relink">' + langs.rebtn + ' >>></a>';
			stopFlashTitle();
			break;
		case 5:
			welive.status = 0;
			welive.autolink = 0;
			welive.flashTitle = 1;
			type = 4;
			d = langs.autooff + '<br><a onclick="welive_link();$(this).parents(\'.msg\').hide();return false;" class="relink">' + langs.rebtn + ' >>></a>';
			break;
		case 6:
			welive.autolink = 0;
			welive.flashTitle = 1;
			type = 4;
			d = langs.kickout;
			break;
		case 7:
			welive.status = 0;
			welive.autolink = 0;
			welive.flashTitle = 1;
			type = 4;
			d = langs.banned;
			break;
		case 9:
			welive.autolink = 0;
			welive.linked = 0;
			break;
		case 10:
			welive.status = 1;
			welive.autolink = 1;
			welive.flashTitle = 1;
			type = 3;
			d = langs.unbann;
			break;
		case 11:
			welive.status = 1;
			welive.autolink = 1;
			guest.aid = data.aid;
			guest.an = data.an;
			welive_op.find("#welive_avatar").attr("src", SYSDIR + "avatar/" + data.av);
			welive_op.find("#welive_name").html(data.an);
			welive_op.find("#welive_duty").html(data.p);
			msger.focus();
			welive.flashTitle = 1;
			type = 3;
			d = langs.transfer + data.an;
			autoOffline();
			break
		}
		break
	}
	welive_output(d, type)
}
function welive_output(d, type) {
	if (d === false || !type) return;
	if (welive.flashTitle) {
		flashTitle();
		if (welive.sound) sounder.html(welive.sound1);
		welive.flashTitle = 0
	}
	switch (type) {
	case 1:
		d = '<div class="msg r"><b></b><div class="b"><div class="i">' + format_output(d) + '</div></div><i>' + getLocalTime() + '</i></div>';
		break;
	case 2:
		d = '<div class="msg l"><b></b><div class="b"><div class="i">' + format_output(d) + '</div></div><i>' + getLocalTime() + '</i></div>';
		break;
	case 3:
		d = '<div class="msg s"><div class="b"><div class="ico"></div><div class="i">' + d + '</div></div></div>';
		break;
	case 4:
		d = '<div class="msg e"><div class="b"><div class="ico"></div><div class="i">' + d + '</div></div></div>';
		break
	}
	historier.append(d);
	history_wrap.welivebar_update('bottom')
}
function welive_verify() {
	welive.linked = 1;
	welive.ws.send('x=6&a=8&gid=' + guest.gid + '&fn=' + guest.fn + '&aid=' + guest.aid + '&l=' + guest.lang + '&k=' + SYSKEY + '&c=' + SYSCODE + '&fr=' + guest.fromurl + '&ag=' + guest.agent + '&i=' + welive.ic)
}
function welive_close() {
	welive.status = 0;
	if (welive.autolink) {
		$("#websocket1212").remove();
		welive_output(langs.failed, 4);
		welive.ttt = setTimeout(welive_link, 6000)
	} else if (!welive.linked) {
		welive_comment()
	}
	welive.linked = 0
}
function welive_send() {
	sender.addClass('loading2');
	if (welive.status && welive.linked) {
		var msg = $.trim(msger.val());
		if (msg) {
			welive.msg = msg;
			msg = msg.replace(/&/g, "||4||");
			welive.ws.send('x=5&i=' + msg);
			msger.val('');
			welive.status = 0;
			autoOffline()
		}
	}
	msger.focus();
	sender.removeClass('loading2')
}
function autoOffline() {
	clearTimeout(welive.ttt);
	welive.ttt = setTimeout(function() {
		if (welive.linked) welive.ws.send('x=6&a=5')
	}, offline_time)
}
function welive_runtime() {
	setInterval(function() {
		if (welive.status && welive.linked) {
			var msg = $.trim(msger.val());
			msg = msg.replace(/&/g, "||4||");
			if (msg && msg != welive.temp && welive.status && welive.linked) {
				welive.ws.send('x=4&i=' + msg);
				welive.temp = msg
			}
		}
	}, update_time)
}
function welive_comment() {
	clearTimeout(welive.ttt);
	historyViewport.removeClass('loading3');
	$(".enter").html('').html('<div id="ajax-loader"></div><div class="savemsg" onclick="submit_comment();">' + langs.submit + '</div>');
	welive_op.find("#welive_name").html(langs.leavemsg);
	welive_op.find("#welive_duty").html(langs.nosuppert);
	var vid = 0;
	$.ajaxSetup({
		async: false
	});
	ajax(SYSDIR + 'welive1618.php?ajax=1&act=vvc', "", function(data) {
		vid = parseInt(data.s)
	});
	$.ajaxSetup({
		async: true
	});
	historier.css("padding-bottom", 0).html('').append('<div class="comment"><form id="comment_form" onsubmit="return false;"><input type="hidden" name="vid" value="' + vid + '"><input type="hidden" name="key" value="' + SYSKEY + '"><input type="hidden" name="code" value="' + SYSCODE + '"><li><b>' + langs.yourname + ':</b><input name="fullname" type="text"><i>*</i></li><li><b>' + langs.email + ':</b><input name="email" type="text"><i>*</i></li><li><b>' + langs.phone + ':</b><input name="phone" type="text"></li><li><b>' + langs.content + ':</b><textarea name="content"></textarea><i>*</i></li><li><b></b><img src="' + SYSDIR + 'welive1618.php?ajax=1&act=get&vid=' + vid + '" onclick="ChangeCaptcha(this);" title="' + langs.newcaptcha + '"> = <input name="vvc" type="text" class="vvc"><i>*</i></li></form></div>')
}
function ChangeCaptcha(i) {
	i.src = i.src + '&' + Math.random()
}
var shakeobj = function(obj) {
		shake(obj, "shake");
		obj.focus();
		return false
	};

function submit_comment() {
	var form = $("#comment_form");
	var fullname = form.find("input[name=fullname]");
	var email = form.find("input[name=email]");
	var content = form.find("textarea[name=content]");
	var vvc = form.find("input[name=vvc]");
	if (!validate_input(fullname.val(), 'fullname')) return shakeobj(fullname);
	if (!validate_input(email.val(), 'email')) return shakeobj(email);
	if (!validate_input(content.val(), 'content')) return shakeobj(content);
	if (!validate_input(vvc.val(), 'vvc')) return shakeobj(vvc);
	ajax(SYSDIR + 'welive1618.php?ajax=1&gid=' + guest.gid, form.serialize(), function(data) {
		if (data.s == 0) {
			alert(langs.badcookie)
		} else if (data.s == 1) {
			$(".enter").html('');
			historier.html('<div class="comsaved">' + langs.saved + '</div>')
		} else if (data.s == 2) {
			shakeobj(fullname)
		} else if (data.s == 3) {
			shakeobj(email)
		} else if (data.s == 4) {
			shakeobj(content)
		} else if (data.s == 5) {
			shakeobj(vvc)
		}
	})
}
function welive_init() {
	sender = $(".sender");
	msger = $(".msger");
	historier = history_wrap.find(".overview");
	sounder = $("#wl_sounder");
	welive.ic = welive_op.find("#|w|e|l|i|v|e|_|c|o|p|y|r|i|g|h|t|>|a".replace(/\|/ig, "")).attr("h|r|e|f".replace(/\|/ig, ""));
	welive_link();
	history_wrap.welivebar();
	msger.keyup(function(e) {
		if (e.keyCode == 13) welive_send()
	});
	$(window).resize(function() {
		history_wrap.height($(window).height() - 89);
		history_wrap.welivebar_update('bottom');
		msger.focus()
	});
	sender.click(function(e) {
		welive_send();
		e.preventDefault()
	});
	if (!isIE6) $(".s_face").tipTip({
		content: $(".smilies_div").html(),
		keepAlive: true,
		maxWidth: "260px",
		defaultPosition: "top",
		edgeOffset: -31,
		delay: 300
	});
	$(document).mousedown(stopFlashTitle).keydown(stopFlashTitle);
	welive.sound1 = '<object data="' + SYSDIR + 'public/sound1.swf" type="application/x-shockwave-flash" width="1" height="1" style="visibility:hidden"><param name="movie" value="' + SYSDIR + 'public/sound1.swf"><param name="quality" value="high"></object>';
	window.onbeforeunload = function(event) {
		clearTimeout(welive.ttt)
	};
	$(window).unload(function() {
		clearTimeout(welive.ttt)
	})
}
var tttt = 0,
	pagetitle, flashtitle_step = 0,
	sounder;
var welive_op, welive_cprt, wrapper_h = 0,
	history_wrap, historyViewport, historier, sender, msger;
var welive = {
	ws: {},
	linked: 0,
	status: 0,
	autolink: 0,
	ttt: 0,
	flashTitle: 0,
	ic: '',
	sound: 1,
	sound1: '',
	msg: '',
	temp: ''
};
$(function() {
	welive_op = $("#welive_operator");
	welive_cprt = $("#welive_copyright");
	history_wrap = $(".history");
	historyViewport = history_wrap.find(".viewport");
	var gid = getCookie(COOKIE_USER);

	guest.gid = parseInt(gid);
	welive_init()
});