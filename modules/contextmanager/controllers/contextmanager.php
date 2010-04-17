<?php  defined('SYSPATH') or die('No direct access allowed.');
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
 * Context manager
 *
 * Manages visible devices, numbers and profiles (contexts) to help with grouping items together for inbound/outbound call routing.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX
 * @subpackage Context
 */
class ContextManager_Controller extends Freepbx_Controller {

    public $baseModel = 'Context';

    /**
     * Array of writable fields in your $baseModel table
     * @var $writable array
     */
    protected $writable = array('name');

    /**
     * Method for the main page of this module
     */
    public function index()
    {
        $this->template->content = new View('generic/grid');

        $this->grid = jgrid::grid($this->baseModel, array(
                'caption' => 'Context List' ,
                'multiselect' => true
                ))
            ->add('context_id', 'ID', array('hidden' => true, 'key' => true))
            ->add('name', 'Context Name')
            ->navButtonAdd('Columns', array(
                'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }' ,
                'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png' ,
                'title' => 'Show/Hide Columns' ,
                'noCaption' => true ,
                'position' => 'first'
                ))
            ->addAction('contextmanager/edit', 'Edit Context', array(
                'arguments' => 'context_id' ,
                'width' => '200'
                ))
            ->addAction('contextmanager/delete', 'Delete Context', array(
                'arguments' => 'context_id' ,
                'width' => '200'
                )
            )
            ->navGrid(array('del' => true));

        // Allow plugins to add to the grid, too
        plugins::views($this);

        // Produces the grid markup or JSON, respectively
        $this->view->grid = $this->grid->produce();
    }

    /**
     * Method to add an item
     */
    public function add()
    {
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' a Context';

        $this->context = new $this->baseModel();
        if ($this->submitted()) {
            if ($this->formSave($this->context)) {
                url::redirect(Router_Core::$controller);
            }
        }
        
        $this->view->context = $this->context;
        plugins::views($this);
    }

    /**
     * Method to edit an item
     * @param $id integer
     */
    public function edit($id = NULL)
    {
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = ucfirst(Router::$method) .' a Context';

        $this->context = Doctrine::getTable($this->baseModel)->find($id);
        if (! $this->context) {
            Kohana::show_404();
        }

        if ($this->submitted()) {
            if ($this->formSave($this->context)) {
                url::redirect(Router_Core::$controller);
            }
        }

        $this->view->context = $this->context;
        plugins::views($this);
    }

    /**
     * Method to delete an item
     * @param $id integer
     */
    public function delete($id = NULL)
    {
        $this->stdDelete($id);
    }
}
