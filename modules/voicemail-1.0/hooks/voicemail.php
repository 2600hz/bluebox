<?php defined('SYSPATH') or die('No direct access allowed.');

    dialplan::register('Voicemail', 'prenumber');

    dialplan::register('Voicemail', 'postnumber');

    Event::add('numbermanager.collectNumberOptions', array('Voicemails', 'provideNumberOptions'));

    Event::add('numbermanager.collectNumberTargets', array('Voicemails', 'provideNumberTargets'));

    Event::add('numbermanager.collectNumberTerminators', array('Voicemails', 'provideNumberTerminator'));

    plugins::register('devicemanager/create', 'view', array('Voicemail_Plugin', 'update'));

    plugins::register('devicemanager/edit', 'view', array('Voicemail_Plugin', 'update'));

    plugins::register('devicemanager', 'save', array('Voicemail_Plugin', 'save'));

    Event::add('bluebox.initialize.device', array('Voicemails', 'initializeDevice'));