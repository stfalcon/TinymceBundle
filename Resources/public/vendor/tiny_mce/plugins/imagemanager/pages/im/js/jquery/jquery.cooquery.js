/* jQuery CooQuery Plugin v2 (minified) - http://cooquery.lenonmarcel.com.br/
Copyright 2009, 2010 Lenon Marcel
Dual licensed under the MIT and GPL licenses.
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html */
(function($){$.setCookie=function(name,value,options){if(typeof name==='undefined'||typeof value==='undefined')
return false;var str=name+'='+encodeURIComponent(value);if(options.domain)str+='; domain='+options.domain;if(options.path)str+='; path='+options.path;if(options.duration){var date=new Date();date.setTime(date.getTime()+options.duration*24*60*60*1000);str+='; expires='+date.toGMTString();}
if(options.secure)str+='; secure';return document.cookie=str;};$.delCookie=function(name){return $.setCookie(name,'',{duration:-1});};$.readCookie=function(name){var value=document.cookie.match('(?:^|;)\\s*'+name.replace(/([-.*+?^${}()|[\]\/\\])/g,'\\$1')+'=([^;]*)');return(value)?decodeURIComponent(value[1]):null;};$.CooQueryVersion='v 2.0';})(jQuery);
