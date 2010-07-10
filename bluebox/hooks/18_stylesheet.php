<?php defined('SYSPATH') or die('No direct access allowed.');

// hook so stylesheet helper can add style/assets to the document when rendered
Event::add('system.post_template', array(
    'stylesheet',
    'addCssAssets'
));
