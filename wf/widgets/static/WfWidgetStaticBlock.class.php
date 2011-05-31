<?php

class WfWidgetStaticBlock extends WfWidget {
	protected $_options = array();
	protected $_table = 'wf_widget_static';
	protected $_block = null;


	public function __construct($options) {
		$this->_options = json_decode($options, true);
	}
	
	public function render() {
		$mysql = WfMysql::getInstance();
		$this->_block = $mysql
			->query("SELECT * FROM {$this->_table} WHERE id = {$this->_options['id']}")
			->fetchRecord();
		
		return $this->_block['content'];
	}
}