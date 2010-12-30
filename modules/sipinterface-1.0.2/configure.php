<?php defined('SYSPATH') or die('No direct access allowed.');

class InterfaceManager_1_0_2_Configure extends Bluebox_Configure
{
    public static $version = '1.0.2';
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
        'freeswitch' => '1.1.1'
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
        $this->addInterface('Authenticated SIP', '', '5060');

        $this->addInterface('Authenticated SIP - NAT', '', '5070', TRUE, FALSE, TRUE);

        $this->addInterface('Unauthenticated SIP', '', '5080', FALSE, FALSE);
    }

    public static function addInterface($name, $ip = '', $port = 5060, $auth = TRUE, $use_inbound_acl = TRUE, $nat = FALSE, $context = 'Publicly Accessible')
    {
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

        $registry = array(
            'options_ping' => $auth,
            'force_rport' => $nat
        );

        $location = Doctrine::getTable('Location')->findOneByName('Main Location');

        if (!empty($location['location_id']))
        {
            $registry['force_register_domain'] = $location['location_id'];
        }

        $sipInterface['registry'] = $registry;

        $sipInterface->save();

        return TRUE;
    }
}