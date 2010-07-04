<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('numbermanager.collectNumberTargets', array('RingGroups', 'provideNumberTargets'));

