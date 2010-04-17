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
 * users.php - users helper class
 *
 * Provides support for user-related functions, such as generating an HTML list of users
 *
 * Created on Jul 26, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */
class users {
    public static $user;

    /**
     * Load the current user into the class. If there is no user and this method is not explicitly defined with allow
     * anonymous then redirect to login page.
     */
    public static function getCurrentUser()
    {
        // If we already have a cached copy of who's logged in, just return that
        if (self::$user) {
            return self::$user;
        }

        $authentic = new Auth();
        if ($authentic->logged_in()) {
            $userEmail = $authentic->get_user();
            self::$user = Doctrine::getTable('User')->findOneByEmailAddress($userEmail); //now you have access to user information stored in the database
            
            // Make sure the user is still valid
            if (self::$user) {
                return self::$user;
            }
            
            // We get here only if the current user is invalid - old cookie! Kill it.
            $authentic->logout(TRUE);
        }

        return FALSE;       // Nobody logged in if we get here
    }

    public static function dropdown($data, $selected = NULL, $extra = '', $nullOption = FALSE) {

        $users = Doctrine::getTable('User')->findAll(Doctrine::HYDRATE_ARRAY);

        // see if the module wants to allow null selections
        if (!empty($nullOption)) {
            $options = array('0' => __($nullOption));
        } else {
            $options = array();
        }

        foreach ($users as $user) {
            $options[$user['user_id']] = $user['first_name'] . ' ' . $user['last_name'];
        }

        // add in a class for skins
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }
        arr::update($data, 'class', ' users_dropdown');

        return form::dropdown($data, $options, $selected, $extra);
    }
}
