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
    'mediamanager',
    'numbermanager',
    'trunkmanager',
    'autoattendant',
    'conference',
    'devicemanager',
    'ringgroup',
    'timeofday',
    'voicemail',

    // Plugins
    'address',
    'callerid',
    'simpleroute',
    'sip',
    'timezone',
    
    // Freeswitch
    'esl',
    'mediaoption',
    'multitenant',
    'netlistmanager',
    'odbc',
    'sipinterface',
    //'sofia',
    'xmleditor',
);

$config['ESLHost'] = '127.0.0.1';
$config['ESLPort'] = '8021';
$config['ESLAuth'] = 'ClueCon';

$config['cfg_root'] = '/usr/local/freeswitch/conf';

$config['audio_root'] = '/usr/local/freeswitch/sounds';

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

// Base file - note that because the query string is //document, this section will contain
// the remaining document and any includes for other filemap sections
/*$config['filemap'][] = array (
    'filename' => $config['cfg_root'] .'/freeswitch.xml',
    'query' => '//document',
);*/

