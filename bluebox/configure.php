<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Bluebox
 * @license    Mozilla Public License (MPL)
 */
class Core_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'core';
    public static $displayName = 'Bluebox Core';
    public static $author = '2600hz Team';
    public static $vendor = '2600hz Foundation';
    public static $license = 'MPL';
    public static $summary = 'Our free software, your next voip system!';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_CORE;
    public static $required = array();

    public function postInstall()
    {
        self::initializeMasterTenant();

        self::initializeSkins();

        self::createDefaultNetlists();
    }

    // NOTE: For multi-tenancy to work this section MUST appear first!
    public static function initializeMasterTenant()
    {
        $session = Session::instance();

        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        $options = array();

        $options['account'] = array('name' => 'Master Account');

        $options['location'] = array('name' => 'Main Location');

        $options['user'] = array(
            'username' => $session->get('installer.adminEmailAddress'),
            'password' => $session->get('installer.adminPassword')
        );

        Bluebox_Tenant::initializeTenant($options);

        Bluebox_Tenant::initializeSite('localhost', 1);

        // Force a login of the master/admin user for the remainder of the install
        Auth::instance()->force_login($session->get('installer.adminEmailAddress'));

        users::isUserAuthentic();

        users::getCurrentUser();
    }

    public static function initializeSkins()
    {
        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        // Install stock Bluebox skin
        $skin = new Skin();

        $skin->name = 'Bluebox';

        $skin->location = 'skins/bluebox';

        $skin->default = TRUE;

        $skin->save();

        // Map all sites with no skin assigned to this default skin
        $sites = Doctrine::getTable('Site')->findAll();

        if ($sites)
        {
            foreach ($sites as $site) if ($site->skin_id == NULL)
            {
                $site->skin_id = $skin->skin_id;

                $site->save();

                $site->free(TRUE);
            }
        }

        $skin->free(TRUE);
    }

    public static function createDefaultNetlists()
    {
        $netList = new NetList();

        $netList->name = 'Private/Local Network (auto)';

        $netList->system_list = 'rfc1918.auto';

        $netList->save(TRUE);

        
        $netList = new NetList();

        $netList->name = 'Private Network (auto)';

        $netList->system_list = 'nat.auto';

        $netList->save(TRUE);


        $netList = new NetList();

        $netList->name = 'Local Network (auto)';

        $netList->system_list = 'localnet.auto';

        $netList->save(TRUE);


        $netList = new NetList();

        $netList->name = 'Loopback Network (auto)';

        $netList->system_list = 'loopback.auto';

        $netList->save(TRUE);


        $netList = new NetList();

        $netList->name = 'Trunks (auto)';

        $netList->system_list = 'trunks.auto';

        $netList->save(TRUE);


        $netList = new NetList();

        $netList->name = 'Public Internet';

        $netList->allow = TRUE;

        $netList->save(TRUE);


        $netItem = new NetListItem();

        $netItem->NetList = $netList;

        $netItem->record = '0.0.0.0/0';

        $netItem->allow = TRUE;

        $netItem->save(TRUE);
    }
}