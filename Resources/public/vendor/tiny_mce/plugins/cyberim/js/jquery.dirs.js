/**
 * Cyber Image Manager
 *
 *
 * @package		Cyber Image Manager
 * @author		Radik
 * @copyright	Copyright (c) 2010, Cyber Applications.
 * @link		http://www.cyberapp.ru/
 * @since		Version 1.1
 * @file 		/js/jquery.dirs.js
 */
 
(function($){$.extend($.fn,{dirs:function(p){var _this=this;var o={};var query=null;$.extend(o,{root:'',filder_event:function(path){},error_event:function(XMLHttpRequest,textStatus,errorThrown){},folder_del_event:function(el){},script:'index.php',back:'...'},p);var them={b:'<ul>',el:'<li class="{#class}" rel="{#path}">'+'<span>{#name}</span>'+'</li>',e:'</ul>'};function bind_events(){$(_this).find('li').hover(function(){$(this).addClass('sen-hover');},function(){$(this).removeClass('sen-hover');});$(_this).find('.sen-back span').bind('dir_back',function(){if(o.root=='')return;var path=$(this).parent().attr('rel');path=path.split('/');path=$.grep(path,function(n,i){return i<(path.length-2);});if(path.length!=0){to_path=path.join('/')+'/';o.root=path.join('/')+'/';}else{to_path='';o.root='';}
create_html(to_path);if($.isFunction(o.folder_event))o.folder_event(to_path);}).bind('click',function(){$(this).triggerHandler('dir_back');});$(_this).find('.sen-el span').bind('dir_open',function(){create_html($(this).parent().attr('rel'));o.root=$(this).parent().attr('rel')
if($.isFunction(o.folder_event))o.folder_event($(this).parent().attr('rel'));}).bind('click',function(){$(this).triggerHandler('dir_open');});}
function create_html(to_path){if(query)query.abort();query=$.ajax({async:true,cache:false,dataType:'json',url:o.script,data:{path:to_path},type:'post',error:o.error_event,success:function(data,textStatus){var html=them.b;if(o.root!='')
html+=them.el.replace('{#name}',o.back).replace('{#path}',o.root).replace('{#class}','sen-back');if(data){$.each(data,function(){html+=them.el.replace('{#name}',this.name).replace('{#path}',this.path).replace('{#class}','sen-el');});}
html+=them.e;$(_this).html(html);bind_events();}});};create_html(o.root);}});})(jQuery)