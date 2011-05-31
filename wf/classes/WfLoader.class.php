<?php
/**
 * Description of WfLoader
 *
 * @author павел
 */
class WfLoader {
	
	protected $_classTree = array();
	protected $_classTreeIsBuild = false;
	
	protected $_debug = false;
	
	public function __construct($debug) {
		$this->_debug = $debug;
	}
	
	public function autoload($className) {
		if (0 !== strpos($className, 'Wf')) {
            return;
        }
		
		$classTree = $this->getClassTree();
		if (!isset($classTree[$className])) {
			throw new Exception('Class ' . $className .
					' not exists or maybe clear cache '
					. WfConfig::getInstance()->dir_cache);
		}
		
		require_once $classTree[$className];
	}
	
	public function getClassTree() {
		if (!$this->_classTreeIsBuild) {
			$this->_buildClassTree();
		}
		
		return $this->_classTree;
	}
	
	protected function _buildClassTree() {
		$classTreeFilePath = WfConfig::getInstance()->dir_cache . 'class_tree.txt';
		if (!$this->_debug && is_file($classTreeFilePath)) {
			$this->_classTree = json_decode(file_get_contents($classTreeFilePath), true);
			
			return $this->_classTree;
		}
		
		$rootDir = WfConfig::getInstance()->dir_root;
		foreach (scandir($rootDir) as $fileResource) {
			if ($fileResource == '.' || $fileResource == '..') {
				continue;
			}
			
			$this->_scan($fileResource, $rootDir);
		}
		
		file_put_contents($classTreeFilePath, json_encode($this->_classTree));
		
		$this->_classTreeIsBuild = true;
		
		return $this->_classTree;
	}
	
	protected function _scan($dirName, $dirPath) {
		$path = $dirPath . '/' . $dirName;
		if (is_file($path)) {
			$this->_checkFile($dirName, $dirPath);
		} elseif (is_dir($path)) {
			foreach (scandir($path) as $fileResource) {
				if ($fileResource == '.' || $fileResource == '..') {
					continue;
				}
				
				$this->_scan($fileResource, $dirPath . '/' . $dirName);
			}
		}
		
	}
	
	protected function _checkFile($fileName, $fileDir) {
		if (false === ($pos = strpos($fileName, '.class.php'))) {
			return false;
		}
		
		$className = substr($fileName, 0, $pos);
		if (isset($this->_classTree[$className])) {
			// dont know why but Exception here trigger Fatal Error
			exit('Class ' . $className . ' appears twice.');
		}
		$this->_classTree[$className] = $fileDir . '/' . $fileName;
		
		return true;
	}
}