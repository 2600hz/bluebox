<?php defined('SYSPATH') or die('No direct access allowed.');

    dialplan::register('SessionRecord', 'conditioning');

    plugins::register('xmlcdr/details', 'view', array('SessionRecord_Plugin', 'update'));

