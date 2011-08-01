<?php defined('SYSPATH') or die('No direct access allowed.');
    plugins::register('sipinterface/add', 'view', array('SharedPresence_Plugin', 'update'));
    plugins::register('sipinterface/edit', 'view', array('SharedPresence_Plugin', 'update'));
    
    plugins::register('sipinterface', 'save', array('SharedPresence_Plugin', 'save'));
?>