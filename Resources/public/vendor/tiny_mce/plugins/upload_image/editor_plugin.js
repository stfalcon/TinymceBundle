/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('upload_image');
    console.log('lang');
	tinymce.create('tinymce.plugins.UploadImage', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
            console.log('1');
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceUpload', function() {
                console.log('2');
                window.open( upload_article_image_path, '_Addimage', 'toolbar=0,location=0,status=0, left=0, top=0, menubar=0,scrollbars=yes,resizable=0,width=820,height=420')//'toolbar=0,location=0,status=0, left=0, top=0, menubar=0,scrollbars=yes,resizable=0,width=820,height=420'
//				ed.windowManager.open({
//					file : '/app_dev.php/tinymce/upload/0',
//					width : 820 + parseInt(ed.getLang('upload_image.delta_width', 0)),
//					height : 420 + parseInt(ed.getLang('upload_image.delta_height', 0)),
//					inline : 1
//				}, {
//					plugin_url : url, // Plugin absolute URL
//					some_custom_arg : 'custom arg' // Custom argument
//				});
			});
console.log('3');
			// Register example button
			ed.addButton('upload_image', {
				title : 'upload_image.desc',
				cmd : 'mceUpload',
				image : url + '/img/insertimage.gif'
			});
console.log('4');
			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('upload_image', n.nodeName == 'IMG');
                console.log('add 5');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Upload image plugin',
				author : 'butt',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('upload_image', tinymce.plugins.UploadImage);
})();