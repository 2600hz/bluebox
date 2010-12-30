<?php defined('SYSPATH') or die('No direct access allowed.');

    if (!Bluebox_Installer::is_installing())
    {
        Event::add('bluebox.message_html', array('ErrorReporter', 'provideHelpLink'));
    }