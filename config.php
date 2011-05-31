<?php
// $this directs to WfConfig class
$this
	->set('db_host', 'localhost')
	->set('db_name', 'web2face')
	->set('db_user', 'root')
	->set('db_pass', 'freddyduck2')
	
	->set('gzip', true)
	
	->set('cookie_expire', 3600 * 24 * 30)
	->set('cookie_domain', '~')
	
	->set('developer_name', 'paulcodiny')
	->set('developer_mail', 'paulcodiny@gmail.com')
	
	->set('dir_root', dirname(__FILE__))
	->set('dir_cache', 'wf/cache/')
		
	->set('dir_site_assets', 'site/assets/')
	->set('dir_site_pages', 'site/pages/')
		
	->set('dir_wf_assets', 'wf/assets/')
	->set('dir_wf_pages', 'wf/pages/')
	
	
		
	// default classes
	->set('class_names', array(
		'default_route' => 'WfRoute'
	))	
		
	->set('classes', array(
		'site' => 'WfSite',
		
		'request' => 'WfRequest',
		'response' => 'WfResponse',
		
		'mysql' => 'WfMysql',
		
		'routing' => array(
			'class' => 'WfRouting',
			'params' => array(
				'routeMap' => array(
					'about' => array(
						'url' => '|/about.html$|',
						'page' => 'default'
					),
					'admin' => array(
						'url' => '|/admin|',
						'page' => 'admin'
					),
					'homepage' => array(
						'url' => '|/|',
						'page' => 'default'
					),
				)
			)
		)
	))
;