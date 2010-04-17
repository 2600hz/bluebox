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
 * locationmanager.php - A unified LocationManager plugin for supporting caller ID settings in:
 *  - devices
 *  - users
 *  - trunks
 *  - anyone else who asks for it (via hooks)
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage LocationManager
 */
class LocationManager_Plugin extends FreePbx_Plugin
{
    protected $baseModel = 'Location';
    public function index()
    {
        $subview = new View('generic/grid');
        $subview->tab = 'main';
        $subview->section = 'locations';
        // What are we working with here?
        $base = $this->getBaseModelObject();
        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
            'caption' => 'Locations'
        ));
        // If there is a base model that contains an account_id, then we want to show locations only that relate to this account
        if ($base and $base->account_id) {
            Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
            // Set a where clause, if we're playing plug-in to someone else
            $grid->where('account_id = ', $base->account_id);
        }
        // Build a grid with a hidden location_id and add an option for the user to select the display columns
        $grid->add('location_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('name', 'Name')->add('domain', 'Domain Name/Realm')->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ));
        // Produces the grid markup or JSON
        $subview->grid = $grid->produce();
        // Add our view to the main application
        $this->views[] = $subview;
    }
}
