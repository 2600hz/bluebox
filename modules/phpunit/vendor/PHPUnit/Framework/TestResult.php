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
 * @version    SVN: $Id: TestResult.php 4625 2009-02-11 05:39:00Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.0.0
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/CodeCoverage.php';
require_once 'PHPUnit/Util/ErrorHandler.php';
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Util/Printer.php';
require_once 'PHPUnit/Util/Test.php';
require_once 'PHPUnit/Util/Timer.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

if (!class_exists('PHPUnit_Framework_TestResult', FALSE)) {

/**
 * A TestResult collects the results of executing a test case.
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
class PHPUnit_Framework_TestResult implements Countable
{
    protected static $xdebugLoaded = NULL;
    protected static $useXdebug = NULL;

    /**
     * @var    array
     */
    protected $errors = array();

    /**
     * @var    array
     */
    protected $failures = array();

    /**
     * @var    array
     */
    protected $notImplemented = array();

    /**
     * @var    array
     */
    protected $skipped = array();

    /**
     * @var    array
     */
    protected $listeners = array();

    /**
     * @var    integer
     */
    protected $runTests = 0;

    /**
     * @var    float
     */
    protected $time = 0;

    /**
     * @var    PHPUnit_Framework_TestSuite
     */
    protected $topTestSuite = NULL;

    /**
     * Code Coverage information provided by Xdebug.
     *
     * @var    array
     */
    protected $codeCoverageInformation = array();

    /**
     * @var    boolean
     */
    protected $collectCodeCoverageInformation = FALSE;

    /**
     * @var    boolean
     */
    protected $convertErrorsToExceptions = TRUE;

    /**
     * @var    boolean
     */
    protected $stop = FALSE;

    /**
     * @var    boolean
     */
    protected $stopOnFailure = FALSE;

    /**
     * @var    boolean
     */
    protected $lastTestFailed = FALSE;

    /**
     * Registers a TestListener.
     *
     * @param  PHPUnit_Framework_TestListener
     */
    public function addListener(PHPUnit_Framework_TestListener $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * Unregisters a TestListener.
     *
     * @param  PHPUnit_Framework_TestListener $listener
     */
    public function removeListener(PHPUnit_Framework_TestListener $listener)
    {
        foreach ($this->listeners as $key => $_listener) {
            if ($listener === $_listener) {
                unset($this->listeners[$key]);
            }
        }
    }

    /**
     * Flushes all flushable TestListeners.
     *
     * @since  Method available since Release 3.0.0
     */
    public function flushListeners()
    {
        foreach ($this->listeners as $listener) {
            if ($listener instanceof PHPUnit_Util_Printer) {
                $listener->flush();
            }
        }
    }

    /**
     * Adds an error to the list of errors.
     * The passed in exception caused the error.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        if ($e instanceof PHPUnit_Framework_IncompleteTest) {
            $this->notImplemented[] = new PHPUnit_Framework_TestFailure($test, $e);
            $notifyMethod           = 'addIncompleteTest';
        }

        else if ($e instanceof PHPUnit_Framework_SkippedTest) {
            $this->skipped[] = new PHPUnit_Framework_TestFailure($test, $e);
            $notifyMethod    = 'addSkippedTest';
        }

        else {
            $this->errors[] = new PHPUnit_Framework_TestFailure($test, $e);
            $notifyMethod   = 'addError';

            if ($this->stopOnFailure) {
                $this->stop();
            }
        }

        foreach ($this->listeners as $listener) {
            $listener->$notifyMethod($test, $e, $time);
        }

        $this->lastTestFailed = TRUE;
        $this->time          += $time;
    }

    /**
     * Adds a failure to the list of failures.
     * The passed in exception caused the failure.
     *
     * @param  PHPUnit_Framework_Test                 $test
     * @param  PHPUnit_Framework_AssertionFailedError $e
     * @param  float                                  $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        if ($e instanceof PHPUnit_Framework_IncompleteTest) {
            $this->notImplemented[] = new PHPUnit_Framework_TestFailure($test, $e);
            $notifyMethod           = 'addIncompleteTest';
        }

        else if ($e instanceof PHPUnit_Framework_SkippedTest) {
            $this->skipped[] = new PHPUnit_Framework_TestFailure($test, $e);
            $notifyMethod    = 'addSkippedTest';
        }

        else {
            $this->failures[] = new PHPUnit_Framework_TestFailure($test, $e);
            $notifyMethod     = 'addFailure';

            if ($this->stopOnFailure) {
                $this->stop();
            }
        }

        foreach ($this->listeners as $listener) {
            $listener->$notifyMethod($test, $e, $time);
        }

        $this->lastTestFailed = TRUE;
        $this->time          += $time;
    }

    /**
     * Informs the result that a testsuite will be started.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->topTestSuite === NULL) {
            $this->topTestSuite = $suite;
        }

        foreach ($this->listeners as $listener) {
            $listener->startTestSuite($suite);
        }
    }

    /**
     * Informs the result that a testsuite was completed.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        foreach ($this->listeners as $listener) {
            $listener->endTestSuite($suite);
        }
    }

    /**
     * Informs the result that a test will be started.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        $this->lastTestFailed = FALSE;
        $this->runTests      += count($test);

        foreach ($this->listeners as $listener) {
            $listener->startTest($test);
        }
    }

    /**
     * Informs the result that a test was completed.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        foreach ($this->listeners as $listener) {
            $listener->endTest($test, $time);
        }

        if (!$this->lastTestFailed) {
            $this->time += $time;
        }
    }

    /**
     * Returns TRUE if no incomplete test occured.
     *
     * @return boolean
     */
    public function allCompletlyImplemented()
    {
        return $this->notImplementedCount() == 0;
    }

    /**
     * Gets the number of incomplete tests.
     *
     * @return integer
     */
    public function notImplementedCount()
    {
        return count($this->notImplemented);
    }

    /**
     * Returns an Enumeration for the incomplete tests.
     *
     * @return array
     */
    public function notImplemented()
    {
        return $this->notImplemented;
    }

    /**
     * Returns TRUE if no test has been skipped.
     *
     * @return boolean
     * @since  Method available since Release 3.0.0
     */
    public function noneSkipped()
    {
        return $this->skippedCount() == 0;
    }

    /**
     * Gets the number of skipped tests.
     *
     * @return integer
     * @since  Method available since Release 3.0.0
     */
    public function skippedCount()
    {
        return count($this->skipped);
    }

    /**
     * Returns an Enumeration for the skipped tests.
     *
     * @return array
     * @since  Method available since Release 3.0.0
     */
    public function skipped()
    {
        return $this->skipped;
    }

    /**
     * Gets the number of detected errors.
     *
     * @return integer
     */
    public function errorCount()
    {
        return count($this->errors);
    }

    /**
     * Returns an Enumeration for the errors.
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Gets the number of detected failures.
     *
     * @return integer
     */
    public function failureCount()
    {
        return count($this->failures);
    }

    /**
     * Returns an Enumeration for the failures.
     *
     * @return array
     */
    public function failures()
    {
        return $this->failures;
    }

    /**
     * Returns the (top) test suite.
     *
     * @return PHPUnit_Framework_TestSuite
     * @since  Method available since Release 3.0.0
     */
    public function topTestSuite()
    {
        return $this->topTestSuite;
    }

    /**
     * Enables or disables the collection of Code Coverage information.
     *
     * @param  boolean $flag
     * @throws InvalidArgumentException
     * @since  Method available since Release 2.3.0
     */
    public function collectCodeCoverageInformation($flag)
    {
        if (is_bool($flag)) {
            $this->collectCodeCoverageInformation = $flag;
        } else {
            throw new InvalidArgumentException;
        }
    }

    /**
     * Returns whether code coverage information should be collected.
     *
     * @return boolean If code coverage should be collected
     * @since  Method available since Release 3.2.0
     */
    public function getCollectCodeCoverageInformation()
    {
        return $this->collectCodeCoverageInformation;
    }

    /**
     * Appends code coverage information to the test
     *
     * @param PHPUnit_Framework_Test $test
     * @param array                  $data
     * @since Method available since Release 3.2.0
     */
    public function appendCodeCoverageInformation(PHPUnit_Framework_Test $test, $data)
    {
        $deadCode       = array();
        $executableCode = array();

        foreach (array_keys($data) as $file) {
            if (PHPUnit_Util_Filter::isFiltered($file, FALSE)) {
                unset($data[$file]);
            }
        }

        $newFilesToCollect = array_diff_key($data, PHPUnit_Util_Filter::getCoveredFiles());

        if (count($newFilesToCollect) > 0) {
            $deadCode       = PHPUnit_Util_CodeCoverage::getDeadLines($newFilesToCollect);
            $executableCode = PHPUnit_Util_CodeCoverage::getExecutableLines($newFilesToCollect);

            foreach (array_keys($newFilesToCollect) as $file) {
                PHPUnit_Util_Filter::addCoveredFile($file);
            }

            unset($newFilesToCollect);
        }

        if ($test instanceof PHPUnit_Framework_TestCase) {
            $linesToBeCovered = PHPUnit_Util_Test::getLinesToBeCovered(
              get_class($test), $test->getName()
            );

            if (!empty($linesToBeCovered)) {
                $data = array_intersect_key($data, $linesToBeCovered);

                foreach (array_keys($data) as $file) {
                    $data[$file] = array_intersect_key($data[$file], array_flip($linesToBeCovered[$file]));
                }
            }
        }

        $executed = PHPUnit_Util_CodeCoverage::getExecutedLines($data);
        unset($data);

        $this->codeCoverageInformation[] = array(
          'test'       => $test,
          'files'      => $executed,
          'dead'       => $deadCode,
          'executable' => $executableCode,
        );
    }

    /**
     * Returns Code Coverage data per test case.
     *
     * Format of the result array:
     *
     * <code>
     * array(
     *   array(
     *     'test'  => PHPUnit_Framework_Test
     *     'files' => array(
     *       "/tested/code.php" => array(
     *         linenumber => flag
     *       )
     *     )
     *   )
     * )
     * </code>
     *
     * flag < 0: Line is executable but was not executed.
     * flag > 0: Line was executed.
     *
     * @param  boolean $filterTests
     * @return array
     */
    public function getCodeCoverageInformation($filterTests = TRUE)
    {
        return PHPUnit_Util_Filter::getFilteredCodeCoverage(
          $this->codeCoverageInformation, $filterTests
        );
    }

    /**
     * Returns unfiltered Code Coverage data per test case.
     * Returns data in the same form as getCodeCoverageInformation().
     *
     * @return array
     */
    public function getUncoveredWhitelistFiles()
    {
        list(, $missing) = PHPUnit_Util_Filter::getFileCodeCoverageDisposition(
          $this->codeCoverageInformation
        );

        return $missing;
    }

    /**
     * Runs a TestCase.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function run(PHPUnit_Framework_Test $test)
    {
        PHPUnit_Framework_Assert::resetCount();

        $error   = FALSE;
        $failure = FALSE;

        $this->startTest($test);

        $errorHandlerSet = FALSE;

        if ($this->convertErrorsToExceptions) {
            $oldErrorHandler = set_error_handler(
              array('PHPUnit_Util_ErrorHandler', 'handleError'), E_ALL | E_STRICT
            );

            if ($oldErrorHandler === NULL) {
                $errorHandlerSet = TRUE;
            } else {
                restore_error_handler();
            }
        }

        if (self::$xdebugLoaded === NULL) {
            self::$xdebugLoaded = extension_loaded('xdebug');
            self::$useXdebug = self::$xdebugLoaded;
        }

        $useXdebug = self::$useXdebug && $this->collectCodeCoverageInformation && !$test instanceof PHPUnit_Extensions_SeleniumTestCase;

        if ($useXdebug) {
            xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
        }

        PHPUnit_Util_Timer::start();

        try {
            $test->runBare();
        }

        catch (PHPUnit_Framework_AssertionFailedError $e) {
            $failure = TRUE;
        }

        catch (Exception $e) {
            $error = TRUE;
        }

        $time = PHPUnit_Util_Timer::stop();

        if ($useXdebug) {
            $codeCoverage = xdebug_get_code_coverage();
            xdebug_stop_code_coverage();

            $this->appendCodeCoverageInformation(
              $test, $codeCoverage
            );
        }

        if ($errorHandlerSet === TRUE) {
            restore_error_handler();
        }

        $test->addToAssertionCount(PHPUnit_Framework_Assert::getCount());

        if ($error === TRUE) {
            $this->addError($test, $e, $time);
        }

        else if ($failure === TRUE) {
            $this->addFailure($test, $e, $time);
        }

        $this->endTest($test, $time);
    }

    /**
     * Gets the number of run tests.
     *
     * @return integer
     */
    public function count()
    {
        return $this->runTests;
    }

    /**
     * Checks whether the test run should stop.
     *
     * @return boolean
     */
    public function shouldStop()
    {
        return $this->stop;
    }

    /**
     * Marks that the test run should stop.
     *
     */
    public function stop()
    {
        $this->stop = TRUE;
    }

    /**
     * Enables or disables the error-to-exception conversion.
     *
     * @param  boolean $flag
     * @throws InvalidArgumentException
     * @since  Method available since Release 3.2.14
     */
    public function convertErrorsToExceptions($flag)
    {
        if (is_bool($flag)) {
            $this->convertErrorsToExceptions = $flag;
        } else {
            throw new InvalidArgumentException;
        }
    }

    /**
     * Enables or disables the stopping when a failure or error occurs.
     *
     * @param  boolean $flag
     * @throws InvalidArgumentException
     * @since  Method available since Release 3.1.0
     */
    public function stopOnFailure($flag)
    {
        if (is_bool($flag)) {
            $this->stopOnFailure = $flag;
        } else {
            throw new InvalidArgumentException;
        }
    }

    /**
     * Returns the time spent running the tests.
     *
     * @return float
     */
    public function time()
    {
        return $this->time;
    }

    /**
     * Returns whether the entire test was successful or not.
     *
     * @return boolean
     */
    public function wasSuccessful()
    {
        return empty($this->errors) && empty($this->failures);
    }
}

}
?>
