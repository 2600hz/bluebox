<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('numbermanager.collectNumberOptions', array('Voicemails', 'provideNumberOptions'));

    Event::add('numbermanager.collectNumberTargets', array('Voicemails', 'provideNumberTargets'));

    Event::add('numbermanager.collectNumberTerminators', array('Voicemails', 'provideNumberTerminators'));

    //dialplan::register('Voicemail', 'prenumber');
    
    //dialplan::register('Voicemail', 'postnumber');
