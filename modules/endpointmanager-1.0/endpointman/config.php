<?php
/**
 * Endpoint Manager config File
 *
 * @author Andrew Nagy
 * @license MPL / GPLv2 / LGPL
 * @package Provisioner
 */
// Check for safe mode

if( ini_get('safe_mode') ){
	die(_('Turn Off Safe Mode'));
}

if(file_exists('/tftpboot')) {
	if(!is_writeable('/tftpboot')) {
		die(_('/tftpboot is not writable'));
	}
} else {
	die(_("Please create /tftpboot, even if you won't use it"));
}

include 'includes/functions.inc';

$debug = NULL;

$endpoint = new endpointmanager();

if(!file_exists(PHONE_MODULES_PATH)) {
	mkdir(PHONE_MODULES_PATH, 0764);
}
if(!file_exists(PHONE_MODULES_PATH."temp/")) {
	mkdir(PHONE_MODULES_PATH."temp/", 0764);
}

if(!is_writeable(LOCAL_PATH)) {
	chmod(LOCAL_PATH, 0764);
}

if(!is_writeable(PHONE_MODULES_PATH)) {
	chmod(PHONE_MODULES_PATH, 0764);
}

if($amp_conf['AMPENGINE'] != 'asterisk') {
	die(_("Sorry, Only Asterisk is supported currently"));
}

if (isset($_REQUEST['page'])) {
	$page = $_REQUEST['page'];
} else {
	$page = "";
}

if($global_cfg['debug']) {
	$debug .= "Request Variables: \n".print_r($_REQUEST, TRUE);
}
