<?php defined('SYSPATH') or die('No direct access allowed.');

    Plugins::register('accountmanager.index', 'view', array('Regenerate_Plugin', 'add_to_account'));

    Plugins::register('contextmanager.index', 'view', array('Regenerate_Plugin', 'add_to_context'));
    
    Plugins::register('numbermanager.index', 'view', array('Regenerate_Plugin', 'add_to_number'));