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
 * @version    SVN: $Id: PEAR.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.3.0
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * A TestListener that logs to a PEAR_Log sink.
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
class PHPUnit_Util_Log_PEAR implements PHPUnit_Framework_TestListener
{
    /**
     * Log.
     *
     * @var    Log
     */
    protected $log;

    /**
     * @param string $type      The type of concrete Log subclass to use.
     *                          Currently, valid values are 'console',
     *                          'syslog', 'sql', 'file', and 'mcal'.
     * @param string $name      The name of the actually log file, table, or
     *                          other specific store to use. Defaults to an
     *                          empty string, with which the subclass will
     *                          attempt to do something intelligent.
     * @param string $ident     The identity reported to the log system.
     * @param array  $conf      A hash containing any additional configuration
     *                          information that a subclass might need.
     * @param int $maxLevel     Maximum priority level at which to log.
     */
    public function __construct($type, $name = '', $ident = '', $conf = array(), $maxLevel = PEAR_LOG_DEBUG)
    {
        if (PHPUnit_Util_Filesystem::fileExistsInIncludePath('Log.php')) {
            PHPUnit_Util_Filesystem::collectStart();
            require_once 'Log.php';
            $this->log = Log::factory($type, $name, $ident, $conf, $maxLevel);

            foreach (PHPUnit_Util_Filesystem::collectEnd() as $blacklistedFile) {
                PHPUnit_Util_Filter::addFileToFilter($blacklistedFile, 'PHPUNIT');
            }
        } else {
            throw new RuntimeException('Log is not available.');
        }
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
        $this->log->crit(
          sprintf(
            'Test "%s" failed: %s',

            $test->getName(),
            $e->getMessage()
          )
        );
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
        $this->log->err(
          sprintf(
            'Test "%s" failed: %s',

            $test->getName(),
            $e->getMessage()
          )
        );
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
        $this->log->info(
          sprintf(
            'Test "%s" incomplete: %s',

            $test->getName(),
            $e->getMessage()
          )
        );
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
        $this->log->info(
          sprintf(
            'Test "%s" skipped: %s',

            $test->getName(),
            $e->getMessage()
          )
        );
    }

    /**
     * A test suite started.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->log->info(
          sprintf(
            'TestSuite "%s" started.',

            $suite->getName()
          )
        );
    }

    /**
     * A test suite ended.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->log->info(
          sprintf(
            'TestSuite "%s" ended.',

            $suite->getName()
          )
        );
    }

    /**
     * A test started.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        $this->log->info(
          sprintf(
            'Test "%s" started.',

            $test->getName()
          )
        );
    }

    /**
     * A test ended.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $this->log->info(
          sprintf(
            'Test "%s" ended.',

            $test->getName()
          )
        );
    }
}
?>
