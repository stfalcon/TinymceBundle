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
 * @file 		/includes/tasks/conf.php
 */
	/*
  		Защита от прямой загрузки
	*/
	defined('ACCESS') or die();
		
	header('Content-type: text/json; charset='.Manager::$conf['general.char_set']);
		
	echo json_encode(array(
		'lang' => Manager::$conf['general.language'],
		'max_file_size' => Manager::$conf['filesystem.max_file_size'],
		'queue_size_limit' => Manager::$conf['filesystem.queue_size_limit']
	));
?>