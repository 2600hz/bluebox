<?php defined('SYSPATH') or die('No direct access allowed.');

$config['default_packages'] = array(
    // Modules
    'contextmanager',
    'locationmanager',
    'numbermanager',
    'devicemanager',



    // Plugins
    'sip',






    // Modules
//    
//    
//    'mediamanager',
//    'trunkmanager',
//    'autoattendant',
//    'conference',
//    'ringgroup',
//    'timeofday',
//    'voicemail',
//
//    // Plugins
//    'address',
//    'callerid',
//    'simpleroute',
//    'sip',
//    'timezone',
//
//    // Freeswitch
//    'esl',
//    'mediaoption',
//    'multitenant',
//    'netlistmanager',
//    'odbc',
//    'sipinterface',
//    'externalxfer',
//    //'sofia',
//    'xmleditor',
);

/*
 * Asterisk engine configuration class
 */
// Do we immediately commit changes to disk?
$config['immediate'] = TRUE;

// Do we automatically reload the engine's configs everytime a change is made?
$config['reload'] = TRUE;

$config['cfg_root'] = '/etc/asterisk';

/* Asterisk Manager Interface */
$config['AmiHost'] = '127.0.0.1';
$config['AmiPort'] = '5038';
$config['AmiUser'] = 'admin';
$config['AmiPass'] = 'admin';
