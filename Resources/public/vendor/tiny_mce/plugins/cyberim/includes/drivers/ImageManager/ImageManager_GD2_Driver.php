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
 * @file 		/includes/drivers/ImageManager/ImageManager_GD2_Driver.php
 */
 
/*
  Защита от прямой загрузки
*/
defined('ACCESS') or die();

class ImageManager_GD2_Driver extends ImageManager_Driver{	
	
	private $im = NULL;
	
	/*
	  Конструктор класса ImageManager_GD_Driver
	  загружает изображение 
	*/
	public function __construct($filename = ''){
		$this->open($filename);
	}
	
	/*
	  Метод изменяет размер изображения в качестве параметра передается 
	  ширина и высота нового изображения
	*/
	public function resize($width = 100, $height = 100, $toFrame = false){
		if ($this->im == NULL || $width == 0 || $height == 0) return false;
		
		if ($toFrame){
			$k1 = $width/imagesx($this->im);
		    $k2 = $height/imagesy($this->im);
		    $k  = $k1 > $k2 ? $k2 : $k1;
			$width=intval(imagesx($this->im)*$k);
        	$height=intval(imagesy($this->im)*$k);
		}
		 
		$im = $this->imagecreatetransparent($width, $height);
		
		if (function_exists('imagecopyresampled')){
			imagecopyresampled($im, $this->im, 0, 0, 0, 0, $width, $height, imagesx($this->im), imagesy($this->im));	
		} else {
			imagecopyresized($im, $this->im, 0, 0, 0, 0, $width, $height, imagesx($this->im), imagesy($this->im));
		}
		
		$this->im = $im;
		return true;
	}
	
	public function open($filename = ''){
		if (!file_exists($filename)) return false;		
		
		if ($this->im != NULL) {
			imagedestroy($this->im);
			$this->im = NULL;
		}
		
		preg_match('/\.([a-z]{3,})$/i', $filename, $ext);
		
		switch (strtolower($ext[1])){
			case 'png' : $this->im = imagecreatefrompng($filename);
						 break;
						 
			case 'jpeg': 
			case 'jpg' : $this->im = imagecreatefromjpeg($filename);
			 			 break;
								 
			case 'gif' : $this->im = imagecreatefromgif($filename);
			             break; 
		}
		
		return $this->im != NULL;
	}
	
	/*
	  Метод получает инфрмацию о озибражении
	*/  
	public function info($filename = ''){
		$i = getimagesize($filename);		
		return array('width' => $i[0], 'height' => $i[1], 'type' => FileManager::get_ext($filename));
	}
	
	/*
	  Метод сохраняет изображение в файл
	*/
	function save($filename = '', $quality = 80){
	    preg_match('/\.([a-z]{3,})$/i', $filename, $ext);
		
		switch (strtolower($ext[1])){
			case 'png' : imagepng($this->im, $filename);
						 break;
						 
			case 'jpeg': 
			case 'jpg' : imagejpeg($this->im, $filename, $quality);
			 			 break;
						 
			case 'gif' : imagegif($this->im, $filename);
			             break; 
		}
		
		
		return file_exists($filename) && chmod($filename, Manager::$conf['filesystem.file_chmod']);
	}
	
	/*
	  Метод создает прозрачное изображение
	*/
	private function imagecreatetransparent($width = 0, $height = 0){
		$blank  = "iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29m";
        $blank .= "dHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADqSURBVHjaYvz//z/DYAYAAcTEMMgBQAANegcCBNCg";
        $blank .= "dyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAAN";
        $blank .= "egcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQ";
        $blank .= "oHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAA";
        $blank .= "DXoHAgTQoHcgQAANegcCBNCgdyBAgAEAMpcDTTQWJVEAAAAASUVORK5CYII=";
		
		if (function_exists('imagecreatetruecolor')){
			$im = imagecreatetruecolor($width, $height);
		} else {
			$im = imagecreate($width, $height);
		}
		
		$blank = imagecreatefromstring(base64_decode($blank));
		imagealphablending($im, false);
		imagesavealpha($im, true);
		imagecopyresized($im, $blank, 0, 0, 0, 0, $width, $height, imagesx($blank), imagesy($blank));
		imagedestroy($blank);
		return $im;
	}
}

?>