<?php defined('SYSPATH') or die('No direct access allowed.');
    plugins::register('installer/telephony/freeswitch', 'view', array('Freeswitch_Plugin', 'install'));
    plugins::register('installer/telephony/freeswitch', 'save', array('Freeswitch_Plugin', 'save'));
    plugins::register('installer/doinstall', 'save', array('Freeswitch_Plugin', 'postInstallReload'));