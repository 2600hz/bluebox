<?php defined('SYSPATH') or die('No direct access allowed.');
class Conferences_Configure extends Bluebox_Configure
{
    public static $version = 0.1;
    public static $packageName = 'conference';
    public static $author = 'Darren Schreiber';
    public static $vendor = 'Bluebox';
    public static $license = 'MPL';
    public static $summary = 'Conference Management';
    public static $default = TRUE;
    public static $type = Bluebox_Installer::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1
    );
    public static $navIcon = 'assets/img/icons/mainConferencesX.png';
    public static $navLabel = 'Conferences';
    public static $navBranch = '/Destinations/';
    public static $navURL = 'conference/index';
    public static $navSubmenu = array(
        'Search Conferences' => '/conference/index',
        'Add Conference' => '/conference/add',
        'Edit Conference' => array(
            'url' => '/conference/edit',
            'disabled' => TRUE
        ) ,
        'Delete Conference' => array(
            'url' => '/conference/delete',
            'disabled' => TRUE
        )
    );
    
    public function postInstall()
    {
        $Sounds = new ConferenceSoundmap();
        $Sounds->name = 'default';
        $Sounds->mute = 'conference/conf-muted.wav';
        $Sounds->unmute = 'conference/conf-unmuted.wav';
        $Sounds->onlymember = 'conference/conf-alone.wav';
        $Sounds->join = 'tone_stream://%(200,0,500,600,700)';
        $Sounds->exit = 'tone_stream://%(500,0,300,200,100,50,25)';
        $Sounds->kicked = 'conference/conf-kicked.wav';
        $Sounds->locked = 'conference/conf-locked.wav';
        $Sounds->unlocked = 'conference/conf-is-unlocked.wav';
        $Sounds->reject_locked = 'conference/conf-is-locked.wav';
        $Sounds->askpin = 'conference/conf-pin.wav';
        $Sounds->badpin = 'conference/conf-bad-pin.wav';
        $Sounds->background = '';
        $Sounds->save();
    }
}
