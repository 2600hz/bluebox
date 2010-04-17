<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author K Anderson
 * @license LGPL
 * @package FreePBX3
 * @subpackage Core
 */
/* hook so jquery helper can add scripts/assets to the document when rendered */
Event::add('system.post_template', array(
    'javascript',
    'addJsAssets'
));
