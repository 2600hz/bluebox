<?php
// Add mISDN device fields to the trunk managemenet application
plugins::register('trunkmanager/index', 'view', array('Misdn_Plugin', 'index'));
plugins::register('trunkmanager/add', 'view', array('Misdn_Plugin', 'view'));
plugins::register('trunkmanager/edit', 'view', array('Misdn_Plugin', 'view'));
plugins::register('trunkmanager', 'save', array('Misdn_Plugin', 'save'));
