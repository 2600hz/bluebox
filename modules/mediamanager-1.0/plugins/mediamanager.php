<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author K Anderson
 * @license MPL
 * @package Bluebox3
 */
class MediaManager_Plugin extends Bluebox_Plugin
{

    public function deleteFile()
    {
        // What are we working with here?
        $base = Event::$data;

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        // TODO: This needs a better hook, checking if the db entry is
        // successfully deleted;
        kohana::log('alert', 'Unlinking file ' . $base->path . $base->name);
        unlink($base->path . $base->name);
    }

}

