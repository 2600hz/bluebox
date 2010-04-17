<?php

// Add SIP device fields to the edit page where devices are listed, added, edited and saved
plugins::register('devicemanager/index', 'view', array('Sip_Plugin', 'index'));
plugins::register('devicemanager/add', 'view', array('Sip_Plugin', 'view'));
plugins::register('devicemanager/edit', 'view', array('Sip_Plugin', 'view'));
plugins::register('devicemanager', 'save', array('Sip_Plugin', 'save'));
plugins::register('devicemanager', 'delete', array('Sip_Plugin', 'delete'));

// Add SIP device fields to the trunk managemenet application
plugins::register('trunkmanager/index', 'view', array('Sip_Plugin', 'index'));
plugins::register('trunkmanager/add', 'view', array('Sip_Plugin', 'view'));
plugins::register('trunkmanager/edit', 'view', array('Sip_Plugin', 'view'));
plugins::register('trunkmanager', 'save', array('Sip_Plugin', 'save'));
plugins::register('trunkmanager', 'delete', array('Sip_Plugin', 'delete'));