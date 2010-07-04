<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is Bluebox Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * mediaoption.php - Media Options support - allows for specifying how media is handled for devices, trunks, etc.
 *
 * This module allows for advanced codec and media handling settings for trunks, devices and other media-handling items
 * connected to a PBX or phone system
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage MediaOption
 */

class MediaOption_Plugin extends Bluebox_Plugin
{
    protected $preloadModels = array('DeviceMediaOption', 'TrunkMediaOption');

    public function index()
    {
    }

    public function view()
    {
        $subview = new View('mediaoption/update');
        $subview->section = 'general';
        $subview->tab = 'main';

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        // If we don't have a Media Options object, create a dummy one.
        // NOTE: This inherently lets other modules know that this module is installed while providing blank/default entries for our view.
        // Because this is created on the view page and NOT on a save page, this will NOT result in an empty record being saved.
        if (!$base->MediaOption) {
            $base->MediaOption = new MediaOption();
        }

        // Populate form data with whatever our database record has
        $subview->mediaoption = $base->MediaOption->toArray();

        // Pass errors from the controller to our view
        $subview->errors = $this->errors();

        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm) && isset($this->repopulateForm['mediaoption'])) {
            $subview->mediaoption = arr::overwrite($subview->mediaoption, $this->repopulateForm['mediaoption']);
        }

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function save()
    {
        // What are we working with here?
        $base = $this->getBaseModelObject();
        
        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        // If this is a brand new record, we must instantiate the right aggregated type
        if ((!$base->MediaOption) or (!$base->MediaOption->media_option_id)) {
            if (get_parent_class($base) == 'Bluebox_Record')
                $class = get_class($base) . 'MediaOption';
            else
                $class = get_parent_class($base) . 'MediaOption';
            if (class_exists($class, TRUE)) {
                $base->MediaOption = new $class();
            } else {
                // No class that extends this plug-in - do nothing
                return FALSE;
            }
        }

        $form = $this->input->post('mediaoption');
        $fieldNames = array('media_workaround');

        foreach ($fieldNames as $fieldName) {
            $base->MediaOption->$fieldName = $form[$fieldName];
        }
    }
}
