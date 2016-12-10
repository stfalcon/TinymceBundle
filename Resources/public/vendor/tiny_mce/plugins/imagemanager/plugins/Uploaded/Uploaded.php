<?php
/**
 * Uploaded.php
 *
 * @package UploadedCookiePlugin
 * @author Moxiecode
 * @copyright Copyright © 2010, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class handles MCImageManager UploadedCookiePlugin stuff.
 *
 * @package UploadedCookiePlugin
 */
class Moxiecode_UploadedPlugin extends Moxiecode_ManagerPlugin {
	/**#@+
	 * @access public
	 */
	var $_maxupload = 10;

	/**
	 * ..
	 */
	function Moxiecode_UploadedPlugin() {
	}

	function onInit(&$man) {
		$man->registerFileSystem('uploaded', 'Moxiecode_UploadedFile');

		return true;
	}

	/**
	 * Gets called after a file action was perforem for example after a rename or copy.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param int $action File action constant for example DELETE_ACTION.
	 * @param BaseFile $file1 File object 1 for example from in a copy operation.
	 * @param BaseFile $file2 File object 2 for example to in a copy operation. Might be null in for example a delete.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onFileAction(&$man, $action, $file1, $file2) {
		if ($action == ADD_ACTION)
			$this->_uploadedFile(&$man, $file1);
		
		return true;
	}

	function _uploadedFile(&$man, &$file) {
		$path = $file->getAbsolutePath();
		$type = $man->getType();
		$maxupload = isset($config["uploaded.max"]) ? $config["uploaded.max"] : $this->_maxupload;
		$cookievalue = $this->getCookieData($type);

		$patharray = array();
		$patharray = split(",", $cookievalue);
		if (count($patharray) > 0) {

			for($i=0;$i<count($patharray);$i++) {
				if ($patharray[$i] == $path) {
					array_splice($patharray, $i, 1);
					break;
				}
			}

			array_unshift($patharray, $path);

			if (count($patharray) > $maxupload)
				array_pop($patharray);

		} else
			$patharray[] = $path;

		$cookievalue = implode(",", $patharray);

		$this->setCookieData($type, $cookievalue);
		return true;
	}

	function getCookieData($type) {
		if (isset($_COOKIE["MCManagerUploadedCookie_". $type]))
			return $_COOKIE["MCManagerUploadedCookie_". $type];
		else
			return "";
	}

	function setCookieData($type, $val) {
		setcookie("MCManagerUploadedCookie_". $type, $val, time()+(3600*24*30), "/"); // 30 days
	}

	function onClearUploaded(&$man) {
		setcookie ("MCManagerUploadedCookie_". $man->getType(), "", time() - 3600, "/"); // 1 hour ago
		return true;
	}
}

class Moxiecode_UploadedFile extends Moxiecode_BaseFileImpl {
	function Moxiecode_UploadedFile(&$manager, $absolute_path, $child_name = "", $type = MC_IS_FILE) {
		$absolute_path = str_replace('uploaded://', '', $absolute_path);
		Moxiecode_BaseFileImpl::Moxiecode_BaseFileImpl($manager, $absolute_path, $child_name, $type);
	}

	function canRead() {
		return true;
	}

	function canWrite() {
		return false;
	}

	function exists() {
		return true;
	}

	function isDirectory() {
		return true;
	}

	function isFile() {
		return false;
	}

	function getParent() {
		return null;
	}

	function &getParentFile() {
		return null;
	}

	/**
	 * Returns an array of File instances.
	 *
	 * @return Array array of File instances.
	 */
	function &listFiles() {
		$files = $this->listFilesFiltered(new Moxiecode_DummyFileFilter());
		return $files;
	}

	/**
	 * Returns an array of MCE_File instances based on the specified filter instance.
	 *
	 * @param MCE_FileFilter &$filter MCE_FileFilter instance to filter files by.
	 * @return Array array of MCE_File instances based on the specified filter instance.
	 */
	function &listFilesFiltered(&$filter) {
		
		$files = array();
		$man = $this->_manager;

		$type = $man->getType();
		$cookievalue = $this->_getCookieData($type);

		$patharray = array();
		if (IndexOf($cookievalue, ",") != -1)
			$patharray = split(",", $cookievalue);
		else if ($cookievalue != "")
			$patharray[] = $cookievalue;

		foreach ($patharray as $path) {
		
			if (!$man->verifyPath($path)) 
				continue;

			$file = $man->getFile($path);

			if (!$file->exists()) {
				$this->_removeUploaded($man, $path);
				continue;
			}

			if ($man->verifyFile($file) < 0)
				continue;

			if ($filter->accept($file) == BASIC_FILEFILTER_ACCEPTED)
				$files[] = $file;
				
		}

		return $files;
	}

	function _getCookieData($type) {
		if (isset($_COOKIE["MCManagerUploadedCookie_". $type]))
			return $_COOKIE["MCManagerUploadedCookie_". $type];
		else
			return "";
	}

	function _removeUploaded(&$man, $path=array()) {
		$type = $man->getType();

		$cookievalue = $this->_getCookieData($type);

		$patharray = array();
		$patharray = split(",", $cookievalue);

		$break = false;
		if (count($patharray) > 0) {
			for($i=0;$i<count($patharray);$i++) {
				if (is_array($path)) {
					if (in_array($patharray[$i], $path))
						$break = true;

				} else {
					if ($patharray[$i] == $path)
						$break = true;
				}
				
				if ($break) {
					array_splice($patharray, $i, 1);
					break;
				}
			}
		}

		$cookievalue = implode(",", $patharray);

		$this->_setCookieData($type, $cookievalue);
		return true;
	}

	function _setCookieData($type, $val) {
		setcookie("MCManagerUploadedCookie_". $type, $val, time()+(3600*24*30), "/"); // 30 days
	}
}

// Add plugin to MCManager
$man->registerPlugin("uploaded", new Moxiecode_UploadedPlugin());
?>