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
 * @file 		/includes/Manager.php
 */
 
/*
  Защита от прямой загрузки
*/
defined('ACCESS') or die();

class Manager {
	public static $file_m;
	public static $image_m;
	public static $sess_m;
	public static $error_m;
	public static $conf;
	
	/*
	  Метод выполняет задачу
	*/
	public function peform($task = ''){
		if (file_exists(TASKS_PATH.$task.EXT)){
			require_once(TASKS_PATH.$task.EXT);
		}
	}	
}
?>