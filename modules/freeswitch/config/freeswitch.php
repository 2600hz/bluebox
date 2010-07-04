<?php

// FreeSwitch

/* FreeSwitch */
$config['ESLHost'] = '127.0.0.1';
$config['ESLPort'] = '8021';
$config['ESLAuth'] = 'ClueCon';

$config['cfg_root'] = '/usr/local/freeswitch/conf';

// WARNING: If you duplicate sections in the filemap, you may end up with empty files!

$config['filemap'][] = array (
    // Where do the files go for this feature? Note, if you end with a / and set the id key
    // you imply each individual id goes into it's own file in a subdirectory
    // and the subdirectory is included, in full, in the main config
    'filename' => $config['cfg_root'] .'/directory/default.xml',
    'query' => '//document/section[@name="directory"]/domain',
);

$config['filemap'][] = array (
    'filename' => $config['cfg_root'] . '/autoload_configs/locations.conf.xml',
    'query' => '//document/section[@name="locations"]'
);

$config['filemap'][] = array (
    'filename' => $config['cfg_root'] .'/sip_profiles/bluebox_sipinterfaces.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="sofia.conf"]/profiles/profile'
);

$config['filemap'][] = array (
    'filename' => $config['cfg_root'] .'/dialplan/bluebox_dialplan.xml',
    'query' => '//document/section[@name="dialplan"]/context'
);

$config['filemap'][] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/conference.conf.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="conference.conf"]'
);

$config['filemap'][] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/ivr.conf.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="ivr.conf"][@description="IVR menus"]'
);

$config['filemap'][] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/bluebox_odbc.conf.xml',
    'query' => '//document/section[@name="odbc"]'
);

$config['filemap'][] = array (
    'filename' => $config['cfg_root'] .'/autoload_configs/acl.conf.xml',
    'query' => '//document/section[@name="configuration"]/configuration[@name="acl.conf"]'
);

// Base file - note that because the query string is //document, this section will contain
// the remaining document and any includes for other filemap sections
/*$config['filemap'][] = array (
    'filename' => $config['cfg_root'] .'/freeswitch.xml',
    'query' => '//document',
);*/
