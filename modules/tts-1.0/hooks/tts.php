<?php defined('SYSPATH') or die('No direct access allowed.');

    Event::add('bluebox.media.widget', array('TextToSpeech', 'provideMediaWidget'));

    Event::add('bluebox.media.text_to_speech', array('TextToSpeech', 'generateConfiguration'));