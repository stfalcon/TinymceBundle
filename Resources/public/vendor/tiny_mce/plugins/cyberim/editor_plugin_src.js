(function() {
 	window.CyberIM = {
 		filebrowserCallBack: function(field, url, type, wind){
			
			f = {
				x : parseInt(screen.width / 2.0) - (wind.width / 2.0),
				y : parseInt(screen.height / 2.0) - (wind.height / 2.0),
				width : 810,
				height : 500,
				inline : 1,
				url: CyberIM.url + '/index.php',
				title: 'Cyber Image Manager'
			};	
			
			CyberIM.params = {field: field, wind: wind};
			
			// Use TinyMCE window API
			if (window.tinymce && tinyMCE.activeEditor){
				CyberIM.w = tinyMCE.activeEditor.windowManager.open(f);
				return CyberIM.w;	
			}
				
			// Use jQuery WindowManager
			if (window.jQuery && jQuery.WindowManager){
				CyberIM.w =  jQuery.WindowManager.open(f);
				return CyberIM.w; 	
			}
				
			// Use native dialogs
			CyberIM.w = window.open(f.url, 'mcImageManagerWin', 'left=' + f.x + 
				',top=' + f.y + ',width=' + f.width + ',height=' + 
				f.height + ',scrollbars=' + (f.scrollbars ? 'yes' : 'no') + 
				',resizable=' + (f.resizable ? 'yes' : 'no') + 
				',statusbar=' + (f.statusbar ? 'yes' : 'no')
			);

			try {
				CyberIM.w.focus();
			} catch (ex) {
				// Ignore
			}
		},
		
		
		execute : function (d){
			CyberIM.params.wind.document.forms[0].elements[CyberIM.params.field].value = d.Data;
			CyberIM.params.wind.document.forms[0].elements[CyberIM.params.field].onchange();
			tinyMCE.activeEditor.windowManager.close(d.w);
		},
		
		getInfo : function (){
			return {
				longname : 'Cyber Image Manager',
				author : 'WSDLab',
				authorurl : 'http://www.wsdlab.ru/',
				infourl : 'http://www.wsdlab.ru/',
				version : '1.0'
			};
		}
	 }
	
	tinymce.create('tinymce.plugins.CyberIMPlugin', {
		init : function(ed, url) {
			ed.settings.file_browser_callback = CyberIM.filebrowserCallBack;
			CyberIM.editor = ed;
			CyberIM.url = url;
		},

		getInfo : function() {
			return CyberIM.getInfo();
		}
	});

	tinymce.PluginManager.add('cyberim', tinymce.plugins.CyberIMPlugin);
})();