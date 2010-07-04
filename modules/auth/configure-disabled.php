<?php defined('SYSPATH') or die('No direct access allowed.');
class Auth_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'auth';
    public static $displayName = 'Authentication';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Service used for authentication';
    public static $default = TRUE;
    public static $type = Bluebox_PackageManager::TYPE_SERVICE;
    public static $required = array(
        'core' => 0.1
    );
    public static $canBeDisabled = FALSE;
    public static $canBeRemoved = FALSE;
}
