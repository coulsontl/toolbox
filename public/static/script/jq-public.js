// JavaScript Document

$(function () {
    fn();
    $(window).on('resize', function () {
        fn()

    });
});
function fn() {
    var winWidth = window.innerWidth || document.documentElement && document.documentElement.clientWidth || 0,
        pageWidth = 'SMALL';

    // 获取 body 宽度设定。
    var pageWidthDef = {
        SMALL: 1000,
        LARGE: 1200
    };
    if (winWidth >= 1200) {    // @media screen and (min-width: 1280px)
        pageWidth = 'LARGE';
    } else {                  // Default width.
        pageWidth = 'SMALL';
    }


    //document.body.className += (' pu' + pageWidth.toLowerCase());
    $("body").attr("class", ' pu' + pageWidth.toLowerCase());
}
$(document).ready(function () {
    //ToolTop
    menuHover($("#menu li>a"), $("#menu li p"), function (_this) {
        _this.siblings().addClass("OnCurt");
    }, function (_this) {
        _this.siblings().removeClass("OnCurt");
    }, 200);
    //Navbar
    var timer;
    $("#Navbar").hover(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            $("#ToolNav").addClass("ToolNavbar-hover");
        }, 300);
    }, function () {
        clearTimeout(timer);
        $("#ToolNav").removeClass("ToolNavbar-hover");
    });

    $(".WrapHid").each(function () {
        checkFocus({
            obj_input: $(this),
            msgBox: $(this).siblings(".CentHid"),
            Tip: "CentHid"
        });
        clearInput({
            obj_input: $(this),
            msgBox: $(this).siblings("._CentHid"),
            Tip: "_CentHid"
        });
    });

    $(".ToolChoese").each(function () {
        _select({
            select: $(this).find(".SearChoese"),
            options: $(this).find("ul.SearChoese-show"),
            option: $(this).find("ul.SearChoese-show li a"),
            t: "slide", //效果（可选参数）
            parents: $(".ToolChoese")//多个select时，传入父级（可选参数）
        });
    });




    //粘贴IP 分解
    $(".ipgroup").each(function () {
        var group = $(this);
        group.find("input[n^='ip']").bind("paste", function (e) {
            var obj = e.target ? e.target : e.srcElement;
            setTimeout(function () {
                var ip = $(obj).val().trim();
                var ipReg = /^((2[0-4]\d|25[0-5]|[01]?\d\d?)\.){3}(2[0-4]\d|25[0-5]|[01]?\d\d?)(\/(2[0-4]\d|25[0-5]|[01]?\d\d?))?$/;
                if (!ipReg.test(ip)) return;
                var ipArr = ip.split('.');
                group.find("[n='ip1']").val(ipArr[0]);
                group.find("[n='ip2']").val(ipArr[1]);
                group.find("[n='ip3']").val(ipArr[2]);
                var iplastByte = ipArr[3];//允许输入127.0.0.1/2  输出结果为 127 0 0 2
                if (iplastByte.indexOf("/")) {
                    iplastByte = iplastByte.split('/');
                    group.find("[n='ip4']").val(iplastByte[iplastByte.length - 1]);
                } else
                    group.find("[n='ip4']").val(iplastByte);
            }, 50);
        });
    });


});

function menuHover(menuObj, menuItem, itemOverbackall, itemOutbackall, timer) {
    if (!timer) timer = 200;
    var hoverTimer, outTimer;
    menuObj.hover(function () {
        var _this = $(this);
        clearTimeout(hoverTimer);
        hoverTimer = setTimeout(function () {
            menuItem.hide();
            _this.nextAll().show();
        }, timer);
    }, function () {
        var _this = $(this);
        clearTimeout(outTimer);
        outTimer = setTimeout(function () {
            _this.nextAll().hide();
        }, timer);
    });
    menuItem.hover(function () {
        var _this = $(this);
        clearTimeout(hoverTimer);
        hoverTimer = setTimeout(function () {
            _this.show();
        }, timer);
        if (itemOverbackall) itemOverbackall(_this);
    }, function () {
        var _this = $(this);
        clearTimeout(outTimer);
        outTimer = setTimeout(function () {
            _this.hide();
        }, timer);
        if (itemOutbackall) itemOutbackall(_this);
    });
};
//SearchWrapHid-Cent
var checkFocus = function (options) {
    var thisCheck = options.obj_input; //当前input
    var msgBox = options.msgBox; //当前提示标签
    checkValue = thisCheck.val();
    var trime = options.trime !== undefined ? options.trime : 200;
    thisCheck.focus(function () {
        msgBox.fadeOut(trime);
    });
    thisCheck.blur(function () {
        if ($(this).val() == "") {
            if (msgBox.hasClass(options.Tip)) {
                msgBox.stop(true, true).fadeIn(trime);
            }
            return false;
        } else {
            msgBox.stop(true, true).fadeOut(trime);
            return true;
        }
    });
    msgBox.click(function () {
        thisCheck.mousedown();
        thisCheck.focus();
    });

    function init() {
//        if (!options.isselchk)
//            $(".publicSearch input[type='text']:first").focus().select();
        if (checkValue !== '') {
            msgBox.stop(true, true).fadeOut(trime);
        } else {
            msgBox.stop(true, true).fadeIn(trime);
        }
    }
    init();
};
var clearInput = function (options) {
    var thisCheck = options.obj_input; //当前input
    var msgBox = options.msgBox; //当前提示标签
    checkValue = thisCheck.val();
    var trime = options.trime !== undefined ? options.trime : 100;
    thisCheck.bind("blur keyup", function () {
        if ($(this).val() == "") {
            if (msgBox.hasClass(options.Tip)) {
                msgBox.stop(true, true).fadeOut(trime);
            }
        } else {
            msgBox.stop(true, true).fadeIn(trime);
        }
    });
    msgBox.click(function () {
        thisCheck.focus();
        msgBox.stop(true, true).fadeOut(trime);
        thisCheck.val("");
    });

    function init() {
        $("input[type='text']:first").focus().select();
        if (checkValue !== '') {
            msgBox.stop(true, true).fadeIn(trime);
        } else {
            msgBox.stop(true, true).fadeOut(trime);
        }
    }
    init();
};
var _select = function (settings) {
    settings.select.bind("selectstart", function () { return false; }); //禁用选中IE，其他-moz-user-select:none;
    settings.select.click(function (e) {
        if (settings.parents)
            if (settings.parents.length > 1) settings.parents.find("ul").not(settings.options).hide(); //如果有多个select隐藏非当前的所有相关ul
    if (settings.options.is(":visible")) showType(0);
    else showType(1);
    if (settings.selectcallback) settings.selectcallback(this);
    e.stopPropagation();
});
settings.option.click(function () {
    settings.select.text($(this).text());
    settings.select.next().val($(this).attr("val"));
    showType(0);
    if (settings.callback) settings.callback(this);
});
$(document).click(function () {
    if (settings.options.is(":visible")) showType(0);
});

function showType(flage) {
    switch (settings.t) {
        case "slide":
            if (flage) {
                settings.options.slideDown(50);
                settings.select.siblings("span").addClass("corUp");
            }
            else {
                settings.options.slideUp(50);
                settings.select.siblings("span").removeClass("corUp");
            }
            break;
        case "fade":
            if (flage) {
                settings.options.fadeIn(200);
                settings.select.siblings("span").addClass("corUp");
            }
            else {
                settings.options.fadeOut(200);
                settings.select.siblings("span").removeClass("corUp");
            }
            break;
        default:
            if (flage) {
                settings.options.show();
                settings.select.siblings("span").addClass("corUp");
            }
            else {
                settings.options.hide();
                settings.select.siblings("span").removeClass("corUp");
            }
            break;
    }
}
};







//滚动事件
var boxScroll = function (options) {
    var settings = {
        _scroll: $("#scroll"), //滚动的div
        _width: 0,
        _height: 0,
        _top: 0, //定位top
        _left: 0, //定位left
        endElm: "", //结束id
        ow: 10, //padding或margin的值，用来准确定位
        isresize: false,
        callback: function () { }
    };
    if (options)
        $.extend(settings, options);
    var _scroll = settings._scroll;
    _scrollfn();
    $(window).scroll(function () {
        _scrollfn();
    });
    //if (settings.isresize) {
        $(window).resize(function () {
            _scrollfn();
        });
    //}
    function _scrollfn() {
        var _scrolltop = $(window).scrollTop();
        var _scrollleft = $(window).scrollLeft();
        var _postiton = "absolute"; //默认
        //if (sys.ie <= 6)
        //    _postiton = "absolute";
        if (settings.endElm) {
            _scroll.parent().addClass("pr");
            var t = _scroll.parent().position().top;
            var endTop = settings.endElm.offset().top; //结束的TOP
            if (_scrolltop <= settings._top) {
                _scroll.css({
                    position: "static"
                });
            }
            else if (_scrolltop + settings._height >= endTop) {//元素到达指定位置不滚动
                //var e_padding = settings.endElm.parent().innerHeight() - settings.endElm.parent().height();
                var e_margin = settings.endElm.parent().outerHeight(true) - settings.endElm.parent().outerHeight();
                var s_padding = _scroll.innerHeight() - _scroll.height();
                //var s_margin = _scroll.outerHeight(true) - _scroll.outerHeight();
                _scroll.css({
                    position: "absolute",
                    //left: "10px",
                    top: endTop - t - _scroll.height() - e_margin - s_padding
                });
            }
            else {
                _scroll.css({
                    position: _postiton,
                    //left:  "10px",
                    top: _scrolltop - t
                });
            }
        } else {
            settings._winWidth = window.innerWidth || document.documentElement && document.documentElement.clientWidth || 0;
            settings._winHeight = window.innerHeight || document.documentElement && document.documentElement.clientHeight || 0;
            var ob = $('.Map-navbar').length ? $('.Map-navbar') : $(".navfixd");
            if (!ob.length) return;
            var l;
            if (settings._winWidth <= ob.width() + 75) {
                _scroll.css({
                    position: _postiton,
                    left: "auto",
                    right:"0",
                    top: (_scrolltop + (settings._winHeight * 0.9) - 160) + "px"
                }).show();
            } else {
                _scroll.css({
                    position: _postiton,
                    left: ob.offset().left + ob.width(),
                    top: (_scrolltop + (settings._winHeight * 0.9) - 160) + "px"
                }).show();
            }
            if (settings.callback) settings.callback(settings, _scrolltop);
        }
    }
};
; (function () {
    window.sys = {};
    var ua = navigator.userAgent.toLowerCase();
    var s;
    (s = ua.match(/msie ([\d.]+)/)) ? sys.ie = s[1] :
	(s = ua.match(/firefox\/([\d.]+)/)) ? sys.firefox = s[1] :
	(s = ua.match(/chrome\/([\d.]+)/)) ? sys.chrome = s[1] :
	(s = ua.match(/opera\/.*version\/([\d.]+)/)) ? sys.opera = s[1] :
	(s = ua.match(/version\/([\d.]+).*safari/)) ? sys.safari = s[1] : 0;

    if (/webkit/.test(ua)) sys.webkit = ua.match(/webkit\/([\d.]+)/)[1];
})();
/*  内容溢出省略替代，num最大长度  */
; (function ($) {
    $.fn.wordLimit = function (num) {
        this.each(function () {
            if (!num) {
                var copyThis = $(this.cloneNode(true)).hide().css({
                    'position': 'absolute',
                    'width': 'auto',
                    'overflow': 'visible'
                });
                $(this).after(copyThis);
                if (copyThis.width() > $(this).width()) {
                    $(this).text($(this).text().substring(0, $(this).text().length - 4));
                    $(this).html($(this).html() + '...');
                    copyThis.remove();
                    $(this).wordLimit();
                } else {
                    copyThis.remove();
                    return;
                }
            } else {
                var maxwidth = num;
                if ($(this).text().length > maxwidth) {
                    $(this).text($(this).text().substring(0, maxwidth));
                    $(this).html($(this).html() + '...');
                }
            }
        });
    }
})(jQuery);

function loadScript(options) {
    var url = options.url, elms = options.elms, callback = options.callback;
    var script = document.createElement("script");
    script.type = "text/javascript";
    if (script.readyState) {
        script.onreadystatechange = function () {
            if (script.readyState == "loaded" || script.readyState == "complete") {
                script.onreadystatechange = null;
                if (callback) callback();
            }
        };
    } else {
        script.onload = function () {
            if (callback) callback();
        };
    }
    script.src = url;
    elms.appendChild(script)
}




//Layer弹框
 function LalyerTemp() {
     this.layerindex = 0;
     this.loadingindex = 0;
     this.moveindex = 0;
     this.h = winHeight = window.innerHeight || document.documentElement && document.documentElement.clientHeight || 0;
     this.w = winHeight = window.innerWidth || document.documentElement && document.documentElement.clientWidth || 0;
 }
 LalyerTemp.prototype.getOpen = function (options) {
     var _this = this;
     jQuery.ajax({
         type: "POST",
         url: options.url,
         beforeSend: function () {
             _this.loadingindex = layer.load(2);
         },
         success: function (data) {
             layer.close(_this.loadingindex);
             if (!data) return;
             _this.getlayer($.extend({ content: data }, options));
         }
     });
 };
 LalyerTemp.prototype.getlayer = function (options) {
     var defaults = { content: "", type: 0, title: "消息", area: "auto", time: 3000, btn: "", btnAlign: "c", moveType: 0, id: "", fixed: true, shift: 5 };
     options = jQuery.extend(defaults, options);
     var _this = this;
     _this.layerindex = layer.open({
         id: options.id,
         type: options.type,
         title: options.title,
         content: options.content,
         area: options.area,
         time: options.time,
         btn: options.btn,
         yes: function (index, layero) {
             return false;
         },
         resize: false,
         btnAlign: options.align,
         shift: options.shift,
         moveType: options.moveType,
         fixed: options.fixed,
         success: function (layero, index) {
             _this.moveindex = index;
             //layer.style(index, { width: "auto", height: $("#" + options.id).height() > $("#" + options.id).children().height() + $(".layui-layer-title").height() + $(".layui-layer-btn").height() + 23 ? $("#" + options.id).children().height() : "auto" });

             if (this.h < $("#" + options.id).height() || _this.w < $("#" + options.id).width()) {
                 layer.style(index, { top: $(document).scrollTop() });
             }

             $(window).resize(function () {
                 var height = window.innerHeight || document.documentElement && document.documentElement.clientHeight || 0;
                 var width = window.innerWidth || document.documentElement && document.documentElement.clientWidth || 0;

                 _this.h = height, _this.w = width;
                 if (_this.h < $("#" + options.id).height() || _this.w < $("#" + options.id).width()) {
                     layer.style(_this.moveindex, { top: $(document).scrollTop() });
                     return;
                 }

                 var dl = getid(options.id);
                 if (dl) {
                     layer.style(index, {
                         top: ((height - dl.clientHeight) / 2) + "px",
                         left: ((width - dl.clientWidth) / 2) + "px"
                     });
                 }
             });
             if (options.callback) options.callback(index, layero);
         },
         moveEnd: function () {
             if (_this.h < $("#" + options.id).height() || _this.w < $("#" + options.id).width()) {
                 layer.style(_this.moveindex, { top: $(document).scrollTop() });
             }
         },
         cancel: function (index) {
             layer.close(index);
         }
     });
 };