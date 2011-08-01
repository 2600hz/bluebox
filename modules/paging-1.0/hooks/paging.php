<?php defined('SYSPATH') or die('No direct access allowed.');
    Event::add('numbermanager.collectNumberTargets', array('PagingManager', 'provideNumberTargets'));

    plugins::register('paging/create', 'view', array('paging_Plugin', 'viewcreate_update'));
    plugins::register('paging/edit', 'view', array('paging_Plugin', 'viewcreate_update'));
?>