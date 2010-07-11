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
 * @author     Jan Borsodi <jb@ez.no>
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id: Object.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.0.0
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Thrown when an assertion for object equality failed.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Jan Borsodi <jb@ez.no>
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.0.0
 */
class PHPUnit_Framework_ComparisonFailure_Object extends PHPUnit_Framework_ComparisonFailure
{
    /**
     * Returns a string describing the difference between the expected and the
     * actual object.
     *
     * @return string
     */
    public function toString()
    {
        if ($this->hasDiff()) {
            $diff = $this->diff(
              print_r($this->expected, TRUE), print_r($this->actual, TRUE)
            );

            if (!empty($diff)) {
                return $diff;
            }
        }

        // Fallback: Either diff is not available or the print_r() output for
        // the expected and the actual object are equal (but the objects are not).

        $expectedClass = get_class($this->expected);
        $actualClass   = get_class($this->actual);

        if ($expectedClass !== $actualClass) {
            return sprintf(
              "%s%sexpected class <%s>\n" .
              '%sgot class      <%s>',

              $this->message,
              ($this->message != '') ? ' ' : '',
              $expectedClass,
              ($this->message != '') ? str_repeat(' ', strlen($this->message) + 1) : '',
              $actualClass
            );
        } else {
            $expectedReflection = new ReflectionClass($expectedClass);
            $actualReflection   = new ReflectionClass($actualClass);

            $diff = "in object of class <{$expectedClass}>:\n";
            $i    = 0;

            foreach($expectedReflection->getProperties() as $expectedAttribute) {
                if ($expectedAttribute->isPrivate() || $expectedAttribute->isProtected()) continue;

                $actualAttribute = $actualReflection->getProperty($expectedAttribute->getName());
                $expectedValue  = $expectedAttribute->getValue($this->expected);
                $actualValue    = $actualAttribute->getValue($this->actual);

                if ($expectedValue !== $actualValue) {
                    if ($i > 0) {
                        $diff .= "\n";
                    }

                    ++$i;

                    $expectedType = gettype($expectedValue);
                    $actualType   = gettype($actualValue);

                    if ($expectedType !== $actualType) {
                        $diffObject = new PHPUnit_Framework_ComparisonFailure_Type($expectedValue, $actualValue, $this->message . 'attribute <' . $expectedAttribute->getName() . '>: ');
                        $diff .= $diffObject->toString();
                    }

                    elseif (is_object($expectedValue)) {
                        if (get_class($expectedValue) !== get_class($actualValue)) {
                            $diffObject = new PHPUnit_Framework_ComparisonFailure_Type($expectedValue, $actualValue, $this->message . 'attribute <' . $expectedAttribute->getName() . '>: ');
                            $diff .= $diffObject->toString();
                        } else {
                            $diff .= 'attribute <' . $expectedAttribute->getName() . '> contains object <' . get_class($expectedValue) . '> with different attributes';
                        }
                    } else {
                        $diffObject = PHPUnit_Framework_ComparisonFailure::diffIdentical($expectedValue, $actualValue, $this->message . 'attribute <' . $expectedAttribute->getName() . '>: ');
                        $diff .= $diffObject->toString();
                    }
                }
            }

            return $diff;
        }
    }
}
?>
