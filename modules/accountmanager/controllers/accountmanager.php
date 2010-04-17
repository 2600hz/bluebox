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
 * AccountManager.php - Account Management Controller Class
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage AccountManager
 */
class AccountManager_Controller extends FreePbx_Controller
{
    public $writable = array(
        'name',
        'basedata',
        'sampledata',
        'user_id',
        'expire'
    );

    protected $baseModel = 'Account';
    
    public function index()
    {
        $this->template->content = new View('generic/grid');
        // Build a grid with a hidden account_id and add an option for the user to select the display columns
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'Accounts'
        ))->add('account_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('name', 'Name')->navButtonAdd('Columns', array(
            'onClickButton' => 'function () {  $(\'#{table_id}\').setColumns(); }',
            'buttonimg' => url::base() . 'assets/css/jqGrid/table_insert_column.png',
            'title' => 'Show/Hide Columns',
            'noCaption' => true,
            'position' => 'first'
        ))->addAction('accountmanager/edit', 'Edit', array(
            'arguments' => 'account_id',
            'width' => '120'
        ))->addAction('accountmanager/delete', 'Delete', array(
            'arguments' => 'account_id',
            'width' => '20'
        ))->navGrid(array(
            'del' => true
        ));
        // dont foget to let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON, respectively
        $this->view->grid = $this->grid->produce();
    }

    public function edit($id = NULL)
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Edit Account';
        $this->account = Doctrine::getTable('Account')->find($id);
        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->account) {
            // Send any errors back to the index
            $error = i18n('Unable to locate account id %1$d!', $id)->sprintf()->s();
            message::set($error, array(
                'translate' => false,
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            if ($this->formSave($this->account)) {
                url::redirect(Router_Core::$controller);
            }
        }
        // Allow our account object to be seen by the view
        $this->view->account = $this->account;
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function add()
    {
        // Overload the add view
        $this->view->title = 'Add Account';
        $this->view->errors = array();
        $this->account = new Account();
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            // TODO: Make this secure.
            if ($this->formSave($this->account)) {
                url::redirect(Router_Core::$controller);
            }
        }

        // Allow our account object to be seen by the view
        $this->view->account = $this->account;
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    public function delete($id = NULL)
    {
        $this->stdDelete($id);
    }
}
