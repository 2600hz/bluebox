<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Core/Helpers/Users
 * @author     Darren Schreiber <d@d-man.org>
 * @license    Mozilla Public License (MPL)
 */
class users
{
    public static $user = array();

    protected static $auth = NULL;

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

        // If the users debug_level is valid then update our threshold and
        // re-init the kohana logger so it takes the new settings
        if (!empty(users::$user['debug_level']) AND (users::$user['debug_level'] <= 4) AND (users::$user['debug_level'] >= 0))
        {
            Kohana::config_set('core.log_threshold', users::$user['debug_level']);

            Kohana::log_directory(Kohana::config('core.log_directory'));

            register_shutdown_function(array('Kohana', 'log_save'));
        }

        // If the session holds a override for user_type then update this users
        // in-memory type
        if ($user_type = $session->get('user.sysadmin.user_type', FALSE))
        {
            self::$user['user_type'] = $user_type;
        }

        return TRUE;
    }

    public static function isUserAuthentic()
    {
        // get a singleton of the auth module
        if (is_null(self::$auth))
        {
            self::$auth = new Auth();
        }

        $authentic = self::$auth;

        if ($authentic->logged_in())
        {
            $userEmail = $authentic->get_user();

            self::$user = Doctrine::getTable('User')->findOneByEmailAddress($userEmail);

            if (!empty(self::$user['account_id']))
            {
                return TRUE;
            }

            // We get here only if the current user is invalid - old cookie!
            $authentic->logout(TRUE);
        }

        // Nobody logged in if we get here
        self::$user = array();
        
        return FALSE;
    }

    /**
     * Returns the current user as an array
     *
     * @return array
     */
    public static function getCurrentUser()
    {
        return (array)self::$user;
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
