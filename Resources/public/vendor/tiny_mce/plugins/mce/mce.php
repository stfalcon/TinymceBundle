<?php
/*
Plugin Name: MCImageManager & MCFileManager
Plugin URI: http://tinymce.moxiecode.com/#
Description: Hookin for MCImageManager & MCFileManager into Wordpress 2.5+.
Author: Moxiecode Systems
Version: 1.1
Author URI: http://tinymce.moxiecode.com/
*/

session_start();

function moxiecode_plugins_url($type) {
	// Get plugins location, can be either installed in the wp-content/plugins/<pluginname> folder or in the plugin folder of tinymce.
	if (file_exists(ABSPATH . "wp-includes/js/tinymce/plugins/". $type ."/editor_plugin.js")) {
		return "../../../wp-includes/js/tinymce/plugins/". $type ."/editor_plugin.js";
	} else if (file_exists(ABSPATH . "wp-content/plugins/". $type ."/editor_plugin.js")) {
		return "../../../wp-content/plugins/". $type ."/editor_plugin.js";
	}

	// Return false if we cant find them
	return false;
}


function moxiecode_plugins_check($plugins) {
	$path = moxiecode_plugins_url("imagemanager");
	// Set path to the editor_plugin.js file
	if ($path)
		$plugins["imagemanager"] = $path;

	$path = moxiecode_plugins_url("filemanager");
	// Set path to the editor_plugin.js file
	if ($path)
		$plugins["filemanager"] = $path;

	return $plugins;
}

function moxiecode_plugins_buttons($buttons) {
	$im = moxiecode_plugins_url("imagemanager");
	$fm = moxiecode_plugins_url("filemanager");

	// Check if we have them installed, if so, add a seperator and the buttons.
	if ($im || $fm)
		$buttons[] = "separator";

	if ($im)
		$buttons[] = "insertimage";

	if ($fm)
		$buttons[] = "insertfile";

	// Check for tinymce-advanced plugin
	$tadv_btns1 = (array) get_option('tadv_btns1');
	if (count($tadv_btns1) != 0) {
		// Seems like tinymce-advanced is installed
		$upd = false;
		// Check for insertimage button, and insert it if its not found
		if ($im && !in_array("insertimage", $tadv_btns1)) {
			$tadv_btns1[] = "insertimage";
			$upd = true;
		}
		
		// Check for insertfile button, and insert it if its not found
		if ($fm && !in_array("insertfile", $tadv_btns1)) {
			$tadv_btns1[] = "insertfile";
			$upd = true;
		}

		// Only update if we need to (usually only first time)
		if ($upd)
			update_option('tadv_btns1', $tadv_btns1);
	}

	return $buttons;
}

function moxiecode_plugins_init($user) {
	// Check if user can edit posts or edit pages
	if ( current_user_can('edit_posts') || current_user_can('edit_pages') ) {
		$im = moxiecode_plugins_url("imagemanager");
		$fm = moxiecode_plugins_url("filemanager");

		// Get upload path
		$upload_path = get_option( 'upload_path' );
		if (trim($upload_path) === '')
			$upload_path = 'wp-content/uploads';

		$upload_path = path_join(ABSPATH, $upload_path);

		// Get daily path
		$up = wp_upload_dir();

		if ($im || $fm)
			$_SESSION["isLoggedIn"] = true;

		if ($im) {
			$_SESSION["imagemanager.filesystem.rootpath"] = $upload_path;
			$_SESSION["imagemanager.filesystem.path"] = $up["path"];
			$_SESSION["imagemanager.general.remember_last_path"] = false;
			$_SESSION["imagemanager.filesystem.exclude_directory_pattern"] = '/^(mcith|js_cache)$/i';
		}

		if ($fm) {
			$_SESSION["filemanager.filesystem.rootpath"] = $upload_path;
			$_SESSION["filemanager.filesystem.path"] = $up["path"];
			$_SESSION["filemanager.general.remember_last_path"] = false;
			$_SESSION["filemanager.filesystem.exclude_directory_pattern"] = '/^(mcith|js_cache)$/i';
		}

	} else {
		// Destroy the im/fm session
		if (isset($_SESSION["isLoggedIn"]))
			unset($_SESSION["isLoggedIn"]);
	}
}

// Check for the plugins, access and setup the sessions.
add_action('init', 'moxiecode_plugins_init');
// Check the paths and add it as plugins
add_filter('mce_external_plugins', 'moxiecode_plugins_check', 0);
// Add the buttons, also checks for tinymce-advanced plugin
add_filter("mce_buttons", "moxiecode_plugins_buttons");

?>