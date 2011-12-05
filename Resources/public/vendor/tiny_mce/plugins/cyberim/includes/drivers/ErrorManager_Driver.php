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
 * @file 		/includes/drivers/ErrorManager_Driver.php
 */
 
/*
  Защита от прямой загрузки
*/
defined('ACCESS') or die();

interface ErrorManager_Driver{
	public function errorHandler($error_num, $error_var, $error_file, $error_line);
}

?>