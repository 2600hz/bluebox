<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Doctrine_Expression_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */
class Doctrine_Expression_TestCase extends Doctrine_UnitTestCase
{
    public function prepareData()
    {
    }

    public function testSavingWithAnExpression()
    {
        $e = new Doctrine_Expression("CONCAT('some', 'one')");
        $this->assertEqual($e->getSql(), "CONCAT('some', 'one')");

        $u = new User();
        $u->name = $e;
        $u->save();
        $u->refresh();
        $this->assertEqual($u->name, 'someone');
    }

    public function testExpressionParserSupportsNumericalClauses()
    {
        $e = new Doctrine_Expression('1 + 2');
        $this->assertEqual($e->getSql(), '1 + 2');
    }

    public function testExpressionParserSupportsFunctionComposition()
    {
        $e = new Doctrine_Expression("SUBSTRING(CONCAT('some', 'one'), 0, 3)");
        $this->assertEqual($e->getSql(), "SUBSTR(CONCAT('some', 'one'), 0, 3)");
    }

    public function testExpressionParserSupportsParensInClauses()
    {
        $e = new Doctrine_Expression("CONCAT('(some)', '(one)')");
        $this->assertEqual($e->getSql(), "CONCAT('(some)', '(one)')");
    }
}

