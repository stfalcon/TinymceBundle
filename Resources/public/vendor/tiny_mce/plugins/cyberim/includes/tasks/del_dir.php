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
 * @file 		/includes/tasks/del_dir.php
 */
	/*
  		Защита от прямой загрузки
	*/
	defined('ACCESS') or die();
	
	echo json_encode(
		array('done' => 
			FileManager::delete_dir(
				FileManager::convertToFileSystem(
					FileManager::clear_path(Manager::$conf['filesystem.files_abs_path'].DS.$_REQUEST['path'])
				)
			)
		)
	);
?>