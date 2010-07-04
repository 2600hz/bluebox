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
 * Michael Phillips
 *
 * @license MPL
 * @package Bluebox
 * @subpackage Odbc
 */



class Odbc_Plugin extends Bluebox_Plugin
{
    protected $preloadModels = array('OdbcMap');

    public function add()
    {
        // Not yet!
    }

    public function update()
    {
        $subview = new View('odbc/manage');
        $subview->tab = 'Store in ODBC?';
        $subview->section = 'ODBC';

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base) {
            return FALSE;	// Nothing to do here.
        }

        // check if there is an odbcMap for this interface, otherwise populate with dummy values....
        if (!empty($base['OdbcMap']['odbc_id'])) {
            // Populate form data with whatever our database record has
            $subview->odbcmap = $base->OdbcMap->toArray();
            $subview->enable_odbc = !empty($base->OdbcMap->odbc_id);
        } else {
            $subview->odbcmap = array('odbc_id' => 0);
            $subview->enable_odbc = FALSE;
        }

        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm)) {
            $subview->odbcmap = arr::overwrite($subview->odbcmap, $this->repopulateForm['odbcmap']);
        }

        $this->views[] = $subview;
    }

    public function save()
    {
        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        // if the enabled_odbc check mark is not present then do not
        // add odbc support, and if it is there remove it.
        if(empty($_POST['odbcmap']['enable_odbc'])) {
            if ($base->relatedExists('OdbcMap')) {
                $base->refreshRelated('OdbcMap');
                unset($base->OdbcMap);
            }
            return TRUE;
        }

        // if odbc support is enabled create a new mapping if one doesnt exist
        if ((!$base->OdbcMap) or (!$base->OdbcMap->odbcmap_id)) {
            if (get_parent_class($base) == 'Bluebox_Record') {
                $class = get_class($base) . 'OdbcMap';
            } else {
                $class = get_parent_class($base) . 'OdbcMap';
            }
            
            if (class_exists($class, TRUE)) {
                $base->OdbcMap = new $class();
            } else {
                // No class that extends this plug-in - do nothing
                return FALSE;
            }
        }

        $form = $this->input->post('odbcmap');
        $fieldNames = array('odbc_id');

        // Only map fields that don't have relations, for "safety"
        foreach ($fieldNames as $fieldName) {
            if (isset($form[$fieldName])) {
                $base->OdbcMap->$fieldName = $form[$fieldName];
            }
        }
    }
}
