<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 * Copyright (C) 2005-2009, Darren Schreiber <d@d-man.org>
 *
 * Version: FPL 1.0 (a modified version of MPL 1.1)
 *
 * The contents of this file are subject to the FreePBX Public License Version
 * 1.0 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.freepbx.org/FPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is FreePBX Modular Telephony Software Library / Application
 *
 * The Initial Developer of the Original Code is
 * Darren Schreiber <d@d-man.org>
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * Darren Schreiber <d@d-man.org>
 *
 */

/**
 * myplugin.php - Description of the plug-in
 *
 * @author Your Name <your@email.org>
 * @license Your License
 * @package _Skeleton
 */

class Myplugin_Plugin extends FreePbx_Plugin
{
    /**
     * Add data to an application's index if it's related to this plugin's data
     */
    public function index()
    {

    }

    /**
     * Setup the subview for this plugin, for viewing a record
     */
    public function view()
    {

    }

    /**
     * Setup the subview for this plugin, for editing a record
     */
    public function edit()
    {
        $subview = new View('myplugin/edit');
        $subview->tab = 'main';     // The tab you want this plugin to show up in
        $subview->section = 'general';  // The section (on the tab) you want this plugin to show up in

        // What base database object are we working with here? (What's the application?)
        if (isset($this->device)) {
            // This is a device!
            $base = $this->device;
        } elseif (isset($this->location)) {
            // This is a user!
            $base = $this->location;
        } else {
            // No object to attach to. Returning
            return FALSE;
        }

        // While the base object may be defined by an app or another plugin, it may not be populated! Double-check
        if (!$base) {
            return FALSE;	// Nothing to do here - no relation.
        }

        // If we don't have a related object popualted for this plugin, create a dummy one.
        // NOTE: This inherently lets other modules know that this module is installed while providing blank/default entries for our view.
        // Because this is created on the edit method and NOT on a save method, this will NOT result in an empty record being saved.
        if (!$base->MyPlugin) {
            $base->MyPlugin = new MyPlugin();
        }

        // Populate form data with whatever our database record has
        $subview->myplugin = $base->MyPlugin->toArray();

        // Pass errors from the controller to our view
        $subview->errors = $this->errors();

        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm)) {
            $subview->myplugin = arr::overwrite($subview->myplugin, $this->repopulateForm['myplugin']);
        }

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function save()
    {
        // What base database object are we working with here? (What's the application?)
        if (isset($this->device)) {
            // This is a device!
            $base = $this->device;
        } elseif (isset($this->location)) {
            // This is a user!
            $base = $this->location;
        } else {
            // No object to attach to. Returning
            return FALSE;
        }

        // Get posted form data
        $form = $this->input->post('myplugin');

        // Map only certain fields from the form post to our database object
        $fieldNames = array('field1', 'field2', 'field3', 'field4');
        foreach ($fieldNames as $fieldName) {
            if (isset($form[$fieldName]))
            $base->MyPlugin->$fieldName = $form[$fieldName];
        }
    }
}
