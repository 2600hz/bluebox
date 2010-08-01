<?php defined('SYSPATH') or die('No direct access allowed.');

    dialplan::register('FeatureCode', 'conditioning');

    dialplan::register('FeatureCode', 'network');

    dialplan::register('FeatureCode', 'preroute');

    dialplan::register('FeatureCode', 'postroute');

    dialplan::register('FeatureCode', 'preanswer');

    dialplan::register('FeatureCode', 'postanswer');

    dialplan::register('FeatureCode', 'catchall');
    
    dialplan::register('FeatureCode', 'postexecute');
    
    Event::add('numbermanager.collectNumberTargets', array('FeatureCodeManager', 'provideNumberTargets'));