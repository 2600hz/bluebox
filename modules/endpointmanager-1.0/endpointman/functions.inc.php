<?PHP
function endpointman_get_config($engine) {
  global $db;
  global $ext; 
  global $core_conf;

  $sql = 'SELECT value FROM `admin` WHERE `variable` LIKE CONVERT(_utf8 \'version\' USING latin1) COLLATE latin1_swedish_ci';
  $version = $db->getOne($sql);

  switch($engine) {
    case "asterisk":
    if (isset($core_conf) && is_a($core_conf, "core_conf") && ($version >= "2.8.0")) {
        $core_conf->addSipNotify('polycom-check-cfg',array('Event' => 'check-sync','Content-Length' => '0'));
        $core_conf->addSipNotify('polycom-reboot',array('Event' => 'check-sync','Content-Length' => '0'));
        $core_conf->addSipNotify('sipura-check-cfg',array('Event' => 'resync','Content-Length' => '0'));
        $core_conf->addSipNotify('grandstream-check-cfg',array('Event' => 'sys-control'));
        $core_conf->addSipNotify('cisco-check-cfg',array('Event' => 'check-sync','Content-Length' => '0'));
        $core_conf->addSipNotify('reboot-snom',array('Event' => 'reboot','Content-Length' => '0'));
        $core_conf->addSipNotify('aastra-check-cfg',array('Event' => 'check-sync','Content-Length' => '0'));
        $core_conf->addSipNotify('linksys-cold-restart',array('Event' => 'reboot_now','Content-Length' => '0'));
        $core_conf->addSipNotify('linksys-warm-restart',array('Event' => 'restart_now','Content-Length' => '0'));
        $core_conf->addSipNotify('spa-reboot',array('Event' => 'reboot','Content-Length' => '0'));
      }
    break;
  }
}
function endpointman_configpageinit($pagename) {
	global $currentcomponent;

        endpointman_applyhooks();
}
function endpointman_applyhooks() {
	global $currentcomponent;

	// Add the 'process' function - this gets called when the page is loaded, to hook into
	// displaying stuff on the page.
	$currentcomponent->addguifunc('endpointman_configpageload');
}
// This is called before the page is actually displayed, so we can use addguielem().
function endpointman_configpageload() {
	global $currentcomponent;

	// Init vars from $_REQUEST[]
	$action = isset($_REQUEST['action'])?$_REQUEST['action']:null;
	$extdisplay = isset($_REQUEST['extdisplay'])?$_REQUEST['extdisplay']:null;

	// Don't display this stuff it it's on a 'This xtn has been deleted' page.
	if ($action != 'del') {
		$langcode = languages_user_get($extdisplay);
		$section = _('End Point Manager');
		$msgInvalidLanguage = _('Please enter a valid Lajnguage Code');
		//$currentcomponent->addguielem($section, new gui_textbox('langcode', $langcode, _('Language Code'), _('This will cause all messages and voice prompts to use the selected language if installed.'), "!isFilename()", $msgInvalidLanguage, true),9);
		//$currentcomponent->addguielem($section, "",9);

	}
}

function endpointman_hookProcess_core($viewing_itemid, $request) {
}