<?php

class WfRouting {

	protected $_url = null;
	protected $_routeMap = null;
	
	protected $_dispatchTimes = 0;
	protected $_dispatchedUrl = '';

	public function __construct($params) {
		$this->_routeMap = $params['routeMap'];
	}

	public function dispatch($url) {
		$this->_dispatchTimes++;
		
//		if ($this->_dispatchTimes > 1) {
//			// cut already dispatched url part
//			$url = str_replace($this->_dispatchedUrl, '', $url);
//		}
		$this->_url = $url;

		return $this->_routeUrls();
	}
	
	public function addUrls(array $urls = array()) {
		foreach ($urls as $name => $urlDefinition) {
			$this->_routeMap[$name] = $urlDefinition;
		}
		
		return $this;
	}

	protected function _routeUrls() {
		$foundContent = null;
		$foundByPattern = null;
		foreach ($this->_routeMap as $name => $content) {
			$patternUrl = $content['url'];
			if (empty($content['url'])) {
				return $content;
			}

			if (!isset($content['priority'])) {
				$content['priority'] = 1;
			}

			if (preg_match($patternUrl, $this->_url)) {
				if ($foundContent === null || $foundContent['priority'] < $content['priority']) {
					$foundContent = $content;
					$foundByPattern = $patternUrl;
				}
			}
		}

		if ($foundContent) {
			$this->_dispatchedUrl .= $this->_url;
			
			$this->_urlContent = $foundContent;
			preg_replace_callback($foundByPattern, array($this, '_setUrlVariables'), $this->_url);

			$routeClass = (isset($foundContent['class']) ? $foundContent['class'] : WfContext::getInstance()->getClassName('default_route'));

			return new $routeClass($name, $this->_urlContent);
		}

		return null;
	}

	protected function _setUrlVariables($matches) {
		if (isset($this->_urlContent)) {
			foreach ($this->_urlContent as $name => $value) {
				// if this is variable from matches
				if ($value[0] == ":") {
					$this->_urlContent[$name] = $matches[(int) substr($value, 1) + 1];
				}
			}
		}
	}

}
