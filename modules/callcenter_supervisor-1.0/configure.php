<?php defined('SYSPATH') or die('No direct access allowed.');

class callcenter_supervisor_1_0_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'callcenter_supervisor';
    public static $displayName = 'Call Center Supervisor';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Queue Supervisor interface for mod_callcenter';
    public static $description = 'This module provides a supervior view of the queues that they are a member of, allowing them to monitor, bridge in to, or control calls to the logged in agents';
    public static $default = False;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'callcenter_core' => 1.0,
        'esl' => 1.1,
        'callmanager' => 1.0
    );
    public static $navLabel = 'Queue Supervisor';
    public static $navBranch = '/Call_Center/';
    public static $navURL = 'callcenter_supervisor/index';
    public static $navSubmenu = array(
        'List' => 'callcenter_supervisor/index'
    );
}

?>