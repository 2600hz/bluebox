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
 * address.php - Address Management Plugin Class
 * Adds support for keeping track of addresses. This is a linked library that provides address support for
 * other modules. Known modules that utilize this library are:
 * - Device Module
 * - Locations Module
 * - Billing Module
 *
 * When this module is loaded along with the above listed modules (and possibly others), address fields will automatically
 * appear within relevant modules. In addition, an order of precedence is set for loaded modules as well.
 *
 * This plugin might be overridden by an alternative address module that has better support for international addresses,
 * validation and other features. Make sure you don't have conflicts.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Address
 */

class Address_Plugin extends Bluebox_Plugin
{
    public static $states = array(
        '' => 'none',
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',

        'DE' => 'Delaware',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',

        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',

        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',

        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',

        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming'
    );

    public function index()
    {

    }

    /**
     * Setup the subview for the address plugin
     */
    public function update()
    {
        $subview = new View('address/update');
        $subview->tab = 'main';
        $subview->section = 'identification';

        // Load the states array to the view
        $subview->states = self::$states;

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base)
            return FALSE;	// Nothing to do here.

        // If we don't have a Address object, create a dummy one.
        // NOTE: This inherently lets other modules know that this module is installed while providing blank/default entries for our view.
        // Because this is created on the view page and NOT on a save page, this will NOT result in an empty record being saved.
        if (!$base->Address) {
            $base->Address = new Address();
        }

        // Populate form data with whatever our database record has
        $subview->address = $base->Address->toArray();
        
        // Pass errors from the controller to our view
        $subview->errors = $this->errors();

        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm)) {
            $subview->address = arr::overwrite($subview->address, $this->repopulateForm['address']);
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

        $form = $this->input->post('address');
        $fieldNames = array('address', 'city', 'state', 'zip');

        // If address is blank and it's not required, just get outta here
        if (!Kohana::config('address.required')) {
            $empty = TRUE;
            foreach ($fieldNames as $fieldName) {
                if (isset($form[$fieldName]) and (strlen($form[$fieldName]) > 0))
                    $empty = FALSE;
            }
            if ($empty)
                return TRUE; // All is well - nothing to save, nothing required.
        }

        if ((!$base->Address) or (!$base->Address->address_id)) {
            if (get_parent_class($base) == 'Bluebox_Record')
                $class = get_class($base) . 'Address';
            else
                $class = get_parent_class($base) . 'Address';
            if (class_exists($class, TRUE)) {
                $base->Address = new $class();
            } else {
                // No class that extends this plug-in - do nothing
                return FALSE;
            }
        }

        // DANGEROUS! Only map fields that don't have relations, for "safety"
        foreach ($fieldNames as $fieldName) {
            if (isset($form[$fieldName]))
            $base->Address->$fieldName = $form[$fieldName];
        }
    }
}
