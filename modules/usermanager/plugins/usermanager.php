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
 * usermanager.php - usermanager plugin class
 * Created on Jun 21, 2009
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage UserManager
 */
class UserManager_Plugin extends FreePbx_Plugin
{
    protected $baseModel = 'User';

    /**
     * This is a callback for the grid, used to put the first and last name together
     *
     * @return string New value for the cell
     * @param object $cell This will always be empty because there is no actual table value for this cell
     * @param object $first_name The first name per row
     * @param object $last_name The last name per row
     */
    public function fullName($cell, $first_name, $last_name)
    {
        return $first_name . ' ' . $last_name;
    }

    public function location($cell, $user_id) {
	$User = Doctrine::getTable('User')->find($user_id);
        return $User->Location->name; 
    }

    public function accountType($accountTypeId)
    {
        if ($accountTypeId == 1) {
            return 'Beta User';
        } else {
            return 'Normal User';
        }
    }

    public function index()
    {
        $this->grid->add('User/full_name', 'Associated User', array(
            'width' => '100',
            'align' => 'left',
            'callback' => array(
                'function' => array(
                    $this,
                    'fullName'
                ) ,
                'arguments' => array(
                    'User/first_name',
                    'User/last_name'
                )
            ) ,
            'link' => array(
                'link' => 'usermanager/edit',
                'arguments' => 'user_id'
            ) ,
            'search' => false,
            'sortable' => false
        ));  

        $this->grid->add('User/location', 'Location', array(
            'width' => '100',
            'align' => 'left',
            'callback' => array(
                'function' => array(
                    $this,
                    'location'
                ) ,
                'arguments' => array(
                    'user_id'
                )
            ) ,
            'search' => false,
            'sortable' => false
        ));  
     }

    public function edit()
    {
        $subview = new View('generic/grid');
        $subview->tab = 'main';
        $subview->section = 'users';

        // What are we working with here?
        $base = $this->getBaseModelObject();

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
            'caption' => 'Users'
        ));

        // By default, show locations column
        $hideLocation = false;

        // If there is a base model that contains an account_id, then we want to show locations only that relate to this account
        if ($base and $base->location_id) {
            // Set a where clause, if we're playing plug-in to someone else
            $grid->where('location_id = ', $base->location_id);
            $hideLocation = true;	// Hide location by default, if we're already searching by location
        }

        // Build a grid with a hidden location_id and add an option for the user to select the display columns
        $grid->add('user_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('full_name', 'Associated User', array(
            'width' => '100',
            'align' => 'left',
            'callback' => array(
                'function' => array(
                    $this,
                    'fullName'
                ) ,
                'arguments' => array(
                    'first_name',
                    'last_name'
                )
            ) ,
            'link' => array(
                'link' => 'usermanager/edit',
                'arguments' => 'user_id'
            ) ,
            'search' => false,
            'sortable' => false
        ))->add('location', 'Location', array(
            'hidden' => $hideLocation,
            'width' => '100',
            'align' => 'left',
            'callback' => array(
                'function' => array(
                    $this,
                    'location'
                ) ,
                'arguments' => array(
                    'user_id'
                )
            ) ,
            'search' => false,
            'sortable' => false
        ))->add('email_address', 'Email')->add('last_login', 'Last Login')->add('account_type_name', 'Account Type', array(
            'width' => '100',
            'callback' => array(
                'function' => array(
                    $this,
                    'accountType'
                ) ,
                'arguments' => array(
                    'account_type'
                )
            )
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('usermanager/edit', 'Edit User', array(
            'arguments' => 'user_id',
            'width' => '120'
        ))->addAction('usermanager/delete', 'Delete User', array(
            'arguments' => 'user_id',
            'width' => '20'
        ));

        // Produces the grid markup or JSON
        $subview->grid = $grid->produce();

        // Add our view to the main application
        $this->views[] = $subview;
    }
}

