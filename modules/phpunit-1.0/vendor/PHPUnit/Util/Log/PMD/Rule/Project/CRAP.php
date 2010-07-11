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
 * @version    SVN: $Id: CRAP.php 4404 2008-12-31 09:27:18Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.2.0
 */

require_once 'PHPUnit/Util/Log/PMD/Rule/Project.php';
require_once 'PHPUnit/Util/Log/PMD/Rule/Function.php';
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

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
 * @since      Class available since Release 3.2.0
 */
class PHPUnit_Util_Log_PMD_Rule_Project_CRAP extends PHPUnit_Util_Log_PMD_Rule_Project
{
    public function __construct($threshold = array(5, 30), $priority = 1)
    {
        parent::__construct($threshold, $priority);
    }

    public function apply(PHPUnit_Util_Metrics $metrics)
    {
        $numCrappyMethods = 0;
        $numMethods       = 0;

        foreach ($metrics->getClasses() as $class) {
            $methods = $class->getMethods();

            foreach ($methods as $method) {
                if ($method->getCrapIndex() > $this->threshold[1]) {
                    $numCrappyMethods++;
                }
            }

            $numMethods += count($methods);
        }

        if ($numMethods > 0) {
            $percent = ($numCrappyMethods / $numMethods) * 100;
        } else {
            $percent = 0;
        }

        if ($percent > $this->threshold[0]) {
            return sprintf(
              "More than %01.2f%% of the project's methods have a Change Risk " .
              'Analysis and Predictions (CRAP) index that is above the threshold ' .
              "of %d.\n" .
              'The CRAP index of a function or method uses cyclomatic complexity ' .
              'and code coverage from automated tests to help estimate the ' .
              'effort and risk associated with maintaining legacy code. A CRAP ' .
              'index over 30 is a good indicator of crappy code.',
              $this->threshold[0],
              $this->threshold[1]
            );
        }
    }
}
?>
