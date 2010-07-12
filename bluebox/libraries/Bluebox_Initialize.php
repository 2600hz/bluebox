<?php defined('SYSPATH') or die('No direct access allowed.');

class Bluebox_Initialize
{
    // NOTE: For multi-tenancy to work this section MUST appear first!
    public static function initializeMasterTenant() {
        $session = Session::instance();

        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        $options = array();
        $options['account'] = array('name' => 'Master Account');
        $options['location'] = array('name' => 'Main Location');
        $options['user'] = array('username' => $session->get('installer.adminEmailAddress'), 'password' => $session->get('installer.adminPassword'));

        Bluebox_Tenant::initializeTenant($options);
        Bluebox_Tenant::initializeSite('localhost', 1);

        // Force a login of the master/admin user for the remainder of the install
        Auth::instance()->force_login($session->get('installer.adminEmailAddress'));
        users::getCurrentUser();

    }

    public static function initializeSkins() {
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
        if ($sites) {
            foreach ($sites as $site) if ($site->skin_id == NULL) {
                $site->skin_id = $skin->skin_id;
                $site->save();
                $site->free(TRUE);
            }
        }
        $skin->free(TRUE);
    }

    /** Initialize default number types **/
     public static function initializeDeviceNumber() {
        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);

        $numberType = new NumberType();
        $numberType->class = 'DeviceNumber';
        $numberType->package_id = 0;
        $numberType->save();
        $numberType->free();
    }

   public static function initializeSystemNumber() {
        Doctrine::getTable('Location')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        
        $numberType = new NumberType();
        $numberType->class = 'SystemNumber';
        $numberType->package_id = 0;
        $numberType->save();
        $numberType->free(TRUE);
    }
}