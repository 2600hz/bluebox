<?php
defined('SYSPATH') or die('No direct access allowed.');
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
 * usermanager.php - User Management Controller Class
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package FreePBX3
 * @subpackage UserManager
 */
class UserManager_Controller extends FreePbx_Controller
{
    protected $writable = array(
        'first_name',
        'last_name',
        'location_id',
        'email_address',
        'password'
    );

    protected $readable = array(
        'user_id',
        'first_name',
        'location_id',
        'last_name',
        'email_address'
    ); // No reading passwords
    protected $baseModel = 'User';

    public function index()
    {
        $this->template->content = new View('generic/grid');
        $this->grid = jgrid::grid($this->baseModel, array(
            'caption' => 'Users',
            'multiselect' => true
        ))->add('user_id', 'ID', array(
            'hidden' => true,
            'key' => true
        ))->add('email_address', 'Email Address')->add('first_name', 'First Name', array(
            'width' => '100',
            'search' => true,
        ))->add('last_name', 'Last Name', array(
            'width' => '100',
            'search' => true,
        ))->add('Location/name', 'Location', array(
            'width' => '100',
            'search' => true,
            'sortable' => true
        ))->add('account_type', 'Account Type')->addAction('usermanager/login', 'Login', array(
            'arguments' => 'user_id'
        ))->addAction('usermanager/edit', 'Edit', array(
            'arguments' => 'user_id'
        ))->addAction('usermanager/delete', 'Delete', array(
            'arguments' => 'user_id'
        ))->navGrid(array(
            'del' => true
        ));
        // Let the plugins add to the grid!
        plugins::views($this);
        // Produces the grid markup or JSON
        $this->view->grid = $this->grid->produce();
    }
    public function edit($id = NULL)
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Edit User';

        $this->user = Doctrine::getTable('User')->find($id);
        $this->password = $this->confirm_password = '';
        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->user) {
            // Send any errors back to the index
            $error = i18n('Unable to locate user id %1$d!', $id)->sprintf()->s();
            message::set($error, array(
                'translate' => false,
                'redirect' => Router::$controller . '/index'
            ));
            return true;
        }
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            if (empty($_POST['user']['password'])) {
                unset($_POST['user']['password']);
            } else {
                $this->password = $_POST['user']['password'];
                $this->confirm_password = $_POST['user']['confirm_password'];
                $rules = FreePbx_Controller::$validation;
                $rules->add_callbacks('password', array(
                    $this,
                    '_strong_pwd'
                ));
                $rules->add_callbacks('password', array(
                    $this,
                    '_match_pwd'
                ));
            }
            if ($this->formSave($this->user)) {
                url::redirect(Router_Core::$controller);
            }
        }
        // Since the confirm_password doesnt exist in the table handle it specially
        $this->view->confirm_password = $this->confirm_password;
        // The password will returned hashed and we need plain text....
        $this->view->password = $this->password;
        // Set our own view variables from the DB records.
        $this->view->user = $this->user;
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }
    public function add()
    {
        // Overload the update view
        $this->template->content = new View(Router::$controller . '/update');
        $this->view->title = 'Add User';

        $this->password = $this->confirm_password = '';
        $this->user = new User();
        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            $this->password = $_POST['user']['password'];
            $this->confirm_password = $_POST['user']['confirm_password'];
            // Username is not visible in the view yet but is required. Set to email_address for now.
            $this->user->username = $_POST['user']['email_address'];
            $rules = FreePbx_Controller::$validation;
            $rules->add_callbacks('password', array(
                $this,
                '_strong_pwd'
            ));
            $rules->add_callbacks('password', array(
                $this,
                '_match_pwd'
            ));
            if ($this->formSave($this->user)) {
                url::redirect(Router_Core::$controller);
            }
        }
        // Since the confirm_password doesnt exist in the table handle it specially
        $this->view->confirm_password = $this->confirm_password;
        // The password will returned hashed and we need plain text....
        $this->view->password = $this->password;
        // Set our own view variables from the DB records.
        $this->view->user = $this->user;
        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }
    public function delete($id = NULL)
    {
       $this->stdDelete($id);
    }
    public function _match_pwd(Validation $array, $field)
    {
        if ($_POST['user']['password'] != $_POST['user']['confirm_password']) {
            $array->add_error('user[confirm_password]', 'nomatch');
        }
    }
    public function _strong_pwd(Validation $array, $field)
    {
        $enforce = Kohana::config('core.pwd_complexity');
        if (empty($enforce)) return true;
        if (!preg_match('/[0-9]{1,}/', $_POST['user']['password'])) { // at least one digit
            $array->add_error('user[password]', 'nodigits');
        }
        if (!preg_match('/[A-Za-z]{1,}/', $_POST['user']['password'])) { // at least one character
            $array->add_error('user[password]', 'noalpha');
        }
    }
    public function login($userId)
    {
        $user = Doctrine::getTable('User')->findOneByUserId($userId, Doctrine::HYDRATE_ARRAY);
        if (!$user) {
            url::redirect('/');
        }
        Auth::instance()->force_login($user['email_address']);
        url::redirect('/');
    }
}
