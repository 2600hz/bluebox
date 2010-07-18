<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author K Anderson
 * @license LGPL
 * @package Bluebox
 * @subpackage NetListManager
 */

class TrunkManager_Plugin extends Bluebox_Plugin
{
    public function delete()
    {
        $base = Event::$data;

        if (!$base)
            return FALSE;

        netlists::removeTrunkFromAuto($base);
    }
}