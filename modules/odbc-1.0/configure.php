<?php defined('SYSPATH') or die('No direct access allowed.');
class Odbc_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'odbc';
    public static $displayName = 'ODBC';
    public static $author = 'Michael Phillips';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'ODBC Connection Manager';
    public static $description = 'Provides access to a variety of databases via obdc';
    public static $default = TRUE;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainVoicemailsX.png';
    public static $navBranch = '/System/';
    public static $navURL = 'odbc/index';
    public static $navSubmenu = array(
        'Search Connections' => '/odbc/index',
        'Add Connection' => '/odbc/add',
        'Update Connection' => array(
            'url' => '/odbc/update',
            'disabled' => TRUE
        ) ,
        'Delete Connection' => array(
            'url' => '/odbc/delete',
            'disabled' => TRUE
        ) ,
        'odbc.ini' => array(
            'url' => '/odbc/config',
            'disabled' => TRUE
        )
    );
    
    public function postInstall()
    {
        // Add the relevant database settings to a new ODBC config automagically
        // TODO: Generate odbc.ini, too?
        $installSamples = Session::instance()->get('installer.samples', FALSE);

        if (empty($installSamples))
        {
            return TRUE;
        }

        $odbc = new Odbc();

        $odbc->dsn_name = 'Bluebox';

        $odbc->database = $_SESSION['installer.dbName'];

        $odbc->user = $_SESSION['installer.dbUserName'];

        $odbc->pass = $_SESSION['installer.dbUserPwd'];

        $odbc->host = $_SESSION['installer.dbHostName'];

        $odbc->port = (integer)$_SESSION['installer.dbPortSelection'];

        $odbc->type = $_SESSION['installer.dbType'];

        $odbc->description = 'Default FreeSWITCH ODBC Connection';

        $odbc->save();
    }
}