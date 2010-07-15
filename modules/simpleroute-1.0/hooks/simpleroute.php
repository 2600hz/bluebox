<?php
// Add routing support to trunks
plugins::register('trunkmanager/create', 'view', array('SimpleRoute_Plugin', 'update'));
plugins::register('trunkmanager/edit', 'view', array('SimpleRoute_Plugin', 'update'));
plugins::register('trunkmanager/index', 'view', array('SimpleRoute_Plugin', 'index'));

// Handle saves
plugins::register('trunkmanager', 'save', array('SimpleRoute_Plugin', 'save'));
plugins::register('trunkmanager', 'delete', array('SimpleRoute_Plugin', 'delete'));
