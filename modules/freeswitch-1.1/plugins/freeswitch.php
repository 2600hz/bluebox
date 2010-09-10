<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * freeswitch.php - Provides logic for the installer telephony configuration step
 * @author K Anderson
 * @license MPL
 * @package Bluebox
 */
class Freeswitch_Plugin extends Bluebox_Plugin
{
    // A list of possible directories that may have the freeswitch.xml
    // file (denoting the FS directory.
    public static $scanDirs = array(
        '/usr/local/freeswitch',
        '/usr/local/freeswitch-trunk',
        '/opt/freeswitch',
        '/opt/freeswitch-trunk',
        'C:\FreeSWITCH',
        'C:\freeswitch'
    );
    
    /**
     * Setup the subview for the address plugin
     */
    public function install()
    {
        $subview = new View('freeswitch/install');

        $subview->tab = 'main';

        $subview->section = 'general';

        $cfg_root = $this->session->get('installer.cfg_root', FALSE);

        $audio_root = $this->session->get('installer.audio_root', FALSE);

        $fsDefaultCfg = Kohana::config('freeswitch.cfg_root');

        if (!$cfg_root)
        {
            foreach (self::$scanDirs as $testDir)
            {
                $testPath = rtrim($testDir) . '/conf/freeswitch.xml';

                if (file_exists($testPath))
                {
                    $cfg_root = rtrim($testDir) . '/conf';

                    break;
                }
            }

            if (empty($cfg_root))
            {
                $cfg_root = $fsDefaultCfg;
            }
        }

        if (!$audio_root)
        {
            foreach (self::$scanDirs as $testDir)
            {
                $testPath = rtrim($testDir . '/') . '/sounds';

                if (is_dir($testPath))
                {
                    $audio_root = $testPath;

                    break;
                }
            }

            if (empty($audio_root))
            {
#                $audio_root = $fsDefaultAudioRoot;
            }
        }

        $subview->cfg_root = $cfg_root;

        $subview->audio_root = $audio_root;

        $subview->esl_host = $this->session->get('installer.esl_host', Kohana::config('freeswitch.ESLHost'));

        $subview->esl_port = $this->session->get('installer.esl_port', Kohana::config('freeswitch.ESLPort'));

        $subview->esl_auth = $this->session->get('installer.esl_auth', Kohana::config('freeswitch.ESLAuth'));

        // Get a list of existing sip_profiles and warn the user that these will be deleted
        $sipProfiles = glob(rtrim($subview->cfg_root, '/') . '/sip_profiles/*.xml', GLOB_MARK);

        // See if any xml files that bluebox uses exist already and warn the user they will be deleted
        $filemaps = Kohana::config('freeswitch.filemap');

        $oldXmlFiles = array();

        foreach ($filemaps as $filemap)
        {
            if ($fsDefaultCfg != $subview->cfg_root)
            {
                $filemap['filename'] = str_replace($fsDefaultCfg, $subview->cfg_root, $filemap['filename']);
            }

            if (file_exists($filemap['filename']))
            {
                $oldXmlFiles[] = $filemap['filename'];
            }
        }

        // Create one unique list of all bluebox xml and extra sip profiles
        $conflictXmlFiles = array_unique(array_merge($oldXmlFiles, $sipProfiles));

        $subview->conflictXmlFiles = $conflictXmlFiles;

        $this->session->set('installer.conflictXmlFiles', $conflictXmlFiles);

        // Add our view to the main application
        $this->views[] = $subview;
    }

    public function save()
    {
        if (!class_exists('DOMDocument'))
        {
            message::set('This driver requires '
                . html::anchor('http://us3.php.net/manual/en/class.domdocument.php', 'DOMDocument', array('target' => '_new'))
                . ' to be installed and active');

            return false;
        }

        // This array maps the telephony returns to the telephony file
        $telephonyOptions = array(
            'cfg_root' => rtrim($this->session->get('installer.cfg_root'), '/'),
            'audio_root' => rtrim($this->session->get('installer.audio_root'), '/'),
            'ESLHost' => $this->session->get('installer.esl_host'),
            'ESLPort' => $this->session->get('installer.esl_port'),
            'ESLAuth' => $this->session->get('installer.esl_auth')
        );

        if ( ! is_dir($telephonyOptions['cfg_root']))
        {
            message::set('Unable to access directory <pre>' .$telephonyOptions['cfg_root'] .'</pre>');
            
            return false;
        }

        if ( ! is_dir($telephonyOptions['audio_root']))
        {
            message::set('Unable to access directory <pre>' .$telephonyOptions['audio_root'] .'</pre>');

            return false;
        }

        // Write $telephonyOptions to freeswitch.php
        if ( ! Installer_Controller::updateConfig($telephonyOptions, 'freeswitch'))
        {
            return false;
        }

        // Set the driver name in telephony.php
        if (!Installer_Controller::updateConfig(array('driver' => 'FreeSwitch'), 'telephony'))
        {
            return false;
        }
        
        // Reload the new telephony options so we can use the filemap
        Kohana::config_clear('freeswitch');

        Kohana::config_load('freeswitch');

        $filemaps = Kohana::config('freeswitch.filemap');

        $notWritable = array();

        foreach ($filemaps as $filemap)
        {
            if ( ! filesystem::is_writable($filemap['filename']))
            {
                $notWritable[] = $filemap['filename'];
            }
        }

        if ( ! empty($notWritable))
        {
            $notWritable = array_unique($notWritable);

            if (empty($this->template->error))
            {
                message::set('Ensure the web server has write permission on these files, and SELINUX allows this action.'
                    .'<div class="write_help">Unable to write to the following file(s):</div>'
                    .'<div>'
                    .arr::arrayToUL($notWritable, array(), array('class' => 'error_details', 'style' => 'text-align:left;'))
                    .'</div>'
                );
            }
            
            return false;
        }

        // Make sure that if the user changed the directory and any conflicts were found that the user
        // knows these will be deleted
        $existingProfiles = glob(rtrim($telephonyOptions['cfg_root'], '/') . '/sip_profiles/*.xml', GLOB_MARK);

        $oldXmlFiles = array();

        foreach ($filemaps as $filemap)
        {
            if (file_exists($filemap['filename']))
            {
                $oldXmlFiles[] = $filemap['filename'];
            }
        }

        $conflictXmlFiles = $this->session->get('installer.conflictXmlFiles');

        foreach (array_unique(array_merge($existingProfiles, $oldXmlFiles)) as $existingFile)
        {
            if ( ! in_array($existingFile, $conflictXmlFiles))
            {
                message::set('Conflicting configuration files will be permanently erased if you continue!');

                message::set('Click next again to proceed...', 'alert');

                // This session var lets the user continue the second time around (after the warning)
                $this->session->set('installer.confirm_delete', true);
                
                return false;
            }
        }

        // If there are conflictXmlFile in the session then the user has seen this list
        // so they SHOULD be aware we are about to delete them... should
        $conflictXmlFiles = $this->session->get('installer.conflictXmlFiles');

        if ( ! empty($conflictXmlFiles) && is_array($conflictXmlFiles))
        {
            $confirmDelete = $this->session->get('installer.confirm_delete', false);

            if (empty($confirmDelete))
            {
                message::set('Conflicting configuration files will be permanently erased if you continue!');

                message::set('Click next again to proceed...', 'alert');

                // This session var lets the user continue the second time around (after the warning)
                $this->session->set('installer.confirm_delete', true);
                
                return false;
            }

            foreach ($conflictXmlFiles as $conflictXmlFile)
            {
                if ( ! filesystem::delete($conflictXmlFile))
                {
                    Kohana::log('error', 'Unable to unlink ' . $conflictXmlFile);
                    
                    $unlinkErrors[] = $conflictXmlFile;
                }
            }
        }

        // If there are files that could not be deleted, inform the user
        if ( ! empty($unlinkErrors))
        {
            message::set('Manually remove these files or change the file permissions.'
                .'<div class="write_help">Unable to delete incompatible file(s):</div>'
                .'<div>'
                .arr::arrayToUL($unlinkErrors, array(), array('class' => 'error_details', 'style' => 'text-align:left;'))
                .'</div>'
            );
            
            return false;
        }

        $this->session->set('installer.default_packages', kohana::config('freeswitch.default_packages'));

        return true;
    }

    public function postInstallReload()
    {
        if (class_exists('EslManager', TRUE))
        {
            $esl = new EslManager();

            $esl->reloadacl();
            
            $esl->reloadxml();
        }
    }
}
