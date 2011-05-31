<?php
class WfSite {
	
	public function run() {
		$routing = WfContext::getInstance()->getRouting();
		if ($currentRoute = $routing->dispatch($_SERVER["REQUEST_URI"])) {
			WfContext::getInstance()->setCurrentRoute($currentRoute);
			$request = WfContext::getInstance()->getRequest();
			$request->setParams($currentRoute->getParams());
			
			$this->execute($request);
		} else {
			throw new Exception('404 page');
		}
	}
	
	public function execute(WfRequest $request) {
		$pageClass = WfUtils::makeClassName($request['page'], 'WfPage');
		$page = new $pageClass();
		$page->methodBefore();
		$page->run();
		$page->methodAfter();
	}
}