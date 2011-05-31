<?php
WfConfig::getInstance()
	->set('admin_routes', array(
		'admin_page_show' => array(
			'url' => '|/admin/pages(/[\w-_]+)[/]?$|',
			'method' => 'pageShow',
			'priority' => 3,
			'param_url' => ':0'
		),
		'admin_index' => array(
			'url' => '|/admin/*|',
			'method' => 'index',
			'priority' => 2
		),
		'admin_widget' => array(
			'url' => '|/admin/pages(/[\w-_]+)/widgets/(\d+)$|',
			'pageClass' => 'WfPageAdminWidget',
			'method' => 'widgetUpdate',
			'priority' => 4,
			'param_url' => ':0',
			'param_widget' => ':1',
		),
	))
;