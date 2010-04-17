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
 * @version    SVN: $Id: TestFailure.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.0.0
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

if (!class_exists('PHPUnit_Framework_TestFailure', FALSE)) {

/**
 * A TestFailure collects a failed test together with the caught exception.
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
class PHPUnit_Framework_TestFailure
{
    /**
     * @var    PHPUnit_Framework_Test
     */
    protected $failedTest;

    /**
     * @var    Exception
     */
    protected $thrownException;

    /**
     * Constructs a TestFailure with the given test and exception.
     *
     * @param  PHPUnit_Framework_Test $failedTest
     * @param  Exception               $thrownException
     */
    public function __construct(PHPUnit_Framework_Test $failedTest, Exception $thrownException)
    {
        $this->failedTest      = $failedTest;
        $this->thrownException = $thrownException;
    }

    /**
     * Returns a short description of the failure.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf(
          '%s: %s',

          $this->failedTest,
          $this->thrownException->getMessage()
        );
    }

    /**
     * Returns a verbose description of the failure.
     *
     * @param  bool $verbose
     * @return string
     * @since  Method available since Release 3.2.0
     */
    public function toStringVerbose($verbose = FALSE)
    {
        return self::exceptionToString($this->thrownException, $verbose);
    }

    /**
     * Returns a verbose description for an exception.
     *
     * @param  Exception $e
     * @param  bool      $verbose
     * @return string
     * @since  Method available since Release 3.2.0
     */
    public static function exceptionToString(Exception $e, $verbose = FALSE)
    {
        if ($e instanceof PHPUnit_Framework_SelfDescribing) {
            if ($e instanceof PHPUnit_Framework_ExpectationFailedException) {
                $comparisonFailure = $e->getComparisonFailure();
                $description       = $e->getDescription();
                $message           = $e->getCustomMessage();

                if ($message == '') {
                    $buffer = '';
                } else {
                    $buffer = $message . "\n";
                }

                if ($comparisonFailure !== NULL) {
                    if ($comparisonFailure->identical()) {
                        if ($comparisonFailure instanceof PHPUnit_Framework_ComparisonFailure_Object) {
                            $buffer .= "Failed asserting that two variables reference the same object.\n";
                        } else {
                            $buffer .= $comparisonFailure->toString() . "\n";
                        }
                    } else {
                        if ($comparisonFailure instanceof PHPUnit_Framework_ComparisonFailure_Scalar) {
                            $buffer .= sprintf(
                              "Failed asserting that %s matches expected value %s.\n",

                              PHPUnit_Util_Type::toString($comparisonFailure->getActual()),
                              PHPUnit_Util_Type::toString($comparisonFailure->getExpected())
                            );
                        }

                        else if ($comparisonFailure instanceof PHPUnit_Framework_ComparisonFailure_Array ||
                                 $comparisonFailure instanceof PHPUnit_Framework_ComparisonFailure_Object ||
                                 $comparisonFailure instanceof PHPUnit_Framework_ComparisonFailure_String) {
                            $buffer .= sprintf(
                              "Failed asserting that two %ss are equal.\n%s\n",

                              strtolower(substr(get_class($comparisonFailure), 36)),
                              $comparisonFailure->toString()
                            );
                        }

                        if ($verbose &&
                           !$comparisonFailure instanceof PHPUnit_Framework_ComparisonFailure_Array &&
                           !$comparisonFailure instanceof PHPUnit_Framework_ComparisonFailure_Object &&
                           !$comparisonFailure instanceof PHPUnit_Framework_ComparisonFailure_String) {
                            $buffer .= $comparisonFailure->toString() . "\n";
                        }
                    }
                } else {
                    $buffer .= $e->toString();
                    $equal   = $buffer == $description;

                    if (!empty($buffer)) {
                        $buffer .= "\n";
                    }

                    if (!$equal) {
                        $buffer .= $description . "\n";
                    }
                }
            }

            else {
                $buffer = $e->toString();

                if (!empty($buffer)) {
                    $buffer .= "\n";
                }
            }
        }

        else if ($e instanceof PHPUnit_Framework_Error) {
            $buffer = $e->getMessage() . "\n";
        }

        else {
            $buffer = get_class($e) . ': ' . $e->getMessage() . "\n";
        }

        return $buffer;
    }

    /**
     * Gets the failed test.
     *
     * @return Test
     */
    public function failedTest()
    {
        return $this->failedTest;
    }

    /**
     * Gets the thrown exception.
     *
     * @return Exception
     */
    public function thrownException()
    {
        return $this->thrownException;
    }

    /**
     * Returns the exception's message.
     *
     * @return string
     */
    public function exceptionMessage()
    {
        return $this->thrownException()->getMessage();
    }

    /**
     * Returns TRUE if the thrown exception
     * is of type AssertionFailedError.
     *
     * @return boolean
     */
    public function isFailure()
    {
        return ($this->thrownException() instanceof PHPUnit_Framework_AssertionFailedError);
    }
}

}
?>
