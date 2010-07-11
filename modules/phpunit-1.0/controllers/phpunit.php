<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * PHPUnit controller.
 * 
 * @package	PHPUnit
 */
class Phpunit_Controller extends Controller
{
	const ALLOW_PRODUCTION = FALSE;

	public function index()
	{
		echo "TODO: Usage Instructions....\n";
	}

	public function group($group = FALSE, $config = 'default')
	{
		if ($group)
		{
			$ut = new PHPUnit(array($group));
		}
		else
		{
			Kohana::config_set('phpunit.default.listGroups',TRUE);
			$ut = new PHPUnit();
		}
		
		$ut->execute($config);
	}
	
	public function run()
	{
		$ut = new PHPUnit();
		$ut->execute();
	}
}