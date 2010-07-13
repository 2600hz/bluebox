<?php defined('SYSPATH') or die('No direct access allowed.');
/*class Lcr_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'lcr';
    public static $displayName = 'Least Cost Routing';
    public static $author = 'Raymond Chandler';
    public static $vendor = 'FreeSWITCH';
    public static $license = 'BSD';
    public static $summary = 'Least Cost Routing Module';
    public static $description = 'When this module is loaded, it will provide an interface for managing your least cost routing table';
    public static $default = FALSE;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
        'freeswitch' => 0.1,
        'trunkmanager' => 0.1,
        'sipinterface' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainSettingsX.png';
    public static $navBranch = '/Routing/';
    public static $navURL = 'lcr/index';
    public static $navSubmenu = array(
        'List' => '/lcr/index',
        'Search' => '/lcr/search',
        'Add' => '/lcr/add',
        'Edit' => array(
            'url' => '/lcr/edit',
            'disabled' => true
        ) ,
        'Delete' => array(
            'url' => '/lcr/delete',
            'disabled' => true
        ) ,
        'Settings' => '/lcr/settings'
    );
    private $views = array();

    public static function _checkExp() {
        return array('warnings' => 'This module is experimental and not ready for production use!');
    }

    public function postInstall()
    {
        $dbh = Doctrine_Manager::connection()->getDbh();
        $dbh->exec('CREATE VIEW carriers AS SELECT provider_id AS id, provider_name AS carrier_name, active AS enabled FROM provider');
        $dbh->exec('CREATE view carrier_gateway AS SELECT trunk_id AS id, provider_id AS carrier_id, CONCAT(\'sofia/gateway/trunk_\', trunk_id, \'/\') AS prefix, \'\' AS suffix, \'\' AS codec, active AS enabled FROM trunk');
    }
    public function postUninstall()
    {
        $dbh = Doctrine_Manager::getInstance()->getConnection()->getDbh();
        $dbh->exec('DROP VIEW carriers');
        $dbh->exec('DROP VIEW carrier_gateway');
    }
}*/
