<?php defined('SYSPATH') or die('No direct access allowed.');

    dialplan::register('SessionRecord', 'postroute');

    plugins::register('xmlcdr/details', 'view', array('SessionRecord_Plugin', 'update'));

