<?php defined('SYSPATH') or die('No direct access allowed.');
class Polycomdriver_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'polycomdriver';
    public static $displayName = 'Polycom Driver';
    public static $author = 'Karl Anderson';
    public static $vendor = 'Bluebox';
    public static $license = 'LGPL';
    public static $summary = 'Polycom Provision Driver';
    public static $default = true;
    public static $type = Bluebox_Installer::TYPE_ENDPOINT;
    public static $required = array(
        'core' => 0.1,
        'provisioner' => 0.1
    );
    //Add our devices to the database
    public function postInstall()
    {                            
        try {
            $driver = 'PolycomEndpoint';
            $id = EndpointManager::addVendor('Polycom', '0004f2', $driver, 'SoundPoint');
            EndpointManager::addModel($id, 6, 'SoundPoint 670');
            EndpointManager::addModel($id, 6, 'SoundPoint 650');
            EndpointManager::addModel($id, 4, 'SoundPoint 560');
            EndpointManager::addModel($id, 4, 'SoundPoint 550');
            EndpointManager::addModel($id, 3, 'SoundPoint 501');
            EndpointManager::addModel($id, 3, 'SoundPoint 450');
            EndpointManager::addModel($id, 2, 'SoundPoint 430');
            EndpointManager::addModel($id, 2, 'SoundPoint 321/331');
            EndpointManager::addModel($id, 1, 'SoundPoint Pro SE-220');
            EndpointManager::addModel($id, 1, 'SoundPoint Pro SE-225');
        } catch(Exception $e) {
            kohana::log('alert', 'Ignoring ' . $e->getMessage());
        }
    }
    public function postUninstall()
    {
        try {
            $driver = 'PolycomEndpoint';
            EndpointManager::deleteDriver($driver);
        } catch(Exception $e) {
            kohana::log('alert', 'Ignoring ' . $e->getMessage());
        }
    }
    public function repair() {
        parent::repair();
        $this->postInstall();
    }

}
