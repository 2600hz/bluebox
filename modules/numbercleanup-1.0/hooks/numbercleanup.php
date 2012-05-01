<?php defined('SYSPATH') or die('No direct access allowed.');
    dialplan::register('NumberCleanup', 'conditioning');

    plugins::register('contextmanager/create', 'view', array('NumberCleanup_Plugin', 'update'));

    plugins::register('contextmanager/edit', 'view', array('NumberCleanup_Plugin', 'update'));

    plugins::register('contextmanager', 'save', array('NumberCleanup_Plugin', 'save'));

    plugins::register('accountmanager', 'delete', array('NumberCleanup_Plugin', 'buildAccountRelationships'));

