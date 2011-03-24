<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('timeofday/create', 'view', array('Timezone_Plugin', 'update'));

    plugins::register('timeofday/edit', 'view', array('Timezone_Plugin', 'update'));

    plugins::register('timeofday', 'save', array('Timezone_Plugin', 'save'));

    Event::add('numbermanager.collectNumberTargets', array('TimeRoutes', 'provideNumberTargets'));