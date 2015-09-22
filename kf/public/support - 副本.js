var userAgent = navigator.userAgent.toLowerCase();
var isIE = window.ActiveXObject && userAgent.indexOf('msie') != -1 && userAgent.substr(userAgent.indexOf('msie') + 5, 3);
function $$(id) {
    return typeof id == "string" ? document.getElementById(id) : id
}
function html(str) {
    return str.replace(/\\/g, '').replace(/\&amp;/g, '&').replace(/\&#039;/g, "'").replace(/\&quot;/g, '"').replace(/\&lt;/g, '<').replace(/\&gt;/g, '>')
}
function flashTitle() {
    clearInterval(tttt);
    flashtitle_step = 1;
    tttt = setInterval(function() {
        if (flashtitle_step == 1) {
            document.title = '【新消息】' + pagetitle;
            flashtitle_step = 2
        } else {
            document.title = '【　　　】' + pagetitle;
            flashtitle_step = 1
        }
    },
    200)
}
function stopFlashTitle() {
    if (flashtitle_step != 0) {
        flashtitle_step = 0;
        clearInterval(tttt);
        document.title = pagetitle
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
function showInfo(info, title, callback, time, success) {
    var ti = time ? time * 1000: 0;
    if (success) {
        var title = "<font color=#33CC00>" + (title ? title: "系统信息") + "</font>";
        var content = "<font color=blue>" + info + "</font>"
    } else {
        var title = "<font color=red>" + (title ? title: "系统信息") + "</font>";
        var content = "<font color=#FF9900>" + info + "</font>"
    }
    easyDialog.open({
        container: {
            header: title,
            content: content,
            yesFn: function() {},
            yesText: '确定'
        },
        autoClose: ti,
        callback: callback
    });
    $("#easyDialogYesBtn").focus()
}
function showDialog(info, title, callback, time) {
    var ti = time ? time * 1000: 0;
    easyDialog.open({
        container: {
            header: "<font color=red>" + (title ? title: "系统信息") + "</font>",
            content: "<font color=#FF9900>" + info + "</font>",
            yesFn: callback,
            yesText: '确定',
            noFn: true,
            noText: '取消'
        },
        autoClose: ti
    });
    $("#easyDialogYesBtn").focus()
} (function(a) {
    a.fn.Jdropdown = function(b, c) {
        if (this.length) {
            "function" == typeof b && (c = b, b = {});
            var d = a.extend({
                event: "mouseover",
                current: "hover",
                delay: 0
            },
            b || {}),
            e = "mouseover" == d.event ? "mouseout": "mouseleave";
            a.each(this, 
            function() {
                var b = null,
                f = null,
                g = !1;
                a(this).bind(d.event, 
                function() {
                    if (g) clearTimeout(f);
                    else {
                        var e = a(this);
                        b = setTimeout(function() {
                            e.addClass(d.current),
                            g = !0,
                            c && c(e)
                        },
                        d.delay)
                    }
                }).bind(e, 
                function() {
                    if (g) {
                        var c = a(this);
                        f = setTimeout(function() {
                            c.removeClass(d.current),
                            g = !1
                        },
                        0)
                    } else clearTimeout(b)
                })
            })
        }
    }
})(jQuery);
function createWin(id, title, recs, lang) {
    var x = x_win_content.replace('888888', recs);
    myWin88.Create(id, title, x, lang)
}
function openWin(id) {
    myWin88.Show(id)
}
function closeWin(id) {
    myWin88.Min(id)
}
function WeLiveWin() {
    this.Create = function(id, title, wbody, lang) {
        var mywin = document.createElement("DIV");
        mywin.setAttribute("id", "win" + id);
        mywin.className = "x-win";
        mywin.onmouseup = function() {
            openWin(id)
        };
        mywin.style.zIndex = zIndex;
        document.body.appendChild(mywin);
        var mytitle = document.createElement("DIV");
        var mybody = document.createElement("DIV");
        var mybottom = document.createElement("DIV");
        mytitle.className = "x-title";
        mybody.className = "x-body";
        mybottom.className = "x-bottom";
        mywin.appendChild(mytitle);
        mywin.appendChild(mybody);
        mywin.appendChild(mybottom);
        var winbody,
        g_tools,
        wintag = [mytitle, mytitle, mytitle, mybody, mybody, mybody, mybottom, mybottom, mybottom];
        for (var i = 0; i < 9; i++) {
            var temp = document.createElement("DIV");
            wintag[i].appendChild(temp);
            if (i == 0) {
                temp.className = "x-titleleft"
            } else if (i == 1) {
                temp.className = "x-titlemid"
            } else if (i == 2) {
                temp.className = "x-titleright"
            } else if (i == 3) {
                temp.className = "x-bodyleft"
            } else if (i == 4) {
                temp.className = "x-bodymid";
                winbody = temp
            } else if (i == 5) {
                temp.className = "x-bodyright"
            } else if (i == 6) {
                temp.className = "x-bottomleft"
            } else if (i == 7) {
                temp.className = "x-bottomid"
            } else if (i == 8) {
                temp.className = "x-bottomright"
            }
            if (i != 4 && i != 2) temp.onmousedown = function(e) {
                myWin88.Move(mywin, e)
            }
        }
        mytitle.childNodes[1].innerHTML = '<div class="x-user">' + title + '&nbsp;</div><div id="min168" class="x-min" onclick="closeWin(' + id + ');" title="最小化" onMouseover="this.className=\'x-min2\';" onMouseout="this.className=\'x-min\';"></div>';
        mybody.childNodes[1].innerHTML = wbody;
        var l = guest.length == 1 ? parseInt(($(window).width() - 780) / 2) : parseInt(Math.random() * ($(window).width() - 780));
        var t = guest.length == 1 ? parseInt(($(window).height() - 500) / 2) : parseInt(Math.random() * ($(window).height() - 500));
        this.Move_e(mywin, l, t);
        $(winbody).children(".g_history").welivebar();
        $(winbody).children(".g_bott").children(".g_msg").keyup(function(e) {
            if (e.keyCode == 13) guest_send()
        });
        g_tools = $(winbody).children(".g_tools");
        g_tools.children(".t_smilies").tipTip({
            content: $(".smilies_div").html().replace(/towhere/ig, id),
            keepAlive: true,
            maxWidth: "354px",
            defaultPosition: "top",
            edgeOffset: 0,
            delay: 300,
            parent: true,
            hoverClass: "hover"
        });
        g_tools.children(".t_phrase").tipTip({
            content: (lang == 1 ? $(".phrases_div").html().replace(/towhere/ig, id) : $(".phrasesen_div").html().replace(/towhere/ig, id)),
            keepAlive: true,
            maxWidth: "354px",
            defaultPosition: "top",
            edgeOffset: 0,
            delay: 400,
            parent: true,
            hoverClass: "hover"
        });
        g_tools.children(".t_kickout").tipTip({
            content: '<input class="save" type="submit" value="确定踢出" onclick="guest_kickout();return false;">',
            activation: "click",
            keepAlive: true,
            maxWidth: "354px",
            defaultPosition: "top",
            edgeOffset: 4,
            delay: 0,
            hoverClass: "hover"
        });
        g_tools.children(".t_transfer").tipTip({
            content: '<div class="s_transfer">&nbsp;</div>',
            enter: function() {
                get_supporters()
            },
            activation: "click",
            keepAlive: true,
            maxWidth: "354px",
            defaultPosition: "top",
            edgeOffset: 0,
            delay: 0,
            parent: true,
            hoverClass: "hover"
        })
    };
    this.Show = function(id) {
        welive.where = 1;
        s_title.addClass("off");
        var o = $('#win' + id);
        if (id != CurrentId) {
            o.css({
                visibility: 'visible',
                'z-index': ++zIndex
            });
            g_online.find("#g" + id + ">b").html(0).hide();
            $('#win' + CurrentId).find('.x-user').removeClass("x-now");
            CurrentId = id;
            o.find('.g_msg').focus();
            o.find('.x-user').addClass("x-now")
        } else if (!o.find('.x-user').hasClass("x-now")) {
            o.find('.g_msg').focus();
            o.find('.x-user').addClass("x-now")
        }
    };
    this.Min = function(id) {
        CurrentId = 0;
        if ($.inArray(id, offline) > -1) {
            guest_delete(id)
        } else {
            $('#win' + id).css('visibility', 'hidden')
        }
        showLast()
    };
    this.Move = function(o, evt) {
        if (!o) return;
        evt = evt ? evt: window.event;
        var obj = evt.srcElement ? evt.srcElement: evt.target;
        if (obj.id == "min168") return;
        var w = o.offsetWidth;
        var h = o.offsetHeight;
        var l = o.offsetLeft;
        var t = o.offsetTop;
        var div = document.createElement("DIV");
        document.body.appendChild(div);
        div.className = "x-drag";
        div.style.cssText = "top:" + t + "px;left:" + l + "px;";
        this.Move_r(div, o, evt)
    };
    this.Move_r = function(o, win, evt) {
        o.style.zIndex = zIndex + 1;
        evt = evt ? evt: window.event;
        var relLeft = evt.clientX - o.offsetLeft;
        var relTop = evt.clientY - o.offsetTop;
        if (!window.captureEvents) {
            o.setCapture()
        } else {
            window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP)
        }
        document.onmousemove = function(e) {
            if (!o) return;
            window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty();
            e = e ? e: window.event;
            var xleft = e.clientX - relLeft;
            var xtop = e.clientY - relTop;
            var xw = $(window).width() - o.offsetWidth - 2;
            var xh = $(window).height() - o.offsetHeight - 2;
            if (xleft <= 1) {
                o.style.left = "1px"
            } else if (xleft >= xw) {
                o.style.left = xw + "px"
            } else {
                o.style.left = xleft + "px"
            }
            if (xtop <= 1) {
                o.style.top = "1px"
            } else if (xtop >= xh) {
                o.style.top = xh + "px"
            } else {
                o.style.top = xtop + "px"
            }
        };
        document.onmouseup = function() {
            if (!o) return;
            if (!window.captureEvents) {
                o.releaseCapture()
            } else {
                window.releaseEvents(Event.MOUSEMOVE | Event.MOUSEUP)
            }
            myWin88.Move_e(win, o.offsetLeft, o.offsetTop);
            var id = win.id.replace(/win/ig, "");
            if (id != CurrentId) {
                openWin(id)
            } else {
                $(win).focus().find('.g_msg').focus()
            }
            document.body.removeChild(o);
            o = null
        }
    };
    this.Move_e = function(o, l, t) {
        if (!o) return;
        o.style.left = l + "px";
        o.style.top = t + "px"
    }
}
function showLast() {
    var o,
    oz,
    xId = 0,
    xIndex = 0;
    $.each(guest, 
    function(i, gid) {
        o = $('#win' + gid);
        if (o.length && o.css("visibility") == 'visible') {
            oz = parseInt(o.css("z-index"));
            if (oz > xIndex) {
                xId = gid;
                xIndex = oz
            }
        }
    });
    if (xId) {
        openWin(xId)
    } else {
        s_msg.focus()
    }
}
function showNext(hide) {
    var o,
    oz,
    xId = 0,
    xIndex = 1000000000,
    xxx = hide ? "hidden": "visible";
    $.each(guest, 
    function(i, gid) {
        o = $('#win' + gid);
        if (o.length && o.css("visibility") == xxx) {
            oz = parseInt(o.css("z-index"));
            if (oz < xIndex) {
                xId = gid;
                xIndex = oz
            }
        }
    });
    if (xId) {
        openWin(xId)
    } else if (CurrentId) {
        openWin(CurrentId)
    }
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
                var div = $('<div></div>').attr({
                    id: 'websocket1212'
                }).css({
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
    data = data.replace(/((href=\"|\')?(((https?|ftp):\/\/)|www\.)([\w\-]+\.)+[\w\.\/=\?%\-&~\':+!#;]*)/ig, 
    function($1) {
        return getURL($1)
    });
    data = data.replace(/([\-\.\w]+@[\.\-\w]+(\.\w+)+)/ig, '<a href="mailto:$1" target="_blank">$1</a>');
    data = data.replace(/\[:(\d*):\]/g, '<img src="' + SYSDIR + 'public/smilies/$1.png">').replace(/\\/g, '');
	data = data.replace(/\[img:(\S*):\]/g, '<img src="' + SYSDIR + '$1" class="upload_img" onclick="show_img(this.src)" height="220"  />').replace(/\\/g, '');
    return data
}
function getURL(url, limit) {
    if (url.substr(0, 5).toLowerCase() == 'href=') return url;
    if (!limit) limit = 60;
    var urllink = '<a href="' + (url.substr(0, 4).toLowerCase() == 'www.' ? 'http://' + url: url) + '" target="_blank" title="' + url + '">';
    if (url.length > limit) {
        url = url.substr(0, 30) + ' ... ' + url.substr(url.length - 18)
    }
    urllink += url + '</a>';
    return urllink
}
function insertSmilie(code, towhere) {
    code = '[:' + code + ':]';
    if (towhere) {
        openWin(towhere);
        var obj = $("#win" + towhere).find(".g_msg")[0];
        if (!obj) return
    } else {
        var obj = s_msg[0]
    }
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
function insertPhrase(me, towhere) {
    code = $(me).children("b").html();
    openWin(towhere);
    var obj = $("#win" + towhere).find(".g_msg")[0];
    if (!obj) return;
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
    $("#tiptip_holder").hide()
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
        welive_parseOut(get.data)
    }
}
function TitleSound(x) {
    if (welive.flashTitle && x) {
        flashTitle();
        if (welive.sound) sounder.html(welive.sound1);
        welive.flashTitle = 0
    } else if (welive.flashTitle) {
        if (welive.sound) sounder.html(welive.sound2);
        welive.flashTitle = 0
    }
}
function welive_clear() {
    var rec = s_history.children("div");
    var len = rec.length;
    if (len >= 100) {
        rec.slice(0, len - 50).remove();
        s_hwrap.welivebar_update('bottom')
    }
}
function admins_update(n) {
    var x = parseInt(s_admins.html());
    x = x + n;
    if (x < 0) x = 0;
    s_admins.html(x)
}
function welive_parseOut(data) {
    var gid = 0,
    d = false,
    type = 0,
    data = $.parseJSON(data);
    switch (data.x) {
    case 4:
        welive_runtime(data.g, data.i);
        return;
        break;
    case 1:
        var time = getLocalTime();
        if (data.u == 0) {
            d = '<div class=me><u>' + admin.fullname + ' (' + admin.post + ')</u><i>' + time + '</i><br>' + format_output(data.i) + '</div>'
        } else {
            welive.flashTitle = 1;
            d = '<div' + (data.t == 1 ? ' class=a': '') + '><u>' + data.u + '</u><i>' + time + '</i><br>' + format_output(data.i) + '</div>'
        }
        break;
    case 2:
        switch (data.a) {
        case 1:
            d = '<div class=i><b></b>' + data.n + ' 上线了</div>';
            s_online.append('<li id="index_' + data.ix + '" title="' + data.p + '"><div><img src="' + SYSDIR + 'avatar/' + data.av + '" title="服务中..."></div><i' + (data.t == 1 ? ' class=a': '') + '>' + data.n + '</i></li>');
            admins_update(1);
            break;
        case 2:
            d = '<div class=i><b></b>' + data.i + ' 已离线</div>';
            s_online.find("#index_" + data.ix).remove();
            admins_update( - 1);
            break;
        case 3:
            var a = s_online.find("#index_" + data.ix);
            d = '<div class=i><b></b>' + a.children("i").html() + ' 已挂起</div>';
            a.children("div").append('<b></b>');
            a.children("div").children("img").attr('title', '已挂起');
            if (data.ix == welive.index) {
                $(".set_busy").hide();
                $(".set_serving").show()
            }
            break;
        case 4:
            var a = s_online.find("#index_" + data.ix);
            d = '<div class=i><b></b>' + a.children("i").html() + ' 解除挂起</div>';
            a.children("div").children("b").remove();
            a.children("div").children("img").attr('title', '服务中...');
            if (data.ix == welive.index) {
                $(".set_serving").hide();
                $(".set_busy").show()
            }
            break;
        case 5:
            gid = data.g;
            if (typeof data.d == "object") {
                var g_note = $("#win" + gid).find(".g_note");
                if (g_note.length) {
                    g_note.find("a.fromurl").attr("href", data.d.fromurl.replace(/\&amp;/g, '&')).html(data.d.fromurl);
                    g_note.find(".ipzone").html(data.d.ipzone);
                    g_note.find("input[name=grade][value=" + data.d.grade + "]").attr("checked", true);
                    g_note.find("input[name=fullname]").val(html(data.d.fullname));
                    g_note.find("input[name=phone]").val(html(data.d.phone));
                    g_note.find("input[name=email]").val(html(data.d.email));
                    g_note.find("input[name=address]").val(html(data.d.address));
                    g_note.find("textarea[name=remark]").val(html(data.d.remark));
                    g_note.attr("loaded", 1)
                }
            }
            break;
        case 6:
            gid = data.g;
            type = 3;
            d = '客人信息保存成功!';
            var o = $("#win" + gid);
            o.find(".g_note").hide();
            o.find(".t_note").removeClass("hover");
            if (data.n != '') {
                g_online.children("#g" + gid).attr("title", data.n).children("i").html(data.n);
                o.find(".x-user").html(data.n)
            }
            break;
        case 8:
            d = '<div class=i><b></b>服务器连接成功!</div>';
            welive.index = data.ix;
            s_hwrap.removeClass('loading3');
            var num = 0,
            status;
            $.each(data.al, 
            function(n, a) {
                num += 1;
                if (a.b == 1) {
                    status = '已挂起"><b></b'
                } else {
                    status = '服务中..."'
                }
                if (a.id == admin.id) admin.aix = a.ix;
                s_online.append('<li id="index_' + a.ix + '" title="' + a.p + '"><div><img src="' + SYSDIR + 'avatar/' + a.av + '" title="' + status + '></div><i' + (a.t == 1 ? ' class=a': '') + '>' + a.n + '</i></li>')
            });
            admins_update(num);
            if (guest.length) {
                $.each(guest, 
                function(i, gid) {
                    if ($.inArray(gid, data.gl) < 0 && $.inArray(gid, offline) < 0) {
                        offline.push(gid);
                        guest_output('客人已离线!', gid, 4);
                        g_online.find('#g' + gid).addClass("offline");
                        var o = $("#win" + gid).find(".x-user");
                        o.html(o.html() + ' -- 已离线')
                    }
                })
            } else {
                $.each(data.gl, 
                function(i, guest) {
                    guest_create(guest.g, guest.n, guest.l, 1)
                })
            }
            $(".set_busy").click(function(e) {
                showDialog('确定挂起吗?<br>注: 挂起后, 将不再接受新客人加入.<br>但是, 转接过来的客人仍会加入.', '', 
                function() {
                    welive_send('x=2&a=3')
                });
                e.preventDefault()
            });
            $(".set_serving").click(function(e) {
                welive_send('x=2&a=4');
                e.preventDefault()
            });
            $(".reset_socket").click(function(e) {
                showDialog('确定重启Socket通讯服务吗?<br>注: 所有连接将中断! 无特殊原因, 请勿重启.', '', 
                function() {
                    welive_send('x=2&a=9');
                    setTimeout(function() {
                        $.ajax({
                            url: './index.php?c=opensocket&a=ajax',
                            dataType: 'json',
                            async: true,
                            cache: false
                        })
                    },
                    2000)
                });
                e.preventDefault()
            });
            setInterval(welive_clear, 1000 * 1200);
            break
        }
        break;
    case 5:
        gid = data.g;
        d = data.i;
        if (data.a == 1) {
            type = 1
        } else {
            welive.flashTitle = 1;
            type = 2
        }
        break;
    case 6:
        switch (data.a) {
        case 8:
            guest_create(data.g, data.n, data.l, data.re);
            break;
        case 3:
            gid = data.g;
            type = 4;
            d = '客人已离线!';
            offline.push(gid);
            g_online.find('#g' + gid).addClass("offline");
            var o = $("#win" + gid).find(".x-user");
            o.html(o.html() + ' -- 已离线');
            break;
        case 11:
            if (data.i == 1) {
                gid = data.g;
                type = 3;
                d = '客人转接成功.';
                offline.push(gid);
                g_online.find('#g' + gid).addClass("offline");
                var o = $("#win" + gid).find(".x-user");
                o.html(o.html() + ' -- 已转接')
            } else {
                gid = data.g;
                type = 4;
                d = '客人转接失败!'
            }
            break
        }
        break
    }
    welive_output(d, gid, type)
}
function guest_create(gid, n, lang, old) {
    if (!gid) return;
    welive.flashTitle = 1;
    var d,
    i = $.inArray(gid, offline);
    if (i > -1) {
        d = '客人重新上线了!';
        offline.splice(i, 1);
        g_online.find('#g' + gid).removeClass("offline");
        var o = $("#win" + gid).find(".x-user");
        o.html(o.html().replace(' -- 已离线', '').replace(' -- 已转接', ''))
    } else if ($.inArray(gid, guest) < 0) {
        guest.push(gid);
        if (n == '') n = ((lang == 1) ? '访客 ': ' Guest ') + gid;
        g_online.prepend('<div id="g' + gid + '" onclick="openWin(' + gid + ');" class="g" title="' + n + '"><i>' + n + '</i><b>0</b></div>');
        var recs = '';
        if (old === 1) {
            d = '重新上线的通知已发送!'
        } else {
            d = '客人上线, 问候语已发送!';
            if (old) {
                $.each(old, 
                function(i, rec) {
                    if (rec.t == 1) {
                        recs += '<div class="msg r"><b></b><div class="b"><div class="i">' + format_output(rec.m) + '</div></div><i>' + rec.d + '</i></div>'
                    } else {
                        recs += '<div class="msg l"><b></b><div class="b"><div class="i">' + format_output(rec.m) + '</div></div><i>' + rec.d + '</i></div>'
                    }
                })
            }
            if (recs != '') recs += '<div class="msg s"><div class="b"><div class="ico"></div><div class="i">... 以上为最近对话记录.</div></div></div>'
        }
        createWin(gid, n, recs, lang)
    } else {
        welive.flashTitle = 0;
        return
    }
    guest_output(d, gid, 3)
}
function guest_update(gid) {
    var o = g_online.find("#g" + gid + ">b");
    var x = parseInt(o.html());
    x = x + 1;
    o.html(x).show()
}
function guest_delete(gid) {
    $('#win' + gid).remove();
    g_online.find('#g' + gid).remove();
    guest.splice($.inArray(gid, guest), 1);
    offline.splice($.inArray(gid, offline), 1)
}
function guest_output(d, gid, type) {
    if (!d || !gid || !type) return;
    TitleSound(1);
    var o = $("#win" + gid).find(".g_history");
    o.find(".overview>div.updating").remove();
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
    o.find(".overview").append(d);
    o.welivebar_update('bottom');
    if (!CurrentId || !welive.where) {
        openWin(gid)
    } else {
        if (CurrentId != gid) guest_update(gid)
    }
}
function welive_output(d, gid, type) {
    if (gid) {
        guest_output(d, gid, type)
    } else {
        if (d === false) return;
        TitleSound();
        s_history.append(d);
        s_hwrap.welivebar_update('bottom')
    }
}
function welive_verify() {
    welive.status = 1;
    welive_send('x=2&a=8&s=' + admin.sid + '&ag=' + admin.agent + '&id=' + admin.id);
    $(".set_serving").hide();
    $(".set_busy").show()
}
function welive_close() {
    if (welive.status) {
        s_online.html("");
        s_admins.html(0)
    }
    welive.status = 0;
    $("#websocket1212").remove();
    welive_output('<div class="i"><b></b>连接失败, 3秒后自动重试 ...</div>');
    welive.ttt = setTimeout(welive_link, 3000)
}
function welive_send(d) {
    s_send.addClass('loading2');
    if (welive.status) {
        welive.ws.send(d);
        s_msg.val('')
    } else {
        welive_output('<div class=i><b></b>服务器连接中, 请等待 ...</div>')
    }
    s_msg.focus();
    s_send.removeClass('loading2')
}
function guest_send() {
    if (!CurrentId || $.inArray(CurrentId, offline) > -1) return;
    var o = $('#win' + CurrentId).find('.g_msg');
    var msg = $.trim(o.val());
    if (welive.status && msg) {
        msg = msg.replace(/&/g, "||4||");
        welive.ws.send('x=5&g=' + CurrentId + '&i=' + msg);
        o.val('')
    }
    o.focus()
}
function guest_kickout() {
    if ($.inArray(CurrentId, offline) < 0) welive.ws.send('x=6&a=6&g=' + CurrentId);
    guest_delete(CurrentId);
    CurrentId = 0;
    $("#tiptip_holder").hide();
    showNext()
}
function guest_banned(me) {
    if ($.inArray(CurrentId, offline) > -1) return;
    welive.ws.send('x=6&a=7&g=' + CurrentId);
    guest_output('客人已被禁言, 但你仍然可以对其发言!', CurrentId, 4);
    $(me).parent().children(".t_unban").show();
    $(me).hide()
}
function guest_unban(me) {
    if ($.inArray(CurrentId, offline) > -1) return;
    welive.ws.send('x=6&a=10&g=' + CurrentId);
    guest_output('客人禁言状态已解除.', CurrentId, 3);
    $(me).parent().children(".t_banned").show();
    $(me).hide()
}
function get_supporters() {
    if ($.inArray(CurrentId, offline) > -1) {
        $('.s_transfer').html('客人已离线, 无法转接!');
        return
    }
    var num = 0;
    $('.s_transfer').html(s_online.html()).children('li').each(function() {
        var aix = $(this).attr('id').replace('index_', '');
        if (admin.aix == aix) {
            $(this).remove();
            return
        }
        num += 1;
        $(this).click(function() {
            if ($.inArray(CurrentId, offline) > -1) {
                guest_output('客人已离线, 无法转接!', CurrentId, 4);
                return
            }
            welive.ws.send('x=6&a=11&g=' + CurrentId + '&aix=' + aix);
            $("#tiptip_holder").hide()
        })
    });
    if (!num) $('.s_transfer').html('暂无其它客服可转接!')
}
function get_guestprofile(me) {
    var btn = $(me);
    var g_note = $('#win' + CurrentId).find('.g_note');
    if (g_note.is(":hidden")) {
        var loaded = g_note.attr("loaded");
        if (loaded != 1) welive.ws.send('x=2&a=5&g=' + CurrentId);
        g_note.show();
        btn.addClass("hover")
    } else {
        g_note.hide();
        btn.removeClass("hover")
    }
}
function guest_save(me) {
    var data = $(me).closest("form").serialize().replace(/\+/g, " ").replace(/\%26/g, "||4||");
    welive.ws.send('x=2&a=6&g=' + CurrentId + '&' + data)
}
function welive_runtime(gid, msg) {
    if (!gid || !msg) return;
    msg = format_output(msg) + ' <img src="' + SYSDIR + 'public/img/writting.gif">';
    var o = $("#win" + gid).find(".g_history");
    var updating = o.find(".overview>div.updating");
    if (updating.length) {
        updating.find(".i").html(msg)
    } else {
        msg = '<div class="msg updating"><b></b><div class="b"><div class="i">' + msg + '</div></div></div>';
        o.find(".overview").append(msg)
    }
    o.welivebar_update('bottom')
}
function welive_init() {
    guest = new Array();
    offline = new Array();
    g_online = $("#g");
    s_chat = $("#s_chat");
    s_msg = s_chat.find(".s_msg");
    s_send = s_chat.find(".s_send");
    s_admins = s_chat.find(".s_admins");
    s_title = s_chat.find(".s_title").children(".l");
    s_hwrap = s_chat.find("#s_hwrap");
    s_owrap = s_chat.find("#s_owrap");
    s_history = s_hwrap.find(".overview");
    s_online = s_owrap.find(".overview");
    sounder = $("#wl_sounder");
    welive_link();
    myWin88 = new WeLiveWin();
    var s_historyViewport = s_hwrap.find(".viewport"),
    s_onlineViewport = s_owrap.find(".viewport"),
    xHeight = $(window).height() - 88;
    g_online.height(xHeight);
    s_chat.height(xHeight);
    s_historyViewport.height(xHeight - 78);
    s_onlineViewport.height(xHeight - 74);
    s_hwrap.welivebar();
    s_owrap.welivebar();
    $(window).resize(function() {
        var wh = $(window).height() - 88;
        g_online.height(wh);
        s_chat.height(wh);
        s_historyViewport.height(wh - 78);
        s_onlineViewport.height(wh - 74);
        s_hwrap.welivebar_update('bottom');
        s_owrap.welivebar_update()
    });
    s_msg.keyup(function(e) {
        if (e.keyCode == 13) s_send.trigger("click")
    }).focus(function() {
        s_title.removeClass("off");
        welive.where = 0;
        if (CurrentId) $('#win' + CurrentId).find('.x-user').removeClass("x-now")
    });
    s_send.click(function(e) {
        var msg = $.trim(s_msg.val());
        if (msg) {
            msg = msg.replace(/&/g, "||4||");
            welive_send('x=1&i=' + msg)
        } else {
            s_msg.focus()
        }
        e.preventDefault()
    });
    $("#wl_ring").click(function(e) {
        if (welive.sound) {
            welive.sound = 0;
            $(this).addClass("s_ringoff").removeClass("s_ring")
        } else {
            welive.sound = 1;
            $(this).addClass("s_ring").removeClass("s_ringoff")
        }
        s_msg.focus();
        e.preventDefault()
    });
    s_chat.find(".s_face").tipTip({
        content: $(".smilies_div").html(),
        keepAlive: true,
        maxWidth: "242px",
        defaultPosition: "top",
        edgeOffset: -31,
        delay: 300
    });
    $(".set_busy").tipTip({
        content: '1. 挂起后, 将不再接受新客人加入, 但其他客服转接过来的客人仍会进入.<br><br>2. 一般地, 当自己特别忙时可使用挂起功能, 如果离开座席较长时间, 建议退出客服.<br><br>3. 如果所有在线的客服都挂起了, 挂起功能将失效.'
    });
    $(".sysinfo>a").tipTip({
        content: '1. 按 Ctrl + Alt: 在客服交流区与当前客人小窗口间切换.<br>2. 按 Ctrl + 下箭头 或 Esc键: 关闭当前客人小窗口.<br>3. 按 Ctrl + 上箭头: 展开关闭的客人小窗口.<br>4. 按 Ctrl + 左或右箭头: 在已展开的客人小窗口间切换.',
        maxWidth: "320px",
        defaultPosition: "top",
        delay: 300
    });
    $(".set_serving, .reset_socket").tipTip();
    pagetitle = document.title;
    $(document).mousedown(stopFlashTitle).keydown(function(e) {
        stopFlashTitle();
        if (e.which == 27 || (e.ctrlKey && e.which == 40)) {
            closeWin(CurrentId)
        } else if (e.ctrlKey && (e.which == 37 || e.which == 39)) {
            showNext()
        } else if (e.ctrlKey && e.which == 38) {
            showNext(1)
        } else if (e.ctrlKey && e.which == 18) {
            if (CurrentId) {
                if (welive.where == 1) {
                    s_msg.focus()
                } else {
                    openWin(CurrentId)
                }
            } else {
                s_msg.focus()
            }
        }
    });
    welive.sound1 = '<object data="' + SYSDIR + 'public/sound1.swf" type="application/x-shockwave-flash" width="1" height="1" style="visibility:hidden"><param name="movie" value="' + SYSDIR + 'public/sound1.swf"><param name="quality" value="high"></object>';
    welive.sound2 = '<object data="' + SYSDIR + 'public/sound2.swf" type="application/x-shockwave-flash"><param name="movie" value="' + SYSDIR + 'public/sound2.swf"></object>';
    window.onbeforeunload = function(event) {
        clearTimeout(welive.ttt);
        return " "
    };
    $(window).unload(function() {
        clearTimeout(welive.ttt)
    })
}
var tttt = 0,
pagetitle,
flashtitle_step = 0,
sounder,
towhere = 0;
var guest,
offline,
g_online,
s_chat,
s_msg,
s_history,
s_online,
s_send,
s_hwrap,
s_owrap,
s_admins,
s_title;
var welive = {
    ws: {},
    index: 0,
    status: 0,
    ttt: 0,
    flashTitle: 0,
    sound: 1,
    sound1: '',
    sound2: '',
    where: 0
};
var myWin88,
CurrentId = 0,
zIndex = 2000;
var x_win_content = '<div class="g_history"><div class="scb_scrollbar scb_radius"><div class="scb_tracker"><div class="scb_mover scb_radius"></div></div></div><div class="viewport"><div class="overview">888888</div></div></div><div class="g_tools"><a class="t_smilies" title="表情符号"></a><a class="t_phrase" title="常用短语"></a><a class="t_transfer" title="转接客人"></a><a class="t_note" title="记录客人信息" onclick="get_guestprofile(this);return false;"></a><a class="t_unban" title="解除禁言" onclick="guest_unban(this);return false;"></a><a class="t_banned" title="禁止发言" onclick="guest_banned(this);return false;"></a><a class="t_kickout" title="踢出客人"></a></div><div class="g_bott"><input name="g_msg" type="text" class="g_msg"><a class="g_send" title="发送" onclick="guest_send();return false;"></a></div><div class="g_note" loaded="0"><form onsubmit="return false;"><li class="f"><b>来源:</b><u><a href="" target="_blank" class="fromurl"></a></u></li><li class="f"><b>地区:</b><u class="ipzone"></u></li><li class="f"><b>意向:</b><input type="radio" value="1" name="grade"><i>1分</i><input type="radio" value="2" name="grade"><i>2分</i><input type="radio" value="3" name="grade"><i>3分</i><input type="radio" value="4" name="grade"><i>4分</i><input type="radio" value="5" name="grade"><i>5分</i></li><li><b>姓名:</b><input name="fullname" type="text" class="s"></li><li><b>电话:</b><input name="phone" type="text" class="l"></li><li><b>Email:</b><input name="email" type="text" class="l"></li><li><b>地址:</b><input name="address" type="text" class="l"></li><li><b>备注:</b><textarea name="remark"></textarea></li><li class="bt"><input class="cancel" type="submit" value="保存更新" onclick="guest_save(this);return false;"></li></form></div>';
$(function() {
    $.ajax({
        url: './index.php?c=opensocket&a=ajax',
        dataType: 'json',
        async: true,
        cache: false
    });
    welive_init();
    $("#topbar dl").Jdropdown({
        delay: 50
    },
    function(a) {});
    $(".logout").click(function(e) {
        showDialog('确定退出 WeLive 在线客服系统吗?', '', 
        function() {
            document.location = 'index.php?a=logout'
        });
        e.preventDefault()
    })
});