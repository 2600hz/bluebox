<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Users
 * @author     Darren Schreiber <d@d-man.org>
 * @license    Mozilla Public License (MPL)
 */
class users
{
    protected static $user = array();

    protected static $authBypass = array(
        'user' => array(
            'login', 'index', 'logout', 'password_request', 'password_reset'
        )
    );

    /**
     * This function will determine there is a valid user on a page the requires
     * a user.  If the user does not exist then it will redirect to the login
     * page.
     *
     * @return bool
     */
    public static function redirectInvalidUser()
    {
        // If the system is installing then there is no login required
        if(Bluebox_Installer::is_installing())
        {
            Kohana::config_set('core.require_login', FALSE);
            
            return TRUE;
        }

        $session = Session::instance();
        
        // If there is no current user
        if (!self::isUserAuthentic())
        {
            $controller = strtolower(Router::$controller);

            $method = strtolower(Router::$method);

            $noAuth = FALSE;

            $controllerBypass = Event::$data->getAuthBypass();

            if (!empty($controllerBypass) AND is_array($controllerBypass))
            {
                self::$authBypass = arr::merge(self::$authBypass, array($controller => $controllerBypass));
            }

            if (!empty(self::$authBypass[$controller]))
            {
                if (in_array($method, self::$authBypass[$controller]))
                {
                    kohana::log('debug', 'The url `' .url::current() .'` is configured for auth bypass, allowing');
                    
                    $noAuth = TRUE;
                }
            }

            if (!$noAuth)
            {
                // this will redirect from the login page back to this page
                kohana::log('debug', 'This user is not authorized for `' .url::current() .'`, presenting login form');
                
                $session->set("requested_url", "/" . url::current());

                if (request::is_ajax())
                {
                    header('HTTP/1.0 401 Forbidden');

                    flush();

                    die();
                } 
                else
                {
                    url::redirect('user/login');
                }
                
                return FALSE;
            }
        }

        Event::run('bluebox.users.redirectInvalidUser');

        $user_debug = self::getAttr('debug_level');

        if ($user_debug > Kohana::config('core.log_threshold'))
        {
            self::changeDebugLevel($user_debug);
        }

        $log = sprintf('Effective user_id %s - account_id %s', self::getAttr('user_id'), self::getAttr('account_id'));

        kohana::log('debug', $log);

        $log = sprintf('Authentic user_id %s - account_id %s', self::getAuthenticAttr('user_id'), self::getAuthenticAttr('account_id'));

        kohana::log('debug', $log);

        return TRUE;
    }

    public static function isUserAuthentic()
    {
        $authentic = Auth::instance();
        
        if($authentic->logged_in())
        {
            if ((self::$user = User::getAuthenticUser()) && ($account_id = self::getAuthenticAttr('account_id')))
            {
                self::$user['Account'] = Doctrine::getTable('Account')->find($account_id, Doctrine::HYDRATE_ARRAY);

                if (self::getAttr('account_id') != $account_id)
                {
                    self::masqueradeAccount(self::getAttr('account_id'));
                }

                return TRUE;
            }

            $authentic->logout(TRUE);
        }
        
        self::$user = array();
       
        self::restoreUser();
 
        return FALSE;
    }

    public static function isAuthentic($paths)
    {
        $paths = func_get_args();

        $attr = call_user_func_array(array('self', 'getAttr'), $paths);

        $authAttr = call_user_func_array(array('self', 'getAuthenticAttr'), $paths);

        return ($attr == $authAttr) ? TRUE : FALSE;
    }

    /**
     * Returns the current user as an array
     *
     * @return array
     */
    public static function getCurrentUser($masquerade = TRUE)
    {
        if (!self::$user)
        {
            return array();
        }

        $masquerades = array();

        if ($masquerade)
        {
            $session = Session::instance();

            $masquerades = $session->get('bluebox.user.masquerades', array());
        }
        
        return arr::merge(self::$user, $masquerades);
    }

    public static function getAttr($paths)
    {
        $user = self::getCurrentUser(TRUE);
       
        if ($paths == 'full_name')
        {
            return self::getAttr('first_name') .' ' .self::getAttr('last_name');
        }

        $paths = func_get_args();

        return arr::get_array($user, $paths);
    }

    public static function getAuthenticAttr($paths)
    {
        $user = self::getCurrentUser(FALSE);

        if ($paths == 'full_name')
        {
            return self::getAuthenticAttr('first_name') .' ' .self::getAuthenticAttr('last_name');
        }

        $paths = func_get_args();

        return arr::get_array($user, $paths);
    }

    public static function masqueradeAttr($value, $paths)
    {
        $paths = func_get_args();

        array_shift($paths);

        $session = Session::instance();

        $masquerades = $session->get('bluebox.user.masquerades', array());

        arr::set_array($masquerades, $value, $paths);

        $session->set('bluebox.user.masquerades', $masquerades);
    }

    public static function masqueradeUser($user_id, $retain_type = TRUE)
    {
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);

        $user = Doctrine::getTable('User')->find($user_id, Doctrine::HYDRATE_ARRAY);

        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);

        if (!$user)
        {
            return FALSE;
        }

        if ($retain_type)
        {
            unset($user['user_type']);
        }
        
        $session = Session::instance();

        $masquerades = $session->get('bluebox.user.masquerades', array());

        $masquerades = arr::merge($user, $masquerades);
        
        $session->set('bluebox.user.masquerades', $masquerades);

        users::isUserAuthentic();
        
        return TRUE;
    }

    public static function masqueradeAccount($account_id)
    {
        $account = Doctrine::getTable('Account')->find($account_id, Doctrine::HYDRATE_ARRAY);

        if (!$account)
        {
            return FALSE;
        }

        $session = Session::instance();

        $masquerades = $session->get('bluebox.user.masquerades', array());

        $masquerades['Account'] = $account;

        $masquerades['account_id'] = $account['account_id'];

        $session->set('bluebox.user.masquerades', $masquerades);

        return TRUE;
    }

    public static function restoreUser()
    {
        $session = Session::instance();
        
        $session->set('bluebox.user.masquerades', array());
    }

    public static function restoreAccount()
    {
        $session = Session::instance();
        
        $masquerades = $session->get('bluebox.user.masquerades', array());

        unset($masquerades['Account'], $masquerades['account_id']);
        
        $session->set('bluebox.user.masquerades', $masquerades);
    }
    
    public static function changeDebugLevel($new_level = NULL)
    {
        // If the users debug_level is valid then update our threshold and
        // re-init the kohana logger so it takes the new settings
        if (($new_level <= 4) AND ($new_level >= 0))
        {
            $old_level = Kohana::config('core.log_threshold');

            Kohana::config_set('core.log_threshold', $new_level);

            Kohana::log_directory(Kohana::config('core.log_directory'));

            register_shutdown_function(array('Kohana', 'log_save'));

            Event::run('bluebox.change_debug_level', $new_level);

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Render a dropdown of users
     * 
     * Additional Data Options:
     *  nullOption = If this is a string then it is used as  the '0' option, or
     *                   if false then no such option will exist
     *
     * @param   string|array  input name or an array of HTML attributes
     * @param   string        option key that should be selected by default
     * @param   string        a string to be attached to the end of the attributes
     * @return  string
     */
    public static function dropdown($data, $selected = NULL, $extra = '')
    {
        $users = Doctrine::getTable('User')->findAll(Doctrine::HYDRATE_ARRAY);

        // standardize the $data as an array, strings default to the class_type
        if ( ! is_array($data))
        {
            $data = array('name' => $data);
        }

        // add in all the defaults if they are not provided
        $data += array(
            'nullOption' => FALSE
        );

        // see if the module wants to allow null selections
        if (!empty($data['nullOption']))
        {
            $options = array('0' => $nullOption);
        } 
        else
        {
            $options = array();
        }

        foreach ($users as $user)
        {
            $options[$user['user_id']] = $user['first_name'] . ' ' . $user['last_name'];
        }

        // set a class name to define the user dropdown
        $data = arr::update($data, 'class', ' users_dropdown');
        
        // unset any keys that are options for this method
        unset($data['nullOption']);

        // Render a dropdown using the form helper
        return form::dropdown($data, $options, $selected, $extra);
    }
}
