<?php
/**
 * Description of WfPageDefault
 *
 * @author павел
 */
class WfPageDefault extends WfPage {
	public function run() {
		$mysql = WfContext::getInstance()->getMysql();
		$widgets = $mysql
			->query('SELECT `pw`.*, `w`.*
			FROM `wf_pages_widgets` `pw`
			LEFT JOIN `wf_widgets` `w` ON `pw`.`widget_id` = `w`.`id`
			ORDER BY `pw`.`order`')
			->fetch();
		$tplWidgets = array();
		
		foreach ($widgets as $widget) {
			$widgetClassName = WfUtils::makeClassName($widget['name'], 'WfWidget');
			$widgetClass = new $widgetClassName($widget['options']);
			$tplWidgets[] = $widgetClass;
		}
		
		$cfg = WfConfig::getInstance();
		
		$twig = WfContext::getInstance()->getTwig();
		$twig->getLoader()->setPaths(array($cfg->dir_root . '/site/templates', $cfg->dir_root . '/wf/pages/default/templates'));
		
		echo $twig->render('wf_default_index.html', array(
			'widgets' => $tplWidgets,
			'base_layout' => 'site_layout.html'
		));
	}
	
	public function index() {
		
	}
}