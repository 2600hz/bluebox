<?php defined('SYSPATH') or die('No direct access allowed.');
class Bluebox_Tenant
{
    public static $accountName = NULL;
    
    public static function initializeAccount($options) {
        $account = new Account();
        $account->name = (isset($options['name']) ? $options['name'] : 'New Account');
        $account->save();

        return $account->account_id;
    }

    public static function initializeLocation($accountId, $options) {
        // Figure out the proper domain name. We could do a lot more here...
        // Is this a real domain name? If not, use the IP address instead, by default, or initial setup is a pain
        $domain = (isset($options['domain']) ? $options['domain'] : network::guessServerIP());
        
        $location = new Location();
        $location->account_id = $accountId;
        $location->name = (isset($options['name']) ? $options['name'] : 'New Location');
        $location->domain = $domain;
        $location->save();

        return $location->location_id;
    }

    public static function initializeUser($accountId, $locationId, $options) {
        $user = new User();
        $user->first_name = (isset($options['first']) ? $options['first'] : 'Admin');
        $user->last_name = (isset($options['last']) ? $options['last'] : 'Admin');
        $user->username = (isset($options['username']) ? $options['username'] : 'admin@localhost');
        $user->email_address = (isset($options['email']) ? $options['email'] : $user->username);     // Use username as email address is no email is specified
        $user->password = (isset($options['password']) ? $options['password'] : substr(mdr(rand(1000000, 9999999)), 0, 5)); // Create a random password if none is given
        $user->location_id = $locationId;
        $user->account_id = $accountId;
        $user->save();

        return $user->user_id;
    }

    public static function initializeContext($accountId) {
            Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

            $context = new Context();
            $context->name = 'In-house Only';
            $context->locked = FALSE;
            $context->account_id = $accountId;
            $context->save();

            $context = new Context();
            $context->name = 'Publicly Accessible';
            $context->locked = FALSE;
            $context->account_id = $accountId;
            $context->save();

            Doctrine::getTable('Context')->getRecordListener()->get('MultiTenant')->setOption('disabled', false);
    }

    /**
     * Use this function to create a new account, location and user all in one shot. You will get an associative array back containing
     * the new account, location and user IDs, respectively
     * @param string $username
     * @param string $password
     * @param string $accountName
     * @param string $locationName
     * @return array
     *
     * TODO: Should we just pass in a list of options, and then pass it around accordingly?
     */
    public static function initializeTenant($options) {
        // Add the core admin user to the system
        // TODO: Should check for errors here...
        Kohana::log('debug', 'Initializing account...');
        try {
            $accountId = self::initializeAccount($options['account']);
            self::$accountName = $options['account']['name'];
        } catch (Exception $e) {
            Kohana::log('error', 'Creating account failed.');
            
            // Bubble up
            throw $e;
        }

        Kohana::log('debug', 'Initializing location...');
        try {
            $locationId = self::initializeLocation($accountId, $options['location']);
        } catch (Exception $e) {
            Kohana::log('error', 'Creating location failed. Rolling back tenant.');
            $account = Doctrine::getTable('Account')->find($accountId);
            $account->delete();

            self::$accountName = NULL;

            // Bubble up
            throw $e;
        }

        Kohana::log('debug', 'Initializing user...');
        try {
            $userId = self::initializeUser($accountId, $locationId, $options['user']);
        } catch (Exception $e) {
            Kohana::log('error', 'Creating user failed. Rolling back tenant.');
            $location = Doctrine::getTable('Location')->find($locationId);
            $location->delete();

            $account = Doctrine::getTable('Account')->find($accountId);
            $account->delete();

            self::$accountName = NULL;

            // Bubble up
            throw $e;
        }
        Kohana::log('debug', 'Initializing contexts...');
        
        self::initializeContext($accountId);
        Kohana::log('debug', 'Scanning modules for tenant-setup routines.');

        $packages = Bluebox_Installer::listPackages(array() , true);
        foreach($packages as $name => $package) {
            if (!empty($package['instance']) && in_array($package['action'], 'createTenant')) {
                try {
                    $result = call_user_func(array(
                            $package['instance'],
                            'createTenant'
                            ) , $package);
                    Kohana::log('debug', 'Multi-tenant initialization routine for ' . $package['instance'] . ' complete');
                }
                catch(Exception $e) {
                    Kohana::log('error', 'Multi-tenant initialization routine ' .$initMethod . ' failed on ' . $package['instance'] . '! ' . $e->getMessage());
                    message::set('Unable to initialize tenant!'
                            .'<div class="error_details">' . $e->getMessage() . '</div>'
                    );

                    self::$accountName = NULL;

                    return false;
                }
            }
        }

        Kohana::log('debug', 'Done creating tenant.');

        self::$accountName = NULL;

        return array ('userId' => $userId, 'locationId' => $locationId, 'accountId' => $accountId); // You can get everything you need from here
    }

    public static function initializeSite($url = 'localhost', $default = 0, $wildcard = 0) {
        $site = new Site();

        // Is the skins module installed? If so, get the key of the default skin
        if (class_exists('Skin', TRUE)) {
            $skin = Doctrine::getTable('Skin')->findOneByDefault(TRUE);
            if ($skin) {
                $site->skin_id = $skin->skin_id;
            }
        }
        
        // Create default site setttings
        $site->url = $url;
        $site->homepage = '/welcome';
        $site->wildcard = $wildcard;
        $site->default = $default;
        $site->save();
        $site->free();
    }
}
