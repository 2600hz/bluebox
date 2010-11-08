<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('bluebox.media.widget', array('TextToSpeech', 'provideMediaWidget'));