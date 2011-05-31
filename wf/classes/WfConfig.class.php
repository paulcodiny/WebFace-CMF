<?php

class WfConfig {

	/**
	 * @var WfConfig
	 */
	private static $_instance = null;
	
	protected $_data = array();
	protected $_currentDir = '';

	public function __construct(array $params = array()) {
		$this->_currentDir = dirname(__FILE__);
	}
	
	/**
	 * @param array $params
	 * @return WfConfig 
	 */
	public static function getInstance(array $params = array()) {
		if (null == self::$_instance) {
			self::$_instance = new self($params);
		}
		
		return self::$_instance;
	}
	
	public function initSite(array $params = array()) {
		require_once $this->_currentDir . '/../../config.php';
		
		if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
			error_reporting(E_ALL);
		} else {
			error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
		}		
		
		$this->_initAutoloader($params);
		$this->_initTwig($params);
		
		$this->_setClasses($this->class_names, $this->classes);
		
		return WfContext::getInstance()->getSite();
	}
	
	protected function _initAutoloader(array $params = array()) {
		require_once $this->_currentDir . '/WfLoader.class.php';
		$loader = new WfLoader($params['debug']);
		spl_autoload_register(array($loader, 'autoload'));
	}
	
	protected function _initTwig(array $params = array()) {
		require_once $this->_currentDir . '/../libs/Twig/Autoloader.php';
		Twig_Autoloader::register();
		
		//Empty array becuase it changes based on the rendering context
        $loader = new Twig_Loader_Filesystem(array());
        
        $twig = new Twig_Environment($loader, array(
            'cache' => $this->dir_cache . '/twig/',
            'debug' => $params['debug']
        )); 
        
        if ($twig->isDebug()) {
            $twig->setCache(null);
        }
		
		WfContext::getInstance()->setTwig($twig);
	}


	public function set($var, $value) {
		$this->_data[$var] = $value;
		
		return $this;
	}
	
	public function get($var, $default = null) {
		if (!isset($this->_data[$var])) {
			return $default;
		}
		
		return $this->_data[$var];
	}
	
	public function getAll() {
		return $this->_data;
	}
	
	public function __set($var, $value) {
		$this->_data[$var] = $value;
	}
	
	public function __get($var) {
		return $this->get($var);
	}
	
	protected function _setClasses(array $classNames = array(), array $classes = array()) {
		WfContext::getInstance()->setClassNames($classNames);
		
		foreach ($classes as $role => $name) {
			$method = 'set' . ucfirst($role);
			$classParams = array();
			if (is_array($name)) {
				if (!isset($name['class'])) {
					throw new Exception('Config classes ' . $role . ' definition is not correct');
				}
				$className = $name['class'];
				if (isset($name['params'])) {
					$classParams = $name['params'];
				}
			} else {
				$className = $name;
			}
			WfContext::getInstance()->$method(new $className($classParams));
		}
	}
}