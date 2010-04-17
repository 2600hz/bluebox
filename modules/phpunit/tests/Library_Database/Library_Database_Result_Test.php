<?php
/**
 * Database Result Library Unit Tests
 *
 * @package     Core
 * @subpackage  Libraries
 * @author      Chris Bandy
 * @group   core
 * @group   core.libraries
 * @group   core.libraries.database
 * @group   core.libraries.database.result
 */
class Library_Database_Result_Test extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		if (($config = Kohana::config('database.testing')) === NULL)
			$this->markTestSkipped('No database.testing config found.');
	}
	
	/**
	 * @group core.libraries.database.result
	 * @test
	 */
	public function test_count()
	{
		$result = db::query('SELECT 1 UNION SELECT 2 UNION SELECT 3')->execute($this->sharedFixture['db']);

		$this->assertEquals(3, $result->count());
	}

	/**
	 * @group core.libraries.database.result.array_access
	 * @test
	 */
	public function test_offset_exists()
	{
		$result = db::query('SELECT 1 UNION SELECT 2 UNION SELECT 3')->execute($this->sharedFixture['db']);

		$this->assertTrue($result->offsetExists(0));
		$this->assertTrue($result->offsetExists(2));
		$this->assertFalse($result->offsetExists(3));
		$this->assertFalse($result->offsetExists(-1));
	}

	/**
	 * @group core.libraries.database.result.array_access
	 * @test
	 */
	public function test_offset_get()
	{
		$result = db::query('SELECT 1 AS value UNION SELECT 2 UNION SELECT 3')->execute($this->sharedFixture['db'])->as_array();

		$this->assertEquals(array('value' => 1), $result->offsetGet(0));
		$this->assertEquals(array('value' => 3), $result->offsetGet(2));
		$this->assertEquals(NULL, $result->offsetGet(3));
		$this->assertEquals(NULL, $result->offsetGet(-1));
	}

	/**
	 * @expectedException Kohana_Exception
	 * @group core.libraries.database.result.array_access
	 * @test
	 */
	public function test_offset_set()
	{
		$result = db::query('SELECT 1')->execute($this->sharedFixture['db']);

		$result->offsetSet(0, TRUE);
	}

	/**
	 * @expectedException Kohana_Exception
	 * @group core.libraries.database.result.array_access
	 * @test
	 */
	public function test_offset_unset()
	{
		$result = db::query('SELECT 1')->execute($this->sharedFixture['db']);

		$result->offsetUnset(0);
	}

	/**
	 * @group core.libraries.database.result.iterator
	 * @test
	 */
	public function test_current()
	{
		$result = db::query('SELECT 1 AS value UNION SELECT 2')->execute($this->sharedFixture['db'])->as_array();

		// Repeated calls should not advance, see #1817
		$this->assertEquals(array('value' => 1), $result->current());
		$this->assertEquals(array('value' => 1), $result->current());
	}

	/**
	 * @group core.libraries.database.result.iterator
	 * @test
	 */
	public function test_next()
	{
		$result = db::query('SELECT 1 AS value UNION SELECT 2')->execute($this->sharedFixture['db'])->as_array();

		$result->next();

		// next can be called before current, see #1817
		$this->assertEquals(1, $result->key());
		$this->assertEquals(array('value' => 2), $result->current());
	}

	/**
	 * @group core.libraries.database.result.iterator
	 * @test
	 */
	public function test_prev()
	{
		$result = db::query('SELECT 1 AS value UNION SELECT 2 UNION SELECT 3')->execute($this->sharedFixture['db'])->as_array();

		$result->seek(2);

		$result->prev();

		$this->assertEquals(1, $result->key());
		$this->assertEquals(array('value' => 2), $result->current());

		$result->prev();

		$this->assertEquals(0, $result->key());
		$this->assertEquals(array('value' => 1), $result->current());

		$result->prev();

		$this->assertFalse($result->valid());
	}

	/**
	 * @group core.libraries.database.result.iterator
	 * @test
	 */
	public function test_seek()
	{
		$result = db::query('SELECT 1 AS value UNION SELECT 2 UNION SELECT 3')->execute($this->sharedFixture['db'])->as_array();

		$result->seek(2);

		$this->assertEquals(2, $result->key());
		$this->assertEquals(array('value' => 3), $result->current());

		$result->seek(0);

		$this->assertEquals(0, $result->key());
		$this->assertEquals(array('value' => 1), $result->current());
	}

	/**
	 * @group core.libraries.database.result.iterator
	 * @test
	 */
	public function test_iteration()
	{
		$result = db::query('SELECT 1 AS value UNION SELECT 2 UNION SELECT 3')->execute($this->sharedFixture['db'])->as_array();

		$this->assertEquals(0, $result->key());
		$this->assertEquals(array('value' => 1), $result->current());
		$this->assertTrue($result->valid());

		$result->next();

		$this->assertEquals(1, $result->key());
		$this->assertEquals(array('value' => 2), $result->current());
		$this->assertTrue($result->valid());

		$result->next();

		$this->assertEquals(2, $result->key());
		$this->assertEquals(array('value' => 3), $result->current());
		$this->assertTrue($result->valid());

		$result->next();

		$this->assertFalse($result->valid());

		$result->rewind();

		$this->assertEquals(0, $result->key());
		$this->assertEquals(array('value' => 1), $result->current());
		$this->assertTrue($result->valid());
	}

	/**
	 * @group core.libraries.database.result
	 * @test
	 */
	public function test_get()
	{
		// FIXME as_array should be removed
		$result = db::query('SELECT 1 AS value UNION SELECT 2')->execute($this->sharedFixture['db'])->as_array();

		$this->assertEquals(1, $result->get('value'));
		$this->assertEquals(1, $result->get('value'));
	}

	/**
	 * @group core.libraries.database.result
	 * @test
	 */
	public function test_array()
	{
		$result = db::query('SELECT 1 AS value UNION SELECT 2')->execute($this->sharedFixture['db'])->as_array(TRUE);

		$this->assertEquals(array(array('value' => 1), array('value' => 2)), $result);
	}

	/**
	 * @group core.libraries.database.result
	 * @test
	 */
	public function test_object()
	{
		$result = db::query('SELECT 1 AS value')->execute($this->sharedFixture['db'])->as_object();

		$row = $result->current();

		$this->assertObjectHasAttribute('value', $row);
		$this->assertEquals(1, $row->value);
	}

	/**
	 * @group core.libraries.database.result
	 * @test
	 */
	public function test_class()
	{
		$result = db::query('SELECT 1 AS value')->execute($this->sharedFixture['db'])->as_object('Library_Database_Result_Test_Class');
		
		$row = $result->current();

		$this->assertTrue($row instanceof Library_Database_Result_Test_Class);
		$this->assertObjectHasAttribute('value', $row);
		$this->assertEquals(1, $row->value);
	}

}


/**
 * Used to test object fetching
 */
final class Library_Database_Result_Test_Class {}
