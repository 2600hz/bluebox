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
 * @version    SVN: $Id: NamePrettifier.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.3.0
 */

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Prettifies class and method names for use in TestDox documentation.
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
class PHPUnit_Util_TestDox_NamePrettifier
{
    /**
     * @var    string
     */
    protected $prefix = 'Test';

    /**
     * @var    string
     */
    protected $suffix = 'Test';

    /**
     * @var    array
     */
    protected $strings = array();

    /**
     * Prettifies the name of a test class.
     *
     * @param  string  $testClassName
     * @return string
     */
    public function prettifyTestClass($testClassName)
    {
        $title = $testClassName;

        if ($this->suffix !== NULL &&
            $this->suffix == substr($testClassName, -1 * strlen($this->suffix))) {
            $title = substr($title, 0, strripos($title, $this->suffix));
        }

        if ($this->prefix !== NULL &&
            $this->prefix == substr($testClassName, 0, strlen($this->prefix))) {
            $title = substr($title, strlen($this->prefix));
        }

        return $title;
    }

    /**
     * Prettifies the name of a test method.
     *
     * @param  string  $testMethodName
     * @return string
     */
    public function prettifyTestMethod($testMethodName)
    {
        $buffer = '';

        if (!is_string($testMethodName) || strlen($testMethodName) == 0) {
            return $buffer;
        }

        $string = preg_replace('#\d+$#', '', $testMethodName);

        if (in_array($string, $this->strings)) {
            $testMethodName = $string;
        } else {
            $this->strings[] = $string;
        }

        $max = strlen($testMethodName);

        if (substr($testMethodName, 0, 4) == 'test') {
            $offset = 4;
        } else {
            $offset = 0;
            $testMethodName[0] = strtoupper($testMethodName[0]);
        }

        $wasNumeric = FALSE;

        for ($i = $offset; $i < $max; $i++) {
            if ($i > $offset &&
                ord($testMethodName[$i]) >= 65 &&
                ord($testMethodName[$i]) <= 90) {
                $buffer .= ' ' . strtolower($testMethodName[$i]);
            } else {
                $isNumeric = is_numeric($testMethodName[$i]);

                if (!$wasNumeric && $isNumeric) {
                    $buffer .= ' ';
                    $wasNumeric = TRUE;
                }

                if ($wasNumeric && !$isNumeric) {
                    $wasNumeric = FALSE;
                }

                $buffer .= $testMethodName[$i];
            }
        }

        return $buffer;
    }

    /**
     * Sets the prefix of test names.
     *
     * @param  string  $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Sets the suffix of test names.
     *
     * @param  string  $prefix
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }
}
?>
