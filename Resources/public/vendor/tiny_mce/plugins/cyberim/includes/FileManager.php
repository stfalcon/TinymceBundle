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
 * @file 		/includes/FileManager.php
 */
 
/*
  Защита от прямой загрузки
*/
defined('ACCESS') or die();

class FileManager {
    private static $instance;
	
	/*
	  Конструктор класса FileManager
	*/
	public function __construct(){
		FileManager::$instance = & $this;	
	}
	
	/*
	  Метод возвращает ссылку на обьект или создает его
	*/
	public static function & instance(){
		empty(FileManager::$instance) and new FileManager;
		return FileManager::$instance;
	}
	
	/*
	  Переводим к кодировке файловой системы  
	*/
	public static function convertToFileSystem($string = ''){
		return FileManager::__convertToCharSet($string, Manager::$conf['general.char_set'], Manager::$conf['filesystem.char_set']);
    }
	
	/*
	  Переводи кодировку к кодировке файловой системы
	*/
	public static function convertToGeniral($string = ''){
		return FileManager::__convertToCharSet($string, Manager::$conf['filesystem.char_set'], Manager::$conf['general.char_set']);
	}
	
	/*
	  Переводим кодировку 
	*/
	public static function __convertToCharSet($string = '', $in_char_set = '', $out_char_set = ''){				
		if (empty($in_char_set) || empty($out_char_set) || strcasecmp($in_char_set, $out_char_set) == 0) {
            return $string;
        }	
		
		$converted = iconv($in_char_set, $out_char_set, $string);	
			
		if ($converted === false) {
           	return $string;
        }

        return $converted;
    }
	
	
	/*
	  Метод получает список файлов и папок
	*/
	public static function get_path_list($path = '', $f = false, $d = false){
		$list = array();
						
		if (($dp = opendir(FileManager::convertToFileSystem(Manager::$conf['filesystem.files_path'].$path))) !== false){
			while (($el = readdir($dp)) !== false){
				if ($el != '.' && $el != '..' && !preg_match(Manager::$conf['filesystem.exclude_directory_pattern'], $el)){
					/*
					  разделение директории и файла
					*/
					$el = FileManager::convertToGeniral($el);					
					$obj = array ('name' => $el, 'path' => $path.$el.'/');
					if (is_file(FileManager::convertToFileSystem(Manager::$conf['filesystem.files_path'].$path.$el)) && $f) {
					    /*
					  	  проверка расширеня файла
						*/
						preg_match('/\.([a-z]{3,})$/i', $el, $ext);
						if (!in_array(strtolower($ext[1]), explode('|', Manager::$conf['filesystem.allowed_extensions']))) continue;
						$list[] = $obj;
					}
					if (is_dir(FileManager::convertToFileSystem(Manager::$conf['filesystem.files_path'].$obj['path'])) && $d) $list[] = $obj;
				}
			}
			closedir($dp);
		} 
		
		/*
		  сортировка списка
		*/
		Manager::$conf['filesystem.sort'] ? sort($list) : rsort($list);	
		
		return $list;
	}
	
	/*
	  Метод создает католог
	*/
    public static function create_dir($path = ''){
   		if ($path == '') return false;		
		return mkdir(FileManager::convertToFileSystem($path), Manager::$conf['filesystem.directory_chmod']);
    }
	
	public static function path_encode($path = ''){
		if ($path == '') return $path;
			
		preg_match('/^(http.{3,4}[a-zA-z.]+\/).*/i', $path, $url);
		$path = preg_replace('/^http.{3,4}[a-zA-z.]+\//i', '', $path);	
			
		$elements = explode('/', $path);
		
		$path = count($url) > 1 ? $url[1] : '';
		foreach ($elements as $element){
			$path .=rawurlencode($element) . '/';
		}
		return substr($path, 0, strlen($path) - 1);
	}
   
    /*
	  Метод удаляет каталог
	*/
	public static function delete_dir($path = '' , $encode = true){
   		$done = false;
		
		if ($path == '') return true;
		
		if (($dp = opendir($path)) !== false){
			while (($el = readdir($dp)) !== false){
				if ($el == '.' || $el == '..') continue;
				
				$obj = $path.DS.$el;
				
				if (is_file($obj)){
					FileManager::delete_file($obj);
					continue;	
				}
				
				if (is_dir($obj)){
					FileManager::delete_dir($obj);
				}
			} 
			closedir($dp);
			rmdir($path);
			$done = true;
		}
		
		return $done;
    }
	
	/*
	  Метод очищает путь от лишних слешев и переводит его в верный формат фаловой системы
	*/
	public static function clear_path($path = ''){
		return preg_replace('/\\\+|\/+/', DS, $path);
	} 
	
	/*
	  Метод возвращает расширения файла
	*/
	public static function get_ext($filename = ''){
		preg_match('/\.([a-z]{3,})$/i', $filename, $ext);
		return $ext[1];
	}
	
	
	/*
	  Метод удаляет файл
	*/
	public static function delete_file($filename = ''){
		return $filename != '' && unlink($filename);
	}
	
	/*
	 Метод переименновывает фил или дерикторию
	*/
	public static function rename($old = '', $new = ''){
		return $old != '' && $new != '' && rename(FileManager::convertToFileSystem($old), FileManager::convertToFileSystem($new));
	}
}
?>