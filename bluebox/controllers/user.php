<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/User
 * @author     Darren Schreiber <d@d-man.org>
 * @license    Mozilla Public License (MPL)
 */
class User_Controller extends Bluebox_Controller
{
    protected $baseModel = 'User';

    /**
     * Locally used authentication class
     * @var Authentic
     */
    private $authentic;

    public function __construct() {
        parent::__construct();

        $this->authentic = new Auth();
    }

    /**
     * Check for duplicate email addresses
     * @param Validation $array Validation hash from Kohana's callbacks for validation
     * @param string $field Field name we are checking for duplicates
     */
    public function _dupe_email(Validation $array, $field)
    {
        // Does this user already have a password? If so, we can assume the account exists already
        if (Auth::instance()->driver->password($array['email_address']))
            $array->add_error($field, 'duplicate');
    }

    public function _strong_pwd(Validation $array, $field)
    {
        if (!preg_match('`[0-9]`',$array[$field])) { // at least one digit
            $array->add_error($field, 'nodigits');
        }
        if (!preg_match('`[A-Za-z]`',$array[$field])) { // at least one character
            $array->add_error($field, 'noalpha');
        }
    }

    public function _match_pwd(Validation $array, $field)
    {
        if ($array['password'] != $array['password2'])
            $array->add_error($field, 'nomatch');
    }

    private function _redirectPriorPage()
    {
        // FIXME: This is a hack, but it does work. For now.
        switch (CONTENT_TYPE) {
            case 'json':
                $authentic = new Auth;
                $userEmail = $authentic->get_user();
                $this->user = Doctrine::getTable('User')->findOneByEmailAddress($userEmail, Doctrine::HYDRATE_ARRAY); //now you have access to user information stored in the database
                unset($this->user['password']);
                echo json_encode(array('success' => TRUE, 'user' => $this->user));
                exit();
                break;
            case 'xml' :
                exit();
                break;
        };

        // Redirect to the last page they were on and delete the record of that page
        if ($this->session->get('requested_url')) {
            url::redirect($this->session->get_once('requested_url'));
        } else {
            url::redirect(Kohana::config('routes._default'));
        }
    }

    private function _redirectIfLoggedIn(Auth $authentic)
    {
        // See if the user is already logged in
        if ($authentic->logged_in()) {
            $this->user = $authentic->get_user(); // Load the user's info into the current class, in case it's needed

            $this->_redirectPriorPage();
        }
    }

    public function login()
    {
        $this->_redirectIfLoggedIn($this->authentic);

        // Are we supposed to be using a combined view for logins and registrations? If so, go there instead
        if (Kohana::config('core.combine_login_register')) {
            url::redirect('/user/index');
        }

        // Do we have user information to try and validate?
        if (sizeof($this->input->post()) != 0) {
            $this->_redirectPriorPage();
        }

        // Make a container view for this page. We don't do anything special so no need to display anything
        $this->template->content = new View('user/login_container');
        // This page is really just a dummy page for the login plugin. No logic here other then above
    }

    public function register()
    {
        $this->_redirectIfLoggedIn($this->authentic);

        // Are we supposed to be using a combined view for logins and registrations? If so, go there instead
        if (Kohana::config('core.combine_login_register')) {
            url::redirect('/user/index');
        }

        // Make a container view for this page. We don't do anything special so no need to display anything
        $this->template->content = new View('user/login_container');


        // Make the user record available to plugins
        $this->newUser = new User();

        // Are we supposed to be saving stuff? (received a form post?)
        if (sizeof($this->input->post()) != 0) {
            // TODO: Don't do this. Do something more intelligent.
            $this->user->Location = new Location();
            $this->user->Location->name = 'New Location';
            $this->user->Location->Account = new Account();
            $this->user->Location->Account->name = 'New Account';
            //$this->user->Location->save();
            
            // TODO: Make this secure.
            if ($this->formSave($this->user)) {
                $this->_redirectPriorPage();
            }
        }

        // Allow our user object to be seen by the view
	$this->view->user = $this->user;

        // This page is really just a dummy page for the login plugin. No logic here other then above

        // Execute plugin hooks here
        plugins::views($this);
    }

    public function index()
    {
        $this->_redirectIfLoggedIn($this->authentic);

        // If we get to this page but dual-reg is NOT enabled, assume we start at the login page
        if (!Kohana::config('core.combine_login_register'))
            url::redirect('/user/login');

        // Make a container view for this page. We don't do anything special so no need to display anything
        $this->template->content = new View('user/login_container');

        $this->view->title = __('Registration / Login');

        // This page is really just a combination of login & register, which are plug-ins. Aside from initializing a base user object, we're done!

        // Make the user record available to plugins
        $this->user = new User();

        // Make a dummy newUser record for new users
        $this->newUser = new User();

        // Are we supposed to be attempting a login or a registration? Note that this is a bit different then most controllers
        // The registration plugin populates the user field on registration, while the login plugin just sets the current user up
        if (sizeof($this->input->post()) != 0) {
            if (isset($_POST['action']) and ($_POST['action'] == 'Login')) {
                if (Kohana::config('core.username_is_email')) {
                    $login = $this->authentic->login($_POST['login']['email_address'], $_POST['login']['password']);
                } else {
                    $login = $this->authentic->login($_POST['login']['username'], $_POST['login']['password']);
                }
                if ($login) {
                    // Redirect to the last page they were on and delete the record of that page
                    $this->_redirectPriorPage();
                } else {
                    message::set('Login incorrect. Please try again.');

                    // Tell other plugins that we want to repopulate form fields with the invalid data
                    $this->repopulateForm = $this->input->post();
                }
            } elseif (isset($_POST['action']) and ($_POST['action'] == 'Register')) {
                // TODO: Don't do this. Do something more intelligent.
                $this->user->Location = new Location();
                $this->user->Location->name = 'New Location';
                $this->user->Location->Account = new Account();
                $this->user->Location->Account->name = 'New Account';

                $_POST['user'] = $_POST['register'];        // Dirty hack

                if ($this->formSave($this->user)) {

                    Auth::instance()->login($this->user['username'], $_POST['register']['password']);
                    $this->_redirectPriorPage();
                }
            }
        }

        $this->view->user = $this->user;

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);
    }

    // Logout from an account
    function logout()
    {
        Auth::instance()->logout();
        $this->session->destroy();
        url::redirect(Kohana::config('routes._default')); // back to main page
    }


    // Update a user's profile
    public function profile()
    {
        // We use a different base model and writable fields for this controller
        $this->writable = array('first_name', 'last_name', 'email_address');
        $this->baseModelObject = 'User';

        // Overload the update view
        $this->template->content = new View(Router::$controller . '/profile');

	$this->user = Doctrine::getTable('User')->findOneByUserId($this->user->user_id);
        $this->new_password = $this->confirm_password = '';

        // Was anything retrieved? If no, this may be an invalid request
        if (!$this->user) {
            message::set('Unable to locate User');
        }

        // Are we supposed to be saving stuff? (received a form post?)
        if ($this->submitted()) {
            $hashedPassword = Auth::instance()->hash_password($_POST['user']['old_password'], Auth::instance()->find_salt($this->user->password));
            if (empty($_POST['user']['old_password'])) {
                message::set('Old password is empty');
            } elseif (empty($_POST['user']['new_password'])) {
                message::set('new password is empty');
            } elseif (empty($_POST['user']['confirm_password'])) {
                message::set('You need to confirm your password');
            } elseif ($_POST['user']['confirm_password'] != $_POST['user']['new_password']) {
                message::set('Your passwords did not match.');
            } elseif ($hashedPassword != $this->user->password) {
                // Password mismatch
                message::set('Old password is wrong');
            } else {
                $this->new_password = $_POST['user']['new_password'];
                $this->confirm_password = $_POST['user']['confirm_password'];

                $rules = Bluebox_Controller::$validation;
                //$rules->add_callbacks('new_password', array($this, '_strong_pwd'));
                $this->formSave($this->user);
            }

        }

        $this->view->old_password = '';

        // Since the confirm_password doesnt exist in the table handle it specially
        $this->view->confirm_password = $this->confirm_password;

        // The password will returned hashed and we need plain text....
        $this->view->new_password = $this->new_password;

        // Set our own view variables from the DB records.
        $this->view->user = $this->user;

        // Execute plugin hooks here, after we've loaded the core data sets
        plugins::views($this);

    }


    // User forgot password, requesting reminder token
    function password_request()
    {
        $form = array
        (
            'email_address'    => ''
        );

        //  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
        $errors = $form;

        // Has the form been submitted?
        if ($_POST) {
            $post = new Validation($_POST);
            $post->add_rules('email_address','required', 'email');

            // Add some filters
            $post->pre_filter('trim', TRUE);

            if ($post->validate()) {
                $token = Auth::instance()->driver->resetToken($post['email_address']);

                // Were we successful in finding the user and setting a token?
                if ($token) {
                    // Send email with the new token

                    // TODO: FIX ME! This should be moved to a template/view, not here!!!

                    $to = $post['email_address'];
                    $from = 'noreply@store.bluebox.com';
                    $subject = 'Password reset';
                    $body = <<<TEXT
<H2>Password Reset Request</H2>
<BR>
You, or someone pretending to be you, requested a password reset on our website.<BR>
<BR>
To reset your password, go to <a href="https://store.bluebox.com/store/user/password_reset">https://store.bluebox.com/store/user/password_reset</a> and follow the directions.<BR>
<BR>
<B>You will be asked for this token:</B> $token<BR>
<BR>
<BR>
Thank you!<BR>
&nbsp; &nbsp;   - the Bluebox team<BR>
<BR>
TEXT;
                    email::send($to, $from, $subject, $body, true);

                    // Redirect training
                    url::redirect('/user/password_reset');
                } else {
                    // Unknown user
                    $post->add_error('email_address', 'unknown');
                }
            }

            if (!$post->validate()) {
                // Errors in validation

                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                // We need to already have created an error message file, for Kohana to use
                // Pass the error message file name to the errors() method
                $errors = arr::overwrite($errors, $post->errors('error_messages'));
            }
        }

        $this->view->form = $form;
        $this->view->errors = $errors;
    }

    // Reset password (requires token from request password)
    function password_reset()
    {
        $email = $this->input->post('email_address');
        $token = $this->input->post('token');
        $password = $this->input->post('password');

        $form = array
        (
            'email_address'    => '',
            'token'     => '',
            'password'  => ''
        );

        //  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
        $errors = $form;

        // Has the form been submitted?
        if ($_POST) {
            $post = new Validation($_POST);
            $post->add_rules('email_address','required', 'email');
            $post->add_rules('token', 'required', 'length[10,60]');
            $post->add_rules('password', 'required', 'length[5,20]');

            // Add some rules, the input field, followed by a list of checks, carried out in order
            $post->add_callbacks('password', array($this, '_strong_pwd'));
            $post->add_rules('*', 'required');

            // Add some filters
            $post->pre_filter('trim', TRUE);

            if ($post->validate()) {
                // We have all required fields - is this a valid reset request?
                if (Auth::instance()->driver->resetPassword($email, $token, $password)) {
                    // Show confirmation of reset page and offer link to login again
                    $this->template->content = new View(Router::$controller . '/password_reset_confirm');
                } else {
                    // Unknown user
                    $post->add_error('email_address', 'unknown');
                }
            }

            if (!$post->validate()) {
                // Errors in validation

                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                // We need to already have created an error message file, for Kohana to use
                // Pass the error message file name to the errors() method
                $errors = arr::overwrite($errors, $post->errors('error_messages'));
            }
        }

        $this->view->form = $form;
        $this->view->errors = $errors;
    }
}
