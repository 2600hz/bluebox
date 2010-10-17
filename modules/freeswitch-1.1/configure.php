<?php defined('SYSPATH') or die('No direct access allowed.');
class Freeswitch_1_1_Configure extends Bluebox_Configure
{
    public static $version = '1.1';
    public static $packageName = 'freeswitch';
    public static $displayName = 'FreeSWITCH Driver';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'TCAPI';
    public static $license = 'MPL';
    public static $summary = 'Driver for FreeSWITCH';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_DRIVER;
    public static $required = array(
        'core' => 0.1,
        'not' => array(
            'asterisk' => 0.1
        )
    );
    /**
     * Ensures that DOMDocument is loaded
     *
     * @return results array
     */
    public static function _checkDOMDocument()
    {
        if (!class_exists('DOMDocument')) {
            return __('The FreeSwitch driver requires ') . html::anchor('http://us3.php.net/manual/en/class.domdocument.php', 'DOMDocument') . __(' to be installed and active');
        }
    }

    public function completedInstall() {
        // TODO: Restart FreeSWITCH automatically
    }
}
