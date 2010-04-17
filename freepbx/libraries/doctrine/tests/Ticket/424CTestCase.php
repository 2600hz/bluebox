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
 * Doctrine_Ticket424B_TestCase
 *
 * This test case tests many-many relationship with non-autoincrement, alias primary key
 *
 * @package     Doctrine
 * @author      Tamcy <7am.online@gmail.com>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */

class Doctrine_Ticket_424C_TestCase extends Doctrine_UnitTestCase
{
    public function prepareData()
    { }

    public function prepareTables()
    {
        $this->tables = array('mmrUser_C', 'mmrGroup_C', 'mmrGroupUser_C');
        parent::prepareTables();
    }

    protected function newGroup($code, $name)
    {
        $group = new mmrGroup_C();
        $group->id = $code;
        $group->name = $name;
        $group->save();
        return $group;
    }

    protected function newUser($code, $name, $groups)
    {
        $u = new mmrUser_C();
        $u->id = $code;
        $u->name = $name;
        foreach ($groups as $idx=>$group) {
            $u->Group[$idx] = $group;
        }
        $u->save();
        return $u;
    }

    public function testManyManyRelationWithAliasColumns()
    {
      $groupA = $this->newGroup(1, 'Group A');
      $groupB = $this->newGroup(2, 'Group B');
      $groupC = $this->newGroup(3, 'Group C');

      $john  = $this->newUser(1, 'John',  array($groupA, $groupB));
      $peter = $this->newUser(2, 'Peter', array($groupA, $groupC));
      $alan  = $this->newUser(3, 'Alan',  array($groupB, $groupC));

      $q = Doctrine_Query::create();
      $gu = $q->from('mmrGroupUser_C')->execute();
      $this->assertEqual(count($gu), 6);

      // Direct query
      $q = Doctrine_Query::create();
      $gu = $q->from('mmrGroupUser_C')->where('group_id = ?', $groupA->id)->execute();
      $this->assertEqual(count($gu), 2);

      // Query by join
      $q = Doctrine_Query::create()
            ->from('mmrUser_C u, u.Group g')
            ->where('g.name = ?', array($groupA->name));

      $userOfGroupAByName = $q->execute();

      $this->assertEqual(count($userOfGroupAByName), 2);
    }
}
