<?php defined('SYSPATH') or die('No direct access allowed.');

    // Add Timezone view to the page where devicemanagers are added
    plugins::register('devicemanager/create', 'view', array('Timezone_Plugin', 'update'));

    // Add Timezone view to the page where devicemanagers are edited
    plugins::register('devicemanager/edit', 'view', array('Timezone_Plugin', 'update'));

    // Generic catcher for saves
    plugins::register('devicemanager', 'save', array('Timezone_Plugin', 'save'));



    // Add Timezone view to the page where devicemanagers are added
    plugins::register('usermanager/create', 'view', array('Timezone_Plugin', 'update'));

    // Add Timezone view to the page where devicemanagers are edited
    plugins::register('usermanager/edit', 'view', array('Timezone_Plugin', 'update'));

    // Generic catcher for saves
    plugins::register('usermanager', 'save', array('Timezone_Plugin', 'save'));
