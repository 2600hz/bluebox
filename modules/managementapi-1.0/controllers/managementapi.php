<?php defined('SYSPATH') or die('No direct access allowed');

class ManagementAPI_Controller extends Bluebox_Controller
{
    protected $authBypass = array('add', 'reload', 'rebuild');

    public function add($data)
    {
        $this->auto_render = FALSE;

        self::authorize();

        switch($data)
        {
            case 'extension':
                if(!($user_id = arr::get($_REQUEST, 'user_id')))
                {
                    $user_id = 1;
                }

                $number = self::requireRequest('number');
                $extcid = self::requireRequest('ext_cid');
                $username = self::requireRequest('username');
                $password = self::requireRequest('password');
                $context_id = self::requireRequest('context_id');
                $user_id = self::requireRequest('user_id');

                $options = array('callerid_internal_number' => $number,
                                 'callerid_external_number' => $extcid,
                                 'sip_username' => $username,
                                 'sip_password' => $password);

                Doctrine_Manager::connection()->beginTransaction();

                $success = Bluebox_Tenant::createUserExtension($user_id,
                                                               $number,
                                                               $context_id,
                                                               NULL,
                                                               $options);

                if(!$success)
                {
                    Doctrine_Manager::connection()->rollback();

                    self::throwErrorAndDie('Could not add extension');
                }
                else
                {
                    Doctrine_Manager::connection()->commit();
                }

                self::returnSuccessAndDie("Added extension");
                break;

            case 'trunk':
                
                $name = self::requireRequest('name');
                $server = self::requireRequest('server');
                $username = self::requireRequest('username');
                $password = self::requireRequest('password');
                $interface_id = self::requireRequest('interface_id');
                $contexts = array_fill_keys(explode(',', self::requireRequest('contexts')), '1');
                $register = self::requireRequest('register');
                $cid_name = self::requireRequest('cid_name');
                $cid_number = self::requireRequest('cid_number');
                $pattern_ids = explode(',', self::requireRequest('pattern_ids'));
                $prepends = explode(',', self::requireRequest('prepends'));
                $account_id = self::requireRequest('account_id');

                foreach($pattern_ids as $index => $pattern_id)
                {
                    $patterns[$pattern_id]['enabled'] = 1;
                    
                    if(($prepend = arr::get($prepends, $index)) === FALSE)
                    {
                        $prepend = "";
                    }

                    $patterns[$pattern_id]['prepend'] = $prepend;
                }

                Doctrine_Manager::connection()->beginTransaction();

                $trunk = new Trunk();

                $trunk->synchronizeWithArray(array('name' => $name,
                                                   'type' => 'sip',
                                                   'server' => $server,
                                                   'plugins' => array('sip' => array('username' => $username,
                                                                                     'password' => $password,
                                                                                     'register' => $register,
                                                                                     'cid_format' => '1',
                                                                                     'caller_id_field' => 'rpid',
                                                                                     'sip_invite_format' => '1'),
                                                                      'sipinterface' => array('sipinterface_id' => $interface_id),
                                                                      'simpleroute' => array('patterns' => $patterns,
                                                                                             'contexts' => $contexts,
                                                                                             'caller_id_name' => $cid_name,
                                                                                             'caller_id_number' => $cid_number,
                                                                                             'continue_on_fail' => '1'))));

                $trunk->account_id = $account_id;

                $trunk->refreshRelated();

                $success = $trunk->save();

                if(!$success)
                {
                    Doctrine_Manager::connection()->rollback();

                    self::throwErrorAndDie('Could not add trunk');
                }
                else
                {
                    Doctrine_Manager::connection()->commit();
                }

                self::returnSuccessAndDie('Added trunk');
                break;

            default:
                self::throwErrorAndDie('Command not recognized');
                break;
        }
    }

    public function reload($data)
    {
        $this->auto_render = FALSE;

        self::authorize();

        switch($data)
        {
            case 'xml':
                Event::run('freeswitch.reload.xml');
                
                self::returnSuccessAndDie('Triggered reloadxml');
                break;

            default:
                self::throwErrorAndDie('Command not recognized');
                break;
        }
    }

    public function rebuild($data)
    {
        $this->auto_render = FALSE;

        self::authorize();

        switch($data)
        {
            case 'account':
                $account_id = self::requireRequest('account_id');

                Regenerate_Controller::account($account_id, FALSE);
    
                self::returnSuccessAndDie('Triggered rebuild');
                break;

            default:
                self::throwErrorAndDie('Command not recognized');
                break;
        }
    }

    private static function authorize() 
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        Kohana::log('debug', $ip . ' is trying to use the Management APIs');

        if(!self::checkACL($ip)) 
        {
            Kohana::log('debug', $ip . ' is rejected by the ACL');

            self::throwErrorAndDie('Access denied');
        }

        Kohana::log('debug', 'Access granted to ' . $ip);
    }

    private static function checkACL($ip) 
    {
        $acl = Kohana::config('managementapi.IP_ACL');

        foreach($acl as $auth_ip) 
        {
            if($ip == $auth_ip) 
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    private static function returnSuccessAndDie($success)
    {
        echo 'SUCCESS: ' . $success;
        flush();
        die();
    }

    private static function throwErrorAndDie($error)
    {
        echo 'ERROR: ' . $error;
        flush();
        die();
    }

    private static function requireRequest($request)
    {
        if(!($response = arr::get($_REQUEST, $request)))
        {
            self::throwErrorAndDie('\'' . $request . '\' is required');
        }

        return $response;
    }
}
