<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('numbermanager.collectNumberOptions', array('ExternalXfers', 'provideNumberOptions'));
    
    Event::add('numbermanager.collectNumberTargets', array('ExternalXfers', 'provideNumberTargets'));
    
    Event::add('ringgroup.avaliablemembers', array('ExternalXfers', 'getAvailableRingGroupMembers'));
?>    