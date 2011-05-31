<?php
/**
 * Description of WfPage
 *
 * @author павел
 */
abstract class WfPage {
	protected $_name = null;
	
	protected $_config = null;
	protected $_twig = null;
	protected $_mysql = null;
	
	/**
	 * @param array $options Array of options
	 */
	public function __construct(array $options = array()) {
		$this->_config = WfConfig::getInstance();
		$this->_mysql  = WfContext::getInstance()->getMysql();
		$this->_twig   = WfContext::getInstance()->getTwig();
	}
	
	public function methodBefore() {
		if (null === $this->_name) {
			throw new Exception('Page must have a name.');
		}
		
		$pageDir = WfConfig::getInstance()->dir_wf_pages . '/' . $this->_name;
		
		if (is_file($pageDir . '/config.php')) {
			require_once $pageDir . '/config.php';
		}
	}
	
	public function methodAfter() {
		
	}
	
	public function run() {
		// get routing for current page
		// dispatch it
	}
	
	public function getPage($url, array $options = array()) {
		$q = $this->_mysql
			->table('wf_pages', 'p')
			->select()
			//->select('column AS columnAlias')
			->where(array('p.url' => $url))
			//->where('p.url LIKE ? AND p.title = ?', array($request['param_url'], 'test'))
			->orderBy('p.order');
		
		if ($options['withWidgets']) {
			$q = $q->leftJoin('wf_pages_widgets', array('p.id' => 'page_widgets.page_id'), 'page_widgets');
		}
		
		return $q
			->execute()
			->fetchRecord(WfMysql::HYDRATION_NODE);;		
	}
}