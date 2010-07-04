<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @author K Anderson
 * @license LGPL
 * @package Bluebox
 * @subpackage Core
 */
// If we're already trying to run the installer, then let things proceed as normal. Clear any redirect events
class InstallerHook
{
    public function checkUri()
    {
        // Are heading somewhere other then the installer? If so, check that we're OK to proceed.
        if (!Bluebox_Core::is_installing()) {
            // Check DB connectivity
            $manager = Doctrine_Manager::getInstance();
            try {
                $manager->getCurrentConnection()->connect();
            }
            catch(Doctrine_Connection_Exception $e) {
                // We can't connect to the database - run the installer!
                // Get the guess the URL to work on
                Kohana::config_set('core.site_domain', '/' . url::guess_site_domain() . '/');
                url::redirect('/installer');
                exit();
            }
        }
    }
}
Event::add('system.post_routing', array(
    'InstallerHook',
    'checkUri'
));
