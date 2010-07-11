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
 * @version    SVN: $Id: XML.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.3.0
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Class.php';
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Util/Printer.php';
require_once 'PHPUnit/Util/XML.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * A TestListener that generates a logfile of the test execution in XML markup.
 *
 * The XML markup used is the same as the one that is used by the JUnit Ant task.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 2.1.0
 */
class PHPUnit_Util_Log_XML extends PHPUnit_Util_Printer implements PHPUnit_Framework_TestListener
{
    /**
     * @var    DOMDocument
     */
    protected $document;

    /**
     * @var    DOMElement
     */
    protected $root;

    /**
     * @var    boolean
     */
    protected $logIncompleteSkipped = FALSE;

    /**
     * @var    boolean
     */
    protected $writeDocument = TRUE;

    /**
     * @var    DOMElement[]
     */
    protected $testSuites = array();

    /**
     * @var    integer[]
     */
    protected $testSuiteTests = array(0);

    /**
     * @var    integer[]
     */
    protected $testSuiteAssertions = array(0);

    /**
     * @var    integer[]
     */
    protected $testSuiteErrors = array(0);

    /**
     * @var    integer[]
     */
    protected $testSuiteFailures = array(0);

    /**
     * @var    integer[]
     */
    protected $testSuiteTimes = array(0);

    /**
     * @var    integer
     */
    protected $testSuiteLevel = 0;

    /**
     * @var    DOMElement
     */
    protected $currentTestCase = NULL;

    /**
     * @var    boolean
     */
    protected $attachCurrentTestCase = TRUE;

    /**
     * Constructor.
     *
     * @param  mixed   $out
     * @param  boolean $logIncompleteSkipped
     */
    public function __construct($out = NULL, $logIncompleteSkipped = FALSE)
    {
        $this->document = new DOMDocument('1.0', 'UTF-8');
        $this->document->formatOutput = TRUE;

        $this->root = $this->document->createElement('testsuites');
        $this->document->appendChild($this->root);

        parent::__construct($out);

        $this->logIncompleteSkipped = $logIncompleteSkipped;
    }

    /**
     * Flush buffer and close output.
     *
     */
    public function flush()
    {
        if ($this->writeDocument === TRUE) {
            $this->write($this->getXML());
        }

        parent::flush();
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
        if ($test instanceof PHPUnit_Framework_SelfDescribing) {
            $buffer = $test->toString() . "\n";
        } else {
            $buffer = '';
        }

        $buffer .= PHPUnit_Framework_TestFailure::exceptionToString($e) . "\n" .
                   PHPUnit_Util_Filter::getFilteredStacktrace($e, FALSE);

        $error = $this->document->createElement(
          'error',
          htmlspecialchars(
            PHPUnit_Util_XML::convertToUtf8($buffer),
            ENT_COMPAT,
            'UTF-8'
          )
        );

        $error->setAttribute('type', get_class($e));

        $this->currentTestCase->appendChild($error);

        $this->testSuiteErrors[$this->testSuiteLevel]++;
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
        if (!$test instanceof PHPUnit_Framework_Warning) {
            if ($test instanceof PHPUnit_Framework_SelfDescribing) {
                $buffer = $test->toString() . "\n";
            } else {
                $buffer = '';
            }

            $buffer .= PHPUnit_Framework_TestFailure::exceptionToString($e) . "\n" .
                       PHPUnit_Util_Filter::getFilteredStacktrace($e, FALSE);

            $failure = $this->document->createElement(
              'failure',
              htmlspecialchars(
                PHPUnit_Util_XML::convertToUtf8($buffer),
                ENT_COMPAT,
                'UTF-8'
              )
            );

            $failure->setAttribute('type', get_class($e));

            $this->currentTestCase->appendChild($failure);

            $this->testSuiteFailures[$this->testSuiteLevel]++;
        }
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
        if ($this->logIncompleteSkipped) {
            $error = $this->document->createElement(
              'error',
              htmlspecialchars(
                PHPUnit_Util_XML::convertToUtf8(
                  "Incomplete Test\n" .
                  PHPUnit_Util_Filter::getFilteredStacktrace($e, FALSE)
                ),
                ENT_COMPAT,
                'UTF-8'
              )
            );

            $error->setAttribute('type', get_class($e));

            $this->currentTestCase->appendChild($error);

            $this->testSuiteErrors[$this->testSuiteLevel]++;
        } else {
            $this->attachCurrentTestCase = FALSE;
        }
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
        if ($this->logIncompleteSkipped) {
            $error = $this->document->createElement(
              'error',
              htmlspecialchars(
                PHPUnit_Util_XML::convertToUtf8(
                  "Skipped Test\n" .
                  PHPUnit_Util_Filter::getFilteredStacktrace($e, FALSE)
                ),
                ENT_COMPAT,
                'UTF-8'
              )
            );

            $error->setAttribute('type', get_class($e));

            $this->currentTestCase->appendChild($error);

            $this->testSuiteErrors[$this->testSuiteLevel]++;
        } else {
            $this->attachCurrentTestCase = FALSE;
        }
    }

    /**
     * A testsuite started.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $testSuite = $this->document->createElement('testsuite');
        $testSuite->setAttribute('name', $suite->getName());

        if (class_exists($suite->getName(), FALSE)) {
            try {
                $class = new ReflectionClass($suite->getName());

                $testSuite->setAttribute('file', $class->getFileName());

                $packageInformation = PHPUnit_Util_Class::getPackageInformation(
                  $suite->getName()
                );

                if (!empty($packageInformation['namespace'])) {
                    $testSuite->setAttribute('namespace', $packageInformation['namespace']);
                }

                if (!empty($packageInformation['fullPackage'])) {
                    $testSuite->setAttribute('fullPackage', $packageInformation['fullPackage']);
                }

                if (!empty($packageInformation['category'])) {
                    $testSuite->setAttribute('category', $packageInformation['category']);
                }

                if (!empty($packageInformation['package'])) {
                    $testSuite->setAttribute('package', $packageInformation['package']);
                }

                if (!empty($packageInformation['subpackage'])) {
                    $testSuite->setAttribute('subpackage', $packageInformation['subpackage']);
                }
            }

            catch (ReflectionException $e) {
            }
        }

        if ($this->testSuiteLevel > 0) {
            $this->testSuites[$this->testSuiteLevel]->appendChild($testSuite);
        } else {
            $this->root->appendChild($testSuite);
        }

        $this->testSuiteLevel++;
        $this->testSuites[$this->testSuiteLevel]          = $testSuite;
        $this->testSuiteTests[$this->testSuiteLevel]      = 0;
        $this->testSuiteAssertions[$this->testSuiteLevel] = 0;
        $this->testSuiteErrors[$this->testSuiteLevel]     = 0;
        $this->testSuiteFailures[$this->testSuiteLevel]   = 0;
        $this->testSuiteTimes[$this->testSuiteLevel]      = 0;
    }

    /**
     * A testsuite ended.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->testSuites[$this->testSuiteLevel]->setAttribute('tests', $this->testSuiteTests[$this->testSuiteLevel]);
        $this->testSuites[$this->testSuiteLevel]->setAttribute('assertions', $this->testSuiteAssertions[$this->testSuiteLevel]);
        $this->testSuites[$this->testSuiteLevel]->setAttribute('failures', $this->testSuiteFailures[$this->testSuiteLevel]);
        $this->testSuites[$this->testSuiteLevel]->setAttribute('errors', $this->testSuiteErrors[$this->testSuiteLevel]);
        $this->testSuites[$this->testSuiteLevel]->setAttribute('time', sprintf('%F', $this->testSuiteTimes[$this->testSuiteLevel]));

        if ($this->testSuiteLevel > 1) {
            $this->testSuiteTests[$this->testSuiteLevel - 1]      += $this->testSuiteTests[$this->testSuiteLevel];
            $this->testSuiteAssertions[$this->testSuiteLevel - 1] += $this->testSuiteAssertions[$this->testSuiteLevel];
            $this->testSuiteErrors[$this->testSuiteLevel - 1]     += $this->testSuiteErrors[$this->testSuiteLevel];
            $this->testSuiteFailures[$this->testSuiteLevel - 1]   += $this->testSuiteFailures[$this->testSuiteLevel];
            $this->testSuiteTimes[$this->testSuiteLevel - 1]      += $this->testSuiteTimes[$this->testSuiteLevel];
        }

        $this->testSuiteLevel--;
    }

    /**
     * A test started.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        if (!$test instanceof PHPUnit_Framework_Warning) {
            $testCase = $this->document->createElement('testcase');
            $testCase->setAttribute('name', $test->getName());

            if ($test instanceof PHPUnit_Framework_TestCase) {
                $class      = new ReflectionClass($test);
                $methodName = $test->getName();

                if ($class->hasMethod($methodName)) {
                    $method = $class->getMethod($test->getName());

                    $testCase->setAttribute('class', $class->getName());
                    $testCase->setAttribute('file', $class->getFileName());
                    $testCase->setAttribute('line', $method->getStartLine());
                }
            }

            $this->currentTestCase = $testCase;
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
        if (!$test instanceof PHPUnit_Framework_Warning) {
            if ($this->attachCurrentTestCase) {
                if ($test instanceof PHPUnit_Framework_TestCase) {
                    $numAssertions = $test->getNumAssertions();
                    $this->testSuiteAssertions[$this->testSuiteLevel] += $numAssertions;

                    $this->currentTestCase->setAttribute('assertions', $numAssertions);
                }

                $this->currentTestCase->setAttribute('time', sprintf('%F', $time));

                $this->testSuites[$this->testSuiteLevel]->appendChild(
                  $this->currentTestCase
                );

                $this->testSuiteTests[$this->testSuiteLevel]++;
                $this->testSuiteTimes[$this->testSuiteLevel] += $time;
            }
        }

        $this->attachCurrentTestCase = TRUE;
        $this->currentTestCase       = NULL;
    }

    /**
     * Returns the XML as a string.
     *
     * @return string
     * @since  Method available since Release 2.2.0
     */
    public function getXML()
    {
        return $this->document->saveXML();
    }

    /**
     * Enables or disables the writing of the document
     * in flush().
     *
     * This is a "hack" needed for the integration of
     * PHPUnit with Phing.
     *
     * @return string
     * @since  Method available since Release 2.2.0
     */
    public function setWriteDocument($flag)
    {
        if (is_bool($flag)) {
            $this->writeDocument = $flag;
        }
    }
}
?>
