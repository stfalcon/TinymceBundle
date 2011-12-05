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
 * @file 		/includes/ErrorManager.php
 */
 
/*
  Защита от прямой загрузки
*/
defined('ACCESS') or die();

class ErrorManager {
	private static $instance;
	private $driver;
	
	
	public function __construct(){
		ErrorManager::$instance = & $this;		
		//загружаем драйвер обработки изображений
		$driver_name = 'ErrorManager_'.Manager::$conf['general.error'].'_Driver';
		require_once(INCLUDE_PATH.'drivers'.DS.'ErrorManager_Driver'.EXT);
		require_once(INCLUDE_PATH.'drivers'.DS.'ErrorManager'.DS.$driver_name.EXT);
		$this->driver = new $driver_name;
		set_error_handler(array($this, 'errorHandler'));
		error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);			
	}
		
	public static function & instance(){
		empty(ErrorManager::$instance) and new ErrorManager;
		return ErrorManager::$instance;
	}	
		
	public function errorHandler($error_num, $error_var, $error_file, $error_line){
		ErrorManager::instance()->driver->errorHandler($error_num, $error_var, $error_file, $error_line);
	}	
}

?>