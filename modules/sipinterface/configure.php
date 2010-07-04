<?php defined('SYSPATH') or die('No direct access allowed.');
class InterfaceManager_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'sipinterface';
    public static $displayName = 'SIP Interface Manager';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'SIP Interface Management Controller Class (FreeSWITCH Specific)';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => 0.1,
        'trunkmanager' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Connectivity/';
    public static $navURL = 'sipinterface/index';    
    public static $navSubmenu = array(
        'SIP Interfaces' => '/sipinterface/index',
        'Add SIP Interface' => '/sipinterface/add',
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
        parent::postInstall();
        
        // By default, we assume a new install is going to want port 5060 on the local machine to be where all traffic comes into
        $sipInterface = new SipInterface();
        $sipInterface->name = 'Default Network Interface';
        $sipInterface->ip_address = network::guessServerIP();
        $sipInterface->port = 5060;
        $sipInterface->auth = TRUE;
        $sipInterface->nat_type = 1;
        $sipInterface->Context = Doctrine::getTable('Context')->findOneByName('In-house Only');
        $sipInterface->nat_net_list_id = netlists::getSystemListId('nat.auto');
        $sipInterface->save();
    }
}
