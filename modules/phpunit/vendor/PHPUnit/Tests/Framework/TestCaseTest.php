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
 * @version    SVN: $Id: TestCaseTest.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.0.0
 */

require_once 'PHPUnit/Framework/TestCase.php';

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Error.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Failure.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'NoArgTestCaseTest.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'SetupFailure.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Success.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'TearDownFailure.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'ThrowExceptionTestCase.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'ThrowNoExceptionTestCase.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'TornDown.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'TornDown2.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'TornDown3.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'TornDown4.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'WasRun.php';

$GLOBALS['a']  = 'a';
$_ENV['b']     = 'b';
$_POST['c']    = 'c';
$_GET['d']     = 'd';
$_COOKIE['e']  = 'e';
$_SERVER['f']  = 'f';
$_FILES['g']   = 'g';
$_REQUEST['h'] = 'h';

/**
 *
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
class Framework_TestCaseTest extends PHPUnit_Framework_TestCase
{
    public function testCaseToString()
    {
        $this->assertEquals(
          'testCaseToString(Framework_TestCaseTest)',
          $this->toString()
        );
    }

    public function testError()
    {
        $this->verifyError(new Error);
    }

    public function testExceptionRunningAndTearDown()
    {
        $result = new PHPUnit_Framework_TestResult();
        $t      = new TornDown4;

        $t->run($result);

        $errors = $result->errors();

        $this->assertEquals(
          'tearDown',
          $errors[0]->thrownException()->getMessage()
        );
    }

    public function testFailure()
    {
        $this->verifyFailure(new Failure);
    }

    /* PHP does not support anonymous classes
    public function testNamelessTestCase()
    {
    }
    */

    public function testNoArgTestCasePasses()
    {
        $result = new PHPUnit_Framework_TestResult();
        $t      = new PHPUnit_Framework_TestSuite('NoArgTestCaseTest');

        $t->run($result);

        $this->assertEquals(1, count($result));
        $this->assertEquals(0, $result->failureCount());
        $this->assertEquals(0, $result->errorCount());
    }

    public function testRunAndTearDownFails()
    {
        $fails = new TornDown2;

        $this->verifyError($fails);
        $this->assertTrue($fails->tornDown);
    }

    public function testSetupFails()
    {
        $this->verifyError(new SetupFailure);
    }

    public function testSuccess()
    {
        $this->verifySuccess(new Success);
    }

    public function testTearDownAfterError()
    {
        $fails = new TornDown;

        $this->verifyError($fails);
        $this->assertTrue($fails->tornDown);
    }

    public function testTearDownFails()
    {
        $this->verifyError(new TearDownFailure);
    }

    public function testTearDownSetupFails()
    {
        $fails = new TornDown3;

        $this->verifyError($fails);
        $this->assertFalse($fails->tornDown);
    }

    public function testWasRun()
    {
        $test = new WasRun;
        $test->run();

        $this->assertTrue($test->wasRun);
    }

    public function testException()
    {
        $test = new ThrowExceptionTestCase('test');
        $test->setExpectedException('Exception');

        $result = $test->run();

        $this->assertEquals(1, count($result));
        $this->assertTrue($result->wasSuccessful());
    }

    public function testNoException()
    {
        $test = new ThrowNoExceptionTestCase('test');
        $test->setExpectedException('Exception');

        $result = $test->run();

        $this->assertEquals(1, $result->failureCount());
        $this->assertEquals(1, count($result));
    }

    public function testWrongException()
    {
        $test = new ThrowExceptionTestCase('test');
        $test->setExpectedException('RuntimeException');

        $result = $test->run();

        $this->assertEquals(1, $result->errorCount());
        $this->assertEquals(1, count($result));
    }

    public function testGlobalsBackupPre()
    {
        global $a;

        $this->assertEquals('a', $a);
        $this->assertEquals('a', $GLOBALS['a']);
        $this->assertEquals('b', $_ENV['b']);
        $this->assertEquals('c', $_POST['c']);
        $this->assertEquals('d', $_GET['d']);
        $this->assertEquals('e', $_COOKIE['e']);
        $this->assertEquals('f', $_SERVER['f']);
        $this->assertEquals('g', $_FILES['g']);
        $this->assertEquals('h', $_REQUEST['h']);

        $GLOBALS['a']   = 'aa';
        $GLOBALS['foo'] = 'bar';
        $_ENV['b']      = 'bb';
        $_POST['c']     = 'cc';
        $_GET['d']      = 'dd';
        $_COOKIE['e']   = 'ee';
        $_SERVER['f']   = 'ff';
        $_FILES['g']    = 'gg';
        $_REQUEST['h']  = 'hh';

        $this->assertEquals('aa', $a);
        $this->assertEquals('aa', $GLOBALS['a']);
        $this->assertEquals('bar', $GLOBALS['foo']);
        $this->assertEquals('bb', $_ENV['b']);
        $this->assertEquals('cc', $_POST['c']);
        $this->assertEquals('dd', $_GET['d']);
        $this->assertEquals('ee', $_COOKIE['e']);
        $this->assertEquals('ff', $_SERVER['f']);
        $this->assertEquals('gg', $_FILES['g']);
        $this->assertEquals('hh', $_REQUEST['h']);
    }

    public function testGlobalsBackupPost()
    {
        global $a;

        $this->assertEquals('a', $a);
        $this->assertEquals('a', $GLOBALS['a']);
        $this->assertEquals('b', $_ENV['b']);
        $this->assertEquals('c', $_POST['c']);
        $this->assertEquals('d', $_GET['d']);
        $this->assertEquals('e', $_COOKIE['e']);
        $this->assertEquals('f', $_SERVER['f']);
        $this->assertEquals('g', $_FILES['g']);
        $this->assertEquals('h', $_REQUEST['h']);

        $this->assertArrayNotHasKey('foo', $GLOBALS);
    }

    protected function verifyError(PHPUnit_Framework_TestCase $test)
    {
        $result = $test->run();

        $this->assertEquals(1, $result->errorCount());
        $this->assertEquals(0, $result->failureCount());
        $this->assertEquals(1, count($result));
    }

    protected function verifyFailure(PHPUnit_Framework_TestCase $test)
    {
        $result = $test->run();

        $this->assertEquals(0, $result->errorCount());
        $this->assertEquals(1, $result->failureCount());
        $this->assertEquals(1, count($result));
    }

    protected function verifySuccess(PHPUnit_Framework_TestCase $test)
    {
        $result = $test->run();

        $this->assertEquals(0, $result->errorCount());
        $this->assertEquals(0, $result->failureCount());
        $this->assertEquals(1, count($result));
    }
}
?>
