<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author K Anderson
 * @license MPL
 * @package Bluebox
 * @subpackage Core
 */
// If we're already trying to run the installer, then let things proceed as normal. Clear any redirect events
class InstallerHook
{
    public function checkDBConnectivity()
    {
        // Are heading somewhere other then the installer? If so, check that we're OK to proceed.
        if (!Bluebox_Installer::is_installing())
        {
            // Check DB connectivity
            $manager = Doctrine_Manager::getInstance();

            try
            {
                $manager->getCurrentConnection()->connect();

                Doctrine::getTable('Package')->findAll();
            }
            catch(Doctrine_Connection_Exception $e)
            {
                // We can't connect to the database - run the installer!
                // Get the guess the URL to work on
                Kohana::config_set('core.site_domain', Bluebox_Installer::guess_site_domain());

                url::redirect('/installer');
            }
        }
    }
}

Event::add('system.ready', array(
    'InstallerHook',
    'checkDBConnectivity'
));
