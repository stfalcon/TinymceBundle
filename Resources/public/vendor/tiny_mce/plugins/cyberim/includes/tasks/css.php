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
 * @file 		/includes/tasks/css.php
 */
	/*
  		Защита от прямой загрузки
	*/
	defined('ACCESS') or die();
	
	$file = PAGES_PATH.Manager::$conf['general.template'].DS.'css'.DS.$_REQUEST['file'].'.css';
	
	if (file_exists($file)){
		header('Content-type: text/css; charset='.Manager::$conf['general.char_set']);
		header('Cache-Control: private');
		readfile($file);	
	} else{
		header('HTTP/1.0 404 Not Found');
	}
?>