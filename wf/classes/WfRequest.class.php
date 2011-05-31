<?php

/**
 * Description of WfRequest
 *
 * @author павел
 */
class WfRequest implements ArrayAccess {

	protected $_data = array();

	public function __construct() {
		$this->_data = array_merge($_GET, $_POST, $_COOKIE);
		if (isset($_SESSION) && is_array($_SESSION)) {
			$this->_data = array_merge($this->_data, $_SESSION);
		}
	}

	public function isMethodPost() {
		return ($_SERVER['REQUEST_METHOD'] == 'POST');
	}

	public function setParams(array $params = array()) {
		foreach ($params as $paramName => $paramValue) {
			$this->setParam($paramName, $paramValue);
		}
		
		return $this;
	}
	
	public function setParam($param, $value) {
		$this->_data[$param] = $value;

		return $this;
	}

	public function getParam($param) {
		return $this->_data[$param];
	}

	public function getParams() {
		return $this->_data;
	}

	// ArrayAccess implementation

	public function offsetExists($offset) {
		return isset($this->_data[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}

	public function offsetSet($offset, $value) {
		$this->_data[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->_data[$offset]);
	}

}