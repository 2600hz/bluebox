<?php
/**
 * Description of accountmanager
 *
 * @author kanderson
 */
class AccountManager_Plugin extends FreePbx_Plugin {
    
    public function createAccountAdmin() {

        $base = $this->getBaseModelObject();

        $email = $this->input->post('account_email', '');
        $passwd = $this->input->post('account_password', '');
        $domain = $this->input->post('account_domain', '');

        // these fields will not auto-populate
        $this->view->account_email = $email;
        $this->view->account_password = $passwd;
        $this->view->account_domain = $domain;
        
        $user = new User();
        $user->first_name = 'Admin';
        $user->last_name = 'User';
        $user->email_address = $email;
        $user->username = $email;
        $user->password = $passwd;

        $user->Location = new Location();
        $user->Location->name = 'Main Location';
        $user->Location->domain = $domain;

        $user->Location->Account = $base;
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        $user->Account = $user->Location->Account;

        try {
            $user->save();
        } catch (FreePbx_Validation_Exception $e) {

            $errors = FreePbx_Controller::$validation->errors();

            // remap errors back to the fields
            if (!empty($errors['user[username]'])) {
                FreePbx_Controller::$validation->add_error('account_email', $errors['user[username]']);
            }
            if (!empty($errors['user[password]'])) {
                FreePbx_Controller::$validation->add_error('account_password', $errors['user[password]']);
            }
            if (!empty($errors['location[domain]'])) {
                FreePbx_Controller::$validation->add_error('account_domain', $errors['location[domain]']);
            }

            // NOTE: the gotcha of doing this is if the user model fails validation
            // the base model IS NOT CHECKED so after correcting a field error on
            // the user email, password, or domain you could still get an error elsewhere....
            throw new FreePbx_Validation_Exception($e->getMessage());
            return FALSE;
        }

        return TRUE;
    }

    public function runTenantSetup() {
        if (Router::$method != 'add') {
            return TRUE;
        }

        $packages = FreePbx_Installer::listPackages();

        foreach($packages as $name => $package) {
            if (empty($package['installedAs'])) {
                unset($packages[$name]);
            } else {
                $packages[$name]['action'] = 'newTenant';
            }
        }

        FreePbx_Installer::_dependencySort($packages);

        foreach($packages as $name => $package) {
            $instance = new $package['configureClass'];

            try {
                $result = call_user_func(array(
                    $instance,
                    'newTenant'
                ) , $package);
            }
            catch(Exception $e) {
            }
        }
    }

    public function removeAssociated() {
        $base = Event::$data;

        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        $users = Doctrine::getTable('User')->findByAccountId($base->account_id);
        foreach($users as $user) {
            $user->delete();
        }

        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        $locations = Doctrine::getTable('Location')->findByAccountId($base->account_id);
        foreach($locations as $location) {
            $location->delete();
        }
    }
}
?>
