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

            foreach($output as $line)
            {
                if (preg_match('/^[^\s]+/', $line, $matches))
                {
                    $interface = $matches[0];

                    continue;
                }

                if (preg_match('/inet addr:([^\s]+)/', $line, $matches))
                {
                    if(!filter_var($matches[1], FILTER_VALIDATE_IP))
                    {
                        continue;
                    }

                    if ($matches[1] != '127.0.0.1')
                    {
                        kohana::log('debug', 'Initializing a sip interfaces for ' .$interface .' with ip ' .$matches[1]);

                        // By default, we assume a new install is going to want port 5060 on the local machine to be where all traffic comes into
                        $sipInterface = new SipInterface();

                        $sipInterface['name'] = 'Network Interface ' .$interface;

                        $sipInterface['ip_address'] = $matches[1];

                        $sipInterface['port'] = 5060;

                        $sipInterface['auth'] = TRUE;

                        $sipInterface['nat_type'] = 1;

                        $sipInterface['Context'] = Doctrine::getTable('Context')->findOneByName('In-house Only');

                        $sipInterface['nat_net_list_id'] = netlists::getSystemListId('nat.auto');

                        $sipInterface['inbound_net_list_id'] = netlists::getSystemListId('trunks.auto');

                        $sipInterface['register_net_list_id'] = 0;

                        $sipInterface->save();
                    }
                }
            }
        }
        catch (Exception $e)
        {
            kohana::log('error', 'Unable to list networks for sip interfaces postInstall: ' .$e->getMessage());
        }
    }
}