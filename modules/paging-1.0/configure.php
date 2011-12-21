<?php defined('SYSPATH') or die('No direct access allowed.');
class paging_1_0_0_Configure extends Bluebox_Configure
{
    public static $version = 1.0;
    public static $packageName = 'paging';
    public static $displayName = 'Paging/Group Intercom';
    public static $author = 'Rob Hutton';
    public static $vendor = '';
    public static $license = 'MPL';
    public static $summary = 'Paging';
    public static $description = 'This module configures paging groups.';
    public static $default = FALSE;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
    	'core' => 0.1,
        'conference' => 1.0,
	);
    public static $navLabel = 'Paging/Grp Int';
    public static $navBranch = '/Applications/';
    public static $navURL = 'paging/index';
    public static $navSubmenu = array(
		'Create' => array(
			'url' => 'paging/create',
		)
    );
    
    public function finalizeInstall()
    {
    	$confobj = new Conference();
    	$confobj->name = 'Intercom';
    	$confobj->profile= array(
     		'energy-level' => '0',
   	   		'enter-sound' => 'tone_stream://%(800,0,500,600,700)',
    		'exit-sound' => '',
    		'tts-engine' => '',
    		'tts-voice' => '',
    		'comfort-noise' => 1,
    		'kicked-sound' => '',
    		'locked-sound' => '',
    		'is-locked-sound' => '',
    		'is-unlocked-sound' => '',
    		'muted-sound' => '',
    		'unmuted-sound' => '',
    		'caller-controls' => 'none',
    		'pin-sound' => '',
    		'bad-pin-sound' => ''
    	);
  	   	$confobj->save();
  	   	
    	$confobj = new Conference();
    	$confobj->name = 'Paging';
    	$confobj->profile= array(
     		'energy-level' => '0',
   	   		'enter-sound' => 'tone_stream://%(800,0,500,600,700)',
    		'exit-sound' => '',
    		'tts-engine' => '',
    		'tts-voice' => '',
    		'comfort-noise' => 1,
    		'kicked-sound' => '',
    		'locked-sound' => '',
    		'is-locked-sound' => '',
    		'is-unlocked-sound' => '',
    		'muted-sound' => '',
    		'unmuted-sound' => '',
    		'caller-controls' => 'none',
    		'pin-sound' => '',
    		'bad-pin-sound' => ''
       	);
   	   	$confobj->save();
  	   	
  	    message::success("Default Conference profiles created...");
    }
    
    public function postUninstall()
    {
    	$confobj = Doctrine::getTable('Conference')->findOneByName('Intercom');
    	if ($confobj)
    		$confobj->delete();
    	$confobj = Doctrine::getTable('Conference')->findOneByName('Paging');
    	if ($confobj)
    		$confobj->delete();
    }
}