<?php defined('SYSPATH') or die('No direct access allowed.');
class Provisioner_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'provisioner';
    public static $author = '<ul><li>Karl Anderson</li><li>Michael Phillips</li></ul>';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Provision Support';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Endpoint/';
    public static $navURL = 'provisioner/index';    
    public static $navSubmenu = array(
        'Endpoints' => '/provisioner/index',
        'Add Endpoint' => '/provisioner/add',
        //'Scan' => '/provisioner/scan',
        'Edit Endpoint' => array(
            'url' => '/provisioner/edit',
            'disabled' => 'true'
        ) ,
        'View Files' => array(
            'url' => '/provisioner/files',
            'disabled' => 'true'
        ) ,
        'Vendors' => '/provisioner/vendors',
        'Models' => '/provisioner/models',
        'Settings' => '/provisioner/settings',
        //'Help' => '/provisioner/help'
    );
    /**
     * Ensures that DOMDocument is loaded
     *
     * @return results array
     */
    public static function _checkDOMDocument()
    {
        if (!class_exists('DOMDocument')) {
            return __('This requires ') . html::anchor('http://us3.php.net/manual/en/class.domdocument.php', 'DOMDocument') . __(' to be installed and active') ;
        }
    }
}
