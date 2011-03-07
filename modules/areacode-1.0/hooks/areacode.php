<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('devicemanager/create', 'view', array('Areacode_Plugin', 'update'));

    plugins::register('devicemanager/edit', 'view', array('Areacode_Plugin', 'update'));
    
    plugins::register('devicemanager', 'save', array('Areacode_Plugin', 'save'));
