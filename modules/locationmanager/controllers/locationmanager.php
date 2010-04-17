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
 *
 */

/**
 * LocationManager.php - Location Management Controller Class
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage LocationManager
 */
class LocationManager_Controller extends FreePbx_Controller {
    public $writable = array('name', 'domain', 'user_id');

    protected $baseModel = 'Location';

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'My Locations'
        ))
        // Build a grid with a hidden location_id and add an option for the user to select the display columns
        ->add('location_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('name', 'Name')->add('domain', 'Domain Name/Realm')->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('locationmanager/edit', 'Edit', array(
            'arguments' => 'location_id',
            'width' => '120'
        ))->addAction('locationmanager/delete', 'Delete', array(
            'arguments' => 'location_id',
            'width' => '20'
        ));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);

        $this->view->grid = $this->grid->produce();
    }

    public function edit($id = NULL)
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Edit Location';

        $this->location = Doctrine::getTable($this->baseModel)->find($id);
        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->location) {
            // Send any errors back to the index
            $error = i18n('Unable to locate location id %1$d!', $id)->sprintf()->s();
            message::set($error, array('translate' => false, 'redirect' => Router::$controller));
            return true;
        }

        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            if ($this->formSave($this->location)) {
                url::redirect(Router_Core::$controller);
            }
        }

        // Allow our location object to be seen by the view
        $this->view->location = $this->location;

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function add()
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Add Location';

        $this->location = new $this->baseModel();
        
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            // TODO: Make this secure.

            // Force our account to be the current user's account.
            // TODO: Allow admins to override this setting so they can manage locations for other people
            // TODO: Make this automatic via the multitenant behavior
            $this->location->account_id = users::$user->Location->account_id;

            if ($this->formSave($this->location)) {
                url::redirect(Router_Core::$controller);
            }
        }

        // Allow our location object to be seen by the view
        $this->view->location = $this->location;

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function delete($id = NULL)
    {
        $this->stdDelete($id);
    }
}
