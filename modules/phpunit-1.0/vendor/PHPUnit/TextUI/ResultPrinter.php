<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2009, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id: ResultPrinter.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.0.0
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Util/Printer.php';
require_once 'PHPUnit/Util/Test.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Prints the result of a TextUI TestRunner run.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 2.0.0
 */
class PHPUnit_TextUI_ResultPrinter extends PHPUnit_Util_Printer implements PHPUnit_Framework_TestListener
{
    const EVENT_TEST_START      = 0;
    const EVENT_TEST_END        = 1;
    const EVENT_TESTSUITE_START = 2;
    const EVENT_TESTSUITE_END   = 3;

    /**
     * @var    integer
     */
    protected $column = 0;

    /**
     * @var    array
     */
    protected $numberOfTests = array();

    /**
     * @var    array
     */
    protected $testSuiteSize = array();

    /**
     * @var    integer
     */
    protected $lastEvent = -1;

    /**
     * @var    boolean
     */
    protected $lastTestFailed = FALSE;

    /**
     * @var    boolean
     */
    protected $colors = FALSE;

    /**
     * @var    boolean
     */
    protected $debug = FALSE;

    /**
     * @var    boolean
     */
    protected $verbose = FALSE;

    /**
     * @var    integer
     */
    protected $numAssertions = 0;

    /**
     * Constructor.
     *
     * @param  mixed   $out
     * @param  boolean $verbose
     * @param  boolean $colors
     * @param  boolean $debug
     * @throws InvalidArgumentException
     * @since  Method available since Release 3.0.0
     */
    public function __construct($out = NULL, $verbose = FALSE, $colors = FALSE, $debug = FALSE)
    {
        parent::__construct($out);

        if (is_bool($colors)) {
            $this->colors = $colors;
        } else {
            throw new InvalidArgumentException;
        }

        if (is_bool($debug)) {
            $this->debug = $debug;
        } else {
            throw new InvalidArgumentException;
        }

        if (is_bool($verbose)) {
            $this->verbose = $verbose;
        } else {
            throw new InvalidArgumentException;
        }
    }

    /**
     * @param  PHPUnit_Framework_TestResult $result
     */
    public function printResult(PHPUnit_Framework_TestResult $result)
    {
        $this->printHeader($result->time());

        if ($result->errorCount() > 0) {
            $this->printErrors($result);
        }

        if ($result->failureCount() > 0) {
            if ($result->errorCount() > 0) {
                print "\n--\n\n";
            }

            $this->printFailures($result);
        }

        if ($this->verbose) {
            if ($result->notImplementedCount() > 0) {
                if ($result->failureCount() > 0) {
                    print "\n--\n\n";
                }

                $this->printIncompletes($result);
            }

            if ($result->skippedCount() > 0) {
                if ($result->notImplementedCount() > 0) {
                    print "\n--\n\n";
                }

                $this->printSkipped($result);
            }
        }

        $this->printFooter($result);
    }

    /**
     * @param  array   $defects
     * @param  integer $count
     * @param  string  $type
     */
    protected function printDefects(array $defects, $count, $type)
    {
        static $called = FALSE;

        if ($count == 0) {
            return;
        }

        $this->write(
          sprintf(
            "%sThere %s %d %s%s:\n",

            $called ? "\n" : '',
            ($count == 1) ? 'was' : 'were',
            $count,
            $type,
            ($count == 1) ? '' : 's'
          )
        );

        $i = 1;

        foreach ($defects as $defect) {
            $this->printDefect($defect, $i++);
        }

        $called = TRUE;
    }

    /**
     * @param  PHPUnit_Framework_TestFailure $defect
     * @param  integer                       $count
     */
    protected function printDefect(PHPUnit_Framework_TestFailure $defect, $count)
    {
        $this->printDefectHeader($defect, $count);
        $this->printDefectTrace($defect);
    }

    /**
     * @param  PHPUnit_Framework_TestFailure $defect
     * @param  integer                       $count
     */
    protected function printDefectHeader(PHPUnit_Framework_TestFailure $defect, $count)
    {
        $failedTest = $defect->failedTest();

        if ($failedTest instanceof PHPUnit_Framework_SelfDescribing) {
            $testName = $failedTest->toString();
        } else {
            $testName = get_class($failedTest);
        }

        $this->write(
          sprintf(
            "\n%d) %s\n",

            $count,
            $testName
          )
        );
    }

    /**
     * @param  PHPUnit_Framework_TestFailure $defect
     */
    protected function printDefectTrace(PHPUnit_Framework_TestFailure $defect)
    {
        $this->write(
          $defect->toStringVerbose($this->verbose) .
          PHPUnit_Util_Filter::getFilteredStacktrace(
            $defect->thrownException(),
            FALSE
          )
        );
    }

    /**
     * @param  PHPUnit_Framework_TestResult  $result
     */
    protected function printErrors(PHPUnit_Framework_TestResult $result)
    {
        $this->printDefects($result->errors(), $result->errorCount(), 'error');
    }

    /**
     * @param  PHPUnit_Framework_TestResult  $result
     */
    protected function printFailures(PHPUnit_Framework_TestResult $result)
    {
        $this->printDefects($result->failures(), $result->failureCount(), 'failure');
    }

    /**
     * @param  PHPUnit_Framework_TestResult  $result
     */
    protected function printIncompletes(PHPUnit_Framework_TestResult $result)
    {
        $this->printDefects($result->notImplemented(), $result->notImplementedCount(), 'incomplete test');
    }

    /**
     * @param  PHPUnit_Framework_TestResult  $result
     * @since  Method available since Release 3.0.0
     */
    protected function printSkipped(PHPUnit_Framework_TestResult $result)
    {
        $this->printDefects($result->skipped(), $result->skippedCount(), 'skipped test');
    }

    /**
     * @param  float   $timeElapsed
     */
    protected function printHeader($timeElapsed)
    {
        $this->write(
          sprintf(
            "%sTime: %s\n\n",
            $this->verbose ? "\n" : "\n\n",
            PHPUnit_Util_Timer::secondsToTimeString($timeElapsed)
          )
        );
    }

    /**
     * @param  PHPUnit_Framework_TestResult  $result
     */
    protected function printFooter(PHPUnit_Framework_TestResult $result)
    {
        if ($result->wasSuccessful() &&
            $result->allCompletlyImplemented() &&
            $result->noneSkipped()) {
            if ($this->colors) {
                $this->write("\x1b[30;42m\x1b[2K");
            }

            $this->write(
              sprintf(
                "OK (%d test%s, %d assertion%s)\n",

                count($result),
                (count($result) == 1) ? '' : 's',
                $this->numAssertions,
                ($this->numAssertions == 1) ? '' : 's'
              )
            );

            if ($this->colors) {
                $this->write("\x1b[0m\x1b[2K");
            }
        }

        else if ((!$result->allCompletlyImplemented() ||
                  !$result->noneSkipped())&&
                 $result->wasSuccessful()) {
            if ($this->colors) {
                $this->write("\x1b[30;43m\x1b[2KOK, but incomplete or skipped tests!\n\x1b[0m\x1b[30;43m\x1b[2K");
            } else {
                $this->write("OK, but incomplete or skipped tests!\n");
            }

            $this->write(
              sprintf(
                "Tests: %d, Assertions: %d%s%s.\n",

                count($result),
                $this->numAssertions,
                $this->getCountString($result->notImplementedCount(), 'Incomplete'),
                $this->getCountString($result->skippedCount(), 'Skipped')
              )
            );

            if ($this->colors) {
                $this->write("\x1b[0m\x1b[2K");
            }
        }

        else {
            $this->write("\n");

            if ($this->colors) {
                $this->write("\x1b[37;41m\x1b[2KFAILURES!\n\x1b[0m\x1b[37;41m\x1b[2K");
            } else {
                $this->write("FAILURES!\n");
            }

            $this->write(
              sprintf(
                "Tests: %d, Assertions: %s%s%s%s%s.\n",

                count($result),
                $this->numAssertions,
                $this->getCountString($result->failureCount(), 'Failures'),
                $this->getCountString($result->errorCount(), 'Errors'),
                $this->getCountString($result->notImplementedCount(), 'Incomplete'),
                $this->getCountString($result->skippedCount(), 'Skipped')
              )
            );

            if ($this->colors) {
                $this->write("\x1b[0m\x1b[2K");
            }
        }
    }

    /**
     * @param  integer $count
     * @param  string  $name
     * @return string
     * @since  Method available since Release 3.0.0
     */
    protected function getCountString($count, $name)
    {
        $string = '';

        if ($count > 0) {
            $string = sprintf(
              ', %s: %d',

              $name,
              $count
            );
        }

        return $string;
    }

    /**
     */
    public function printWaitPrompt()
    {
        $this->write("\n<RETURN> to continue\n");
    }

    /**
     * An error occurred.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->writeProgress('E');
        $this->lastTestFailed = TRUE;
    }

    /**
     * A failure occurred.
     *
     * @param  PHPUnit_Framework_Test                 $test
     * @param  PHPUnit_Framework_AssertionFailedError $e
     * @param  float                                  $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->writeProgress('F');
        $this->lastTestFailed = TRUE;
    }

    /**
     * Incomplete test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->writeProgress('I');
        $this->lastTestFailed = TRUE;
    }

    /**
     * Skipped test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->writeProgress('S');
        $this->lastTestFailed = TRUE;
    }

    /**
     * A testsuite started.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->verbose) {
            $name = $suite->getName();

            if (empty($name)) {
                $name = 'Test Suite';
            }

            $this->write(
              sprintf(
                "%s%s%s\n",

                $this->lastEvent == self::EVENT_TESTSUITE_END ? "\n" : '',
                str_repeat(' ', count($this->testSuiteSize)),
                $name
              )
            );
        }

        if ($this->verbose || empty($this->numberOfTests)) {
            array_push($this->numberOfTests, 0);
            array_push($this->testSuiteSize, count($suite));
        }

        $this->lastEvent = self::EVENT_TESTSUITE_START;
    }

    /**
     * A testsuite ended.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->verbose) {
            array_pop($this->numberOfTests);
            array_pop($this->testSuiteSize);

            $this->column = 0;

            if ($this->lastEvent != self::EVENT_TESTSUITE_END) {
                $this->write("\n");
            }
        }

        $this->lastEvent = self::EVENT_TESTSUITE_END;
    }

    /**
     * A test started.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        if ($this->verbose) {
            $this->numberOfTests[count($this->numberOfTests)-1]++;
        }

        else if (isset($this->numberOfTests[0])) {
            $this->numberOfTests[0]++;
        }

        else {
            $this->numberOfTests = array(1);
        }

        $this->lastEvent = self::EVENT_TEST_START;

        if ($this->debug) {
            $this->write(
              sprintf(
                "\nStarting test '%s'.\n", PHPUnit_Util_Test::describe($test)
              )
            );
        }
    }

    /**
     * A test ended.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if (!$this->lastTestFailed) {
            $this->writeProgress('.');
        }

        if ($test instanceof PHPUnit_Framework_TestCase) {
            $this->numAssertions += $test->getNumAssertions();
        }

        $this->lastEvent      = self::EVENT_TEST_END;
        $this->lastTestFailed = FALSE;
    }

    /**
     * @param  string $progress
     */
    protected function writeProgress($progress)
    {
        $indent = max(0, count($this->testSuiteSize) - 1);

        if ($this->column == 0) {
            $this->write(str_repeat(' ', $indent));
        }

        $this->write($progress);

        if ($this->column++ == 60 - 1 - $indent) {
            if ($this->verbose) {
                $numberOfTests = $this->numberOfTests[count($this->numberOfTests)-1];
                $testSuiteSize = $this->testSuiteSize[count($this->testSuiteSize)-1];
            } else {
                $numberOfTests = $this->numberOfTests[0];
                $testSuiteSize = $this->testSuiteSize[0];
            }

            $width = strlen((string)$testSuiteSize);

            $this->write(
              sprintf(
                ' %' . $width . 'd / %' . $width . "d\n",

                $numberOfTests,
                $testSuiteSize
              )
            );

            $this->column = 0;
        }
    }
}
?>
