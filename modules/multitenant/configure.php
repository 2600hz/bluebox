<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * MultiTenant - Add multi-tenancy support to all of FreePBX v3
 *
 * Gives info about how to install this module
 *
 * @author Darren Schreiber
 * @package FreePBX3
 * @subpackage NetList
 */
class MultiTenant_Configure extends FreePbx_Configure
{
    public static $version = 0.1;
    public static $packageName = 'multitenant';
    public static $displayName = 'Multi-Tenancy Support';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'SwitchFreedom';
    public static $license = 'Closed / Copyright 2010 SwitchFreedom.';
    public static $summary = 'Adds multi-tenancy support to all of FreePBX v3.';
    public static $description = 'Adds appropriate columns, hooks and query checks to allow for multiple tenants to exist on the same system and database schema.';
    public static $default = TRUE;
    public static $type = FreePbx_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => '0.1'
    );

    public function completedInstall() {
        // Add a super-admin?

    }
}
