<?php

/**
 * Description of WfContext
 *
 * @author павел
 */
class WfContext {

	/**
	 * @var WfContext
	 */
	private static $_instance = null;

	protected $_classNames = array();
	
	/**
	 * @var WfConfig
	 */
	protected $_config = null;
	
	/**
	 * @var WfRoute
	 */
	protected $_route = null;
	
	/**
	 * @var WfRouting
	 */
	protected $_routing = null;
	
	/**
	 * @var WfRequest
	 */
	protected $_request = null;
	
	/**
	 * @var WfResponse
	 */
	protected $_response = null;
	
	/**
	 * @var Twig_Environment
	 */
	protected $_twig = null;
	
	/**
	 * @var WfMysql
	 */
	protected $_mysql = null;
	
	/**
	 *
	 * @var WfUtils
	 */
	protected $_utils = null;

	public function __construct() {
		
	}
	
	/**
	 * @return WfContext
	 */
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	

	/**
	 * @param WfConfig $config
	 */
	public function setConfig(WfConfig $config) {
		$this->_config = $config;

		return $this;
	}

	/**
	 * @return WfConfig
	 */
	public function getConfig() {
		return $this->_config;
	}
	
	/**
	 * @param WfSite $site
	 */
	public function setSite(WfSite $site) {
		$this->_site = $site;

		return $this;
	}

	/**
	 * @return WfSite
	 */
	public function getSite() {
		return $this->_site;
	}

	/**
	 * @param Request $request
	 */
	public function setRequest(WfRequest $request) {
		$this->_request = $request;

		return $this;
	}

	/**
	 * @return WfRequest
	 */
	public function getRequest() {
		return $this->_request;
	}

	/**
	 * @param WfResponse $response
	 */
	public function setResponse(WfResponse $response) {
		$this->_response = $response;

		return $this;
	}

	/**
	 * @return Response
	 */
	public function getResponse() {
		return $this->_response;
	}
	
	/**
	 * @param Twig_Environment $twig
	 */
	public function setTwig(Twig_Environment $twig) {
		$this->_twig = $twig;

		return $this;
	}

	/**
	 * @return Twig_Environment
	 */
	public function getTwig() {
		return $this->_twig;
	}

	/**
	 * @param WfRoute $route
	 */
	public function setCurrentRoute(WfRoute $route) {
		$this->_route = $route;

		return $this;
	}

	/**
	 * @return WfRoute
	 */
	public function getCurrentRoute() {
		return $this->_route;
	}

	/**
	 * @param WfRouting $routing
	 */
	public function setRouting(WfRouting $routing) {
		$this->_routing = $routing;

		return $this;
	}

	/**
	 * @return WfRouting
	 */
	public function getRouting() {
		return $this->_routing;
	}

	/**
	 * @param WfMysql $mysql
	 */
	public function setMysql(WfMysql $mysql) {
		$this->_mysql = $mysql;

		return $this;
	}

	/**
	 * @return WfMysql
	 */
	public function getMysql() {
		return $this->_mysql;
	}

	/**
	 * @param WfUtils $utils
	 */
	public function setUtils(WfUtils $utils) {
		$this->_utils = $utils;

		return $this;
	}

	/**
	 * @return WfUtils
	 */
	public function getUtils() {
		return $this->_utils;
	}
	
	public function setClassNames(array $classNames = array()) {
		foreach ($classNames as $role => $name) {
			$this->setClassName($role, $name);
		}
		
		return $this;
	}
	
	public function setClassName($role, $name) {
		$this->_classNames[$role] = $name;
		
		return $this;
	}
	
	public function getClassName($role) {
		if (!isset($this->_classNames[$role])) {
			throw new Exception('Classname for ' . $role . ' not exists');
		}
		
		return $this->_classNames[$role];
	}

}