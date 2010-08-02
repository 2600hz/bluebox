<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('accountmanager/create', 'view', array('ContextManager_Plugin', 'initialAccountContext'));

    plugins::register('accountmanager/edit', 'view', array('ContextManager_Plugin', 'index'));
