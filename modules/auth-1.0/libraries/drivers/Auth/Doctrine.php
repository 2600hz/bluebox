<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Doctrine Auth driver.
 * Note: this Auth driver does not support roles nor auto-login.
 *
 * $Id: File.php 3917 2009-04-02 03:06:22Z zombor $
 *
 * @package    Auth
 * @author     Darren Schreiber
 * @author     K Anderson
 * @copyright  (c) 2009 Darren Schreiber
 * @license    Mozilla Public License (MPL) v1.1
 */
class Auth_Doctrine_Driver extends Auth_Driver
{
    /**
     * VERSION
     */
    const VERSION = '0.1';

    /**
     * Constructor loads the user list into the class.
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Logs a user in.
     *
     * @param   string   username
     * @param   string   password
     * @param   boolean  enable auto-login (not supported)
     * @return  boolean
     */
    public function login($username, $password, $remember, $requirePassword = TRUE)
    {
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        $user = Doctrine::getTable('User')->findOneByEmailAddress($username);

        if ((($user) AND ($user['password'] === $password)) OR $requirePassword === FALSE)
        {
            if ($user['user_type'] != User::TYPE_SYSTEM_ADMIN)
            {
                if ($user['account_id'] == NULL)
                {
                    kohana::log('error', 'User ' .$username . ' tried to login but has no account id');

                    return FALSE;
                }

                if ($user['location_id'] == NULL)
                {
                    kohana::log('error', 'User ' .$username . ' tried to login but has no location id');

                    return FALSE;
                }

                $account = Doctrine::getTable('Account')->findOneByAccountId($user['account_id']);

                if (!$account)
                {
                    kohana::log('error', 'User ' .$username . ' tried to login but has an invalid account id ' .$user['account_id']);

                    return FALSE;
                }
            }

            $user['logins'] += 1;

            $user['last_login'] = date('Y-m-d H:i:s', time());

            $user['last_logged_ip'] = $this->getRealIpAddr();

            $user->save();

            if (!empty($user['debug_level']) AND ($user['debug_level'] <= 4) AND ($user['debug_level'] >= 0))
            {
                Kohana::config_set('core.log_threshold', $user['debug_level']);
            }

            // Complete the login
            $_SESSION['user_id'] = $user['user_id'];

            return $this->complete_login($username);
        }

        // Login failed
        return FALSE;
    }

    /**
     * Forces a user to be logged in, without specifying a password.
     *
     * @param   mixed    username
     * @return  boolean
     */
    public function force_login($username)
    {
        // Complete the login
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        $this->login($username, NULL, NULL, FALSE);

        return $this->complete_login($username);
    }

    /**
     * Get the stored password for a username.
     *
     * @param   mixed   username
     * @return  string
     */
    public function password($username)
    {
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        $user = Doctrine::getTable('User')->findOneByEmailAddress($username);

        return ($user ? $user->password : FALSE);
    }

    /**
     * Generate and return a token that allows for resetting of a user's password
     * @param string $username Username of the user to reset
     * @return string Returns a token/hash that should be sent to a user via a confirmed method to allow for reset
     */
    public function resetToken($username)
    {

        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        $user = Doctrine::getTable('User')->findOneByEmailAddress($username);

        if ($user)
        {
            // Set a password reset token
            $user->password_reset_token = sha1(rand(1000000, 9999999));

            $user->save();
        } 
        else
        {
            return FALSE;
        }

        return $user->password_reset_token;
    }

    /**
     * Force a new password to be recorded, if a password reset token matches the stored token previously generated.
     * @param string $username
     * @param string $token Password token, generated and recorded by resetToken()
     * @param string $newPassword
     * @return boolean True if reset is successful
     */
    public function resetPassword($username, $token, $newPassword)
    {
        // Tokens are hashes of a large random # - they must be at least 10 chars
        if (strlen($token) < 10)
        {
            return FALSE;
        }

        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        $user = Doctrine::getTable('User')->findOneByEmailAddress($username);

        if (($user) && ($user->email_address == $username) && ($user->password_reset_token == $token))
        {
            $user->password = $newPassword;

            // Clear the token - it can only be used once
            $user->password_reset_token = '';

            $user->save();
        } 
        else
        {
            return FALSE;
        }
        
        return TRUE;
    }

    public function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   
        {
            //check ip from share internet
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   
        {
            //to check ip is pass from proxy
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

} // End Auth_File_Driver