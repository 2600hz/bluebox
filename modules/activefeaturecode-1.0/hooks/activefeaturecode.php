<?php defined('SYSPATH') or die('No direct access allowed.');

    plugins::register('externalxfer/create', 'view', array('ActiveFeatureCode_Plugin', 'update'));

    plugins::register('externalxfer/edit', 'view', array('ActiveFeatureCode_Plugin', 'update'));

    plugins::register('externalxfer', 'save', array('ActiveFeatureCode_Plugin', 'save'));
