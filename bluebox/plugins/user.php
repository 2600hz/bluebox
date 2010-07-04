<?php defined('SYSPATH') or die('No direct access allowed.');

class User_Plugin extends Bluebox_Plugin
{
    public function login()
    {
        $subview = new View('user/login');
        $subview->tab = 'main';
        $subview->section = 'login';

        // What are we working with here?
        $base = $this->user;

        // Must have an initialized user model object to get started
        if (!$base)
            return FALSE;	// Nothing to do here.

        // Populate form data with whatever our database record has
        $subview->login = $base->toArray();

        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm) and isset($this->repopulateForm['login'])) {
            $subview->login = arr::overwrite($subview->login, $this->repopulateForm['login']);
        }

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function register()
    {
        $subview = new View('user/register');
        $subview->tab = 'main';
        $subview->section = 'register';

        // What are we working with here?
        $base = $this->newUser;

        // Must have an initialized user model object to get started
        if (!$base)
            return FALSE;	// Nothing to do here.

        // Populate form data with whatever our database record has
        $subview->register = $base->toArray();

        // Pass errors from the controller to our view
        $validation = Bluebox_Controller::$validation;
        foreach ($validation->errors() as $field => $error) {
            // the form helper will expect the errors based on the field name
            // of register
            $field = preg_replace('/^user/', 'register', $field, 1, $count);
            if (!empty($count)) {
                $validation->add_error($field, $error);
            }
        }
        
        // If we are coming from a previous form field/post, we want to repopulate the previous field entries again on this page so
        // that errors/etc. can be corrected, rather then lost.
        if (isset($this->repopulateForm) and isset($this->repopulateForm['register'])) {
            $subview->register = arr::overwrite($subview->register, $this->repopulateForm['register']);
            $subview->confirm_password = $this->input->post('confirm_password');
        } else
            $subview->confirm_password = '';

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function save()
    {
        // Handle only register here. Assume logins get hijacked before plugins are called.
        
        // What are we working with here?
        $base = $this->getBaseModelObject();

        $registerData = $this->input->post('register');
        $confirmPassword = $this->input->post('confirm_password');

        // If the username is supposed to match the password then let's go ahead and make them the same thing
        if (Kohana::config('core.username_is_email')) {
            $registerData['username'] = $registerData['email_address'];
        }

        // If password + password confirmation match, go forward
        if ($registerData['password'] == $confirmPassword) {
            $base->fromArray($registerData);
        }
    }
}
