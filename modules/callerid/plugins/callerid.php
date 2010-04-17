<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is FreePBX Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * callerid.php - A unified CallerID plugin for supporting caller ID settings in:
 *  - devices
 *  - users
 *  - trunks
 *  - anyone else who asks for it (via hooks)
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage CallerId
 */

class CallerId_Plugin extends FreePbx_Plugin
{
    public function index()
    {
        
    }

    public function update()
    {
        $subview = new View('callerid/update');
        $subview->tab = 'main';
        $subview->section = 'identification';

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        // If we don't have a CallerId object, create a dummy one.
        // NOTE: This inherently lets other modules know that this module is installed while providing blank/default entries for our view.
        // Because this is created on the view page and NOT on a save page, this will NOT result in an empty record being saved.
        if (!$base->CallerId) {
            $base->CallerId = new CallerId();
        }

        // Populate form data with whatever our database record has
        $subview->callerid = $base->CallerId->toArray();

        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm)) {
            $subview->callerid = arr::overwrite($subview->callerid, $this->repopulateForm['callerid']);
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

        if ((!$base->CallerId) or (!$base->CallerId->caller_id_id)) {
            if (get_parent_class($base) == 'FreePbx_Record')
                $class = get_class($base) . 'CallerId';
            else
                $class = get_parent_class($base) . 'CallerId';
            if (class_exists($class, TRUE)) {
                $base->CallerId = new $class();
            } else {
                // No class that extends this plug-in - do nothing
                return FALSE;
            }
        }


        $form = $this->input->post('callerid');
        $fieldNames = array('internal_name', 'internal_number', 'external_name', 'external_number');

        // DANGEROUS! Only map fields that don't have relations, for "safety"
        foreach ($fieldNames as $fieldName) {
            if (isset($form[$fieldName]))
            $base->CallerId->$fieldName = $form[$fieldName];
        }
    }
}
