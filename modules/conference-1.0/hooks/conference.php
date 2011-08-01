<?php defined('SYSPATH') or die('No direct access allowed.');
    
    Event::add('numbermanager.collectNumberTargets', array('Conferences', 'provideNumberTargets'));
	
	plugins::register('conference/create', 'view', array('Media_Plugin', 'update'));

	plugins::register('conference/edit', 'view', array('Media_Plugin', 'update'));
	
	plugins::register('conference', 'save', array('Media_Plugin', 'save'));