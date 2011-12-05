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
 * @file 		/includes/drivers/ImageManager/ImageManager_ImageMagick_Driver.php
 */
 
/*
  Защита от прямой загрузки
*/
defined('ACCESS') or die();

class ImageManager_ImageMagick_Driver extends ImageManager_Driver{
	
	private $im = NULL;
	
	/*
	  коснтруктор класса ImageManager_ImageMagick_Driver
	*/
	public function __construct($filename = ''){
		$this->open($filename);
	}
	
	/*
	  метод открывает изображение с именем $filename
	*/
	public function open($filename = ''){
		if (!file_exists($filename)) return false;
		
		if ($this->im != NULL) {
			$this->im->destroy();
			$this->im = NULL;
		}
		
		$this->im = new Imagick($filename);
		return $this->im != NULL;
	}
	
	/*
	  метод изменяет размер изображения на $width%$hienght исли указаны параметор $toFrame то вписывает его в рамку 
	*/
	public function resize($width = 100, $height = 100, $toFrame = false){
		if ($this->im == NULL || $width == 0 || $height == 0) return false;
		
		if ($toFrame){
			$k1 = $width/$this->im->getImageWidth();
		    $k2 = $height/$this->im->getImageHeight();
		    $k  = $k1 > $k2 ? $k2 : $k1;
			$width=intval($this->im->getImageWidth()*$k);
        	$height=intval($this->im->getImageHeight()*$k);
		}
		
		return $this->im->adaptiveResizeImage($width, $height);
	}
	
	/*
	  метод возвращает информацию о изображении
	*/
	public function info($filename = ''){
		$this->open($filename);
		$i['width'] = $this->im->getImageWidth();
		$i['height'] = $this->im->getImageHeight();
		$i['type'] = FileManager::get_ext($filename);
		return $i;
	}
	
	
	/*
	  метод сохраняет изображение в файле с путем $filename
	*/
	public function save($filename = ''){
		
		return $this->im->writeImage($filename) and chmod($filename, Manager::$conf['filesystem.file_chmod']);;
	}
} 
?>