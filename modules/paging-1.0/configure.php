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
    		'rate' => '1000',
    		'interval' => '20',
     		'energy-level' => '0',
   	   		'enter-sound' => 'tone_stream://%(200,0,500,600,700)',
    		'exit-sound' => 'tone_stream://%(500,0,300,200,100,50,25)',
   	   		'caller-id-name' => '$${outbound_caller_name}',
  	   		'caller-id-number' => '$${outbound_caller_id}');
  	   	$confobj->save();
  	   	
    	$confobj = new Conference();
    	$confobj->name = 'Paging';
    	$confobj->profile= array(
    		'rate' => '1000',
    		'interval' => '20',
     		'energy-level' => '0',
   	   		'enter-sound' => 'tone_stream://%(200,0,500,600,700)',
    		'exit-sound' => 'tone_stream://%(500,0,300,200,100,50,25)',
   	   		'caller-id-name' => '$${outbound_caller_name}',
  	   		'caller-id-number' => '$${outbound_caller_id}');
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