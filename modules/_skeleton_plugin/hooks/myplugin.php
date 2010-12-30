<?php defined('SYSPATH') or die('No direct access allowed.');

// Define what you are hooking into here
// Format is
//   plugins::register('the/url', 'view|save', array('Class', 'method'));
//
// Whenever the/url loads, we will hook the view or save method and call your callback function so it can add it's own view data
// or perform it's own save actions.

    //plugins::register('modulename/page', 'view', array('MyPlugin_Plugin', 'update'));
