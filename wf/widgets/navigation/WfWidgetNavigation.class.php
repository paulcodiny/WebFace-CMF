<?php
/**
 * Description of WfWidgetNavigation
 *
 * @author павел
 */
class WfWidgetNavigation extends WfWidget {
	protected $_name = 'navigation';
	protected $_options = array();


	public function __construct($options) {
		parent::__construct($options);
		
		$this->_options = $options;
	}
	
	public function render() {
		$navigation = $this->_mysql
			->query("SELECT * FROM `wf_pages` ORDER BY `order`")
			->fetch();
		
		return $this->_twig->render('wf_navigation_' . $this->_options['context'] . '.html', array(
			'wf_navigation' => $navigation
		));
	}
}