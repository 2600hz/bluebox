<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author K Anderson
 * @license LGPL
 * @package FreePBX3
 * @subpackage NetListManager
 */

class TrunkManager_Plugin extends FreePbx_Plugin
{
    public function delete() {
        $base = Event::$data;

        if (!$base)
            return FALSE;

        netlists::removeTrunkFromAuto($base);
    }

}