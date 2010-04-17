<?php

// Add ourselves to the menu bar
/*navigation::addNode(
    'Permissions',
    '/permission/',
    'assets/img/icons/mainSettingsX.png',
    array(
        'Groups' => '/permission/group',
        'Permissions' => '/permission/permission',

    )
);*/

/*
 *         'Edit Device' => array('url' => '/devicemanager/edit', 'disabled' => true),
        'Delete Device' => array('url' => '/devicemanager/delete' , 'disabled' => true)
        */


// Add view to the page where usermanager are added
plugins::register('usermanager/add', 'view', array('Permission_Plugin', 'update'));

// Add view to the page where usermanager are edited
plugins::register('usermanager/edit', 'view', array('Permission_Plugin', 'update'));

// Generic catcher for saves
plugins::register('usermanager', 'save', array('Permission_Plugin', 'save'));

