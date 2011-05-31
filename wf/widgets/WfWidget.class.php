<?php

abstract class WfWidget {
	
	protected $_name = '';
	
	/**
	 * @var WfConfig
	 */
	protected $_config = null;
	
	/**
	 * @var Twig_Environment
	 */
	protected $_twig = null;
	
	/**
	 * @var WfMysql
	 */
	protected $_mysql = null;
	
	/**
	 * @param array $options Array of options
	 */
	public function __construct($options, $additionalTemplatePaths = array()) {
		$this->_config = WfConfig::getInstance();
		$this->_mysql  = WfContext::getInstance()->getMysql();
		$this->_twig   = WfContext::getInstance()->getTwig();
		
		foreach ($additionalTemplatePaths as &$path) {
			// relative path, absolutize it
			if ($path[0] != '/') {
				
			}
		} 
		$this->addWidgetTemplatePaths($additionalTemplatePaths);
	}
	
	public function __toString() {
		return $this->render();
	}
	
	public function render() {
	
	}
	
	public function install() {
	
	}
	
	public function addWidgetTemplatePaths(array $additionalPaths = array()) {
		$loader = $this->_twig->getLoader();
		// widget base templates dir
		$additionalPaths[] = $this->_config->dir_root . '/wf/widgets/'.$this->_name.'/templates';
		foreach ($additionalPaths as $widgetTemplateDir) {
			if (is_dir($widgetTemplateDir)) {
				$paths = $loader->getPaths();
				$loader->setPaths(array_merge($paths, $additionalPaths));
			}
		}
	}
	
}