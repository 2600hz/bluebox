<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * WARNING: If you duplicate sections in the filemap, you may end up with empty files!
 *
 * NOTICE: If you do not manually number the array indexes during install you end up with empty files!
 *         This is because during install this file may be loaded more than once.
 *
 * Best not to mess with this unless you think you know what you are doing ;)
 */
$config['default_packages'] = array(
    // Modules
    'contextmanager',
    'locationmanager',
    'numbermanager',
    'trunkmanager',
    'autoattendant',
    'conference',
    'devicemanager',
    'ringgroup',
    'timeofday',
    'voicemail',
    'featurecode',
    'quickadd',
    'regenerate',
    
    // Plugins
    'address',
    'callerid',
    'simpleroute',
    'sip',
    'timezone',
    
    // Freeswitch
    'esl',
    'mediafile',
    'tts',
    'multitenant',
    'netlistmanager',
    'odbc',
    'sipinterface',
    'externalxfer',
    //'sofia',
    'xmleditor',
    'xmlcdr'
);

$config['ESLHost'] = '127.0.0.1';
$config['ESLPort'] = '8021';
$config['ESLAuth'] = 'ClueCon';

$config['cfg_root'] = '/usr/local/freeswitch/conf';

$config['audio_root'] = '/usr/local/freeswitch/sounds';

// The fopen mode used when saving the config files back to disk, the only
// two values that make sense here are 'w' or 'c'.  Usefull if you have a DFS
// that does not like the truncating action of fopen with 'w'.
$config['fopen_mode'] = 'w';

// The amount of time to poll an empty file for contents, before
// considering it empty (usefull for slow DFS implementations so we dont
// mistake uncached files as empty).  Set as quarter second counts
// IE: to wait 1 second set this to 4 (4 X 250ms = 1000mx = 1s).
$config['dfs_wait_time'] = FALSE;

$config['filemap'][0] = array (
    // Where do the files go for this feature? Note, if you end with a / and set the id key
    // you imply each individual id goes into it's own file in a subdirectory
    // and the subdirectory is included, in full, in the main config
    'filename' => $config['cfg_root'] .'/directory/default.xml',
    'query' => '//document/section[@name="directory"]/domain',
);

$config['filemap'][1] = array (
    'filename' => $config['cfg_root'] . '/autoload_configs/locations.conf.xml',
    'query' => '//document/section[@name="locations"]'
);

$config['filemap'][2] = array (
    'filename' => $config['cfg_root'] .'/sip_profiles/bluebox_sipinterfaces.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="sofia.conf"]/profiles/profile'
);

$config['filemap'][3] = array (
    'filename' => $config['cfg_root'] .'/dialplan/bluebox_dialplan.xml',
    'query' => '//document/section[@name="dialplan"]/context'
);

$config['filemap'][4] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/conference.conf.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="conference.conf"]'
);

$config['filemap'][5] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/ivr.conf.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="ivr.conf"][@description="IVR menus"]'
);

$config['filemap'][6] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/bluebox_odbc.conf.xml',
    'query' => '//document/section[@name="odbc"]'
);

$config['filemap'][7] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/acl.conf.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="acl.conf"]'
);

$config['filemap'][8] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/xml_cdr.conf.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="xml_cdr.conf"][@description="XML CDR CURL logger"]'
);

$config['filemap'][9] = array (
    'filename' => $config['cfg_root'] .'/jingle_profiles/bluebox_clients.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="dingaling.conf"]/profile'
);

$config['filemap'][10] = array (
    'filename' => $config['cfg_root'] .'/dialplan/bluebox_routes.xml',
    'query' => '//document/section[@name="routes"]/context[@name="multitenant_routing_context"]'
);

$config['filemap'][11] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/callcenter.conf.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="callcenter.conf"][@description="CallCenter"]'
);

$config['filemap'][12] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/distributor.conf.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="distributor.conf"]/lists'
);

$config['filemap'][]= array (
    'filename' => $config['cfg_root'] .'/autoload_configs/directory.conf.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="directory.conf"]'
);
// Base file - note that because the query string is //document, this section will contain
// the remaining document and any includes for other filemap sections
/*$config['filemap'][] = array (
    'filename' => $config['cfg_root'] .'/freeswitch.xml',
    'query' => '//document',
);*/

