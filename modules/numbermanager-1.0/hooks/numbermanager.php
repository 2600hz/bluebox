<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('bluebox.pre_save', array('Numbers', 'customValidation'));

    Event::add('bluebox.pre_save', array('Numbers', 'disassociateNumbers'));

    Event::add('bluebox.post_save', array('Numbers', 'associateNumbers'));

    Event::add('bluebox.prepare_update_view', array('Numbers', 'dynamicNumberPlugin'));


    if (Session::instance()->get('ajax.base_controller') == 'numbermanager')
    {
        Event::add('ajax.updateobject', array('Numbers', 'updateOnPageObjects'));
    }


    Event::add('bluebox.create.extensionnumber', array('Numbers', 'createExtensionNumber'));


    plugins::register('numbermanager/create', 'view', array('NumberManager_Plugin', 'numberTargets'));

    plugins::register('numbermanager/edit', 'view', array('NumberManager_Plugin', 'numberTargets'));



    plugins::register('numbermanager/create', 'view', array('NumberManager_Plugin', 'terminateOptions'));

    plugins::register('numbermanager/edit', 'view', array('NumberManager_Plugin', 'terminateOptions'));

    

    plugins::register('numbermanager/create', 'view', array('NumberManager_Plugin', 'numberPools'));

    plugins::register('numbermanager/edit', 'view', array('NumberManager_Plugin', 'numberPools'));

    

    plugins::register('numbermanager/create', 'view', array('NumberManager_Plugin', 'numberContexts'));

    plugins::register('numbermanager/edit', 'view', array('NumberManager_Plugin', 'numberContexts'));