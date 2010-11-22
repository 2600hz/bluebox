<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('bluebox.ready', array('Maintenance', 'conditionalTrigger'));