<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('bluebox.media.widget', array('MediaLib', 'provideMediaWidget'));

    Event::add('bluebox.media.media_file', array('MediaLib', 'generateConfiguration'));

    Event::add('bluebox.maintenance', array('MediaLib', 'maintenance'));