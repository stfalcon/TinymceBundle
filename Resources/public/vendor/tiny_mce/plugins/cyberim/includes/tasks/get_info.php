<?php
/**
 * Cyber Image Manager
 *
 *
 * @package		Cyber Image Manager
 * @author		Radik
 * @copyright	Copyright (c) 2010, Cyber Applications.
 * @link		http://www.cyberapp.ru/
 * @since		Version 1.1
 * @file 		/includes/tasks/get_info.php
 */
 
	/*
  		Защита от прямой загрузки
	*/
	defined('ACCESS') or die();
	header('Content-type: text/json; charset='.Manager::$conf['general.char_set']);
		
	$filename = FileManager::convertToFileSystem(
		urldecode(
			str_ireplace(Manager::$conf['filesystem.path'], Manager::$conf['filesystem.files_path'], $_POST['filename'])
		)
	);
	$info = ImageManager::instance()->info($filename);
	echo json_encode($info);
?>