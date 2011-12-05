/**
 * Cyber Image Manager
 *
 *
 * @package		Cyber Image Manager
 * @author		Radik
 * @copyright	Copyright (c) 2010, Cyber Applications.
 * @link		http://www.cyberapp.ru/
 * @since		Version 1.1
 * @file 		/plugin.js
 */
 
(function (){
	window.CyberIM = {
		execute : function (d) {
			CKEDITOR.tools.callFunction(d.CKEditorFuncNum, d.Data, '');
		}
	}
	
	CKEDITOR.plugins.add('cyberim', {
		init: function (e){
			e.config['filebrowserImageBrowseUrl'] = CKEDITOR.basePath+'plugins/cyberim/index.php';
		}	
	});	
})()