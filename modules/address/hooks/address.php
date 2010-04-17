<?php
// Add support for address management on device manager pages (view, edit, save)
plugins::register('devicemanager/add', 'view', array('Address_Plugin', 'update'));
plugins::register('devicemanager/edit', 'view', array('Address_Plugin', 'update'));
plugins::register('devicemanager/index', 'view', array('Address_Plugin', 'index'));
plugins::register('devicemanager', 'save', array('Address_Plugin', 'save'));    // Applies to all pages

// Add support for address management on user management pages (view, edit, save)
plugins::register('usermanager/add', 'view', array('Address_Plugin', 'update'));
plugins::register('usermanager/edit', 'view', array('Address_Plugin', 'update'));
plugins::register('usermanager/index', 'view', array('Address_Plugin', 'index'));
plugins::register('usermanager', 'save', array('Address_Plugin', 'save'));
