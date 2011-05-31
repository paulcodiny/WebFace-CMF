<?php
/**
 * Description of MySite
 *
 * @author павел
 */
class MySite extends WfSite {
	public function run() {
		echo '<h1>Hello from MySite</h1>';
		
		$mysql = WfContext::getInstance()->getMysql();
		$widgets = $mysql
			->query('SELECT `pw`.*, `w`.*
			FROM `wf_pages_widgets` `pw`
			LEFT JOIN `wf_widgets` `w` ON `pw`.`widget_id` = `w`.`id`
			ORDER BY `pw`.`order`')
			->fetch();
		print_r($widgets);
		echo '<br />';
		foreach ($widgets as $widget) {
			$widgetClassName = WfUtils::makeClassName($widget['name'], 'WfWidget');
			$widgetClass = new $widgetClassName($widget['options']);
			echo $widgetClass;
		}
	}
}