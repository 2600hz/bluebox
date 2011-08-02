<?php defined('SYSPATH') or die('No direct access allowed.');
    plugins::register('feature/create', 'view', array('feature_Plugin', 'setupCreateEdit'));
    plugins::register('feature/edit', 'view', array('feature_Plugin', 'setupCreateEdit'));

    dialplan::register('Feature', 'postroute');
    Event::add('numbermanager.collectNumberTargets', array('FeatureManager', 'provideNumberTargets'));
    Event::add('numbermanager.collectNumberOptions', array('FeatureManager', 'provideNumberOptions'));
?>