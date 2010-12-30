<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Asterisk
 * @author     K Anderson <bitbashing@gmail.com>
 * @license    Mozilla Public License (MPL)
 */
class Asterisk_Plugin extends Bluebox_Plugin
{
    public function trunkView()
    {
        $subview = new View('asterisk/trunkView');

        $subview->tab = 'main';

        $subview->section = 'routing';

        $subview->trunk = $this->trunk;

        // Add our view to the main application
        $this->views[] = $subview;
    }

    /**
     * Setup the subview for the address plugin
     */
    public function install()
    {                    
        $subview = new View('asterisk/install');

        $subview->tab = 'main';

        $subview->section = 'general';

        $subview->ast_root = $this->session->get('installer.ast_root', '/etc/asterisk');

        $subview->ami_host = $this->session->get('installer.ami_host', Kohana::config('asterisk.AmiHost'));

        $subview->ami_port = $this->session->get('installer.ami_port', Kohana::config('asterisk.AmiPort'));

        $subview->ami_user = $this->session->get('installer.ami_user', Kohana::config('asterisk.AmiUser'));

        $subview->ami_pass = $this->session->get('installer.ami_pass', Kohana::config('asterisk.AmiPass'));

        message::set('This module is experimental and not ready for production use!', 'alert');

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function save()
    {
        // This array maps the telephony returns to the telephony file
        $telephonyOptions = array(
            'cfg_root' => rtrim($this->session->get('installer.ast_root'), '/'),
            'AmiHost' => $this->session->get('installer.ami_host'),
            'AmiPort' => $this->session->get('installer.ami_port'),
            'AmiUser' => $this->session->get('installer.ami_user'),
            'AmiPass' => $this->session->get('installer.ami_pass'),
        );

        if (!is_dir($telephonyOptions['cfg_root']))
        {
            message::set('Unable to access directory <pre>' .$telephonyOptions['cfg_root'] .'</pre>');

            return false;
        }

        // Write $telephonyOptions to asterisk.php
        if (!Installer_Controller::updateConfig($telephonyOptions, 'asterisk'))
        {
            return false;
        }

        // Set the driver name in telephony.php
        if (!Installer_Controller::updateConfig(array('driver' => 'Asterisk'), 'telephony'))
        {
            return false;
        }

        // Reload the new asterisk options
        Kohana::config_clear('asterisk');

        Kohana::config_load('asterisk');

        $this->session->set('installer.default_packages', kohana::config('asterisk.default_packages'));

        return true;
    }
}