<?php
//hooks for plugin support

// Add view to the page where usermanager are added
plugins::register('numbermanager/add', 'view', array('TimeOfDay_Plugin', 'edit'));

// Add view to the page where usermanager are edited
plugins::register('numbermanager/edit', 'view', array('TimeOfDay_Plugin', 'edit'));

// Generic catcher for saves
plugins::register('numbermanager', 'save', array('TimeOfDay_Plugin', 'save'));
