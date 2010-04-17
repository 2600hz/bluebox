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
 * User.php - User model, for tracking logins/users/etc. Most items relate to users (or locations/accounts)
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage Core
 */

class User extends FreePbx_Record
{
    /**
     * @var string this holds an un-hashed copy of the password during a save cycle (for validation to run against)
     */
    protected $unHashedPassword = '';

    public static $errors = array (
        'first_name' => array (
            'required' => 'First name is required.',
            'alpha' => 'Only letters are allowed.',
            'length' => 'Must be 3-40 characters.',
            'default' => 'Invalid First Name.',
        ),
        'last_name' => array(
            'required' => 'Last name is required.',
            'alpha' => 'Only letters are allowed.',
            'length' => 'Must be 3-40 characters.',
            'default' => 'Invalid Last Name.',
        ),
        'username' => array(
            'required' => 'Username is required.',
            'unique' => 'Username is not unique',
            'default' => 'Invalid username',
        ),
        'email_address' => array (
            'required' => 'Email is required.',
            'duplicate' => 'Email is already in use.',
            'email' => 'Email is invalid.',
            'unknown' => 'Email address is not recognized.',
            'default' => 'Invalid Email Address.',
        ),
        'password' => array (
            'required' => 'Password is required.',
            'nodigits' => 'Letters & digits required',
            'noalpha' => 'Letters & digits required',
            'length' => 'Must be 5-20 characters',
        ),
        'company_name' => array (
            'required' => 'Company name is required.',
            'alpha' => 'Only letters are allowed.',
            'length' => 'Must be 3-50 characters.',
            'default' => 'Invalid Company Name.',
        ),
        'password2' => array (
            'nomatch' => 'Passwords do not match.'
        ),
        'confirm_password' => array (
            'nomatch' => 'Passwords do not match',
        ),
    );

    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('user_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('location_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('first_name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('last_name', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('username', 'string', 100, array('unique' => true, 'notnull' => true, 'notblank' => true));
        $this->hasColumn('email_address', 'string', 100, array('unique' => true, 'notnull' => true, 'notblank' => true));
        $this->hasColumn('password', 'string', 64, array('notnull' => true, 'notblank' => true, 'minlength' => 8));
        $this->hasColumn('logins', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('last_login', 'timestamp', null, array('past' => true));
        $this->hasColumn('password_reset_token', 'string', '64');
        $this->hasColumn('user_type', 'integer', NULL, array('notnull' => true, 'unsigned' => true, 'default' => 0));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
    // RELATIONSHIPS
        $this->hasMany('Device', array('local' => 'user_id', 'foreign' => 'user_id'));
        $this->hasOne('Location', array('local' => 'location_id', 'foreign' => 'location_id'));
        // File is not loaded yet during this phase of the install.
        //$this->hasMany('File', array('local' => 'user_id', 'foreign' => 'user_id'));

        // BEHAVIORS
        $this->actAs('Timestampable');
        $this->actAs('LogIpAddress');
        $this->actAs('MultiTenant');
   }

    /**
     * Create an accessor for setting of passwords - hash them when they're set
     * NOTE: We use a Kohana function here, so this assumes Kohana is running! Could be dangerous...
     */
    public function setPassword($password)
    {
        if ($password) {
            // We need to tmp provide an un-hashed pwd string for validation
            $this->unHashedPassword = $password;
            return $this->_set('password', Auth::instance()->hash_password($password));
        }
    }
}