<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('numbermanager.collectNumberTargets', array('AutoAttendants', 'provideNumberTargets'));

    plugins::register('autoattendant/create', 'view', array('Media_Plugin', 'update'));

    plugins::register('autoattendant/edit', 'view', array('Media_Plugin', 'update'));

    plugins::register('autoattendant', 'save', array('Media_Plugin', 'save'));

    Event::add('bluebox.account.initialize', array('AutoAttendants', 'initSampleData'));
    