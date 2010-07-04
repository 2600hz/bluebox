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
 * Doctrine_Query_Having_TestCase
 *
 * @package     Doctrine
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @version     $Revision$
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 */
class Doctrine_Query_Having_TestCase extends Doctrine_UnitTestCase 
{
    public function testAggregateFunctionsInHavingReturnValidSql() 
    {
        $q = new Doctrine_Query();
        
        $q->parseDqlQuery('SELECT u.name FROM User u LEFT JOIN u.Phonenumber p HAVING COUNT(p.id) > 2');
        
        $this->assertEqual($q->getSqlQuery(), 'SELECT e.id AS e__id, e.name AS e__name FROM entity e LEFT JOIN phonenumber p ON e.id = p.entity_id WHERE (e.type = 0) HAVING COUNT(p.id) > 2');
    }
    public function testAggregateFunctionsInHavingReturnValidSql2() 
    {
        $q = new Doctrine_Query();
        
        $q->parseDqlQuery("SELECT u.name FROM User u LEFT JOIN u.Phonenumber p HAVING MAX(u.name) = 'zYne'");

        $this->assertEqual($q->getSqlQuery(), "SELECT e.id AS e__id, e.name AS e__name FROM entity e LEFT JOIN phonenumber p ON e.id = p.entity_id WHERE (e.type = 0) HAVING MAX(e.name) = 'zYne'");
    }

    public function testAggregateFunctionsInHavingSupportMultipleParameters() 
    {
        $q = new Doctrine_Query();

        $q->parseDqlQuery("SELECT CONCAT(u.name, u.loginname) name FROM User u LEFT JOIN u.Phonenumber p HAVING name = 'xx'");
    }

    public function testReturnFuncIfNumeric()
    {
       $having =  new Doctrine_Query_Having(new Doctrine_Query());
       $part = $having->load("1");
       $this->assertEqual("1",trim($part));

    }

}
