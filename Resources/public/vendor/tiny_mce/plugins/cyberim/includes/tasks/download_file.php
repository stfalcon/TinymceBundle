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
 * @file 		/includes/tasks/download_file.php
 */
 
	/*
  		Защита от прямой загрузки
	*/
	defined('ACCESS') or die();
	
	$file = FileManager::clear_path(
		Manager::$conf['filesystem.files_abs_path'].DS.$_REQUEST['file']
	);
	
	if (file_exists(FileManager::convertToFileSystem($file))){
		Manager::$conf['stream.mimes']['use_gzip']=false;
		//заружаем файл
		$data = file_get_contents(FileManager::convertToFileSystem($file));
		//получаем расширение файла	
		$ext = strtolower(FileManager::get_ext($file));
		
		// Устанавливаем mime поумолчанию если не можем найти тип этого фала
		if (!isset(Manager::$conf['stream.mimes'][$ext])){
			$mime = 'application/octet-stream';
		}
		else{
			$mime = (is_array(Manager::$conf['stream.mimes'][$ext])) ? 
					Manager::$conf['stream.mimes'][$ext][0] : 
					Manager::$conf['stream.mimes'][$ext];
		}
		
		// Генирируем заголовки сервера
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")){
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($data));
		}else{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($data));
		}
		
		echo $data;
	}
?>