<?php defined('SYSPATH') or die('No direct access allowed.');
class MisdnManager_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'misdnmanager';
    public static $displayName = 'mISDN Manager';
    public static $author = '<ul><li>Reto Haile</li></ul>';
    public static $vendor = 'Selmoni Ingenieur AG';
    public static $license = 'FPL';
    public static $summary = 'Configuration of mISDN compatible interface cards';
    public static $default = false;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Connectivity/';
    public static $navURL = 'misdnmanager/index';    
    public static $navSubmenu = array(
        'Cards' => '/misdnmanager/index',
        'Driver Settings' => '/misdnmanager/settings',
        'Scan' => '/misdnmanager/scan',
        'Add Card' => array(
            'url' => '/misdnmanager/add',
            'disabled' => true
        ) ,
        'Edit Card' => array(
            'url' => '/misdnmanager/edit',
            'disabled' => true
        ) ,
        'Edit Port' => array(
            'url' => '/misdnmanager/ported',
            'disabled' => true
        ) ,
        'Delete Card' => array(
            'url' => '/misdnmanager/delete',
            'disabled' => true
        ) ,
        'Vendors' => '/misdnmanager/vendors',
        'Models' => '/misdnmanager/models',
        'Save' => '/misdnmanager/save'
    );

    public function postInstall()
    {
        include_once ('libraries/MisdnManager.php');
        $vendorId = MisdnManager::addVendor('beroNet');
        MisdnManager::addModel($vendorId, '1397:b560', 'BN4S0', 'BN4S0', 4);
        MisdnManager::addModel($vendorId, '1397:b562', 'BN8S0', 'BN8S0', 8);
        MisdnManager::addModel($vendorId, '1397:b563', 'BN1E1+', 'BN2E1', 1);
        MisdnManager::addModel($vendorId, '1397:b564', 'BN2E1', 'BN2E1', 2);
        MisdnManager::addModel($vendorId, '1397:b566', 'BN2S0', 'BN2S0', 2);
        MisdnManager::addModel($vendorId, '1397:b568', 'BN4S0 (miniPCI)', 'BN4S0', 4);
        MisdnManager::addModel($vendorId, '1397:b569', 'BN2S0 (miniPCI)', 'BN2S0', 2);
        MisdnManager::addModel($vendorId, '1397:b56a', 'BN1E1', 'BN2E1', 1);
        MisdnManager::addModel($vendorId, '1397:b56b', 'BN8S0 (LEDs)', 'BN8S0', 8);
        MisdnManager::addModel($vendorId, '1397:b761', 'BN2S0e (PCIexpress)', 'BN2S0', 2);
        MisdnManager::addModel($vendorId, '1397:b762', 'BN4S0e (PCIexpress', 'BN4S0', 4);
        $vendorId = MisdnManager::addVendor('Junghanns');
        MisdnManager::addModel($vendorId, '1397:b550', 'quadBRI', 'BN4S0', 4);
        MisdnManager::addModel($vendorId, '1397:b553', 'singleE1', 'BN2E1', 1);
        MisdnManager::addModel($vendorId, '1397:b55b', 'octoBRI', 'BN8S0', 8);
        $vendorId = MisdnManager::addVendor('DummyInc');
        MisdnManager::addModel($vendorId, '5853:0001', 'Dummy card for virtual machines', 'BN8S0', 8);
        //        MisdnManager::addDriverSetting(NULL, 'dsp_debug', 0);
        //        MisdnManager::addDriverSetting(NULL, 'dsp_options', 0);
        //        MisdnManager::addDriverSetting(NULL, 'hfcmulti_debug', 0);
        //        MisdnManager::addDriverSetting(NULL, 'hfcmulti_poll', 128);
        //        MisdnManager::addDriverSetting(NULL, 'devnode_user', 'root');
        //        MisdnManager::addDriverSetting(NULL, 'devnode_group', 'root');
        MisdnManager::addMisdnSettings();
        //        MisdnManager::addDriverSetting('mISDN_dsp', array('debug' => 0, 'options' => 0, 'dtmfthreshold' => NULL, 'poll' => NULL));
        //        MisdnManager::addDriverSetting('hfcmulti', array('debug' => 0, 'poll' => 128, 'pcm' => NULL));
        //        MisdnManager::addDriverSetting('devnode', array('user' => 'root', 'group' => 'root', 'mode' => 644));
        
    }
}
