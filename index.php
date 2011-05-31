<?php
require_once(dirname(__FILE__).'/wf/classes/WfConfig.class.php');

$site = WfConfig::getInstance()->initSite(array('debug' => true));
$site->run();

exit();