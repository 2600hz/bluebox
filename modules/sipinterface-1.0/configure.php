<?php defined('SYSPATH') or die('No direct access allowed.');

class InterfaceManager_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'sipinterface';
    public static $displayName = 'SIP Interface Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'SIP Interface Management Controller Class (FreeSWITCH Specific)';
    public static $default = false;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => 0.1
    );
    public static $navBranch = '/Connectivity/';
    public static $navURL = 'sipinterface/index';    
    public static $navSubmenu = array(
        'SIP Interfaces' => '/sipinterface/index',
        'Add SIP Interface' => '/sipinterface/create',
        'Edit SIP Interface' => array(
            'url' => '/sipinterface/edit',
            'disabled' => true
        ) ,
        'Delete SIP Interface' => array(
            'url' => '/sipinterface/delete',
            'disabled' => true
        )
    );
    
    public function postInstall()
    {
        try
        {
            $cmd = "/sbin/ifconfig";

            $output = array();

            exec($cmd, $output);

            $interface = '';

            $ips = array();

            foreach($output as $line)
            {
                if (preg_match('/^[^\s]+/', $line, $matches))
                {
                    $interface = $matches[0];

                    continue;
                }

                if (preg_match('/inet[^0-9]*([^\s]+)/', $line, $matches))
                {
                    if(!filter_var($matches[1], FILTER_VALIDATE_IP))
                    {
                        continue;
                    }

                    kohana::log('debug', 'Found a valid interface at ' .$interface .' with ip ' .$matches[1]);

                    if (($matches[1] != '127.0.0.1') and (!preg_match('/^169.254./', $matches[1]))) // No localhost or auto-assigned IPs
                    {
                        $ips[] = $matches[1];
                    }
                }
            }

            $ips = array_unique($ips);
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to list networks for sip interfaces postInstall: ' .$e->getMessage());
        }

        if (count($ips) > 0) foreach ($ips as $ip) {
            $this->addInterface('Authenticated SIP on ' . $ip, $ip, '5060');

            $this->addInterface('Unauthenticated SIP on ' . $ip, $ip, '5080', FALSE, FALSE);
        } else {
            // If no known IP addresses, leave IP address blank (FreeSWITCH will auto-detect)
            $this->addInterface('Authenticated SIP', '', '5060');

            $this->addInterface('Unauthenticated SIP', '', '5080', FALSE, FALSE);
        }
    }

    public static function addInterface($name, $ip = '', $port = 5060, $auth = TRUE, $use_inbound_acl = TRUE, $context = 'Publicly Accessible') {
        Kohana::log('debug', 'Adding SIP interface for IP ' . $ip . ' on port ' . $port);

        $sipInterface = new SipInterface();

        $sipInterface['name'] = $name;

        $sipInterface['ip_address'] = $ip;

        $sipInterface['port'] = $port;

        $sipInterface['auth'] = $auth;

        $sipInterface['nat_type'] = 1;

        $sipInterface['Context'] = Doctrine::getTable('Context')->findOneByName($context);

        $sipInterface['nat_net_list_id'] = netlists::getSystemListId('nat.auto');

        $sipInterface['inbound_net_list_id'] = ($use_inbound_acl ? netlists::getSystemListId('trunks.auto') : 0);

        $sipInterface['register_net_list_id'] = 0;

        $location = Doctrine::getTable('Location')->findOneByName('Main Location');

        if (!empty($location['location_id']))
        {
            $sipInterface['registry'] = array('force_register_domain' => $location['location_id']);
        }

        $sipInterface->save();

        return TRUE;
    }
}