<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('bluebox.media.widget', array('MediaLib', 'provideMediaWidget'));

    Event::add('bluebox.maintenance', array('MediaLib', 'maintenance'));