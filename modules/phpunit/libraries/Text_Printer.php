<?php

require_once Kohana::find_file('vendor', 'PHPUnit/Framework');
require_once Kohana::find_file('vendor', 'PHPUnit/Util/Filter');
require_once Kohana::find_file('vendor', 'PHPUnit/Util/Printer');
require_once Kohana::find_file('vendor', 'PHPUnit/Util/Test');

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Prints the result of a TextUI TestRunner run.
 *
 * @category   Testing
 * @package	PHPUnit
 * @author	 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license	http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version	Release: @package_version@
 * @link	   http://www.phpunit.de/
 * @since	  Class available since Release 2.0.0
 */
class Text_Printer extends PHPUnit_Util_Printer implements PHPUnit_Framework_TestListener
{
	protected $line_length = 0;
	protected $max_line_length = 0;
	
	protected $lines = array();
	protected $line = array();
	
	protected $test_success = TRUE;
	
	protected $num_assertions = 0;
	
	public function __construct($out = NULL, $verbose = FALSE, $colors = FALSE, $debug = FALSE)
	{
		parent::__construct($out);
	}
	
	public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
	{
		$this->test_success = FALSE; 
		$this->writeOutput("ERROR", TRUE);
	}

	/**
	 * A failure occurred.
	 *
	 * @param  PHPUnit_Framework_Test				 $test
	 * @param  PHPUnit_Framework_AssertionFailedError $e
	 * @param  float								  $time
	 */
	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
	{
		$this->test_success = FALSE;
		$this->writeOutput("FAILURE", TRUE);
	}

	/**
	 * Incomplete test.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  Exception			  $e
	 * @param  float				  $time
	 */
	public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
	{
		$this->test_success = FALSE;
		$this->writeOutput("INCOMPLETE", TRUE);
	}

	/**
	 * Skipped test.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  Exception			  $e
	 * @param  float				  $time
	 * @since  Method available since Release 3.0.0
	 */
	public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
	{
		$this->test_success = FALSE;
		$this->writeOutput("SKIPPED", TRUE);
	}

	/**
	 * A test suite started.
	 *
	 * @param  PHPUnit_Framework_TestSuite $suite
	 * @since  Method available since Release 2.2.0
	 */
	public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
	{
		$this->writeOutput($suite->getName()."\n");
		$this->resetLength();
		$i = 0;
		
		while ($i != strlen($suite->getName()))
		{
			$this->writeOutput("-");
			$i++;
		}
		$this->writeOutput("\n");
		$this->resetLength();
	}

	/**
	 * A test suite ended.
	 *
	 * @param  PHPUnit_Framework_TestSuite $suite
	 * @since  Method available since Release 2.2.0
	 */
	public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
	{
		$this->writeOutput("\n");
		$this->resetLength();
	}

	/**
	 * A test started.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 */
	public function startTest(PHPUnit_Framework_Test $test)
	{
		$this->test_success = TRUE;
		$this->writeOutput($test->getName());
	}

	/**
	 * A test ended.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  float				  $time
	 */
	public function endTest(PHPUnit_Framework_Test $test, $time)
	{
		if ($test instanceof PHPUnit_Framework_TestCase) {
            $this->num_assertions += $test->getNumAssertions();
        }
        
		if ($this->test_success)
		{
			$this->writeOutput("SUCCESS", TRUE);
		}
		$this->writeOutput("\n");
		$this->resetLength();
		
	}
	
	protected function writeOutput($string, $result = FALSE)
	{
		if ($result)
		{
			// Pad to 45 chars
			$padding = 45 - $this->line_length; 
			$i = 0;

			while ($i < $padding)
			{
				echo " ";
				$i++;
			}
		}
		else
		{
			$this->line_length = $this->line_length + strlen($string);
		}
		echo $string;
	}
	
	protected function resetLength()
	{	
		$this->line_length = 0;
	}
	
}
?>
