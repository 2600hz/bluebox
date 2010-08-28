<?php 
require_once $amp_conf['AMPWEBROOT'].'/admin/modules/endpointman/config.php';

switch ($page) {
	case 'advanced':
		include LOCAL_PATH.'includes/advanced.inc';	
		break;
	
	case 'template_manager':
		include LOCAL_PATH.'includes/template_manager.inc';	
		break;

	case 'devices_manager';
		include LOCAL_PATH.'includes/devices_manager.inc';
		break;

	case 'brand_model_manager':
		include LOCAL_PATH.'includes/brand_model_manager.inc';
		break;
		
	case 'installer':
	  include LOCAL_PATH.'install.php';
	  break;

	default:
		include LOCAL_PATH.'includes/devices_manager.inc';
}