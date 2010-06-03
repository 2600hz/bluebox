<?php defined('SYSPATH') or die('No direct access allowed.');
class FreePbx_Initialize
{
    // NOTE: For multi-tenancy to work this section MUST appear first!
    public static function initializeMasterTenant() {
       $session = Session::instance();

        $options = array();
        $options['account'] = array('name' => 'Master Account');
        $options['location'] = array('name' => 'Main Location', 'domain' => 'localhost');
        $options['user'] = array('username' => $session->get('installer.adminEmailAddress'), 'password' => $session->get('installer.adminPassword'));

        FreePbx_Tenant::initializeTenant($options);
        FreePbx_Tenant::initializeSite('localhost', 1);
       
        // Add the core admin user to the system
/*        $user = new User();
        $user->first_name = 'Admin';
        $user->last_name = 'User';
        $user->email_address = $session->get('installer.adminEmailAddress');
        $user->username = $user->email_address;
        $user->password = $session->get('installer.adminPassword');

        $user->Location = new Location();
        $user->Location->name = 'Main Location';
        $domain = $_SERVER['SERVER_NAME'];
        // Is this a real domain name? If not, use the IP address instead, by default, or initial setup is a pain
        if (!filter_var($domain, FILTER_VALIDATE_URL)) {
            $domain = network::guessServerIP();
        }
        $user->Location->domain = $domain;

        $user->Location->Account = new Account();
        $user->Location->Account->name = 'Main Account';
        $user->Location->Account->type = 0;
        Doctrine::getTable('User')->getRecordListener()->get('MultiTenant')->setOption('disabled', true);
        $user->Location->Account->save();
        $user->Account = $user->Location->Account;
        $user->save();
        $user->free(TRUE);
 */

        // Force a login of the master/admin user for the remainder of the install
        Auth::instance()->force_login($session->get('installer.adminEmailAddress'));
        users::getCurrentUser();

    }

/*    public static function initializeContext() {
        FreePbx_Installer::disableTelephony();

        // Add a couple samples in there, too
        $context = new Context();
        $context->name = 'In-house User/Device';
        $context->locked = FALSE;
        $context->save();
        $context->free();

        $context = new Context();
        $context->name = 'Publicly Accessible';
        $context->locked = FALSE;
        $context->save();
        $context->free();

        FreePbx_Installer::restoreTelephony();
    }*/

    public static function initializeNetList() {
        FreePbx_Installer::disableTelephony();

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
        $netList->name = 'Public Internet';
        $netList->allow = TRUE;
        $netList->save(TRUE);
        
        $netItem = new NetListItem();
        $netItem->NetList = $netList;
        $netItem->record = '0.0.0.0/0';
        $netItem->allow = TRUE;
        $netItem->save(TRUE);

       FreePbx_Installer::restoreTelephony();
    }

    public static function initializeSkins() {
        // TODO: Move this to the individual skins once skins are actual modules
        FreePbx_Installer::disableTelephony();
        
        // Install stock FreePBX skin
        $skin = new Skin();
        $skin->name = 'FreePBX';
        $skin->location = 'skins/freepbx';
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

        // Install additional Phonebooth skin
        $skin = new Skin();
        $skin->name = 'Phonebooth';
        $skin->location = 'skins/phonebooth';
        $skin->default = FALSE;
        $skin->save();
        $skin->free(TRUE);

        // Install additional Darkness skin
        $skin = new Skin();
        $skin->name = 'Darkness';
        $skin->location = 'skins/darkness';
        $skin->default = FALSE;
        $skin->save();
        $skin->free(TRUE);
        
        FreePbx_Installer::restoreTelephony();
    }

    /** Initialize default number types **/
     public static function initializeDeviceNumber() {
        FreePbx_Installer::disableTelephony();

        $numberType = new NumberType();
        $numberType->class = 'DeviceNumber';
        $numberType->module_id = 0;
        $numberType->save();
        $numberType->free();

       FreePbx_Installer::restoreTelephony();
    }

   public static function initializeSystemNumber() {
        FreePbx_Installer::disableTelephony();
        
        $numberType = new NumberType();
        $numberType->class = 'SystemNumber';
        $numberType->module_id = 0;
        $numberType->save();
        $numberType->free(TRUE);

        FreePbx_Installer::restoreTelephony();
    }
}