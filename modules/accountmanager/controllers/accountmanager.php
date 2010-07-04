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
* All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
*
*
*/
/**
 * AccountManager.php - Account Management Controller Class
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package Bluebox
 * @subpackage AccountManager
 */
class AccountManager_Controller extends Bluebox_Controller
{
    public $writable = array(
        'name',
        'basedata',
        'sampledata',
        'user_id'
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
        // Account management is a bit "deeper" then most functions, so we do NOT utilize formSave() and instead
        // we use a custom version of that, and just about everything else

        // Overload the add view
        $this->view->title = 'Add Account';
        $this->view->errors = array();
        $this->account = new Account();

        if ($_POST) {
            $this->account->fromArray($this->input->post('account'));

            // Process all posted form fields manually
            $username = $this->input->post('account_username', '');
            $password = $this->input->post('account_password', '');
            $domain = $this->input->post('account_domain', '');
            $url = $this->input->post('account_url', '');
                //if (!filter_var('http://' . $domain, FILTER_VALIDATE_URL)) {
                //if (!filter_var($url, FILTER_VALIDATE_URL)) {
        } else {
            $username = $password = $domain = $url = '';
        }

        // Save data and all relations
        if ($this->submitted()) {
            // Check for unique domain name
            Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
            Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
            if (Doctrine::getTable('Location')->findOneBy('domain', $domain)) {
                message::set('You must specify a unique domain name.');
            } elseif ((!$username) or (!$password)) {
                message::set('Username and password are required.');
            } else {
                // Looks good.
                try {
                    // Allow plugins to process any form-related data we just got back and attach to our data object
                    plugins::save($this);

                    $options = array();
                    $options['account'] = $this->account->toArray();
                    $options['location'] = array('name' => 'New Location', 'domain' => $domain);
                    $options['user'] = array('username' => $username, 'password' => $password);
                    Bluebox_Tenant::initializeTenant($options);
                    // TODO: Create a site based on URL...

                    // Success - display a custom save message, or a generalized one using the class name
                    message::set('New tenant created!', array(
                        'type' => 'success'
                    ));

                    url::redirect(Router_Core::$controller);
                }
                catch(Doctrine_Connection_Exception $e) {
                    message::set('Doctrine error: ' . $e->getMessage());
                }
                catch(Bluebox_Exception $e) {
                    kohana::log('alert', $e->getMessage());
                    // Note that $this->view->errors is automatically populated by Bluebox_Record
                    message::set('Please correct the errors listed below.');
                }
                catch (Exception $e) {
                    message::set($e->getMessage());
                }
            }
        }
        
        // Tell other plugins that we want to repopulate form fields with the invalid data
        // these fields will not auto-populate
        $this->view->account_username = $username;
        $this->view->account_password = $password;
        $this->view->account_domain = $domain;
        $this->view->account_url = $url;

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
