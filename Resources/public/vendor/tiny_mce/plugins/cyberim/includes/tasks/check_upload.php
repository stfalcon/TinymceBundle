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
 * @file 		/includes/tasks/check_upload.php
 */
 
	/*
  		Защита от прямой загрузки
	*/
	defined('ACCESS') or die();
		
	header('Content-type: text/json; charset='.Manager::$conf['general.char_set']);	
		
	$fileArray = array();
	foreach ($_POST as $key => $value) {
		if ($key != 'folder' && !empty($value)) {
			$f = FileManager::clear_path(Manager::$conf['filesystem.files_abs_path'].$_POST['folder'].$value);
			if (file_exists(FileManager::convertToFileSystem($f))){
				$fileArray [] = $key;
			}
		}	
	}
	echo json_encode($fileArray);
?>