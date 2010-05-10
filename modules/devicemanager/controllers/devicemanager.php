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
* K Anderson
*
*/
/**
 * devicemanager.php - Device Management Controller Class
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage DeviceManager
 */
class DeviceManager_Controller extends FreePbx_Controller
{
    public $writable = array(
        'class_type',
        'name',
        'context_id',
        'user_id'
    );
    
    protected $baseModel = 'Device';

    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Build a grid with a hidden device_id, class_type, and add an option for the user to select the display columns
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'My Devices',
            'multiselect' => true
        ))->add('device_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('name', 'Name')->add('class_type', 'Type', array(
            'width' => '50',
            'search' => false,
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => TRUE,
            'position' => 'first'
        ))->addAction('devicemanager/edit', 'Edit', array(
            'arguments' => 'device_id',
            'width' => '120'
        ))->addAction('devicemanager/delete', 'Delete', array(
            'arguments' => 'device_id',
            'width' => '20'
        ))->navGrid(array('del' => TRUE));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON, respectively
        $this->view->grid = $this->grid->produce();
    }

    public function edit($id = NULL)
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Edit Device';

        $this->device = Doctrine::getTable($this->baseModel)->find($id);
        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->device) {
            // Send any errors back to the index
            $error = i18n('Unable to locate device id %1$d!', $id)->sprintf()->s();
            message::set($error, array(
                'translate' => FALSE,
                'redirect' => Router::$controller
            ));
            return TRUE;
        }

        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            // Force rebuild of dialplan for these numbers 
            $this->device->_dirtyNumbers = array('type' => 'DeviceNumber', 'id' => 'device_id');

            if ($this->formSave($this->device)) {
                 url::redirect(Router_Core::$controller);
            }
        }
        // Allow our device object to be seen by the view
        $this->view->device = $this->device;
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function add()
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Add Device';

        $this->device = new $this->baseModel();
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            // TODO: Make this secure.
            $classType = $_POST['device']['class_type'];
            $this->device = new $classType;
            if ($this->formSave($this->device)) {
                url::redirect(Router_Core::$controller);
            }
        }
        // Allow our device object to be seen by the view
        $this->view->device = $this->device;
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }
    
    public function delete($id = NULL)
    {
        $this->stdDelete($id);
    }
}
