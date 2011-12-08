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
 * @file 		/includes/drivers/SeesionManager/SessionManager_Sample_Driver.php
 */
 
/*
  Защита от прямой загрузки
*/
defined('ACCESS') or die();

class SessionManager_Sample_Driver implements SessionManager_Driver{
	public function authorisation(){
		return true;
	}	
}
?>