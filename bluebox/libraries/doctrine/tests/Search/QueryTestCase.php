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
 * Doctrine_Search_Query_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */
class Doctrine_Search_Query_TestCase extends Doctrine_UnitTestCase
{
    public function prepareTables()
    {
        $this->tables = array('SearchTest', 'SearchTestIndex');

        parent::prepareTables();
    }
    public function prepareData()
    { }

    public function testInitData()
    {
        $e = new SearchTest();

        $e->title = 'Once there was an ORM framework';
        $e->content = 'There are many ORM frameworks, but nevertheless we decided to create one.';

        $e->save();

        $e = new SearchTest();

        $e->title = 'Doctrine development continues';
        $e->content = 'The development has been going well so far.';

        $e->save();
    }

    public function testParseClauseSupportsAndOperator()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('doctrine AND orm');

        $sql = 'id IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'AND id IN (SELECT id FROM search_test_index WHERE keyword = ?)';

        $this->assertEqual($ret, $sql);
    }

    public function testParseClauseSupportsMixingOfOperatorsAndParenthesis()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('((doctrine OR orm) AND dbal) OR database');

        $sql = '(id IN (SELECT id FROM search_test_index WHERE keyword = ? OR keyword = ?) AND id IN (SELECT id FROM search_test_index WHERE keyword = ?)) OR keyword = ?';

        $this->assertEqual($ret, $sql);
    }

    public function testParseClauseSupportsMixingOfOperators3()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('doctrine OR orm AND dbal');

        $sql = 'keyword = ? OR id IN (SELECT id FROM search_test_index WHERE keyword = ?) AND id IN (SELECT id FROM search_test_index WHERE keyword = ?)';

        $this->assertEqual($ret, $sql);
    }

    public function testParseClauseSupportsMixingOfOperators()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('(doctrine OR orm) AND dbal');

        $sql = 'id IN (SELECT id FROM search_test_index WHERE keyword = ? OR keyword = ?) AND id IN (SELECT id FROM search_test_index WHERE keyword = ?)';

        $this->assertEqual($ret, $sql);
    }

    public function testParseClauseSupportsMixingOfOperators2()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('(doctrine OR orm) dbal');

        $sql = 'id IN (SELECT id FROM search_test_index WHERE keyword = ? OR keyword = ?) AND id IN (SELECT id FROM search_test_index WHERE keyword = ?)';

        $this->assertEqual($ret, $sql);
    }

    public function testParseClauseSupportsMixingOfOperatorsAndDeeplyNestedParenthesis()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('(((doctrine OR orm) AND dbal) OR database) AND rdbms');

        $sql = '((id IN (SELECT id FROM search_test_index WHERE keyword = ? OR keyword = ?) AND id IN (SELECT id FROM search_test_index WHERE keyword = ?)) OR keyword = ?) AND id IN (SELECT id FROM search_test_index WHERE keyword = ?)';

        $this->assertEqual($ret, $sql);
    }

    public function testParseClauseSupportsMixingOfOperatorsAndParenthesis2()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('rdbms (dbal OR database)');

        $sql = 'id IN (SELECT id FROM search_test_index WHERE keyword = ?) AND id IN (SELECT id FROM search_test_index WHERE keyword = ? OR keyword = ?)';

        $this->assertEqual($ret, $sql);
    }

    public function testParseClauseSupportsMixingOfOperatorsAndDeeplyNestedParenthesis2()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('rdbms (((doctrine OR orm) AND dbal) OR database)');

        $sql = 'id IN (SELECT id FROM search_test_index WHERE keyword = ?) AND ((id IN (SELECT id FROM search_test_index WHERE keyword = ? OR keyword = ?) AND id IN (SELECT id FROM search_test_index WHERE keyword = ?)) OR keyword = ?)';

        $this->assertEqual($ret, $sql);
    }

    public function testParseClauseSupportsNegationOperator()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('rdbms -doctrine');

        $sql = 'id IN (SELECT id FROM search_test_index WHERE keyword = ?) AND '
             . 'id NOT IN (SELECT id FROM search_test_index WHERE keyword = ?)';

        $this->assertEqual($ret, $sql);
    }

    public function testParseClauseOrOperator2()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('rdbms doctrine OR database');

        $sql = 'id IN (SELECT id FROM search_test_index WHERE keyword = ?) AND '
             . 'id IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'OR keyword = ?';

        $this->assertEqual($ret, $sql);
    }

    public function testParseClauseSupportsNegationOperatorWithOrOperator()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $ret = $q->parseClause('rdbms -doctrine OR database');

        $sql = 'id IN (SELECT id FROM search_test_index WHERE keyword = ?) AND '
             . 'id NOT IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'OR keyword = ?';

        $this->assertEqual($ret, $sql);
    }

    public function testSearchSupportsAndOperator()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $q->query('doctrine AND orm');

        $sql = 'SELECT COUNT(keyword) AS relevance, id '
             . 'FROM search_test_index '
             . 'WHERE id IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'AND id IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'GROUP BY id ORDER BY relevance DESC';

        $this->assertEqual($q->getSqlQuery(), $sql);
    }


    public function testSearchSupportsOrOperator()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $q->query('doctrine OR orm');

        $sql = 'SELECT COUNT(keyword) AS relevance, id '
             . 'FROM search_test_index '
             . 'WHERE keyword = ? OR keyword = ? '
             . 'GROUP BY id ORDER BY relevance DESC';

        $this->assertEqual($q->getSqlQuery(), $sql);
    }


    public function testQuerySupportsSingleWordquery()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $q->query('doctrine');

        $sql = 'SELECT COUNT(keyword) AS relevance, id '
             . 'FROM search_test_index WHERE keyword = ? GROUP BY id ORDER BY relevance DESC';

        $this->assertEqual($q->getParams(), array('doctrine'));
        $this->assertEqual($q->getSqlQuery(), $sql);
    }

    public function testSearchSupportsMixingOfOperators()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $q->query('(doctrine OR orm) AND dbal');

        $sql = 'SELECT COUNT(keyword) AS relevance, id '
             . 'FROM search_test_index '
             . 'WHERE id IN (SELECT id FROM search_test_index WHERE keyword = ? OR keyword = ?) '
             . 'AND id IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'GROUP BY id ORDER BY relevance DESC';

        $this->assertEqual($q->getParams(), array('doctrine', 'orm', 'dbal'));
        $this->assertEqual($q->getSqlQuery(), $sql);
    }

    public function testSearchSupportsSingleTermWithQuotes()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $q->query("'doctrine orm'");

        $sql = 'SELECT COUNT(keyword) AS relevance, id '
             . 'FROM search_test_index WHERE keyword = ? '
             . 'AND (position + 1) = (SELECT position FROM search_test_index WHERE keyword = ?) '
             . 'GROUP BY id ORDER BY relevance DESC';

        $this->assertEqual($q->getParams(), array('doctrine', 'orm'));
        $this->assertEqual($q->getSqlQuery(), $sql);
    }

    public function testSearchSupportsSingleLongTermWithQuotes()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $q->query("'doctrine orm dbal'");

        $sql = 'SELECT COUNT(keyword) AS relevance, id '
             . 'FROM search_test_index WHERE keyword = ? '
             . 'AND (position + 1) = (SELECT position FROM search_test_index WHERE keyword = ?) '
             . 'AND (position + 2) = (SELECT position FROM search_test_index WHERE keyword = ?) '
             . 'GROUP BY id ORDER BY relevance DESC';

        $this->assertEqual($q->getParams(), array('doctrine', 'orm', 'dbal'));
        $this->assertEqual($q->getSqlQuery(), $sql);
    }

    public function testQuerySupportsMultiWordquery()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $q->query('doctrine orm');

        $sql = 'SELECT COUNT(keyword) AS relevance, id '
             . 'FROM search_test_index '
             . 'WHERE id IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'AND id IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'GROUP BY id ORDER BY relevance DESC';

        $this->assertEqual($q->getSqlQuery(), $sql);
    }

    public function testQuerySupportsMultiWordSearchAndSingleLetterWildcards()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $q->query('doct?ine orm');

        $sql = 'SELECT COUNT(keyword) AS relevance, id '
             . 'FROM search_test_index '
             . 'WHERE id IN (SELECT id FROM search_test_index WHERE keyword LIKE ?) '
             . 'AND id IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'GROUP BY id ORDER BY relevance DESC';

        $this->assertEqual($q->getParams(), array('doct?ine', 'orm'));
        $this->assertEqual($q->getSqlQuery(), $sql);
    }
    public function testQuerySupportsMultiWordSearchAndMultiLetterWildcards()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $q->query('doc* orm');

        $sql = 'SELECT COUNT(keyword) AS relevance, id '
             . 'FROM search_test_index '
             . 'WHERE id IN (SELECT id FROM search_test_index WHERE keyword LIKE ?) '
             . 'AND id IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'GROUP BY id ORDER BY relevance DESC';

        $this->assertEqual($q->getParams(), array('doc%', 'orm'));
        $this->assertEqual($q->getSqlQuery(), $sql);
    }
    public function testSearchSupportsMultipleTermsWithQuotes()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $q->query("doctrine 'orm database'");

        $sql = 'SELECT COUNT(keyword) AS relevance, id '
             . 'FROM search_test_index '
             . 'WHERE id IN (SELECT id FROM search_test_index WHERE keyword = ?) '
             . 'AND id IN (SELECT id FROM search_test_index WHERE keyword = ? '
             . 'AND (position + 1) = (SELECT position FROM search_test_index WHERE keyword = ?)) '
             . 'GROUP BY id ORDER BY relevance DESC';

        $this->assertEqual($q->getParams(), array('doctrine', 'orm', 'database'));
        $this->assertEqual($q->getSqlQuery(), $sql);
    }

    public function testSearchReturnsFalseForEmptyStrings()
    {
        $q = new Doctrine_Search_Query('SearchTestIndex');
        $result = $q->query(' ');

        $this->assertFalse($result);
    }

}
