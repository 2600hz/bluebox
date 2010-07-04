<?php defined('SYSPATH') or die('No direct access allowed.');
/* 
 *  Bluebox Modular Telephony Software Library / Application
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
 */

/**
 * locations.php - locations class
 * Created on Aug 26, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 */
class locations {
    public static function dropdown($data, $selected = NULL, $extra = '') {
        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => FALSE
        );

        // append or insert the class
        arr::update($data, 'class', ' locations_dropdown');

        // render a null option if its been set in data
        if (!empty($data['nullOption'])) {
            $options = array(0 => __($data['nullOption']));
        } else {
            $options = array();
        }
        unset($data['nullOption']);

        // list all the locations from the location table
        $locations = Doctrine::getTable('Location')->findAll(Doctrine::HYDRATE_ARRAY);
        foreach ($locations as $location) {
            $options[$location['location_id']] = $location['name'] . ' (' . $location['domain'] .')';
        }

        // use kohana helper to generate the markup
        return form::dropdown($data, $options, $selected, $extra);
    }
    
    public function getLocationDomain($location_id) {
        $location = Doctrine::getTable('Location')->findOneByLocationId(array($location_id));

        if($location)
        {
            return $location->domain;
        }
    }
}
