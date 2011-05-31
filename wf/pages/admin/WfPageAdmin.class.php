<?php
/**
 * Description of WfPageAdmin
 *
 * @author павел
 */
class WfPageAdmin extends WfPage {
	
	protected $_name = 'admin';
	
	public function run() {
		$routing = WfContext::getInstance()->getRouting();
		$routes = WfConfig::getInstance()->admin_routes;
		foreach ($routes as $routeName => $routeParams) {
			$routes[$routeName]['page'] = $this->_name;
		}
		$routing->addUrls($routes);
		if ($currentRoute = $routing->dispatch($_SERVER["REQUEST_URI"])) {
			$this->_twig->getLoader()->setPaths(array(
				$this->_config->dir_root . '/wf/templates',
				$this->_config->dir_root . '/wf/pages/' . $this->_name . '/templates'
			));
			
			WfContext::getInstance()->setCurrentRoute($currentRoute);
			$request = WfContext::getInstance()->getRequest();
			$request->setParams($currentRoute->getParams());
			
			if ($request['pageClass']) {
				$pageClass = new $request['pageClass'];
			} else {
				$pageClass = $this;
			}
			
			$pageClass->$request['method']($request);
		}
	}
	
	public function index($request) {
		$widgetNavigation = new WfWidgetNavigation(array('context' => 'admin'));
		
		echo $this->_twig->render('wf_admin_index.html', array(
			'widget_navigation' => $widgetNavigation->render()
		));
	}
	
	public function pageShow($request) {
		$page = $this->getPage($request['param_url'], array('withWidgets' => true));
		
		echo $this->_twig->render('wf_admin_page_show.html', array(
			'page' => $page
		));
	}
}