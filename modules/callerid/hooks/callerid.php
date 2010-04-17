<?php
// Add caller ID support to device management application
plugins::register('devicemanager/add', 'view', array('CallerId_Plugin', 'update'));
plugins::register('devicemanager/edit', 'view', array('CallerId_Plugin', 'update'));
plugins::register('devicemanager/index', 'view', array('CallerId_Plugin', 'index'));
plugins::register('devicemanager', 'save', array('CallerId_Plugin', 'save')); // All pages

// Add caller ID support to user management application
//plugins::register('usermanager/add', 'view', array('CallerId_Plugin', 'update'));
//plugins::register('usermanager/edit', 'view', array('CallerId_Plugin', 'update'));
//plugins::register('usermanager/index', 'view', array('CallerId_Plugin', 'index'));
//plugins::register('usermanager', 'save', array('CallerId_Plugin', 'save')); // All pages

// Add XML for caller ID to global telephony hooks
dialplan::register('CallerId', 'conditioning');
