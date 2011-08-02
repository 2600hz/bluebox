<?php defined('SYSPATH') or die('No direct access allowed.');
    plugins::register('callcenter_tiers/create', 'view', array('callcenter_tiers_Plugin', 'viewcreate_update'));
    plugins::register('callcenter_tiers/create', 'bluebox.load_base_model', array('callcenter_tiers_Plugin', 'populate_parms'));

    plugins::register('callcenter_tiers/edit', 'view', array('callcenter_tiers_Plugin', 'viewcreate_update'));
?>