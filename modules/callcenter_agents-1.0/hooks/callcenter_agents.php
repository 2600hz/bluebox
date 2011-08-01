<?php defined('SYSPATH') or die('No direct access allowed.');
    plugins::register('callcenter_agents/create', 'view', array('callcenter_agents_Plugin', 'viewcreate_update'));

    plugins::register('callcenter_agents/edit', 'view', array('callcenter_agents_Plugin', 'viewcreate_update'));
    plugins::register('callcenter_agents/edit', 'view', array('callcenter_agents_Plugin', 'subview_tiers'));
?>