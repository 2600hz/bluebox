<?php

// Add Voicemail view to the page where usermanager are added
plugins::register('devicemanager/add', 'view', array('Voicemail_Plugin', 'listVoicemailBoxes'));

// Add Voicemail view to the page where usermanager are edited
plugins::register('devicemanager/edit', 'view', array('Voicemail_Plugin', 'listVoicemailBoxes'));

// Generic catcher for saves
plugins::register('devicemanager', 'save', array('Voicemail_Plugin', 'save'));

// Add voicemail selections to destinations
plugins::register('destinations/selector', 'view', array('Voicemail_Plugin', 'selector'));

// We need to add timeouts at the beginning of bridge commands and voicemail transfers at the end of bridge commands for devices & other items
dialplan::register('Voicemail', 'prenumber');
dialplan::register('Voicemail', 'postnumber');
