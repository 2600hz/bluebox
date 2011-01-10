<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Bluebox
 * @license    Mozilla Public License (MPL)
 */
class Core_Configure extends Bluebox_Configure
{
    public static $version = '1.0.4-dev';
    public static $packageName = 'core';
    public static $displayName = 'Bluebox Core';
    public static $author = '2600hz Team';
    public static $vendor = '2600hz Foundation';
    public static $license = 'MPL';
    public static $summary = 'Our free software, your next voip system! Vist us at www.2600hz.org';
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

        $options['account'] = array(
            'name' => 'Master Account',
            'type' => Account::TYPE_NORMAL
        );

        $options['location'] = array('name' => 'Main Location');

        $options['user'] = array(
            'username' => $session->get('installer.adminEmailAddress'),
            'password' => $session->get('installer.adminPassword'),
            'user_type' => User::TYPE_SYSTEM_ADMIN
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

        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);

        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);
    }

    public static function createDefaultNetlists()
    {
        Doctrine::getTable('NetList')->getRecordListener()->get('MultiTenant')->setOption('disabled', TRUE);
        $netList = new NetList();

        $netList['name'] = 'Private/Local Network (auto)';

        $netList['system_list'] = 'rfc1918.auto';

        $netList['account_id'] = 1;

        $netList->save(TRUE);

        
        $netList = new NetList();

        $netList['name'] = 'Private Network (auto)';

        $netList['system_list'] = 'nat.auto';

        $netList['account_id'] = 1;

        $netList->save(TRUE);


        $netList = new NetList();

        $netList['name'] = 'Local Network (auto)';

        $netList['system_list'] = 'localnet.auto';

        $netList['account_id'] = 1;

        $netList->save(TRUE);


        $netList = new NetList();

        $netList['name'] = 'Loopback Network (auto)';

        $netList['system_list'] = 'loopback.auto';

        $netList['account_id'] = 1;

        $netList->save(TRUE);


        $netList = new NetList();

        $netList['name'] = 'Trunks (auto)';

        $netList['system_list'] = 'trunks.auto';

        $netList['account_id'] = 1;

        $netList->save(TRUE);


        $netList = new NetList();

        $netList['name'] = 'Public Internet';

        $netList['allow'] = TRUE;

        $netList['NetListItem']->fromArray(array(
                array('record' => '0.0.0.0/0', 'allow' => TRUE)
            )
        );

        $netList['account_id'] = 1;

        $netList->save(TRUE);

        Doctrine::getTable('NetList')->getRecordListener()->get('MultiTenant')->setOption('disabled', FALSE);
    }
}
