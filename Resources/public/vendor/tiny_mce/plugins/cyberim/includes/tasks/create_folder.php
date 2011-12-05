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
 * @file 		/includes/tasks/create_folder.php
 */
 
	/*
  		Защита от прямой загрузки
	*/
	defined('ACCESS') or die();
	
	echo json_encode(array('done' => FileManager::create_dir(Manager::$conf['filesystem.files_path'].$_REQUEST['path'])));
?>