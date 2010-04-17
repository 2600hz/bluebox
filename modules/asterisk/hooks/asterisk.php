<?php
plugins::register('installer/telephony/asterisk', 'view', array('Asterisk_Plugin', 'install'));
plugins::register('installer/telephony/asterisk', 'save', array('Asterisk_Plugin', 'save'));

// Add context support to trunks
plugins::register('trunkmanager/add', 'view', array('Asterisk_Plugin', 'trunkView'));
plugins::register('trunkmanager/edit', 'view', array('Asterisk_Plugin', 'trunkView'));