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
    
    public $supportedTrunkTypes = array();

    public function index()
    {
        $this->template->content = new View('generic/grid');

        // Setup the base grid object
        $grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Trunks/Gateways',
            )
        );

        // Add the base model columns to the grid
        $grid->add('trunk_id', 'ID', array(
                'hidden' => true,
                'key' => true
            )
        );
        $grid->add('name', 'Trunk Name');
        $grid->add('type', 'Type', array(
                'width' => '50',
                'search' => false,
            )
        );

        // Add the actions to the grid
        $grid->addAction('trunkmanager/edit', 'Edit', array(
                'arguments' => 'trunk_id'
            )
        );
        $grid->addAction('trunkmanager/delete', 'Delete', array(
                'arguments' => 'trunk_id'
            )
        );

        // Let plugins populate the grid as well
        $this->grid = $grid;
        plugins::views($this);

        // Produce a grid in the view
        $this->view->grid = $this->grid->produce();
    }

    public function prepareUpdateView()
    {
        parent::prepareUpdateView();

        $this->view->supportedTrunkTypes = $this->supportedTrunkTypes;
    }

    protected function save_succeeded(&$object)
    {
        //netlists::addToTrunkAuto($object);
        
        parent::save_succeeded($object);
    }

    protected function delete_succeeded(&$object)
    {
        //netlists::addToTrunkAuto($object);

        parent::delete_succeeded($object);
    }
}