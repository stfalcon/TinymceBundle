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
 * @file 		/includes/ImageManager.php
 */
 
/*
  Защита от прямой загрузки
*/
defined('ACCESS') or die();

class ImageManager {
	private static $instance;
	private $driver;
	
	
	public function __construct(){
		ImageManager::$instance = & $this;		
	    //загружаем драйвер обработки изображений
		$driver_name = 'ImageManager_'.Manager::$conf['thumbnail.driver'].'_Driver';
		require_once(INCLUDE_PATH.'drivers'.DS.'ImageManager_Driver'.EXT);
		require_once(INCLUDE_PATH.'drivers'.DS.'ImageManager'.DS.$driver_name.EXT);
		$this->driver = new $driver_name;
	}
	
	public function thunbnail($src = '', $dest = '', $width = 0, $height = 0){
		if ($src == '' || $dest == '') return false;
		return $this->driver->open($src) &&
		       $this->driver->resize($width, $height, Manager::$conf['thumbnail.resize_to_frame']) && 
			   $this->driver->save($dest, Manager::$conf['thumbnail.jpeg_quality']) && 
			   chmod($dest, Manager::$conf['filesystem.file_chmod']);
	}
	
	
	public function info($filename = ''){
		if ($filename == '' || !file_exists($filename)){
			return false;
		}
		
		$i = $this->driver->info($filename);
		
		
		return array_merge(array('size' => filesize($filename)), $i);
	}
	
	public static function & instance(){
		empty(ImageManager::$instance) and new ImageManager;
		return ImageManager::$instance;
	}	
}

?>