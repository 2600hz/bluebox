<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('numbermanager.collectNumberOptions', array('DeviceManager', 'provideNumberOptions'));

    Event::add('numbermanager.collectNumberTargets', array('DeviceManager', 'provideNumberTargets'));

    Event::add('ringgroup.avaliablemembers', array('DeviceManager', 'getAvaliableRingGroupMembers'));