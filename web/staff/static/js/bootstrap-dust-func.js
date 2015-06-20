
//basic

if(typeof(String.prototype.trim) === "undefined")
{
	String.prototype.trim = function()
	{
		return String(this).replace(/^\s+|\s+$/g, '');
	};
}

function utf8_to_b64( str ) {
	return window.btoa(unescape(encodeURIComponent( str )));
}

function b64_to_utf8( str ) {
	return decodeURIComponent(escape(window.atob( str )));
}

// Usage:
//utf8_to_b64('✓ à la mode'); // "4pyTIMOgIGxhIG1vZGU="
//b64_to_utf8('4pyTIMOgIGxhIG1vZGU='); // "✓ à la mode"

/**
 * Extracts the browser name and version number from user agent string.
 *
 * @param userAgent
 *			The user agent string to parse. If not specified, the contents of
 *			navigator.userAgent are parsed.
 * @param elements
 *			How many elements of the version number should be returned. A
 *			value of 0 means the whole version. If not specified, defaults to
 *			2 (major and minor release number).
 * @return A string containing the browser name and version number, or null if
 *		 the user agent string is unknown.
 */
function identifyBrowser(userAgent, elements) {
	var regexps = {
			'Chrome': [ /Chrome\/(\S+)/ ],
			'Firefox': [ /Firefox\/(\S+)/ ],
			'MSIE': [ /MSIE (\S+);/ ],
			'Opera': [
				/Opera\/.*?Version\/(\S+)/,	 /* Opera 10 */
				/Opera\/(\S+)/				  /* Opera 9 and older */
			],
			'Safari': [ /Version\/(\S+).*?Safari\// ]
		},
		re, m, browser, version;

	if (userAgent === undefined)
		userAgent = navigator.userAgent;

	if (elements === undefined)
		elements = 2;
	else if (elements === 0)
		elements = 1337;

	for (browser in regexps)
		while (re = regexps[browser].shift())
			if (m = userAgent.match(re)) {
				version = (m[1].match(new RegExp('[^.]+(?:\.[^.]+){0,' + --elements + '}')))[0];
				return browser + ' ' + version;
			}

	return null;
}


// 有关后台操作的对象和方法

if (!this.Dust) {
	var Dust = {};
}
(function($$, $){
	$$.extend = function (destination, source, callback) {
		for (var property in source)
		  destination[property] = source[property];
		if($$.dev) destination['__noSuchMethod__'] = function (prop, args){ error(prop, " : no such method exists", args); };
		if ($.isFunction(callback)) callback();
		return destination;
	};
	$$.cache = {};
	$$.extend(window, {
		log: ($$.dev && window.console) ? function() { console.log.apply(console, arguments); } : function() { },
		// error: ($$.dev && window.console) ? function() { console.error.apply(console, arguments); } : function() { },
		dir: ($$.dev && window.console) ? function(a) { console.dir(a); } : function() { },
		info: ($$.dev && window.console) ? function(a) { console.info(a); } : function() { },
	}, function(){
		log("logging enabled");
		log("Window object extended");
	});

	$$.dialog_tpl = '<div id="dialog" class="modal fade" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">' +
	  '<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title"></h4></div>' +
	  '<div class="modal-body"> <p></p> </div>' +
	  '<div class="modal-footer"><button type="button" class="btn hide btn-default" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary">OK</button></div>' +
	'</div><!-- /.modal-content --> </div><!-- /.modal-dialog --> </div><!-- /.modal -->';

	$$.genDialog = function(title, message) {
		var dialog = $('#dialog');
		if (dialog.length == 0) {
			$('body').append($$.dialog_tpl);
			dialog = $('#dialog');
		}
		dialog.find('.modal-title').text(title);
		dialog.find('.modal-body>p').html(message);
		dialog.modal('show').on('shown.bs.modal', function () {
			$(this).find('input, select, textarea').first().focus();
		}).on('hide.bs.modal', function(){
			$(this).find('button.btn-default').addClass('hide');
		});

		return dialog;
	}

	$$.extend($$, {

		alert: function(message, title, callback){
			if ($.isFunction(title)) {
				callback = title;
				title = false;
			}
			title = title || "提示!";
			var dialog = $$.genDialog(title, message);
			dialog.find('button.btn-primary').click(function(){
				if ($.isFunction(callback)) callback();
				dialog.modal('hide');
			});

		},
		confirm: function(title, message, callback, falseCallBack, height, width){
			if(title == null) title = "确认";
			if(message == null) message = "Are you sure want to proceed ?";
			var dialog = $$.genDialog(title, message);
			dialog.find('button.btn-default').removeClass('hide').click(function(){
				if ($.isFunction(falseCallBack)) falseCallBack();
				dialog.modal('hide');
			});
			dialog.find('button.btn-primary').click(function(){
				if ($.isFunction(callback)) callback();
				dialog.modal('hide');
			});
		},
		prompt: function(title, message, default_value, callback, optional_message){
			optional_message = optional_message || "";
			if (title == null || message == null || callback == null)
				return false;
			default_value = default_value || "";
			var dialog = $$.genDialog(title, message);
			dialog.find('.modal-body>p').empty().append('<label for="prompt_value">'+message+'</label>&nbsp;&nbsp;\
						<input type="text" id="prompt_value" value="'+default_value+'"/><br><br><span>'+optional_message+'</span>');
			dialog.find('button.btn-default').removeClass('hide').click(function(){
				dialog.modal('hide');
			});
			dialog.find('button.btn-primary').click(function(){
				var value = $('#prompt_value').val();
				if ($.isFunction(callback)) callback(value);
				dialog.modal('hide');
			});

		},
		splitAjaxResult: function(data) { //console.log(data, typeof data);
			if (typeof data === "undefined" || data === null) {
				return "操作完成，但返回结果为空";
			}
			var msg, lb_true = "操作成功！", lb_false = "操作失败！！", lb_errno = "\t返回代码: ", lb_error = "\t返回说明: ";
			if (typeof data == "boolean" || typeof data == "string" || typeof data == "number") {
				msg = data ? lb_true : lb_false;
			}
			else if ($.isArray(data) && data.length > 0) {
				msg = data[0] ? lb_true : lb_false;
				if (data.length > 1) {
					msg += lb_errno + data[1];
					if (data.length > 2) msg += lb_error + data[2];
				}
			}
			else if(data && data.status == 'ok' || parseInt(data) > 0){
				msg = lb_true;
				if (typeof data.message === "string") {
					msg += "\n返回消息：" + data.message;
				}
			}else{
				msg = lb_false;
				if(typeof data.errors === "object") {
					if(data.errors.code) msg += lb_errno + data.errors.code;
					if(data.errors.reason) msg += lb_error + data.errors.reason;
					else if(data.errors.message) msg += lb_error + data.errors.message;
				}
				else {
					if (data.errno && data.errno > 0) {
						msg += lb_errno + data.errno;
					}
					if (data.error && data.error !== "") {
						msg += lb_error + data.error;
					}
				}
			}
			return msg;
		}
		,
		/**
		 * image preview control
		 * Dust.previewImage("input#picfile", "#preview_box", {clear: '#btn_upload_clear'});
		 */
		previewImage: function(el_file, el_preview, options) {
			options = $.extend({
				clear:  false
			}, options || {});
			$(el_file).change(function(){
				var files = this.files;
				if (files.length == 0) {
					return;
				}
				var file = files[0]; //log(file);
				var imageType = /image.+/;
				if (!file.type.match(imageType)) { // 非图片文件
					$(el_file).val(null);
					$(el_preview).empty().hide();
					return;
				}
				var img = document.createElement("img");
				img.classList.add("obj");
				img.file = file;
				$(el_preview).html(img);
				var reader = new FileReader();
				reader.onloadend = (function(aImg) {
					return function(e) {
						aImg.src = e.target.result;
						$(aImg).parent().fadeIn();
					};
				})(img);
				reader.readAsDataURL(file);

			});
			if (typeof options.clear == "string" || typeof options.clear == "obj") {
				$(options.clear).click(function(){
					$(el_file).val(null);
					$(el_preview).fadeOut().empty();
				});
			}
		}

	}, function (){
		log("$$ object extended");
	});


	// 使对象居中
	$.fn.center = function () {
		this.css("position","absolute");
		this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
		this.css("left", ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
		return this;
	}

})(Dust, jQuery);

/**
 * 处理ajax操作的返回结果
 * 返回结果的格式
 * {
 *  	success: true or false,
 *  	errors: { code: 'error_code', reason: 'error_reason'},
 * }
 *
 */
function alertAjaxResult(res, callback)
{
	if(typeof res === "string" && res !== "") res = JSON.parse(res);
	try{
		log(res, typeof res, typeof res.meta);
	} catch(e){}
	var title = !!res.meta.ok ? '操作成功！' : '操作失败！！', msg = '', data = res.data;

	if (typeof res.data != "undefined") {
		if ($.isArray(data)) {
			msg += "\n返回消息：";
			$.each(data, function(i, item){
				msg += (i+1) + ": <pre>" + dump(item) + "</pre>\n";
			});
		} else {
			msg += "\n返回消息：";
			msg += '<pre>'+dump(data)+'</pre>';
		}
	}

	if (typeof res.error != "undefined") {
		msg += "\n错误：" + dump(res.error);
	}
	Dust.alert(msg, title, callback);
}


/**
 * Function : dump()
 * Arguments: The data - array,hash(associative array),object
 *	The level - OPTIONAL
 * Returns  : The textual representation of the array.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "  ";

	if(typeof(arr) == 'object') { //Array/Hashes/Objects
		for(var item in arr) {
			var value = arr[item];

			if (null === value) {
				dumped_text += level_padding + "'" + item + "' => null\n";
			}
			else if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}
$(document).ready(function(){
	if(typeof $.fn.fmatter !== "undefined") {
		$.fn.fmatter.dump = function (cellval, opts, act) {
			var str = dump(cellval)
			str = str.replace(/(\'|\")/ig, ''); // '
			return str;
			//return "<span class=\"tooltip\" title=\""+ str +"\" >"+ str.replace(/( +|\n)/ig,' ').substr(0, 60) + "...</span>";
		};
	}
});

jQuery.extend({
	// jQuery has removed the internal $.httpData() method since version 1.5.
	httpData: function( xhr, type, s ) {
		var ct = xhr.getResponseHeader("content-type"),
			xml = type == "xml" || !type && ct && ct.indexOf("xml") >= 0,
			script = type == "script" || !type && ct && ct.indexOf("script") >= 0,
			json = type == "json" || !type && ct && ct.indexOf("json") >= 0,
			data = xml ? xhr.responseXML : xhr.responseText;

		if ( xml && data.documentElement.tagName == "parsererror" )
			throw "parsererror";

		// Allow a pre-filtering function to sanitize the response
		// s != null is checked to keep backwards compatibility
		if( s && s.dataFilter )
			data = s.dataFilter( data, type );

		// If the type is "script", eval it in global context
		if ( script )
			jQuery.globalEval( data );

		// Get the JavaScript object, if JSON is used.
		if ( json )
			data = eval("(" + data + ")");

		return data;
	}
});


(function($) {
	var as = $.ajaxSettings;

	$.xhr = {
		registry: {
			xhr: as.xhr
		},
		register:function( name, fn ){
			this.registry[name] = fn;
		}
	};

	// The built-in method is used by default
	// To set another one as default, use $.ajaxSetup({ transport:'my_xhr' })
	as.transport = 'xhr';

	// This handler is used instead, don't override it
	as.xhr = function(){
		return $.xhr.registry[ this.transport ]( this );
	};

	$.fn.editable = function(target, options) {

	};

	/**
	 * example: <div id="land_box"></div>
	 * $('#land_box').radioButtons({data: Catalog.all.land, name: 'land'});
	 */
	$.fn.radioButtons = function(options) {
		options = $.extend({
			data: {'0':'Choice 1','1':'Choice 2'},
			name: 'radio',
			selected: '',
			skipEmpty: true
		}, options);
		var self = $(this).empty(), i = 0;
		for( var k in options.data) {
			if (options.skipEmpty && k === "") continue;
			var id = 'ws_'+options.name+'_'+i, text = options.data[k], radio =
			$("<input />").attr("id", id)
				.attr("type", "radio").attr("name", options.name)
				.attr("value", k);
			if (k === options.selected) radio.attr('checked', 'checked');
			radio.appendTo(self);
			$("<label />").attr("for", id).text(text).appendTo(self);
			i ++;
		}
		self.buttonset();
		return this;
	};

	$.expr[':'].range = function(el) {
		var type = el.getAttribute("type");
		return type && type == 'range' || !!$(el).filter("input").data("rangeinput");
	};

	$.fn.rangeInput = function(options) {
		options = $.extend({
			min: 1,
			max: 100,
			step: 1,
			value: 50,
			range: 'min'
		}, options);
		var input = $(this), slider = $('<div class="range-slider"></div>').insertBefore(input).slider({
			min: options.min,
			max: options.max,
			step: options.step,
			range: options.range,
			value: options.value,
			slide: function(event, ui) {
				input.val(ui.value - 1);
			}
		});
		input.change(function() {
			slider.slider("value", this.value);
		}).addClass('range').val(options.value);

	};

	// format user agent
	$.fn.formatUA = function(options) {
		options = $.extend({
			value: function() {
				return $(this).attr("title");
			}
		}, options);
		var elements = this;

		function format() {
			elements.each(function() {
				var ib = identifyBrowser(options.value.apply(this));
				if ( ib && $(this).text() != ib )
					$(this).text( ib );
			});
		}
		format();
		return this;
	};

})(jQuery);

function ARGB_int_toColor(num) { // packed_argb to int
	num >>>= 0;
	var b = num & 0xFF,
		g = (num & 0xFF00) >>> 8,
		r = (num & 0xFF0000) >>> 16,
		a = ( (num & 0xFF000000) >>> 24 ) / 255 ;
	return "rgba(" + [r, g, b, a].join(",") + ")";
}
// toColor(-5952982)
//"rgba(165,42,42,1)"
// toColor(-12525360)
//"rgba(64,224,208,1)"
