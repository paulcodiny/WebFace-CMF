<?php

class WfRoute {

	protected $_name = null;
	protected $_params = array();

	public function __construct($name, $urlDefinition) {
		$this->_name = $name;

		if (!isset($urlDefinition['page'])) {
			throw new Exception('Url ' . $name . ' must contain page param');
		}

		$this->_params = $urlDefinition;
	}

	public function getName() {
		return $this->_name;
	}

	public function getParams() {
		return $this->_params;
	}

}