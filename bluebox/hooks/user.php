<?php

// Add support for user management
plugins::register('user/login', 'view', array('User_Plugin', 'login'));
plugins::register('user/register', 'view', array('User_Plugin', 'register'));
plugins::register('user/index', 'view', array('User_Plugin', 'login'));
plugins::register('user/index', 'view', array('User_Plugin', 'register'));
plugins::register('user/login', 'save', array('User_Plugin', 'save'));    // Applies to all pages. Ignores logins via controller
plugins::register('user/register', 'save', array('User_Plugin', 'save'));    // Applies to all pages. Ignores logins via controller
plugins::register('user/index', 'save', array('User_Plugin', 'save'));    // Applies to all pages. Ignores logins via controller
