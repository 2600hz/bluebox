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
 * All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * Timezone Support - Adds support for keeping track of timezones.
 *
 * This is a linked library that provides timezone support for other modules.
 *
 * When this module is loaded along with the above listed modules (and possibly others), timezone fields will automatically
 * appear within relevant modules. In addition, an order of precedence is set for loaded modules as well.
 *
 * @author Darren Schreiber
 * @license MPL
 * @package Bluebox
 * @subpackage Timezone
 */
class Timezone_Plugin extends Bluebox_Plugin
{
    public function index()
    {
        //$this->Grid->addColumn('device->Timezone', 'columnName', 'width 50px', 'showhide=true, sortable=true');
    }

    /**
     * Setup the subview for the timezone plugin
     */
    public function update()
    {
        $subview = new View('timezone/update');
        $subview->tab = 'main';
        $subview->section = 'identification';

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        // If we don't have a Timezone object, create a dummy one.
        // NOTE: This inherently lets other modules know that this module is installed while providing blank/default entries for our view.
        // Because this is created on the view page and NOT on a save page, this will NOT result in an empty record being saved.
        if (!$base->Timezone) {
            $base->Timezone = new Timezone();
        }

        // Populate form data with whatever our database record has
        $subview->timezone = $base->Timezone->toArray();

        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm)) {
            $subview->timezone = arr::overwrite($subview->timezone, $this->repopulateForm['timezone']);
        }

        $subview->errors = array('timezone[timezone]' => '');

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

        if ((!$base->Timezone) or (!$base->Timezone->timezone_id)) {
            if (get_parent_class($base) == 'Bluebox_Record')
                $class = get_class($base) . 'Timezone';
            else
                $class = get_parent_class($base) . 'Timezone';
            if (class_exists($class, TRUE)) {
                $base->Timezone = new $class();
            } else {
                // No class that extends this plug-in - do nothing
                return FALSE;
            }
        }

        $form = $this->input->post('timezone');
        $fieldNames = array('timezone');

        // DANGEROUS! Only map fields that don't have relations, for "safety"
        foreach ($fieldNames as $fieldName) {
            if (isset($form[$fieldName]))
                $base->Timezone->$fieldName = $form[$fieldName];
        }
    }
}
