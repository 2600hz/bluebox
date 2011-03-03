<?php defined('SYSPATH') or die('No direct access allowed.');

class TimeOfDay_1_1_0_Configure extends Bluebox_Configure
{
    public static $version = 1.1;
    public static $packageName = 'timeofday';
    public static $displayName = 'Time Based Routes';
    public static $author = 'Karl Anderson & Jort Bloem';
    public static $vendor = 'BitBashing & BTG';
    public static $license = 'MPL';
    public static $default = true;
    public static $type = Package_Manager::TYPE_MODULE;
    public static $required = array(
        'core' => 0.1,
	'timezone' => 1.0
    );
    public static $navLabel = 'Time Based Routes';
    public static $navBranch = '/Applications/';
    public static $navURL = 'timeofday/index';
    public static $navSubmenu = array(
        'Search Time Based Routes' => 'timeofday/index',
        'Add Time Based Route' => 'timeofday/create',
        'Edit Time Based Route' => array(
            'url' => 'timeofday/edit',
            'disabled' => true
        ) ,
        'Delete Time Based Route' => array(
            'url' => 'timeofday/delete',
            'disabled' => true
        )
    );
    public function migrate()
    {
	$conn = Doctrine_Manager::connection();
	$alter = array (
		'timezone'=>array(
			'type'=>'string',
			'length' => 100,
		)
	);
	$conn->export->alterTable('time_of_day', array('add'=>$alter));
	/*
	foreach (Doctrine::getTable('TimeOfDay')->findAll() AS $timeofday) {
		$timeofday->timezone=Kohana::config('locale.timezone');
		$timeofday->save();
	}
	$alter['timezone']['notnull']=true;
	$alter['timezone']['notblank']=true;
	#$conn->export->alterTable('time_of_day', array('change'=>$alter));
	*/
    }
}
