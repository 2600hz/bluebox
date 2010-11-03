<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('bluebox.list_media', array('MediaLib', 'listMediaFiles'));