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
 * @file 		/includes/tasks/get_folder_list.php
 */
 
	/*
  		Защита от прямой загрузки
	*/
	defined('ACCESS') or die();
	
	header('Content-type: text/json; charset='.Manager::$conf['general.char_set']);
	$list = Manager::$file_m->get_path_list($_REQUEST['path'], false, true);
	echo json_encode($list);
?>