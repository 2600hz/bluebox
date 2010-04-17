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
 * Doctrine_Collection_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */
class Doctrine_Collection_TestCase extends Doctrine_UnitTestCase 
{
    public function testLoadRelatedForAssociation() 
    {
        $coll = $this->connection->query('FROM User');

        $this->assertEqual($coll->count(), 8);

        $coll[0]->Group[1]->name = 'Actors House 2';

        $coll[0]->Group[2]->name = 'Actors House 3';

        $coll[2]->Group[0]->name = 'Actors House 4';
        $coll[2]->Group[1]->name = 'Actors House 5';
        $coll[2]->Group[2]->name = 'Actors House 6';
        
        $coll[5]->Group[0]->name = 'Actors House 7';
        $coll[5]->Group[1]->name = 'Actors House 8';
        $coll[5]->Group[2]->name = 'Actors House 9';
        
        $coll->save();
        
        $this->connection->clear();
        
        $coll = $this->connection->query('FROM User');

        $this->assertEqual($coll->count(), 8);
        $this->assertEqual($coll[0]->Group->count(), 2);
        $this->assertEqual($coll[1]->Group->count(), 1);
        $this->assertEqual($coll[2]->Group->count(), 3);
        $this->assertEqual($coll[5]->Group->count(), 3);

        $this->connection->clear();
        
        $coll = $this->connection->query('FROM User');

        $this->assertEqual($coll->count(), 8);

        $count = $this->connection->count();

        $coll->loadRelated('Group');
        $this->assertEqual(($count + 1), $this->connection->count());
        $this->assertEqual($coll[0]->Group->count(), 2);
        $this->assertEqual(($count + 1), $this->connection->count());
        $this->assertEqual($coll[1]->Group->count(), 1);

        $this->assertEqual(($count + 1), $this->connection->count());

        $this->assertEqual($coll[2]->Group->count(), 3);

        $this->assertEqual(($count + 1), $this->connection->count());
        $this->assertEqual($coll[5]->Group->count(), 3);

        $this->assertEqual(($count + 1), $this->connection->count());

        $this->connection->clear();
    }

    public function testOffsetGetWithNullArgumentReturnsNewRecord() 
    {
        $coll = new Doctrine_Collection('User');
        $this->assertEqual($coll->count(), 0);

        $coll[]->name = 'zYne';

        $this->assertEqual($coll->count(), 1);
        $this->assertEqual($coll[0]->name, 'zYne');
    }

    public function testLoadRelatedForNormalAssociation() 
    {
        $resource = new Doctrine_Collection('Resource');
        $resource[0]->name = 'resource 1';
        $resource[0]->Type[0]->type = 'type 1';
        $resource[0]->Type[1]->type = 'type 2';
        $resource[1]->name = 'resource 2';
        $resource[1]->Type[0]->type = 'type 3';
        $resource[1]->Type[1]->type = 'type 4';

        $resource->save();
        
        $this->connection->clear();

        $resources = $this->connection->query('FROM Resource');

        $count = $this->connection->count();
        $resources->loadRelated('Type');

        $this->assertEqual(($count + 1), $this->connection->count());
        $this->assertEqual($resources[0]->name, 'resource 1');
        $this->assertEqual($resource[0]->Type[0]->type, 'type 1');
        $this->assertEqual($resource[0]->Type[1]->type, 'type 2');
        $this->assertEqual(($count + 1), $this->connection->count());

        $this->assertEqual($resource[1]->name, 'resource 2');
        $this->assertEqual($resource[1]->Type[0]->type, 'type 3');
        $this->assertEqual($resource[1]->Type[1]->type, 'type 4');
        $this->assertEqual(($count + 1), $this->connection->count());
    }
    
    public function testAdd() 
    {
        $coll = new Doctrine_Collection($this->objTable);
        $coll->add(new User());
        $this->assertEqual($coll->count(),1);
        $coll->add(new User());
        $this->assertTrue($coll->count(),2);

        $this->assertEqual($coll->getKeys(), array(0,1));

        $coll[2] = new User();

        $this->assertTrue($coll->count(),3);
        $this->assertEqual($coll->getKeys(), array(0,1,2));
    }

    public function testLoadRelated() 
    {
        $coll = $this->connection->query('FROM User u');

        $q = $coll->loadRelated();

        $this->assertTrue($q instanceof Doctrine_Query);
        
        $q->addFrom('User.Group g');

        $this->assertEqual($q->getSqlQuery($coll->getPrimaryKeys()), 'SELECT e.id AS e__id, e.name AS e__name, e.loginname AS e__loginname, e.password AS e__password, e.type AS e__type, e.created AS e__created, e.updated AS e__updated, e.email_id AS e__email_id, e2.id AS e2__id, e2.name AS e2__name, e2.loginname AS e2__loginname, e2.password AS e2__password, e2.type AS e2__type, e2.created AS e2__created, e2.updated AS e2__updated, e2.email_id AS e2__email_id FROM entity e LEFT JOIN groupuser g ON (e.id = g.user_id) LEFT JOIN entity e2 ON e2.id = g.group_id AND e2.type = 1 WHERE e.id IN (?, ?, ?, ?, ?, ?, ?, ?) AND (e.type = 0)');

        $coll2 = $q->execute($coll->getPrimaryKeys());
        $this->assertEqual($coll2->count(), $coll->count());

        $count = $this->connection->count();
        $coll[0]->Group[0];
        $this->assertEqual($count, $this->connection->count());
    }

    public function testLoadRelatedForLocalKeyRelation() 
    {
        $coll = $this->connection->query('FROM User');

        $this->assertEqual($coll->count(), 8);
        
        $count = $this->connection->count();
        $coll->loadRelated('Email');

        $this->assertEqual(($count + 1), $this->connection->count());

        $this->assertEqual($coll[0]->Email->address, 'zYne@example.com');

        $this->assertEqual(($count + 1), $this->connection->count());

        $this->assertEqual($coll[2]->Email->address, 'caine@example.com');

        $this->assertEqual($coll[3]->Email->address, 'kitano@example.com');

        $this->assertEqual($coll[4]->Email->address, 'stallone@example.com');

        $this->assertEqual(($count + 1), $this->connection->count());

        $this->connection->clear();
    }

    public function testLoadRelatedForForeignKey() 
    {
        $coll = $this->connection->query("FROM User");
        $this->assertEqual($coll->count(), 8);
        
        $count = $this->connection->count();
        $coll->loadRelated("Phonenumber");

        $this->assertEqual(($count + 1), $this->connection->count());

        $this->assertEqual($coll[0]->Phonenumber[0]->phonenumber, "123 123");

        $this->assertEqual(($count + 1), $this->connection->count());

        $coll[0]->Phonenumber[1]->phonenumber;

        $this->assertEqual(($count + 1), $this->connection->count());

        $this->assertEqual($coll[4]->Phonenumber[0]->phonenumber, "111 555 333");
        $this->assertEqual($coll[4]["Phonenumber"][1]->phonenumber, "123 213");
        $this->assertEqual($coll[4]["Phonenumber"][2]->phonenumber, "444 555");

        $this->assertEqual($coll[5]->Phonenumber[0]->phonenumber, "111 222 333");


        $this->assertEqual($coll[6]->Phonenumber[0]->phonenumber, "111 222 333");
        $this->assertEqual($coll[6]["Phonenumber"][1]->phonenumber, "222 123");
        $this->assertEqual($coll[6]["Phonenumber"][2]->phonenumber, "123 456");
        
        $this->assertEqual(($count + 1), $this->connection->count());
        
        $this->connection->clear();
    }

    public function testCount() 
    {
        $coll = new Doctrine_Collection($this->connection->getTable('User'));
        $this->assertEqual($coll->count(), 0);
        $coll[0];
        $this->assertEqual($coll->count(), 1);
    }

    public function testGenerator() 
    {
        $coll = new Doctrine_Collection($this->objTable);
        $coll->setKeyColumn('name');

        $user = new User();
        $user->name = "name";
        $coll->add($user);

        $this->assertTrue($coll["name"] === $user);

        $this->connection->getTable("email")->setAttribute(Doctrine::ATTR_COLL_KEY,"address");
        $emails = $this->connection->getTable("email")->findAll();
        foreach($emails as $k => $v) {
            $this->assertTrue(gettype($k), "string");
        }

    }

    public function testFetchCollectionWithIdAsIndex() 
    {
        $user = new User();
        $user->attribute(Doctrine::ATTR_COLL_KEY, 'id');
        
        $users = $user->getTable()->findAll();
        $this->assertFalse($users->contains(0));
        $this->assertEqual($users->count(), 8);
    }

    public function testFetchCollectionWithNameAsIndex() 
    {
        $user = new User();
        $user->attribute(Doctrine::ATTR_COLL_KEY, 'name');
        
        $users = $user->getTable()->findAll();
        $this->assertFalse($users->contains(0));
        $this->assertEqual($users->count(), 8);
    }

    public function testFetchMultipleCollections() 
    {
        $this->connection->clear();
        
        $user = new User();
        $user->attribute(Doctrine::ATTR_COLL_KEY, 'id');
        $phonenumber = new Phonenumber();
        $phonenumber->attribute(Doctrine::ATTR_COLL_KEY, 'id');


        $q = new Doctrine_Query();
        $users = $q->from('User u, u.Phonenumber p')->execute();
        $this->assertFalse($users->contains(0));
        $this->assertEqual($users->count(), 8);

        $this->assertEqual($users[4]->name, 'zYne');

        $this->assertEqual($users[4]->Phonenumber[0]->exists(), false);
        $this->assertEqual($users[4]->Phonenumber[1]->exists(), false);
    }

    public function testCustomManagerCollectionClass()
    {
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_COLLECTION_CLASS, 'MyCollection');

        $user = new User();
        $this->assertTrue($user->Phonenumber instanceof MyCollection);

        $manager->setAttribute(Doctrine::ATTR_COLLECTION_CLASS, 'Doctrine_Collection');
    }

    public function testCustomConnectionCollectionClass()
    {
        $conn = Doctrine::getTable('Phonenumber')->getConnection();
        $conn->setAttribute(Doctrine::ATTR_COLLECTION_CLASS, 'MyConnectionCollection');

        $user = new User();
        $this->assertTrue($user->Phonenumber instanceof MyConnectionCollection);

        $conn->unsetAttribute(Doctrine::ATTR_COLLECTION_CLASS);
    }

    public function testCustomTableCollectionClass()
    {
        $userTable = Doctrine::getTable('Phonenumber');
        $userTable->setAttribute(Doctrine::ATTR_COLLECTION_CLASS, 'MyPhonenumberCollection');

        $user = new User();
        $this->assertTrue($user->Phonenumber instanceof MyPhonenumberCollection);

        $userTable->unsetAttribute(Doctrine::ATTR_COLLECTION_CLASS);
    }
}

class MyCollection extends Doctrine_Collection
{
}

class MyConnectionCollection extends MyCollection
{
}

class MyPhonenumberCollection extends MyConnectionCollection
{
}