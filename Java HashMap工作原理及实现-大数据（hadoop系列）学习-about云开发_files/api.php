(function(){	//原型部分img
	var tuiFixed = new Function();
	tuiFixed.prototype = {
		init : function () {
			if (!this.oBox) {
				document.write("<div id='tuiFixedTemp_" + this['request']['aid'] + "'></div>");
				var tempDom = document.getElementById('tuiFixedTemp_' + this.request.aid)
					this.oBox = tempDom.parentNode;
				this.oBox.removeChild(tempDom);
			};
			//若宽度自适应，计算父容器宽度
			if (this['set']['style']['style_width'] == 0) {
				var pl = parseInt(getEyeJsStyle(this.oBox, 'paddingLeft')) || 0;
				var pr = parseInt(getEyeJsStyle(this.oBox, 'paddingRight')) || 0;
				var oWidth = parseInt(this.oBox.offsetWidth) - pl - pr;
				this['width'] = oWidth;
				var li_width = Number(this['set']['pic']['pic_width']) + 14;
				var ul_width = oWidth - 22;
				var pic_col = Math.floor(ul_width / li_width) || 1;
				//console.log (oWidth,li_width,ul_width,pic_col)
				this['style_pic_col'] = pic_col;
			} else {
				this['width'] = this['set']['style']['style_width'];
				this['style_pic_col'] = this['set']['style']['style_pic_col'];
			};
			function getEyeJsStyle(obj,styleName){
				if(obj.currentStyle){//ie
				   return obj.currentStyle[styleName];
				}else{ //ff
				   var arr=window.getComputedStyle(obj , null)[styleName];
				   return arr;
				};
			};
			//若高度自适应，计算内容高度
			if (this['set']['style']['style_length'] == 0) {
				//??
			} else {
				this['height'] = this['set']['style']['style_length'];
			};
			//分页
			this.page = 1;
			//每页数据
			if (this['set']['base']['data_type'] == 0) {
				this.perPage = this['set']['style']['style_txt_col'] * this['set']['style']['style_txt_row'];
			} else if (this['set']['base']['data_type'] == 2) {
				this.perPage = this['style_pic_col'] * this['set']['style']['style_pic_row'];
			} else {
				this.perPage = this['style_pic_col'] * this['set']['style']['style_pic_row'] + this['set']['style']['style_txt_col'] * this['set']['style']['style_txt_row'];
			};
			/***默认数据***/
			if (!this['set']['slide']) {
				this['set']['slide'] = {
					"slide_type" : "0", //切换类型 0 = 关闭 1 = 换一换 2 = 幻灯
					"change_title" : "1", //换一换 文字 0 = 未开启 1 = 开启
					"change_title_txt" : "换一换", //换一换 文字
					"change_title_size" : "12", //换一换 字体大小
					"change_title_bold" : "0", //换一换 字体粗细	0=无 1=有
					"change_title_family" : "0", //换一换 字体
					"change_title_color" : "333333", //换一换 字体颜色
					"change_icon" : "1", //换一换 图标 0 = 未开启 1 = 开启
					"change_icon_type" : "1", //换一换 标志 0 = 白色 1 = 橙色
					"change_background_color" : "0", //换一换 背景 0 = 灰色 1 = 白色透明
					"change_show_type" : "0" //换一换 形式 0 = 无动画 1 = 左右 2 = 渐隐渐现
				};
			};
			//数据总数
			this.total = this.data.length;
			//总页数
			this.maxPage = Math.floor(this.total / this.perPage);
			this.maxPage = Math.min(this.maxPage, 3);
			this.maxPage = Math.max(this.maxPage, 1);
			//创建iframe
			var iframe = document.createElement('iframe');
			iframe.setAttribute('allowTransparency', 'true');
			iframe.setAttribute('frameBorder', '0');
			iframe.setAttribute('scrolling', 'no');
			iframe.style.cssText = 'float:none;display:block;overflow:hidden;z-index:2147483646;margin:0;padding:0;border:0 none;background:none;';
			iframe.style.height = this['height'] + 'px';
			iframe.style.width = this['width'] + 'px';
			this.oBox.appendChild(iframe);
			if (/msie/i.test(navigator.userAgent)) {
				var that = this;
				try {
					iframe.contentWindow.document;
					this.o = iframe;
					//创建内容
					this.createHtml();
				} catch (e) {
					var base = document.getElementsByTagName('base');
					if (base && base.length > 0) {
						var baseTarget = {};
						for (var i=0;i<base.length;i++) {
							baseTarget[base[i]] = base[i].target;
							if (base[i].target == '_self') {
								continue;
							};
							base[i].target = '_self';
						};
					};
					
					iframe.src = 'javascript:void((function(){document.open();document.domain="' + document.domain + '";document.close()})())';
					if (!window.XMLHttpRequest) {
						setTimeout(function () {
							that.o = iframe;
							that.createHtml();
						}, 0);
					} else {
						this.o = iframe
							//创建内容
							this.createHtml();
					};
					
					if (base && base.length > 0) {
						for (var i=0;i<base.length;i++) {
							if (base[i].target != '_self') {
								continue;
							};
							base[i].target = baseTarget[base[i]];
						};
					};
				}
			} else {
				this.o = iframe
					//创建内容
					this.createHtml();
			};
			//请求
			if (!this.demo) {
				this.funcQuery();
			};

		},
		createHtml : function () {
			var that = this;
			this.oDoc = this.o.contentWindow.document;
			this.oDoc.open();
			this.oDoc.write("<!doctype html><html><head><meta charset='utf-8'><title>无标题文档</title><style type='text/css'>body,div,ul,li,em,span,a,p{padding:0;margin:0;}img{border:0 none;display:block;}em{font-weight:normal;font-style:normal;}ol,ul{list-style:none;}table{border-collapse:collapse;border-spacing:0;}.tui{overflow:hidden;border-width:1px;border-style:solid;position:relative;}.title{overflow:hidden;position:relative;}.tools_0{position:absolute;top:0;right:5px;overflow:hidden;}.tools_1{position:relative;height:30px;line-height:30px;overflow:hidden;right:5px;}.logo{float:left;}.logo a{display:block;width:18px;height:12px;overflow:hidden;text-indent:-999em;cursor:pointer;position:absolute;left:10px;top:50%;margin-top:-6px;}.logo span{float:left;padding-left:33px;}.change{height:20px;overflow:hidden;line-height:20px;display:none;position:relative;right:0;top:50%;margin-top:-10px;}.change{float:right;}.change a{height:20px;overflow:hidden;font-size:12px;float:left;color:#333;text-decoration:none;}.change a:hover{background:none;}.change,.change em,.change b,.change span,.change i{cursor:pointer;}.change em{float:left;height:20px;position:relative;z-index:2;font-style:normal;overflow:hidden;}.change b{display:block;text-indent:-999em;width:10px;height:20px;float:left;}.change span{float:left;height:20px;}.change i{float:left;height:20px;width:20px;background-repeat:no-repeat;}.change i.i_1{background-position:0 0;}.change i.i_0{background-position:0 -20px;_margin-top:-20px;}.change a.a_0 em{background:#eee;}.change a.a_0 b.b_0{background-position:0 0;_margin-top:0;}.change a.a_0 b.b_1{background-position:0 -30px;_margin-top:-30px;}.change a.a_0:hover b.b_0{background-position:0 -60px;_margin-top:-60px;}.change a.a_0:hover b.b_1{background-position:0 -90px;_margin-top:-90px;}.change a.a_0:hover em{background-color:#e1e1e1;}.change a.a_1 em{background-position:0 -240px;background-repeat:repeat-x;_filter:progid:DXImageTransform.Microsoft.gradient(enabled='true',startColorstr='#33FFFFFF',endColorstr='#33FFFFFF');_background:none;}.change a.a_1 b.b_0{background-position:0 -120px;_margin-top:-120px;}.change a.a_1 b.b_1{background-position:0 -150px;_margin-top:-150px;}.change a.a_1:hover b.b_0{background-position:0 -180px;_margin-top:-180px;}.change a.a_1:hover b.b_1{background-position:0 -210px;_margin-top:-210px;}.change a.a_1:hover em{background-position:0 -270px;background-repeat:repeat-x;_background:none;_filter:progid:DXImageTransform.Microsoft.gradient(enabled='true',startColorstr='#7FFFFFFF',endColorstr='#7FFFFFFF');}.link{float:right;display:none;}#link a,#foot a{color:#969696;font-size:12px;text-decoration:none;}#link a:hover,#foot a:hover{text-decoration:underline;}.content{overflow:hidden;margin:0 10px;}.box,.img,.txt{width:9999em;overflow:hidden;}.img ul{float:left;}.img li{float:left;overflow:hidden;margin-top:10px;display:inline;}.img li.i_0{margin-left:0;}.img img{display:block;overflow:hidden;}.img a{display:block;width:100%;}.img a em{display:block;overflow:hidden;cursor:pointer;}.img a span{display:block;overflow:hidden;padding-top:5px;cursor:pointer;}.txt{padding-top:10px;}.txt ul{float:left;}.txt li{display:inline;float:left;overflow:hidden;}.hot{overflow:hidden;margin-top:10px;}.hot ul{overflow:hidden;*zoom:1;}.hot li{float:left;word-wrap:normal;word-break:keep-all;padding-left:10px;}.foot{height:30px;line-height:30px;text-align:right;font-size:12px;}.focus{height:16px;line-height:16px;overflow:hidden;float:right;display:none;position:relative;right:0;top:50%;margin-top:-8px;}.focus li{float:left;width:16px;height:16px;text-align:center;padding-left:5px;}.focus a{display:block;width:16px;height:16px;overflow:hidden;text-decoration:none;font-size:12px;}</style></head><body><div class='tui' id='tui'><div id='title' class='title'><div class='logo' id='logo'><a href='' target='_blank' hidefocus='true' title='云推荐'>云推荐</a><span></span></div></div><div id='tools'><div id='change' class='change'><a href='javascript:;' hidefocus='true'><b class='b_0'></b><em><i></i><span></span></em><b class='b_1'></b></a></div><div id='link' class='link'><a href='' target='_blank' title='云推荐'>云推荐</a></div><ol id='focus' class='focus'></ol></div><div id='content' class='content'><div class='box' id='box'><div class='img' id='img'></div><div id='txt' class='txt'></div></div><div id='hot' class='hot'></div><div id='foot' class='foot'><a href='' target='_blank' title='云推荐'>云推荐</a></div></div></div>" + this.funcStyle() + "</body></html>");
			this.oDoc.close();
			//定义容器
			this.oTui = this.oDoc.getElementById('tui'); //外容器
			this.oTitle = this.oDoc.getElementById('title'); //标题栏
			this.oImg = this.oDoc.getElementById('img'); //图片容器
			this.oTxt = this.oDoc.getElementById('txt'); //文字容器
			this.oHot = this.oDoc.getElementById('hot'); //热词容器
			this.oFoot = this.oDoc.getElementById('foot'); //底部	链接
			this.oLink = this.oDoc.getElementById('link'); //头部 链接
			this.oChange = this.oDoc.getElementById('change'); //换一换
			this.oFocus = this.oDoc.getElementById('focus');	//轮播
			this.oContent = this.oDoc.getElementById('content'); //内容
			this.oCon = this.oDoc.getElementById('box'); //图文容器
			this.oLogo = this.oDoc.getElementById('logo'); //logo
			this.oTools = this.oDoc.getElementById('tools'); //tools
			//标题
			var ts = this.oLogo.getElementsByTagName('span')[0];
			var ta = this.oLogo.getElementsByTagName('a')[0];
			if (this['set']['txt']['txt_title_icon'] == 0 && this['set']['txt']['txt_title'] == 0) {
				this.oTitle.style.display = 'none';
				this.oTools.className = "tools_1";
			} else {
				this.oTools.className = "tools_0";
				if (this['set']['txt']['txt_title_icon'] == 1) {
					ta.href = this.tuiUrl + '?pd=logo';
					if (this['set']['logo']['logo_background_user'] != 2) {
						ta.className = 'a_' + this['set']['logo']['logo_background_user'];
					} else {
						ta.style.backgroundImage = 'url(' + this['set']['logo']['logo_background_img'] + ')';
						ta.style.backgroundPosition = '0 0';
						ta.style.backgroundRepeat = 'no-repeat';
					};
				} else {
					ts.style.paddingLeft = '10px';
					ta.style.display = 'none';
				};
				if (this['set']['txt']['txt_title'] == 1) {
					ts.innerHTML = this['set']['txt']['txt_title_txt'];
				};
			};
			//tools是否出现
			if ((this['set']['slide']['slide_type'] == 0 || this.maxPage == 1) && this['set']['logo']['logo_position'] != 1) {
				this.oTools.style.display = "none";
			} else {
				//换一换
				if (this['set']['slide']['slide_type'] != 1 || this.maxPage == 1) {
					this.oChange.style.display = "none";
				} else {
					this.oChange.style.display = "block";
					var cs = this.oChange.getElementsByTagName("span")[0]; //文字
					var ca = this.oChange.getElementsByTagName("a")[0]; //按钮背景
					var ci = this.oChange.getElementsByTagName("i")[0]; //图标
					this.oChange.title = this['set']['slide']['change_title_txt'] || '换一换';
					if (this['set']['slide']['change_title'] == 1) {
						cs.innerHTML = this['set']['slide']['change_title_txt'];
					} else {
						cs.style.display = "none";
					};
					if (this['set']['slide']['change_icon'] == 1) {
						ci.className = "i_" + this['set']['slide']['change_icon_type'];
					} else {
						ci.style.display = "none";
					};
					ca.className = "a_" + this['set']['slide']['change_background_color'];
					this.oChange.onclick = function () {
						that.funcChange();
					};
				};
				//轮播
				clearInterval(that.autoTime);
				if (this['set']['slide']['slide_type'] != 2 || this.maxPage == 1) {
					this.oFocus.style.display = "none";
				} else if (this['set']['slide']['slide_type'] == 2) {
					clearInterval(that.autoTime);
					var focusHtml = '';
					for (var i = 1;i <= this.maxPage;i++) {
						focusHtml += '<li><a href="javascript:;">'+ i +'</a></li>';
					};
					this.oFocus.innerHTML = focusHtml;
					this.oFocus.style.display = "block";
					var focusA = this.oFocus.getElementsByTagName('a');
					focusA[0].className = 'active';
					for (var i=0;i<focusA.length;i++) {
						focusA[i].index = i;
						focusA[i].onclick = function (){
							clearInterval(that.autoTime)
							showFocus(this.index);
						};
					};
					function showFocus(index,boo){
						removeAllClass();
						var node = focusA[index] || focusA[0];
						node.className = 'active';
						var page = index + 1;
						if (boo) {
							that.funcChange();
						} else {
							that.funcChange(1,page);
						};
						
					};
					function autoFocus(pages){
						clearInterval(that.autoTime);
						pages = pages || 1;
						var timer = that['set']['slide']['slide_timer'] || 5;
						timer = timer * 1000;
						that.autoTime = setInterval(
							function(){
								showFocus(pages,1);
								pages == that.maxPage ? pages = 1 : pages ++;
							},
							timer
						);
					};
					autoFocus();
					this.oDoc.onmouseout = function (){
						for (var i = 0;i < focusA.length;i++) {
							if (focusA[i].className == 'active') {
								autoFocus(i+1);
								break;
							};
						};
					};
					this.oDoc.onmouseover = function (){
						clearInterval(that.autoTime);
					};
					function removeAllClass(){
						for (var i=0;i<focusA.length;i++) {
							focusA[i].className = '';
						};
					};
				};
				//云推荐位置
				if (this['set']['logo']['logo_position'] && this['set']['logo']['logo_position'] == 1) {
					this.oFoot.style.display = "none";
					this.oLink.style.display = "block";
				} else {
					this.oFoot.style.display = "block";
					this.oLink.style.display = "none";
				};
			};
			this.oFoot.getElementsByTagName('a')[0].href = this.tuiUrl + '?pd=PowerBy';
			this.oLink.getElementsByTagName('a')[0].href = this.tuiUrl + '?pd=PowerBy';
			//内容
			for (var j = 0; j < this.maxPage; j++) {
				var dataLength = this['data'].length - this.perPage * j;
				var target = '_blank';
				if (this['set']['txt']['txt_link_target'] == 1 && !this.demo) {
					target = '_parent';
				};
				//图片容器内容
				if (this['set']['base']['data_type'] != 0) {
					//图片
					//显示图片数量
					var imgLength = Math.min(this['style_pic_col'] * this['set']['style']['style_pic_row'], dataLength);
					var trueimg = 0,
					defaultimg = 0,
					itelimg = 0,
					totalimg = 0;
					var ihtml = '';
					for (var x = 0; x < imgLength; x++) {
						var i = x + this.perPage * j;
						if (!this['data'][i]['title']) {
							continue;
						}
						if (x % this['style_pic_col'] != 0) {
							ihtml += "<li>";
						} else {
							ihtml += '<li class="i_0">';
						};
						var has_thumb = this['data'][i]['has_thumb'] || 'false';
						var is_smart_thumb = this['data'][i]['is_smart_thumb'] || 'false';
						ihtml += "<a href='" + this['data'][i]['url'] + "' target='" + target + "' title='" + this['data'][i]['title'] + "'><em><img src='" + this['imgLoad'] + "' alt='" + this['data'][i]['thumbnail'] + "' title='" + this['data'][i]['title'] + "' hidefocus='true' jsdata='" + has_thumb + "' userimg='" + this['data'][i]['algId'] + "' data-img='" + is_smart_thumb + "'></em>";
						if (this['set']['pic']['pic_summary'] == '1') {
							if (this['data'][i]['title']) {
								ihtml += "<span>" + this['data'][i]['title'] + "</span>";
							} else {
								ihtml += "<span></span>";
							};
						};
						ihtml += "</a></li>";
					};
					var imgUl = this.oDoc.createElement("ul");
					imgUl.innerHTML = ihtml;
					this.oImg.appendChild(imgUl);

					//load图片
					var Img = this.oImg.getElementsByTagName('img');

					//yahoo
					if (window.location.href.indexOf('yahoo.com') != -1) {
						this['set']['pic']['pic_scale'] = 2;
					};
					for (var i = 0; i < Img.length; i++) {
						loadImg(Img[i]);
					};
				} else {
					imgLength = 0;
					this.oImg.style.display = 'none';
				};
				//文字
				//剩余数据量
				var dataLeft = (this['data'].length - imgLength) || 0;
				//文字显示数量
				var txtLength = Math.min(this['set']['style']['style_txt_col'] * this['set']['style']['style_txt_row'], dataLeft);
				//文字容器内容
				if (this['set']['base']['data_type'] != '2' && dataLeft >= 1) {
					var thtml = '<ul>';
					for (var x = imgLength; x < imgLength + txtLength; x++) {
						var i = x + this.perPage * j;
						if (this['data'][i]['title']) {
							if (this['set']['txt']['txt_focus'] == 1) {
								thtml += "<li>&bull;&nbsp;";
							} else if (this['set']['txt']['txt_focus'] == 2) {
								thtml += "<li>▪&nbsp;";
							} else {
								thtml += "<li>";
							};
							thtml += "<a href='" + this['data'][i]['url'] + "' target='" + target + "' title='" + this['data'][i]['title'] + "' hidefocus='true'>" + this['data'][i]['title'] + "</a></li>";
						};
					};
					thtml += '</ul>';
					this.oTxt.innerHTML += thtml;
				} else {
					this.oTxt.style.display = 'none';
				};
			};
			//图片功能
			function loadImg(oBj) {
				if (Sys().ie == '6.0' || Sys().ie == '7.0') {
					var a = oBj.parentNode.parentNode;
					a.onclick = function () {
						if (that['set']['txt']['txt_link_target'] == 1 && !that.demo) {
							window.location.href = a.href;
						} else {
							window.open(a.href);
						};
						return false;
					};
				};
				tryImg(oBj, 0)
			};
			function tryImg(oBj, index) {
				var src = oBj.getAttribute('alt');
				var jsdata = oBj.getAttribute('jsdata');
				var userimg = oBj.getAttribute('userimg'); //stick
				var size = Math.max(Number(that['set']['pic']['pic_width']), Number(that['set']['pic']['pic_length']));
				var is_smart_thumb = oBj.getAttribute('data-img');
				var img = new Image();
				var ErrorNum = that.errorNum || 7;
				
				if (jsdata == 'false') {
					userimg = 'false';
				};
				
				if (userimg == 'stick' || window.location.href.indexOf('meishichina.com') != -1) {
					if (index == 1) {
						index = 2;
						src = that.errorDir + Math.ceil(Math.random() * ErrorNum) + '.jpg';
					};
				} else {
					if ((jsdata == 'false' || index >= 2) && !that.demo) {
						src = that.errorDir + Math.ceil(Math.random() * ErrorNum) + '.jpg';
					} else if (index == 0) {
						if (userimg == 'stick') {
							src = src;
						} else if (size > 96) {
							src = src + '_b';
						};
					} else if (index == 1) {
						if (size <= 96) {
							src = src + '_b';
						};
					};
				};

				img.onload = function () {
					totalimg++
					if (jsdata == 'false' || index >= 2) {
						defaultimg++;
					} else if (is_smart_thumb != 'false') {
						itelimg++;
					} else {
						trueimg++;
					};
					imgStatus();
					loadFunc(this, oBj, that['set']['pic']['pic_scale'], src);
				};
				img.onerror = img.onabort = function () {
					if (index < 2) {
						index++;
						tryImg(oBj, index);
					};
				};
				img.src = src;
			};
			function imgStatus() {
				if (totalimg == Img.length && !that.demo) {
					var url = '&' + encodeURIComponent(String.fromCharCode(1)) + '&ch=wprdsp&l=img&hid=' + that['request']['hid'] + '&trueimg=' + trueimg + '&defaultimg=' + defaultimg + '&itelimg=' + itelimg;
					questImg(url);
				};
			};
			function loadFunc(img, oBj, type, src) {
				var w = img.width;
				var h = img.height;
				var w0 = Number(that['set']['pic']['pic_width']);
				var h0 = Number(that['set']['pic']['pic_length']);
				if (!type || type == 0) {
					if (oBj) {
						oBj.style.height = h0 + 'px';
						oBj.style.width = w0 + 'px';
					};
				} else if (type == 1) {
					if (w * h0 >= w0 * h) {
						var h1 = Math.ceil(w0 * h / w);
						if (oBj) {
							oBj.style.width = w0 + 'px';
							oBj.style.height = h1 + 'px';
							oBj.style.marginTop = (h0 - h1) / 2 + 'px';
						};
					} else {
						var w1 = Math.ceil(w * h0 / h);
						if (oBj) {
							oBj.style.width = w1 + 'px';
							oBj.style.height = h0 + 'px';
							oBj.style.marginLeft = (w0 - w1) / 2 + 'px';
						};
					};
				} else if (type == 2) {
					if (w * h0 >= w0 * h) {
						var w1 = Math.ceil(w * h0 / h);
						if (oBj) {
							oBj.style.height = h0 + 'px';
							oBj.style.width = w1 + 'px';
						};
					} else {
						var h1 = Math.ceil(w0 * h / w);
						if (oBj) {
							oBj.style.width = w0 + 'px';
							oBj.style.height = h1 + 'px';
						};
					};
				};
				if (oBj)
					oBj.setAttribute('src', src);
			};
			//热词
			if (this['set']['hot']['data_hot'] != 0 && this['set']['hot']['data_hot_txt'] != '') {
				var hhtml = '<ul>';
				var hotLength = this['set']['hot']['data_hot_num'];
				var hotList = this['set']['hot']['data_hot_txt'].split(',');
				
				if (hotLength == 0) {
					hotLength = Math.max(5, hotList.length)
				};
				
				//标签热词
				var hotReco = [];
				if (typeof(aliyun_recommend_opts) == 'object' && aliyun_recommend_opts['tags']) {
					var hotRecoTmp = aliyun_recommend_opts['tags'].split(',');
					for (var i=0;i<hotRecoTmp.length;i++) {
						if (!hotRecoTmp[i]) {
							continue;
						};
						hotReco.push(hotRecoTmp[i]);
					};
				};
				//用户热词
				var hotUserNum = this['set']['hot']['data_hot_txt_user'] || 0;
				if (hotUserNum == 0) {
					hotLength = Math.min(hotLength, hotList.length + hotReco.length);
					if (hotReco[0]) {
						for (var i = 0; i < Math.min(hotReco.length,hotLength); i++) {
							hhtml += "<li><a href='" + this.searchUrl + "?kw=" + encodeURIComponent(hotReco[i]) + "&site=" + (this.request.sid || '') + "&ip=" + (this.ip || '') + "&pui=czb&cok=" + (this.Rcookie || '') + "&vr=1&hid=" + (this.request.hid || '') + "&bkt=" + (this.request.bkt || '') + "&ch=kwrdc&l=click&ft=" + this['ft'] + "&ps=" + i + "&wd=" + encodeURIComponent(hotReco[i]) + "&aid=" + this['request']['aid'] + "&sid=" + this['request']['aid'] + "' target='_blank' title='" + hotReco[i] + "' hidefocus='true'>" + hotReco[i] + "</a></li>";
						};
					};
					hotList.sort(function () {
						return 0.5 > Math.random();
					});
					for (var i = 0; i < hotLength - Math.min(hotReco.length, hotLength); i++) {
						if (hotList[i]) {
							hhtml += "<li><a href='" + this.searchUrl + "?kw=" + encodeURIComponent(hotList[i]) + "&site=" + (this.request.sid || '') + "&ip=" + (this.ip || '') + "&pui=czb&cok=" + (this.Rcookie || '') + "&vr=1&hid=" + (this.request.hid || '') + "&bkt=" + (this.request.bkt || '') + "&ch=kwrdc&l=click&ft=" + this['ft'] + "&ps=" + i + "&wd=" + encodeURIComponent(hotList[i]) + "&aid=" + this['request']['aid'] + "&sid=" + this['request']['aid'] + "' target='_blank' title='" + hotList[i] + "' hidefocus='true'>" + hotList[i] + "</a></li>";
						};
					};
				} else {
					var hotUser = [];
					var hotAgg = [];
					for (var i = 0; i < hotLength; i++) {
						if (i < hotUserNum) {
							hotUser.push(hotList[i]);
						} else {
							hotAgg.push(hotList[i])
						};
					};
					hotList = hotReco.concat(hotAgg);
					if (hotUser[0]) {
						for (var i = 0; i < Math.min(hotUser.length,hotLength); i++) {
							hhtml += "<li><a href='" + this.searchUrl + "?kw=" + encodeURIComponent(hotUser[i]) + "&site=" + (this.request.sid || '') + "&ip=" + (this.ip || '') + "&pui=czb&cok=" + (this.Rcookie || '') + "&vr=1&hid=" + (this.request.hid || '') + "&bkt=" + (this.request.bkt || '') + "&ch=kwrdc&l=click&ft=" + this['ft'] + "&ps=" + i + "&wd=" + encodeURIComponent(hotUser[i]) + "&aid=" + this['request']['aid'] + "&sid=" + this['request']['aid'] + "' target='_blank' title='" + hotUser[i] + "' hidefocus='true'>" + hotUser[i] + "</a></li>";
						};
					};
					hotList.sort(function () {
						return 0.5 > Math.random();
					});
					for (var i = 0; i < (hotLength - Math.min(hotUser.length, hotLength)); i++) {
						if (hotList[i]) {
							hhtml += "<li><a href='" + this.searchUrl + "?kw=" + encodeURIComponent(hotList[i]) + "&site=" + (this.request.sid || '') + "&ip=" + (this.ip || '') + "&pui=czb&cok=" + (this.Rcookie || '') + "&vr=1&hid=" + (this.request.hid || '') + "&bkt=" + (this.request.bkt || '') + "&ch=kwrdc&l=click&ft=" + this['ft'] + "&ps=" + i + "&wd=" + encodeURIComponent(hotList[i]) + "&aid=" + this['request']['aid'] + "&sid=" + this['request']['aid'] + "' target='_blank' title='" + hotList[i] + "' hidefocus='true'>" + hotList[i] + "</a></li>";
						};
					};
				};
				
				hhtml += '</ul>';
				this.oHot.innerHTML = hhtml;
			} else {
				this.oHot.style.display = 'none';
			};
			//边框
			if (this['set']['txt']['txt_border'] == 1) {
				this.oTxt.className += " bor";
			};
			//无间隔滚动
			if ((this['set']['slide']['change_show_type'] == 1 && this['set']['slide']['slide_type'] == 1) || (this['set']['slide']['focus_show_type'] == 1 && this['set']['slide']['slide_type'] == 2)) {
				if (this['set']['base']['data_type'] == 0) {
					var ul_0 = this.oDoc.createElement('ul');
					ul_0.innerHTML = this.oTxt.getElementsByTagName('ul')[0].innerHTML;
					this.oTxt.appendChild(ul_0);
				} else if (this['set']['base']['data_type'] == 2){
					var ul_1 = this.oDoc.createElement('ul');
					ul_1.innerHTML = this.oImg.getElementsByTagName('ul')[0].innerHTML;
					this.oImg.appendChild(ul_1);
					var img_1 = ul_1.getElementsByTagName('img');
					for (var i = 0; i < img_1.length; i++) {
						loadImg(img_1[i]);
					};
				} else if (this['set']['base']['data_type'] == 1) {
					var ul_0 = this.oDoc.createElement('ul');
					ul_0.innerHTML = this.oTxt.getElementsByTagName('ul')[0].innerHTML;
					this.oTxt.appendChild(ul_0);
					var ul_1 = this.oDoc.createElement('ul');
					ul_1.innerHTML = this.oImg.getElementsByTagName('ul')[0].innerHTML;
					this.oImg.appendChild(ul_1);
					var img_1 = ul_1.getElementsByTagName('img');
					for (var i = 0; i < img_1.length; i++) {
						loadImg(img_1[i]);
					};
				};
			};
		},
		funcChange : function (boo,page) {
			var that = this;
			var imgNode = this.oImg.getElementsByTagName("ul");
			var txtNode = this.oTxt.getElementsByTagName("ul");
			//无效果
			if ((this['set']['slide']['change_show_type'] == 0 && this['set']['slide']['slide_type'] == 1) || (this['set']['slide']['focus_show_type'] == 0 && this['set']['slide']['slide_type'] == 2)) {
				if (!boo) {
					if (this.page == this.maxPage) {
						this.page = 1;
					} else {
						this.page++;
					};
				} else {
					this.page = page;
				};
				if (this['set']['base']['data_type'] != 0) {
					for (var i = 0; i < imgNode.length; i++) {
						if (i == that.page - 1) {
							imgNode[i].style.display = 'block';
						} else {
							imgNode[i].style.display = 'none';
						};
					};
				};
				if (this['set']['base']['data_type'] != 2) {
					for (var i = 0; i < txtNode.length; i++) {
						if (i == that.page - 1) {
							txtNode[i].style.display = 'block';
						} else {
							txtNode[i].style.display = 'none';
						};
					};
				};
			};
			//左右
			if ((this['set']['slide']['change_show_type'] == 1 && this['set']['slide']['slide_type'] == 1) || (this['set']['slide']['focus_show_type'] == 1 && this['set']['slide']['slide_type'] == 2)) {
				if (!boo) {
					if (this.page - this.maxPage == 1) {
						that.oCon.style.marginLeft = 0;
						this.page = 1;
					};
					this.page ++;
				} else {
					this.page = page;
				};
				clearTimeout(that.scrollTime);
				var w = this['width'] - 22;
				var t = 0 - w * this.page + w;
				this.scrollTime = setInterval(
					function () {
						var left = parseInt(that.oCon.style.marginLeft || 0);
						var step = (t - left) / 10;
						step = step > 0 ? Math.ceil(step) : Math.floor(step);
						if (left == t) {
							clearInterval(that.scrollTime);
						} else {
							that.oCon.style.marginLeft = left + step + 'px';
						};
					}, 10
				);
			};
			//渐现
			if ((this['set']['slide']['change_show_type'] == 2 && this['set']['slide']['slide_type'] == 1) || (this['set']['slide']['focus_show_type'] == 2 && this['set']['slide']['slide_type'] == 2))  {
				if (!boo) {
					if (this.page == this.maxPage) {
						this.page = 1;
					} else {
						this.page++;
					};
				} else {
					this.page = page;
				};
				clearTimeout(that.fadeTime);
				var t = 0;
				if (this['set']['base']['data_type'] != 0) {
					for (var i = 0; i < imgNode.length; i++) {
						if (i == that.page - 1) {
							imgNode[i].style.display = 'block';
						} else {
							imgNode[i].style.display = 'none';
						};
					};
				};
				if (this['set']['base']['data_type'] != 2) {
					for (var i = 0; i < txtNode.length; i++) {
						if (i == that.page - 1) {
							txtNode[i].style.display = 'block';
						} else {
							txtNode[i].style.display = 'none';
						};
					};
				};
				var node = that.oCon;
				if (!document.documentMode && (Sys().ie == "6.0" || Sys().ie == "7.0") || Sys().ie == "8.0") {
					node = that.oContent;
				};
				this.fadeTime = setInterval(
						function () {
						if (t > 100) {
							clearInterval(that.timer);
						} else {
							setOpacity(node, t);
							t += 1;
						};

					}, 15);
				function setOpacity(elem, level) {
					if (elem.filters) {
						elem.style.filter = "alpha(opacity=" + level + ")";
						elem.style.zoom = 1;
					} else {
						elem.style.opacity = level / 100;
					};
				};
			};
			if (!this.demo) {
				var hid = this['request']['hid'];
				var bkt = this['request']['bkt'];
				var la = encodeURIComponent(String.fromCharCode(1));
				var lb = encodeURIComponent(String.fromCharCode(2));
				var url = '';
				url = '&' + la + '&ch=wprdsp&l=flush&pg='+ this.page +'&hid=' + hid + '&bkt=' + bkt;
				questImg(url);
			};
		},
		funcStyle : function () {
			//计算图片间距 MM=( W - (MW+4)*COL - 22) / (COL-1)
			var W = Number(this['width']);
			var MW = Number(this['set']['pic']['pic_width']);
			var MH = Number(this['set']['pic']['pic_length']);
			var MCOL = Number(this['style_pic_col']);
			var MROW = Number(this['set']['style']['style_pic_row']);
			var MM = Math.floor((W - MW * MCOL - 4 * MCOL - 22) / (MCOL - 1));
			//文字宽度 TW=(W - COL*20 + 18 )/COL
			var TCOL = Number(this['set']['style']['style_txt_col']);
			var TW = Math.floor((W - 22 - TCOL * 10) / TCOL);
			//热词行高
			var HLH = Number(this['set']['hot']['hot_body_margin']);
			//标题行高
			var TLH = Number(this['set']['txt']['txt_title_margin']);
			//文字行高
			var BLH = Number(this['set']['txt']['txt_body_margin'])
				//各种配置
				var style = "<style type='text/css'>";
			style += ".tui{width:" + (W - 2) + "px;height:" + (this['set']['style']['style_length'] - 2) + "px;background:#" + this['set']['style']['style_background_color'] + ";border-color:#" + this['set']['style']['style_border_color'] + ";}";
			var font_family = ['arial', 'tahoma', 'sans-serif', 'SimSun', 'SimHei', 'Microsoft YaHei'];
			var bold = 400;
			if (this['set']['txt']['txt_title_bold'] == 1) {
				bold = 700;
			};
			style += ".title {height:" + this['set']['txt']['txt_title_margin'] + "px;line-height:" + this['set']['txt']['txt_title_margin'] + "px;font-size:" + this['set']['txt']['txt_title_size'] + "px;font-weight:" + bold + ";font-family:" + font_family[this['set']['txt']['txt_title_family']] + ";color:#" + this['set']['txt']['txt_title_color'] + ";}";
			style += ".tools_0 {height:" + this['set']['txt']['txt_title_margin'] + "px;line-height:" + this['set']['txt']['txt_title_margin'] + "px;}"
			if (this["set"]["txt"]["txt_title_background"] == 1) {
				style += ".title {background:url(" + this["set"]["txt"]["txt_title_bgimage"] + ") 0 0 repeat;}";
			} else {
				style += ".title {background-color:#" + this["set"]["txt"]["txt_title_bgcolor"] + ";}";
			};
			style += ".content{width:" + (W - 22) + "px;}";
			style += ".bor{background:url(" + this.imgDir + "border.png) left top repeat-x;}";
			style += ".logo a.a_1{background:url(" + this.imgDir + "logo_pink.png) 0 0 no-repeat;_filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=noscale,src='" + this.imgDir + "logo_pink.png');_background:none;}";
			style += ".logo a.a_0{background:url(" + this.imgDir + "logo_white.png) 0 0 no-repeat;_filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=noscale,src='" + this.imgDir + "logo_white.png');_background:none;}";
			style += ".change b{background:url(" + this.imgDir + "change_btn.png) 0 0 no-repeat;_filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=noscale,src='" + this.imgDir + "change_btn.png');_background:none;}";
			style += ".change i{background-image:url(" + this.imgDir + "change_ico.png);_filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=noscale,src='" + this.imgDir + "change_ico.png');_background:none;_margin-right:6px;_height:40px;}";
			style += ".change a.a_1 em {background-image:url(" + this.imgDir + "change_btn.png) !important;_background-image:url(" + this.imgDir + "change_btn_8.png)}";
			var bold = 400;
			if (this['set']['slide']['change_title_bold'] == 1) {
				bold = 700;
			};
			style += ".change span {font-size:"+ this['set']['slide']['change_title_size'] +"px;color:#"+ this['set']['slide']['change_title_color'] +";font-weight:"+ bold +";font-family:" + font_family[this['set']['slide']['change_title_family']] + "}";
			style += ".img ul{width:" + (W - 22) + "px;}";
			var bold = 400;
			if (this['set']['txt']['txt_body_bold'] == 1) {
				bold = 700;
			};
			style += ".img li {width:" + (MW + 4) + "px;margin-left:" + MM + "px;font-size:" + this['set']['txt']['txt_body_size'] + "px;font-weight:" + bold + ";font-family:" + font_family[this['set']['txt']['txt_body_family']] + "}";
			style += ".img a:hover{background-color:#" + this['set']['style']['style_hover_color'] + ";}";
			style += ".img em {width:" + this['set']['pic']['pic_width'] + "px;height:" + MH + "px;}";
			if (this['set']['pic']['pic_summary_row'] == 1) {
				style += '.img a span {height:' + BLH + 'px;line-height:' + BLH + 'px;text-align:center;}';
			} else {
				style += '.img a span {height:' + BLH * 2 + 'px;line-height:' + BLH + 'px;}';
			};
			style += ".txt ul{width:" + (W - 22) + "px;}";
			style += ".txt li{width:" + TW + "px;height:" + this['set']['txt']['txt_body_margin'] + "px;line-height:" + this['set']['txt']['txt_body_margin'] + "px;font-size:" + this['set']['txt']['txt_body_size'] + "px;font-weight:" + bold + ";font-family:" + font_family[this['set']['txt']['txt_body_family']] + ";padding-right:10px;}";
			style += ".box li a:link{color:#" + this['set']['txt']['txt_default_color'] + ";}";
			style += ".box li a:visited{color:#" + this['set']['txt']['txt_clicked_color'] + ";}";
			style += ".box li a:hover{color:#" + this['set']['txt']['txt_hover_color'] + ";}";
			style += ".box li a:active{color:#" + this['set']['txt']['txt_click_color'] + ";}";
			if (this['set']['txt']['txt_default_underline'] == 0) {
				style += ".box li a:link{text-decoration:none;}";
			} else {
				style += ".box li a:link{text-decoration:underline;}";
			};
			if (this['set']['txt']['txt_clicked_underline'] == 0) {
				style += ".box li a:visited{text-decoration:none;}";
			} else {
				style += ".box li a:visited{text-decoration:underline;}";
			};
			if (this['set']['txt']['txt_hover_underline'] == 0) {
				style += ".box li a:hover{text-decoration:none;}";
			} else {
				style += ".box li a:hover{text-decoration:underline;}";
			};
			if (this['set']['txt']['txt_click_underline'] == 0) {
				style += ".box li a:active{text-decoration:none;}";
			} else {
				style += ".box li a:active{text-decoration:underline;}";
			};
			var bold = 400;
			if (this['set']['hot']['hot_body_bold'] == 1) {
				bold = 700;
			};
			style += ".hot{background-color:#" + this['set']['hot']['hot_body_background'] + ";height:" + this['set']['hot']['hot_body_margin'] + "px;line-height:" + this['set']['hot']['hot_body_margin'] + "px;font-size:" + this['set']['hot']['hot_body_size'] + "px;font-weight:" + bold + ";font-family:" + font_family[this['set']['hot']['hot_body_family']] + "}";
			style += ".hot li a:link{color:#" + this['set']['hot']['hot_default_color'] + ";}";
			style += ".hot li a:visited{color:#" + this['set']['hot']['hot_clicked_color'] + ";}";
			style += ".hot li a:hover{color:#" + this['set']['hot']['hot_hover_color'] + ";}";
			style += ".hot li a:active{color:#" + this['set']['hot']['hot_click_color'] + ";}";
			if (this['set']['hot']['hot_default_underline'] == 0) {
				style += ".hot li a:link{text-decoration:none;}";
			} else {
				style += ".hot li a:link{text-decoration:underline;}";
			};
			if (this['set']['hot']['hot_clicked_underline'] == 0) {
				style += ".hot li a:visited{text-decoration:none;}";
			} else {
				style += ".hot li a:visited{text-decoration:underline;}";
			};
			if (this['set']['hot']['hot_hover_underline'] == 0) {
				style += ".hot li a:hover{text-decoration:none;}";
			} else {
				style += ".hot li a:hover{text-decoration:underline;}";
			};
			if (this['set']['hot']['hot_click_underline'] == 0) {
				style += ".hot li a:active{text-decoration:none;}";
			} else {
				style += ".hot li a:active{text-decoration:underline;}";
			};
			if (this['set']['slide']['change_title'] == 0 || this['set']['slide']['change_title'] == '') {
				style += ".change b {display:none}#change a em {background:none;}"
			};
			style += ".focus a {color:#" + this['set']['slide']['focus_txt_color'] + ";}";
			style += ".focus a {font-family:" + font_family[this['set']['slide']['focus_txt_family']] + ";}";
			style += ".focus a {background-color:#" + this['set']['slide']['focus_hover_background'] + ";}";
			style += ".focus a.active {background-color:#" + this['set']['slide']['focus_txt_background'] + ";}";
			if (this['set']['pic']['pic_border'] == 1) {
				style += ".img a em {border:1px solid #ddd;}";
			} else {
				style += ".img a em {padding:1px;}";
			};
			if (this['style_pic_col'] == 1) {
				style += ".img li,.img li.i_0 {float:none;display:block;margin-left:auto;margin-right:auto;}";
			};
			style += '</style>';
			return (style);
		},
		funcQuery : function () {
			var that = this;
			var hid = this['request']['hid'];
			var bkt = this['request']['bkt'];
			var la = encodeURIComponent(String.fromCharCode(1));
			var lb = encodeURIComponent(String.fromCharCode(2));
			var dspsize = Math.min(this.perPage,this.data.length);
			//打开页面
			var url = '';
			url = '&' + la + '&ch=wprdsp&l=view&pg=1&hid=' + hid + '&bkt=' + bkt + '&dspsize=' + dspsize;
			//热词
			if (this['set']['hot']['data_hot'] != 0) {
				if (this['set']['hot']['data_hot_txt'] != '') {
					url += '&' + lb + '&has=true&ch=hkwrdsp&l=view&hid=' + hid + '&bkt=' + bkt;
				} else {
					url += '&' + lb + '&has=false&ch=hkwrdsp&l=view&hid=' + hid + '&bkt=' + bkt;
				};
			};
			questImg(url);

			//若不在第一屏
			var ot = getElemPos(this.o).y || 0;
			var tt;
			if (document.compatMode == 'BackCompat') {
				tt = document.body.clientHeight;
			} else {
				tt = document.documentElement.clientHeight;
			};
			var seenCount = 0;
			function seeOnce() {
				if (seenCount == 0) {
					var st = Math.max(document.body.scrollTop, document.documentElement.scrollTop, 0);
					if (tt + st > ot) {
						var url = '&' + la + '&ch=wprdsp&l=action&act=001&hid=' + hid + '&bkt=' + bkt;
						questImg(url);
						seenCount++;
					};
				};
			};
			if (ot > tt) {
				addEvent(window, 'scroll', function () {
					seeOnce();
				});
			} else {
				seeOnce();
			};

			//鼠标第一次经过
			var mouseCount = 0;
			this.o.onmouseover = function () {
				if (mouseCount == 0) {
					var url = '&' + la + '&ch=wprdsp&l=action&act=002&hid=' + hid + '&bkt=' + bkt;
					questImg(url);
					mouseCount++;
				};
			};

			//热词点击
			if (this['set']['hot']['data_hot'] != 0 && this['set']['hot']['data_hot_txt'] != '') {
				var a = this.oHot.getElementsByTagName('a');
				for (var i = 0; i < a.length; i++) {
					a[i].index = i;
					a[i].onclick = function () {
						var url = '';
						url = '&' + la + '&ch=kwrdc&l=click&ps=' + this.index + '&wd=' + encodeURIComponent(this.innerHTML) + '&hid=' + hid + '&bkt=' + bkt;
						questImg(url);
					};
				}
			};
			var jumpUrl = this.jumpUrl;
			var jumpAid = this.request.aid;
			var jumpFt = 0;
			var jumpRef = window.location.href || parent.location.href;
			//链接
			var a = this.oImg.getElementsByTagName('a');
			for (var i = 0; i < a.length; i++) {
				a[i].index = i;
				a[i].onclick = function () {
					var urltemp = '&' + la + '&ch=wprc&l=click&ps=' + this.index + '&hid=' + hid + '&bkt=' + bkt + '&isimg=1&curl=' + encodeURIComponent(this.href);
					questImg(urltemp);
					if (jumpUrl) {
						if (that['set']['txt']['txt_link_target'] == 1) {
							window.location.href = jumpUrl + '&url=' + encodeURIComponent(this.href) + '&ref=' + encodeURIComponent(jumpRef) + '&aid=' + jumpAid + '&ft=' + jumpFt;
						} else {
							window.open(jumpUrl + '&url=' + encodeURIComponent(this.href) + '&ref=' + encodeURIComponent(jumpRef) + '&aid=' + jumpAid + '&ft=' + jumpFt);
						};
						return false;
					};
				};
			};
			var a = this.oTxt.getElementsByTagName('a');
			for (var i = 0; i < a.length; i++) {
				a[i].index = i;
				a[i].onclick = function () {
					var urltemp = '&' + la + '&ch=wprc&l=click&ps=' + this.index + '&hid=' + hid + '&bkt=' + bkt + '&isimg=0&curl=' + encodeURIComponent(this.href);;
					questImg(urltemp);
					if (jumpUrl) {
						if (that['set']['txt']['txt_link_target'] == 1) {
							window.location.href = jumpUrl + '&url=' + encodeURIComponent(this.href) + '&ref=' + encodeURIComponent(jumpRef) + '&aid=' + jumpAid + '&ft=' + jumpFt;
						} else {
							window.open(jumpUrl + '&url=' + encodeURIComponent(this.href) + '&ref=' + encodeURIComponent(jumpRef) + '&aid=' + jumpAid + '&ft=' + jumpFt);
						};
						return false;
					};
				};
			};
		}
	};
	//原型结束
	function Sys() {
		var Sys = {};
		var ua = navigator.userAgent.toLowerCase();
		var s;
		(s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
			(s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
			(s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
			(s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
			(s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;
		return Sys;
	};	var tuiFixedRun = new tuiFixed();
	//*********配置参数***************************
	tuiFixedRun.oBox = document.getElementById("aliyun_cnzz_tui_1000072387");
	tuiFixedRun.demo = false;
	
	tuiFixedRun.set = {"logo":{"logo_background_user":1,"logo_background_img":"","logo_position":0},"style":{"style_length":366,"style_width":778,"style_pic_col":5,"style_pic_row":1,"style_txt_col":3,"style_txt_row":5,"style_background_color":"ffffff","style_hover_color":"ffffff","style_border_color":"3495b5"},"pic":{"pic_length":96,"pic_width":96,"pic_scale":0,"pic_summary":1,"pic_summary_row":0,"pic_border":1},"hot":{"data_hot":1,"data_hot_num":"0","data_hot_txt":"","hot_body_background":"e5e5e5","hot_body_size":"12","hot_body_bold":0,"hot_body_margin":"26","hot_body_family":0,"hot_default_color":"222222","hot_default_underline":0,"hot_hover_color":"ff6600","hot_hover_underline":1,"hot_click_color":"222222","hot_click_underline":0,"hot_clicked_color":"222222","hot_clicked_underline":0},"txt":{"txt_title_icon":"1","txt_title_txt":"\u76f8\u5173\u5185\u5bb9","txt_title":1,"txt_title_size":14,"txt_title_bold":"0","txt_title_margin":"35","txt_title_background":0,"txt_title_bgcolor":"ffffff","txt_title_bgimage":"http:\/\/tui.cnzz.net\/templates\/images\/fix_txt_img\/3\/title.png","txt_title_color":"222222","txt_title_family":0,"txt_body_size":12,"txt_body_bold":0,"txt_body_margin":"20","txt_body_family":0,"txt_default_color":"222222","txt_hover_color":"ff6600","txt_click_color":"222222","txt_clicked_color":"222222","txt_default_underline":"0","txt_hover_underline":"1","txt_click_underline":"0","txt_clicked_underline":"0","txt_focus":2,"txt_border":1,"txt_link_target":0},"locat":{"locat_left_right":0,"locat_float":0,"locat_mark":0,"locat_color":0,"locat_txt_color":null,"locat_txt":null,"locat_background":null,"locat_float_hide":0},"slide":{"slide_type":2,"slide_timer":5,"change_icon":1,"change_title":1,"change_title_txt":"\u6362\u4e00\u6362","change_background_color":"0","change_icon_type":1,"change_title_size":"12","change_title_bold":0,"change_title_family":"arial","change_title_color":"333333","change_show_type":0,"focus_show_type":1,"focus_txt_background":"ff9010","focus_hover_background":"b4b4b4","focus_txt_family":0,"focus_txt_color":"ffffff"},"search":{"search_show":0,"search_high":0,"search_high_color":null},"keywords":{"images":null,"node":null,"txt_color":null,"underline":0,"number":0,"bold":0,"italic":0,"count":0,"node_text":null},"summary":{"txt_body_size":0,"txt_body_bold":0,"txt_body_family":0,"txt_default_color":null,"txt_hover_color":null,"txt_click_color":null,"txt_clicked_color":null,"txt_default_underline":0,"txt_hover_underline":0,"txt_click_underline":0,"txt_clicked_underline":0},"wap":{"auto_show":0},"base":{"cloud_id":"10118342","r_name":"\u4e91\u63a8\u8350\u4e00","r_type":"1","r_style_id":"40","r_style_name":"\u84dd\u8272\u7b80\u7ea6\u7ad6\u7248\uff08\u5206\u680f\uff09","r_status":"1","data_type":"1","nyn_host":"0","domain_source":null,"r_radius":"0","img_type":"1","cnzz_code_id":"0","sf_deploy":0,"yn_host":"0"}};
	//列表数据
	tuiFixedRun.data = [{"url":"http:\/\/www.aboutyun.com\/thread-11895-1-1.html","title":"\u3010Kafka\u4e8c\u3011Kafka\u5de5\u4f5c\u539f\u7406\u8be6\u89e3-Kafka","algId":"10","unigramlist":"\u539f\u7406\u8be6\u89e3\u5de5\u4f5c","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/5C3E7927395A4A1D6B40156A19713F3068427900","has_thumb":"true","ctr":"0.553825","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/forum.php?mod=viewthread&page=1&tid=9024","title":"Eclipse\u8fd0\u884chbase\u4f8b\u5b50\u62a5: org\/apache\/zookeeper\/KeeperException","algId":"2-hot","unigramlist":"Eclipse\tKeeperException\tapache\thbase\tzookeeper\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/301F7846747E084E5D0023713B06266D6A5B3800","has_thumb":"true","ctr":"-1.308092","page_num":"1","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?highlight=hadoop&mod=viewthread&tid=4415","title":"\u4ece\u5165\u95e8\u5230\u63d0\u9ad8 hadoop (3.4G)\u89c6\u9891\u6559\u6750\u5206\u4eab","algId":"2-hot","unigramlist":"hadoop\t\u5165\u95e8\t\u5206\u4eab\t\u6559\u6750\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/70216278774D10737440194A5B3C5D3C53020900","has_thumb":"true","ctr":"-1.365630","page_num":"1","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?highlight=hbase&mod=viewthread&tid=8153","title":"Phoenix\u4ecb\u7ecd: \u5b9e\u73b0\u5411HBase\u53d1\u9001\u6807\u51c6SQL\u8bed\u53e5","algId":"2-hot","unigramlist":"HBase\tPhoenix\t\u53d1\u9001\t\u6807\u51c6\t\u8bed\u53e5\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/6C0F18471878223D2100507656484F7811054E40","has_thumb":"true","ctr":"-1.396420","page_num":"1","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-6502-1-13.html","title":"hive\u5b9e\u6218\u7ecf\u9a8c:\u9047\u5230\u95ee\u9898\u603b\u7ed3","algId":"2-hot","unigramlist":"hive\t\u5b9e\u6218\t\u7ecf\u9a8c\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/653A5D7D4313103245401F6A4D3F73136B081C00","has_thumb":"true","ctr":"-1.397027","page_num":"1","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?extra=&mod=viewthread&page=1&tid=11218","title":"cinder backup\u5de5\u4f5c\u539f\u7406-Cinder","algId":"10","unigramlist":"\u539f\u7406\u5de5\u4f5ccinderbackup","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/130D4C1E5443617E2840176602735B6B2A4C5A40","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-11262-1-1.html","title":"\u4f7f\u7528HIVE SQL\u5b9e\u73b0\u63a8\u8350\u7cfb\u7edf\u6570\u636e\u8865\u5168-Hive","algId":"10","unigramlist":"\u5b9e\u73b0\u6570\u636e\u4f7f\u7528\u7cfb\u7edf\u63a8\u8350","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/665D494F66037A0D3000385666494D6457370840","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/home.php?mod=space&uid=5862&do=blog&id=1861","title":"spark1.02\u600e\u4e48\u5b9e\u73b0\u8bfb\u53d6hbase\u7684\u6570\u636e - zhanggl\u7684\u65e5\u5fd7","algId":"10","unigramlist":"\u8bfb\u53d6\u6570\u636e\u5b9e\u73b0hbase\u7684#\u6570\u636e","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/47232962186E726841000858733E121450365B40","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-12330-1-1.html","title":"Sqoop1.4.4\u5b9e\u73b0\u5173\u7cfb\u578b\u6570\u636e\u5e93\u591a\u8868\u540c\u65f6\u5bfc\u5165HDFS\u6216Hive\u4e2d-Sqoop","algId":"10","unigramlist":"\u5bfc\u5165\u6570\u636e#\u5e93\u5b9e\u73b0\u5173\u7cfb\u6570\u636ee#\u4e2d","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/591552610D5E68051840212F622224605D2E3E00","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-12380-1-1.html","title":"\u6b64\u79cd\u6d77\u91cf\u6570\u636e\u7edf\u8ba1\u5982\u4f55\u7528HIVE\u5b9e\u73b0\uff1f-Hive","algId":"10","unigramlist":"\u7edf\u8ba1\u5b9e\u73b0\u6d77\u91cf\u6570\u636e","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/2428631C446E4E244200254F723F620013730E40","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-12561-2-1.html","title":"\u6570\u636e\u6316\u6398\u539f\u7406\u4e0e\u7b97\u6cd5\u3010334\u9875\u3011-\u6570\u636e\u6316\u6398-about\u4e91\u5f00\u53d1-\u7b2c2\u9875","algId":"10","unigramlist":"\u6316\u6398\u7b97\u6cd5\u539f\u7406\u6570\u636e334\u539f\u7406#\u4e0e","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/0C2218214B7B0B68134005155F396C75742E0840","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/forum.php?extra=page=1&mod=viewthread&page=1&pid=92731&tid=13295","title":"\u5982\u4f55\u5b9e\u73b0\u4ece\u5b57\u5178\u91cc\u67e5\u627e\u6570\u636e\u5e76\u52a0\u5165\u5df2\u6709\u6587\u4ef6\u4e2d\uff1f-MapReduce","algId":"10","unigramlist":"\u67e5\u627e\u5b57\u5178\u5b9e\u73b0\u6570\u636e\u5df2#\u6709\u6587\u4ef6","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/5A652B5A7914420C6200423837110A0A534D6340","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/blog-6183-2403.html","title":"\u5229\u7528Hive\u5b9e\u73b0\u6c42\u4e24\u6761\u76f8\u90bb\u6570\u636e\u65f6\u95f4\u5dee - redhat1986\u7684\u65e5\u5fd7","algId":"10","unigramlist":"\u5229\u7528\u5b9e\u73b0\u65f6\u95f4\u76f8\u90bb\u6570\u636e\u4e24#\u6761","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/2F5E026F7D51610C0500321F66266F66571B1640","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/forum.php?extra=page=1&mod=viewthread&page=2&tid=6723","title":"MapReduce\u5de5\u4f5c\u539f\u7406\u8bb2\u89e3-Mapreduce-about\u4e91\u5f00\u53d1-\u7b2c2\u9875","algId":"10","unigramlist":"\u8bb2\u89e3\u539f\u7406\u5de5\u4f5c","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/287576425E346A1431403B6C5C5A4E442A412300","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/forum.php?action=viewthreadmod&mod=misc&tid=8401","title":"\u5927\u6570\u636e\uff08hadoop\u7cfb\u5217\uff09\u5b66\u4e60 - Hbase - hbase\u5f00\u53d1\u73af\u5883\u642d\u5efa\u53ca\u8fd0\u884chbase\u5c0f\u5b9e\u4f8b\uff08HBase 0.98.3\u65b0api\uff09","algId":"10","unigramlist":"hadoop\u5927#\u6570\u636e\u7cfb\u5217\u6570\u636e\u5b66\u4e60","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/3D6B6240494F366637404A56063B5278276F4740","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/forum.php?action=viewthreadmod&mod=misc&tid=8857","title":"\u5927\u6570\u636e\uff08hadoop\u7cfb\u5217\uff09\u5b66\u4e60 - Hbase - hbase\u5982\u4f55\u521b\u5efa\u4e8c\u7ea7\u7d22\u5f15\u4ee5\u53ca\u521b\u5efa\u4e8c\u7ea7\u7d22\u5f15\u5b9e\u4f8b","algId":"10","unigramlist":"hadoop\u5927#\u6570\u636e\u7cfb\u5217\u6570\u636e\u5b66\u4e60","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/7A360B4C1810564F2B000D1E3C5278406F3D0900","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/forum-117-1.html","title":"\u5927\u6570\u636e\uff08hadoop\u7cfb\u5217\uff09\u5b66\u4e60","algId":"10","unigramlist":"hadoop\u5927#\u6570\u636e\u7cfb\u5217\u6570\u636e\u5b66\u4e60","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/461B7F164F5A296D4200557E760C1A281F545640","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/forum.php?fid=117&filter=digest&mod=forumdisplay&mod=forumdisplay&page=23","title":"\u5927\u6570\u636e\uff08hadoop\u7cfb\u5217\uff09\u5b66\u4e60-about\u4e91\u5f00\u53d1-\u7b2c23\u9875","algId":"10","unigramlist":"hadoop\u5927#\u6570\u636e\u7cfb\u5217\u6570\u636e\u5b66\u4e60","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/51562D7F004A65642740273328004626713D4440","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-1562-1-1.html","title":"MapReduce\u600e\u6837\u5b9e\u73b0\u6570\u636e\u7684\u67e5\u8be2\uff1f-\u5927\u6570\u636e\uff08hadoop\u7cfb\u5217\uff09","algId":"10","unigramlist":"\u5b9e\u73b0\u6570\u636e\u67e5\u8be2","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/4D3A0B515C163C3E1140744F2509106700484F00","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-3058-1-1.html","title":"\u5982\u4f55\u5b9e\u73b0\u4f01\u4e1a\u6570\u636e\u8fc1\u79fb-\u7a0b\u5e8f\u5458\u4f11\u95f2\u9605\u8bfb\u8ba8\u8bba\u533a","algId":"10","unigramlist":"\u8fc1\u79fb\u5b9e\u73b0\u6570\u636e\u4f01\u4e1a","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/5D1A32426D346568720018192C3D44015F7D0A00","ctr":"0.065612","page_num":"1","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-6789-1-1.html","title":"Hadoop CDH\u56db\u79cd\u5b89\u88c5\u65b9\u5f0f\u603b\u7ed3\u53ca\u5b9e\u4f8b\u6307\u5bfc","algId":"2-hot","unigramlist":"Hadoop\t\u5b9e\u4f8b\t\u6307\u5bfc\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/2F306655762738661E001C186D19171370363540","has_thumb":"true","ctr":"-1.429281","page_num":"2","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-12521-1-1.html","title":"\u4f7f\u7528\u865a\u62df\u673a\u642d\u5efahadoop\u3001openstack\u96c6\u7fa4\u5fc5\u5907\u57fa\u7840\u77e5\u8bc6: \u865a\u62df\u673a\u5feb\u7167-\u4e91\u6280\u672f\u57fa\u7840","algId":"2-hot","unigramlist":"hadoop\topenstack\t\u4e91#\u6280\u672f\t\u57fa\u7840\t\u5fc5\u5907\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/6C0D01587E115F3F1200727B135703447B2A3E40","has_thumb":"true","ctr":"-1.437017","page_num":"2","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?highlight=hadoop+ha&mod=viewthread&tid=7480","title":"CDH4-Hadoop_Hive_HBase\u5b89\u88c5\u53cahadoop2.0.0-CDH4.2.0\u7cfb\u5217\u624b\u5de5\u5b89\u88c5\u6307\u5357\u6587\u6863\u4e0b\u8f7d","algId":"2-hot","unigramlist":"HBase\tHadoop\tHive\thadoop\t\u624b\u5de5\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/7D04487F464F7E52170068177020326A116D5F40","has_thumb":"true","ctr":"-1.443992","page_num":"2","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-5299-1-1.html","title":"OpenStack\u670d\u52a1\u5668\u8282\u70b9\u8fc1\u79fb\uff08\u4fee\u6539IP\uff09\u540e\u5f15\u53d1\u7684nova-compute\u4e0d\u542f\u52a8","algId":"2-hot","unigramlist":"OpenStack\tcompute\tnova\t\u4fee\u6539\t\u542f\u52a8\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/447E5179745C7E437E004D5B22115F7200091B00","has_thumb":"true","ctr":"-1.450716","page_num":"2","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-8203-1-3.html","title":"about\u4e91\u8d44\u6e90\u6c47\u603b\u6307\u5f15V1.5:\u5305\u62echadoop,openstack,storm,spark\u7b49\u89c6\u9891\u6587\u6863\u4e66\u7c4d\u6c47\u603b","algId":"2-hot","unigramlist":"hadoop\topenstack\tspark\tstorm\t\u4e66\u7c4d\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/0E433D16464F607D264079506978421104177340","has_thumb":"true","ctr":"-1.479296","page_num":"2","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-4065-1-1.html","title":"\u600e\u6837\u5b89\u5168\u5b9e\u73b0\u5546\u5e97\u4e0e\u516c\u53f8\u6570\u636e\u7684\u4e92\u76f8\u8fde\u63a5\u8bbf\u95ee\uff1f-\u5927\u6570\u636e\u3001\u4e91\u5e73\u53f0\u5b89\u5168","algId":"10","unigramlist":"\u4e92\u76f8\u5b9e\u73b0\u5546\u5e97\u8fde\u63a5\u6570\u636e\u5b89\u5168\u8bbf\u95ee\u516c\u53f8\u516c\u53f8#\u7684","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/5A2715763470680A60402636740A72105D6D7400","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/forum.php?mod=viewthread&tid=13925&page=1","title":"\u5c0f\u77e5\u8bc6: \u56fe\u89e3 HDFS \u5de5\u4f5c\u539f\u7406-\u5927\u6570\u636e\uff08hadoop\u7cfb\u5217\uff09\u5b66\u4e60","algId":"10","unigramlist":"\u539f\u7406\u56fe\u89e3\u5c0f#\u77e5\u8bc6\u77e5\u8bc6\u5de5\u4f5c","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/3751065C413168006D40243524033A3D363E0000","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-5492-1-4.html","title":"\u5728Windows Azure\u4e0a\u5b66\u4e60\u5927\u6570\u636e\u7684\u5e94\u7528\u4e0e\u5f00\u53d1-\u5fae\u8f6f\u4e91WindowsAzure","algId":"10","unigramlist":"\u5927#\u6570\u636e\u5b66\u4e60\u5e94\u7528\u6570\u636e\u5f00\u53d1\u4e0e#\u5f00\u53d1\u5e94\u7528#\u4e0e\u7684#\u5e94\u7528\u7684#\u4e0e","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/453C6F7C7604614C55001D062432752C71167040","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/forum.php?extra=&mod=viewthread&page=1&pid=53451&tid=5881","title":"\u5b66\u4e60Cloudera Hadoop 4\u7cfb\u5217\u5b9e\u6218\u8bfe\u7a0b\u8d44\u6599-\u5927\u6570\u636e\uff08hadoop\u7cfb\u5217\uff09\u8d44\u6e90","algId":"10","unigramlist":"\u5b9e\u6218\u8bfe\u7a0b\u7cfb\u5217\u5b66\u4e60\u8d44\u6599","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/395F1D5240534C2429007C54384F42220B475040","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-6412-1-7.html","title":"\u5b89\u88c5openstack\u5f00\u53d1\u73af\u5883\u5b66\u4e60\u7b14\u8bb0-Openstack\u5f00\u53d1","algId":"10","unigramlist":"\u7b14\u8bb0\u5b66\u4e60\u5b89\u88c5\u5f00\u53d1\u73af\u5883openstack","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/0A015835004E36021B00573141437B6B50415A00","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-6723-1-2.html","title":"MapReduce\u5de5\u4f5c\u539f\u7406\u8bb2\u89e3-Mapreduce","algId":"10","unigramlist":"\u8bb2\u89e3\u539f\u7406\u5de5\u4f5c","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/152F663B256D1152554012250357061A6D452740","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-7636-1-14.html","title":"\u57fa\u4e8eHBase\u5b9e\u73b0\u7684\u624b\u673a\u6570\u636e\u5907\u4efd\u7cfb\u7edf\u6e90\u7801\u4e0b\u8f7d-\u5927\u6570\u636e\uff08hadoop\u7cfb\u5217\uff09\u5b66\u4e60","algId":"10","unigramlist":"\u5907\u4efd\u6e90\u7801\u57fa\u4e8e\u5b9e\u73b0\u6570\u636e\u7cfb\u7edf\u4e0b\u8f7d\u624b\u673a\u7684#\u624b\u673a\u7684#\u6570\u636ee#\u7684","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/1E5E050A405E44203F00307C0A1359492C2B5E40","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-7732-1-1.html","title":"\u7d22\u5f15: \u57fa\u4e8eSolr DIH\u5b9e\u73b0MySQL\u8868\u6570\u636e\u5168\u91cf\u7d22\u5f15\u548c\u589e\u91cf\u7d22\u5f15-Solr|Nutch|Lucence","algId":"10","unigramlist":"\u589e\u91cf\u5b9e\u73b0\u7d22\u5f15\u57fa\u4e8e\u6570\u636e","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/68590B5A662E76394C40012526463E5B26447840","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-9615-1-1.html","title":"Spark\u6559\u7a0b\uff082\uff09Spark\u673a\u5668\u5b66\u4e60\u7cfb\u5217-KNN\u4e4b\u609f-\u673a\u5668\u5b66\u4e60","algId":"10","unigramlist":"\u6559\u7a0b\u5b66\u4e60\u673a\u5668\u7cfb\u5217","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/4C3D5302284637725940587F37693C214C385C40","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-10371-1-1.html","title":"MapReduce\u7b97\u6cd5\u7684\u5b9e\u73b0\u2014\u20141 TB\u6570\u636e\u6392\u5e8f\u6e90\u7801\u5206\u6790-Mapreduce","algId":"10","unigramlist":"\u7b97\u6cd5\u6392\u5e8f\u5b9e\u73b0\u6e90\u7801\u6570\u636e\u5206\u6790e#\u7684","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/516E255C423774643C40485A7444545812213F00","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-10577-1-3.html","title":"\u4f7f\u7528Mirantis\u63d0\u4f9b\u7684\u514d\u8d39\u5f00\u53d1\u7248\u5b66\u4e60OpenStack-openstack\u5b66\u4e60","algId":"10","unigramlist":"\u514d\u8d39#\u7248\u63d0\u4f9b\u4f7f\u7528\u5f00\u53d1\u5b66\u4e60\u514d\u8d39s#\u7684","bodykey":"","pagetime":"","search_grams_num":"3","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/01735F311C203A636B405816501E5B541D183D40","ctr":"0.065612","page_num":"2","cluster_name":"cnzznew"},{"url":"http:\/\/www.aboutyun.com\/thread-6780-1-1.html","title":"\u96f6\u57fa\u7840\u5b66\u4e60hadoop\u5230\u4e0a\u624b\u5de5\u4f5c\u7ebf\u8def\u6307\u5bfc\uff08\u521d\u7ea7\u7bc7\uff09","algId":"2-hot","unigramlist":"hadoop\t\u4e0a\u624b\t\u521d\u7ea7\t\u57fa\u7840\t\u5de5\u4f5c\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/2570746C1A774E3516407A227E2069526F210F00","has_thumb":"true","ctr":"-1.552986","page_num":"2","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?highlight=datanode&mod=viewthread&tid=7930","title":"hadoop \u5b8c\u5168\u5206\u5e03\u5f0f \u4e0b datanode\u65e0\u6cd5\u542f\u52a8\u89e3\u51b3\u65b9\u6cd5","algId":"2-hot","unigramlist":"datanode\thadoop\t\u5206\u5e03\u5f0f\t\u542f\u52a8\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/4A102411530C63412500293F1E603E5A0F1A3E40","has_thumb":"true","ctr":"-1.561561","page_num":"2","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?mod=viewthread&page=1&tid=9126","title":"hadoop2.5\u9700\u8981\u4ec0\u4e48\u7248\u672c\u7684zookeeper\u3001\u53cahbase","algId":"2-hot","unigramlist":"hadoop\thbase\tzookeeper\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/4E561F1250465A6D4B403F0211595B733E0D2B40","has_thumb":"true","ctr":"-1.589791","page_num":"2","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-10992-1-1.html","title":"\u3010\u5df2\u89e3\u51b3\u3011\u4f7f\u7528hbase shell \u547d\u4ee4get_counter\u7684\u95ee\u9898-HBase","algId":"2-hot","unigramlist":"HBase\tcounter\thbase\tshell\t\u547d\u4ee4\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/3D2D137E730A35323E405F49170A324C172B5500","has_thumb":"true","ctr":"-1.591196","page_num":"2","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?highlight=OpenStac&mod=viewthread&tid=8563","title":"\u5728openstack\u5b9e\u4f8b\u4e2d\u505aHA, \u5b9e\u73b0Web\u53cc\u673a\u70ed\u5907","algId":"2-hot","unigramlist":"openstack\t\u5b9e\u4f8b\t\u673a#\u70ed\t\u70ed#\u5907\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/1E1B4B0E234C2653310011003A580402484C4800","has_thumb":"true","ctr":"-1.487896","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-7161-1-1.html","title":"\u5b89\u88c5openstack\u8fc7\u7a0b\u4e2d: sed\u547d\u4ee4\u7684\u4f5c\u7528\u662f\u4ec0\u4e48","algId":"2-hot","unigramlist":"openstack\t\u547d\u4ee4\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/582B4044453A0C0B1D40592536160B6E776B6C00","has_thumb":"true","ctr":"-1.498028","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-8401-1-1.html","title":"hbase\u5f00\u53d1\u73af\u5883\u642d\u5efa\u53ca\u8fd0\u884chbase\u5c0f\u5b9e\u4f8b\uff08HBase 0.98.3\u65b0api\uff09-Hbase","algId":"2-hot","unigramlist":"0.98\tHBase\tHbase\thbase\t\u5b9e\u4f8b\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/6C157D6723073B0777407F32556E720C5D7F6B40","has_thumb":"true","ctr":"-1.500945","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-7693-1-1.html","title":"\u770b\u770b\u8001\u5916\u7684\u89c6\u9891, \u600e\u4e48\u6559\u4f60\u5b89\u88c5Hive\u3001Sqoop","algId":"2-hot","unigramlist":"Hive\tSqoop\t\u8001\u5916\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/2F60487B5A2E045C2B4057394F5B401469490E00","has_thumb":"true","ctr":"-1.534340","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?mod=viewthread&tid=9218","title":"OpenStack Installation Guide for Ubuntu\u4e2d\u6587\u7ffb\u8bd1\u7248","algId":"2-hot","unigramlist":"Guide\tInstallation\tOpenStack\tUbuntu\t\u4e2d\u6587\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/0F7A1253726A6500250047216A027F2F09462840","has_thumb":"true","ctr":"-1.536030","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?highlight=hive&mod=viewthread&tid=6845","title":"Impala\u4e0eHive\u7684\u76f8\u4f3c\u4e4b\u5904, \u533a\u522b\u5728\u4ec0\u4e48\u5730\u65b9\uff1f","algId":"2-hot","unigramlist":"Hive\tImpala\t\u533a\u522b\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/34735B3D440E2043594045020C3807195A2B3400","has_thumb":"true","ctr":"-1.605451","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-6964-1-1.html","title":"java\u57fa\u7840: eclipse\u7f16\u7a0b\u4e0d\u5f97\u4e0d\u77e5\u9053\u7684\u6280\u5de7","algId":"2-hot","unigramlist":"eclipse\tjava\t\u4e0d\u77e5\u9053\t\u57fa\u7840\t\u6280\u5de7\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/73274D44044D75500B400A547A5B0164786D0100","has_thumb":"true","ctr":"-1.609005","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-7765-1-5.html","title":"Hadoop Hive\u4e0eHbase\u6574\u5408+thrift","algId":"2-hot","unigramlist":"Hadoop\tHbase\tHive\tthrift\t\u6574\u5408\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/512C0402676A6572284021117C5C7C100C014C00","has_thumb":"true","ctr":"-1.622975","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?highlight=openstack%D7%B0&mod=viewthread&tid=7052","title":"openstack\u5b89\u88c5\u82f1\u6587\u7248openstack-install-guide-ubuntu12_04-apt-trunk","algId":"2-hot","unigramlist":"guide\tinstall\topenstack\ttrunk\tubuntu\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/1B321602572A77757200552B5476042C51135940","has_thumb":"true","ctr":"-1.625201","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-7091-1-1.html","title":"mapreduce\u5b66\u4e60\u6307\u5bfc\u53ca\u7591\u96be\u89e3\u60d1\u6c47\u603b","algId":"2-hot","unigramlist":"mapreduce\t\u6307\u5bfc\t\u6c47\u603b\t\u7591\u96be\t\u89e3\u60d1\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/4B664B2D1148677474406E0947036A751D230700","has_thumb":"true","ctr":"-1.639340","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-7746-1-1.html","title":"hbase 0.96\u6574\u5408\u5230hadoop2.2\u4e09\u4e2a\u8282\u70b9\u5168\u5206\u5e03\u5f0f\u5b89\u88c5\u9ad8\u53ef\u9760\u6587\u6863","algId":"2-hot","unigramlist":"0.96\thadoop\thbase\t\u5206\u5e03\u5f0f\t\u53ef\u9760\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/6A2A314C4C6747276F00654330202A505C656E00","has_thumb":"true","ctr":"-1.661361","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?highlight=cloudstack&mod=viewthread&tid=6948","title":"CloudStack 4.2\u5728 CentOS 6.4 \u4e0a\u5b89\u88c5","algId":"2-hot","unigramlist":"CentOS\tCloudStack\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/6564107B36222A4D56403E13737E2E4F2F1D3E40","has_thumb":"true","ctr":"-1.671742","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-6566-1-1.html","title":"\u57fa\u4e8e Hadoop \u5efa\u7acb\u4e91\u8ba1\u7b97\u7cfb\u7edf-\u5927\u6570\u636e\uff08hadoop\u7cfb\u5217\uff09\u8d44\u6e90","algId":"2-hot","unigramlist":"Hadoop\thadoop\t\u4e91#\u8ba1\u7b97\t\u5efa\u7acb\t\u5efa\u7acb#\u4e91\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/3D56395A4643731026402360292F530E041E0240","has_thumb":"true","ctr":"-1.719974","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-9303-1-1.html","title":"Cloudera Manager5\u53caCDH5\u5728\u7ebf\uff08cloudera-manager-installer.bin\uff09\u5b89\u88c5\u8be6\u7ec6\u6587\u6863","algId":"2-hot","unigramlist":"Cloudera\tManager\tcloudera\tinstaller\tmanager\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/7C01612E362C0C3332404E271B575A6C5C191D40","has_thumb":"true","ctr":"-1.721715","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-6876-1-1.html","title":"Yahoo\u7684Storm-YARN\u79bb\u5b9e\u65f6Hadoop\u67e5\u8be2\u66f4\u8fdb\u4e00\u6b65","algId":"2-hot","unigramlist":"Hadoop\tStorm\tYARN\tYahoo\t\u5b9e\u65f6\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/362D56253B150C0C36405E710B76034D07091900","ctr":"-1.724847","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/forum.php?highlight=&mod=viewthread&tid=8683","title":"hadoop\u3001hbase\u3001hive\u7248\u672c\u5bf9\u5e94\u5173\u7cfb\u67e5\u627e\u8868","algId":"2-hot","unigramlist":"hadoop\thbase\thive\t\u67e5\u627e\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/0E681F653C58204E1100265D5D003D5A1D1C3240","has_thumb":"true","ctr":"-1.728956","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-8178-1-1.html","title":"hadoop\u5bb6\u65cf\u3001strom\u3001spark\u3001Linux\u3001flume\u7b49jar\u5305\u3001\u5b89\u88c5\u5305\u6c47\u603b\u4e0b\u8f7d(\u6301\u7eed\u66f4\u65b0)","algId":"2-hot","unigramlist":"Linux\tflume\thadoop\tspark\tstrom\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/3E36516915711B0A0C40416D7B66771E18602100","has_thumb":"true","ctr":"-1.732907","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-9167-1-1.html","title":"\u5927\u795e\u4eec\u7ed9\u8bb2\u8bb2win7+eclipse+ubuntu \u73af\u5883\u4e0bmapreduce?\u6c42\u52a9-\u5927\u6570\u636e\uff08hadoop\u7cfb\u5217\uff09","algId":"2-hot","unigramlist":"eclipse\thadoop\tmapreduce\tubuntu\t\u5927\u795e\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/4A645427520F16670D4044404D51400004427F00","has_thumb":"true","ctr":"-1.744157","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-7902-1-1.html","title":"\u6784\u5efaOpenStack\u7684\u9ad8\u53ef\u7528\u6027\uff08HA, High Availability\uff09","algId":"2-hot","unigramlist":"Availability\tHigh\tOpenStack\t\u53ef\u7528\u6027\t\u6784\u5efa\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/753A152C1125444041004B143872101063430100","has_thumb":"true","ctr":"-1.756119","page_num":"3","cluster_name":"hot_redis"},{"url":"http:\/\/www.aboutyun.com\/thread-8433-1-1.html","title":"about\u4e91\u5206\u6790discuz\u8bba\u575bapache\u65e5\u5fd7hadoop\u5927\u6570\u636e\u9879\u76ee: \u9700\u6c42\u5206\u6790\u8bf4\u660e\u4e66","algId":"2-hot","unigramlist":"apache\tdiscuz\thadoop\t\u4e91#\u5206\u6790\t\u5206\u6790\t","bodykey":"","pagetime":"","thumbnail":"http:\/\/recimg.cn-hangzhou.oss.aliyun-inc.com\/6C3A11375E62080C26005065117B26153E1C7D40","has_thumb":"true","ctr":"-1.760171","page_num":"3","cluster_name":"hot_redis"}];
	//处理热词
	tuiFixedRun.set.hot.data_hot_txt = "hadoop,hashmap,java";
	//自定义热词数量
	tuiFixedRun.set.hot.data_hot_txt_user = 0;
	//图片loading
	tuiFixedRun.imgLoad = "http://img.cnzz.net/adt/cnzz_tui/vip/loading.gif";
	//错误图片
	tuiFixedRun.errorDir = "http://img.cnzz.net/adt/cnzz_tui/vip/error/error_";
	//logo 点击地址
	tuiFixedRun.tuiUrl = "http://tui.cnzz.com";
	//统计地址
	tuiFixedRun.tongjiUrl = "http://log.so.cnzz.net/stat.php?";
	tuiFixedRun.errorNum = 35;
	//搜索地址
	tuiFixedRun.searchUrl = "http://s.cnzz.net/";
	//图片路径
	tuiFixedRun.imgDir = "http://img.cnzz.net/adt/cnzz_tui/vip/";
	//ip
	tuiFixedRun.ip = "117.184.110.242";
	//cookie
	tuiFixedRun.Rcookie = "71fb479f3c663ecc0f1937984692a5ee";
	//jumpUrl
    tuiFixedRun.jumpUrl = "";
    //是否米尔用户
    tuiFixedRun.isMier  = "0";
	//公用方法
	function getEyeJsStyle(oBj, styleName) {
		if (oBj.currentStyle) {
			return oBj.currentStyle[styleName];
		} else {
			return getComputedStyle(oBj, null)[styleName];
		};
	};
	function addEvent(Elem, type, handle) {
		if (Elem.addEventListener) {
			Elem.addEventListener(type, handle, false);
		} else if (Elem.attachEvent) {
			Elem.attachEvent("on" + type, handle);
		};
	};
	function getElemPos(obj) {
		var pos = {
			"top" : 0,
			"left" : 0
		};
		if (obj.offsetParent) {
			while (obj.offsetParent) {
				pos.top += obj.offsetTop;
				pos.left += obj.offsetLeft;
				obj = obj.offsetParent;
			}
		} else if (obj.x) {
			pos.left += obj.x;
		} else if (obj.x) {
			pos.top += obj.y;
		}
		return {
			x : pos.left,
			y : pos.top
		};
	};
	if (tuiFixedRun["set"]["hot"]["data_hot"]!=0 && tuiFixedRun["set"]["hot"]["data_hot_txt"]!="") {
		tuiFixedRun.ft = "block_kw";
	} else{
		tuiFixedRun.ft = "block_s";
	};
	tuiFixedRun.request = {
		"common" : tuiFixedRun.tongjiUrl+"ip="+tuiFixedRun.ip+"&pui=cntp&cok="+tuiFixedRun.Rcookie+"&vr=1&aid=1000072387&sid=aboutyun.com&img=" + tuiFixedRun["set"]["base"]["data_type"] + "&so=dz&ft=" + tuiFixedRun.ft + "",
		"sid" : "aboutyun.com",
		"aid" : "1000072387",
		"hid" : "98f817490f5dcdcc9ebff44a657891d4",
		"bkt" : "0",
		"so" : "dz"
	};
	function questImg(url) {
		var Img = new Image();
		var d = new Date();
		Img.onload = Img.onabort = Img.onerror = function () {
			Img = null;
		};
		Img.src = tuiFixedRun.request.common + url + "&"+ encodeURIComponent(String.fromCharCode(1)) + "&oref=" + encodeURIComponent(document.referrer) + "&purl=" + encodeURIComponent(window.location.href) +"&_rnd=" + (Date.parse(d) + "." + d.getMilliseconds());
	};
	function checkData() {
		return true;
		var t = 0;		//总数
		var dt = 0;		//总需
		var r = false;	//结果
		var n = 0.6;	//良品率
		var set = tuiFixedRun.set;
		//计算总需
		if (set.base.data_type == 0) {
			dt = Number(set.style.style_txt_col) * Number(set.style.style_txt_row);
		}else if (set.base.data_type == 2) {
			dt = Number(set.style.style_pic_col) * Number(set.style.style_pic_row);
		}else {
			dt = Number(set.style.style_pic_col) * Number(set.style.style_pic_row) + Number(set.style.style_txt_col) * Number(set.style.style_txt_row);
		};
		if (tuiFixedRun.data.length < dt * n){
			return false;
		}else {
			//计算良好数据
			for (var i=0;i<tuiFixedRun.data.length;i++) {
				if (tuiFixedRun.data[i].title) {
					t++;
				};
			};
			var l = t / dt;
			l < n ? r = false : r = true;
			return r;
		};
	};
	//*********************************
	if (!tuiFixedRun.demo) {
		//运行之
		if (tuiFixedRun.data && tuiFixedRun.data[0]) {
			if (checkData()) {
				tuiFixedRun.init();
			}else {
				var url =  "&" + encodeURIComponent(String.fromCharCode(1)) + "&has=false&ch=wprdsp&l=view&good=false";
				questImg(url)
			};
		} else {
			var url =  "&" + encodeURIComponent(String.fromCharCode(1)) + "&has=false&ch=wprdsp&l=view";
			questImg(url)
		};
	};
	//*********配置参数***************************
	})();