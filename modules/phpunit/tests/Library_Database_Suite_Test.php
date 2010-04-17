<?php
/**
 * @package     Core
 * @subpackage  Libraries
 * @author      Chris Bandy
 * @group   core
 * @group   core.libraries
 * @group   core.libraries.database
 */
class Library_Database_Suite_Test extends PHPUnit_Test_Suite
{
	public static function suite()
	{
		return new Library_Database_Suite_Test('Library_Database', 'tests/Library_Database');
	}

	protected function setUp()
	{
		$this->sharedFixture['db'] = 'testing';
	}

	protected function tearDown()
	{
		$this->sharedFixture = NULL;
	}
}
