<?php

// Add SIP device fields to the page where devices are added
plugins::register('devicemanager/add', 'view', array('MediaOption_Plugin', 'view'));

// Add SIP device fields to the edit page where devices are edited
plugins::register('devicemanager/edit', 'view', array('MediaOption_Plugin', 'view'));

// Add SIP device fields to the page where devices are added
plugins::register('devicemanager', 'save', array('MediaOption_Plugin', 'save'));

// Add SIP device fields to the edit page where devices are edited
//plugins::register('mediaoption/index', 'view', array('MediaOption_Plugin', 'index'));
//plugins::register('mediaoption/grid', 'view', array('MediaOption_Plugin', 'index'));
