<?php

class WfResponse {

    protected $_content = '';
	
	protected $_javascripts = array();
	protected $_styles = array();
    
    public function __construct() {
        
    }

    public function redirect($url = null, $params = array()) {
        header("HTTP/1.0 302 Found");
        header('Location: ' . ($url ? $url : $_SERVER["REQUEST_URI"]));
        exit();
    }

    public function redirectBack() {
        $this->redirect($_SERVER["HTTP_REFERER"]);
    }

}