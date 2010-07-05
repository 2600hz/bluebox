<?php
defined('SYSPATH') or die('No direct access allowed.');
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
* All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
*
* Contributor(s):
* K Anderson
* Reto Haile <reto.haile@selmoni.ch>
*
*/
/**
 * trunkmanager.php - Trunk Management Controller Class
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage Trunk
 */
class TrunkManager_Controller extends Bluebox_Controller
{
    protected $baseModel = 'Trunk';
    protected $writable = array(
        'name',
        'server',
        'class_type',
        'context_id'
    );
    public $supportedTypes = array();
    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Build a grid with a hidden trunk_id, trunk info, and add an option for the user to select the display columns
        $this->grid = jgrid::grid('Trunk', array(
            'caption' => 'Trunks/Gateways',
            'multiselect' => true
        ))->add('trunk_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('name', 'Trunk Name')->add('class_type', 'Type', array(
            'width' => '50',
            'search' => false,
        ))->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('trunkmanager/edit', 'Edit Trunk', array(
            'arguments' => 'trunk_id',
            'width' => '120'
        ))->addAction('trunkmanager/delete', 'Delete Trunk', array(
            'arguments' => 'trunk_id',
            'width' => '120'
        ))->navGrid(array(
            'del' => true
        ));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON, respectively
        $this->view->grid = $this->grid->produce();
    }
    public function add()
    {
        // Use the edit view here, too
        $this->view->title = 'Add a Trunk';

        $this->trunk = new Trunk();
        // get trunk related form data
        $frmDataTrunk = $this->input->post('trunk');
        // check if we have a class type
        if (!empty($frmDataTrunk['class_type'])) {

            $this->trunk = new $frmDataTrunk['class_type'];
            $this->trunk->class_type = $frmDataTrunk['class_type'];
            // TODO: This code needs review. We should really be able to use the same view.
            $this->view = new View('trunkmanager/edit');
            $this->view->title = 'Add a Trunk';
            
            // Are we supposed to be saving stuff? (received a form post?)
            if (!empty($_POST['edit_form']) && $this->submitted()) {
                if ($this->formSave($this->trunk)) {
                    netlists::addToTrunkAuto($this->trunk);
                    url::redirect(Router_Core::$controller);
                }
            }
        }
        // Allow our trunk object to be seen by the view
        $this->view->trunk = $this->trunk;
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
        $this->view->supportedTypes = $this->supportedTypes;
    }
    public function edit($id)
    {
        $this->view->title = 'Edit Trunk';
        
        $this->trunk = Doctrine::getTable('Trunk')->find($id);
        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->trunk) {
            $error = i18n('Unable to locate trunk id %1$d!', $id)->sprintf()->s();
            message::set($error, array(
                'translate' => false,
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            if ($this->formSave($this->trunk)) {
                netlists::addToTrunkAuto($this->trunk);
                url::redirect(Router_Core::$controller);
            }
        }
        // Allow our trunk object to be seen by the view
        $this->view->trunk = $this->trunk;
        // dont foget to let the plugins add to the grid!
        plugins::views($this);
    }
    public function delete($id = NULL)
    {
        $this->stdDelete($id);
    }
}
