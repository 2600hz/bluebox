<?php defined('SYSPATH') or die('No direct access allowed.');

    dialplan::register('FeatureCode', 'postroute');

    Event::add('numbermanager.collectNumberTargets', array('FeatureCodeManager', 'provideNumberTargets'));
