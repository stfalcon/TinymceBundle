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
defined('ACCESS') or die('Restricted access');

class SessionManager_Joomla_1_5_tinymce_Driver implements SessionManager_Driver{
	public function authorisation(){
		@session_destroy();
		$old_cwd = getcwd();
		
		chdir($old_cwd . "../../../../../../../../administrator");		
				
		define('_JEXEC', 1);
		define('JPATH_BASE', getcwd());
			
		require_once(JPATH_BASE .DS.'includes'.DS.'defines.php');
		require_once(JPATH_BASE .DS.'includes'.DS.'framework.php');
			
		$mainframe =& JFactory::getApplication('administrator');
		$mainframe->initialise(array(
			'language' => $mainframe->getUserState( "application.lang", 'lang' )
		));
			
		$user = $mainframe->getUser();
			
		chdir($old_cwd);
			
		// пользователь не авторизован
		if ($user->id == 0)
			return false;	
			
		// заменяем все значения {#user#} в файле конфигурации на имя пользователя
		foreach(Manager::$conf as $key => $value){
			if (!is_string($value) || empty($value))
				continue;
				
			Manager::$conf[$key] = str_replace('{#user#}', $user->username, $value);
		}	
			
		//проверяем разрешен ли доступ этому пользователю	
		return preg_match(Manager::$conf['session.valid_users'], $user->username);
	}	
}
?>