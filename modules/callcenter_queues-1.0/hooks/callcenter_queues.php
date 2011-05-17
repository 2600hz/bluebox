<?php defined('SYSPATH') or die('No direct access allowed.');
    plugins::register('callcenter_queues/create', 'view', array('callcenter_queues_Plugin', 'viewcreate_update'));

    plugins::register('callcenter_queues/edit', 'view', array('callcenter_queues_Plugin', 'viewcreate_update'));
    plugins::register('callcenter_queues/edit', 'view', array('callcenter_queues_Plugin', 'subview_tiers'));

    Event::add('numbermanager.collectNumberTargets', array('CallCenterManager', 'provideNumberTargets'));
?>