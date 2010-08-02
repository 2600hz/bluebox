<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('bluebox.message_html', array('ErrorReporter', 'provideHelpLink'));