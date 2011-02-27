<?php defined('SYSPATH') or die('No direct access allowed.');
class Switchboard_1_0_0_Configure extends Bluebox_Configure
{
    public static $version = 1.1;
    public static $packageName = 'switchboard';
    public static $displayName = 'switchboard';
    public static $author = '<ul><li>Mell Rosandich</li></ul>';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'FreeSWTICH SwitchBoard using Event Socket Layer Support';
    public static $description = 'This module will give you a switchboard in realtime.  This will allow you to do transfers, put on hold, kill a call,record a call, play sounds over a call.';
    public static $default = TRUE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'freeswitch' => 0.1,
        'core' => 0.1,
        'sipinterface' => 0.1
    );
    public static $navBranch = '/Status/';
    public static $navURL = 'switchboard/index';
    /**
     * Check dynamic loading library.  Is this a security issue? Mike J mentioned statically compiling
     */
    public static function _checkESL()
    {
        $issues = array();
        /*if (!extension_loaded("ESL")) {
            $issues['warnings'][] = __('The FreeSWITCH PHP/ESL module is not installed (or not working). We will fail back to socket-based/native ESL support. Some features may not work reliably, such as voicemail indicator lights. This error is not critical, you can install now and fix this later. See ' )
                .html::anchor('http://wiki.freeswitch.org/wiki/Event_Socket_Library', NULL, array(
                        'title' => 'Learn about the freeswitch ESL PHP extension.',
                        'target' => '_blank'
                    ));
        }*/
        return $issues;
    }
}
