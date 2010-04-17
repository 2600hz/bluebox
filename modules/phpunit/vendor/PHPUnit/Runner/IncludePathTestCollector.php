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
 * @version    SVN: $Id: IncludePathTestCollector.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.1.0
 */

require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Runner/TestCollector.php';
require_once 'PHPUnit/Util/FilterIterator.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * A test collector that collects tests from one or more directories
 * recursively. If no directories are specified, the include_path is searched.
 *
 * <code>
 * $testCollector = new PHPUnit_Runner_IncludePathTestCollector(
 *   array('/path/to/*Test.php files')
 * );
 *
 * $suite = new PHPUnit_Framework_TestSuite('My Test Suite');
 * $suite->addTestFiles($testCollector->collectTests());
 * </code>
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
class PHPUnit_Runner_IncludePathTestCollector implements PHPUnit_Runner_TestCollector
{
    /**
     * @var    string
     */
    protected $filterIterator;

    /**
     * @var    array
     */
    protected $paths;

    /**
     * @var    string
     */
    protected $suffix;

    /**
     * @param  array  $paths
     * @param  string $suffix
     */
    public function __construct(array $paths = array(), $suffix = 'Test.php')
    {
        if (!empty($paths)) {
            $this->paths = $paths;
        } else {
            $this->paths = explode(PATH_SEPARATOR, get_include_path());
        }

        $this->suffix = $suffix;
    }

    /**
     * @return array
     */
    public function collectTests()
    {
        $pathIterator = new AppendIterator;
        $result       = array();

        foreach ($this->paths as $path) {
            $pathIterator->append(
              new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
              )
            );
        }

        $filterIterator = new PHPUnit_Util_FilterIterator(
          $pathIterator, $this->suffix
        );

        if ($this->filterIterator !== NULL) {
            $class          = new ReflectionClass($this->filterIterator);
            $filterIterator = $class->newInstance($filterIterator);
        }

        return $filterIterator;
    }

    /**
     * Adds a FilterIterator to filter the source files to be collected.
     *
     * @param  string $filterIterator
     * @throws InvalidArgumentException
     */
    public function setFilterIterator($filterIterator)
    {
        if (is_string($filterIterator) && class_exists($filterIterator)) {
            try {
                $class = new ReflectionClass($filterIterator);

                if ($class->isSubclassOf('FilterIterator')) {
                    $this->filterIterator = $filterIterator;
                }
            }

            catch (ReflectionException $e) {
                throw new InvalidArgumentException;
            }
        } else {
            throw new InvalidArgumentException;
        }
    }
}
?>
