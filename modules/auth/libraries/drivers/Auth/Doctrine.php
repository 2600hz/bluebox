<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Doctrine Auth driver.
 * Note: this Auth driver does not support roles nor auto-login.
 *
 * $Id: File.php 3917 2009-04-02 03:06:22Z zombor $
 *
 * @package    Auth
 * @author     Darren Schreiber
 * @copyright  (c) 2009 Darren Schreiber
 * @license    Mozilla Public License (MPL) v1.1
 */
class Auth_Doctrine_Driver extends Auth_Driver {
    /**
     * VERSION
     */
    const VERSION                   = '0.1';

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
	public function login($username, $password, $remember)
	{
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        $user = Doctrine::getTable('User')->findOneByEmailAddress($username);
		
		if (($user) AND ($user->password === $password))
		{
			// Complete the login
			$_SESSION['user_id'] = $user->user_id;
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

        if ($user) {
            // Set a password reset token
            $user->password_reset_token = sha1(rand(1000000, 9999999));
            $user->save();
        } else {
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
    public function resetPassword($username, $token, $newPassword) {
        // Tokens are hashes of a large random # - they must be at least 10 chars
        if (strlen($token) < 10) {
            return FALSE;
        }

        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        $user = Doctrine::getTable('User')->findOneByEmailAddress($username);

        if (($user) && ($user->email_address == $username) && ($user->password_reset_token == $token)) {
            $user->password = $newPassword;
            $user->password_reset_token = ''; 	// Clear the token - it can only be used once
            $user->save();
        } else {
            return FALSE;
        }
        
        return TRUE;
    }

} // End Auth_File_Driver
