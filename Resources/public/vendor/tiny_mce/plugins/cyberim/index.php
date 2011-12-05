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
 * @file 		/index.php
 */
 
	define('ACCESS', true);
	define('EXT', '.php');
	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT_PATH', dirname(__FILE__).DS);
	define('INCLUDE_PATH', ROOT_PATH.'includes'.DS);
	define('TASKS_PATH', INCLUDE_PATH.'tasks'.DS);
	define('CONF_PATH', ROOT_PATH);
	define('PAGES_PATH', ROOT_PATH.'pages'.DS);
	define('SCRIPT_PATH', ROOT_PATH.'js'.DS);
	define('LANG_PATH', ROOT_PATH.'lang'.DS);
	
	//инициализация буфера вывода
	ob_start();
	ob_implicit_flush(0);
		
	require_once(INCLUDE_PATH.'Manager.php');
		
		
	//загружаем насройки
	require_once(CONF_PATH.'config'.EXT);
	Manager::$conf = $conf;
	unset($conf);
		
	//загрузка менеджера обработки ошибок 
	require_once(INCLUDE_PATH.'ErrorManager'.EXT);
	Manager::$error_m = new ErrorManager;	
		
	//загрузка менеджера сессий
	require_once(INCLUDE_PATH.'SessionManager'.EXT);
	Manager::$sess_m = new SessionManager;
	
	//проверка авторизации
	if (!Manager::$sess_m->authorisation()) die();
		
	//загружаем менеджер изображений
	require_once(INCLUDE_PATH.'ImageManager'.EXT);		
	Manager::$image_m = new ImageManager; 
		    
	//загружаем менеджер файловой системы
	require_once(INCLUDE_PATH.'FileManager'.EXT);
	Manager::$file_m = new FileManager;
	
	//получаем абсолютный путь к папке с фалами
	Manager::$conf['filesystem.files_abs_path'] = FileManager::clear_path(realpath(Manager::$conf['filesystem.files_path']).DS);
	
	$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : 'page';
	Manager::peform($task);
	
	//получение и очистка буфера вывода
	$buffer = ob_get_contents();
	ob_end_clean();
	
	//определяем нужно ли сжимать
	if (Manager::$conf['stream.use_gzip']) {
		//определяем метод сжатия
		if (strpos((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
			$encoding = 'x-gzip';
		} elseif (strpos((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
			$encoding = 'gzip';
		} elseif (strpos((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false) {
			$encoding = 'deflate';
		}
		//производим сжатие данных	
		if (isset($encoding)){
			header('Content-Encoding: '.(string) $encoding);
			$buffer = ($encoding == 'gzip' || $encoding == 'x-gzip') 
				? gzencode($buffer, Manager::$conf[(string) trim('stream.compression_level')])
				: gzdeflate($buffer,  Manager::$conf[(string) trim('stream.compression_level')]);  
			}
	}
	//выводим буфер	
	echo $buffer;
?>