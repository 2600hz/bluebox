<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package     PHPUnit
 */
class PHPUnit_Test_Suite_Core extends PHPUnit_Framework_TestSuite
{
	public function __construct($name, $directory)
	{
		$this->name = $name;

		$files = Kohana::list_files($directory);
		foreach ($files as $file)
		{
			if ( ! is_file($file))
				continue;

			if (substr_compare($file, 'Base_Test'.EXT, strlen($file) - strlen('Base_Test'.EXT)) !== 0)
			{
				PHPUnit_Util_Filter::addFileToFilter($file);
				$this->addTestFile($file);
			}
		}
	}
}
