<?php defined('SYSPATH') or die('No direct access allowed.');
    dialplan::register('SipEncryption', 'conditioning');

    plugins::register('sipinterface/add', 'view', array('SipEncrption_Plugin', 'update'));
    plugins::register('sipinterface/edit', 'view', array('SipEncryption_Plugin', 'update'));
    
    plugins::register('sipinterface', 'save', array('SipEncryption_Plugin', 'save'));
?>