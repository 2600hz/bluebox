<?php

class Doctrine_Relation_ManyToMany_TestCase extends Doctrine_UnitTestCase {
    public function prepareData() { }
    public function prepareTables() {
        $this->tables = array('JC1', 'JC2', 'JC3', 'RTC1', 'RTC2', 'M2MTest', 'M2MTest2');
        parent::prepareTables();
    }

    public function testManyToManyRelationWithAliasesAndCustomPKs() {
        $component = new M2MTest2();

        try {
            $rel = $component->getTable()->getRelation('RTC5');
            $this->pass();
        } catch(Doctrine_Exception $e) {
            $this->fail();
        }
        $this->assertTrue($rel instanceof Doctrine_Relation_Association);

        $this->assertTrue($component->RTC5 instanceof Doctrine_Collection);

        try {
            $rel = $component->getTable()->getRelation('JC3');
            $this->pass();
        } catch(Doctrine_Exception $e) {
            $this->fail();
        }
        $this->assertEqual($rel->getLocal(), 'oid');
    }
    public function testJoinComponent() {
        $component = new JC3();
        
        try {
            $rel = $component->getTable()->getRelation('M2MTest2');
            $this->pass();
        } catch(Doctrine_Exception $e) {
            echo $e->getMessage();
            $this->fail();
        }
        $this->assertEqual($rel->getForeign(), 'oid');
    }

    public function testManyToManyRelationFetchingWithAliasesAndCustomPKs2() {
        $q = new Doctrine_Query();

        try {
            $q->from('M2MTest2 m INNER JOIN m.JC3');
            $this->pass();
        } catch(Doctrine_Exception $e) {
            $this->fail();
        }
        try {
            $q->execute();
            $this->pass();
        } catch(Doctrine_Exception $e) {
            $this->fail();
        }
    }
    public function testManyToManyHasRelationWithAliases4() {

        try {
            $component = new M2MTest();

            $this->pass();
        } catch(Doctrine_Exception $e) {
            $this->fail();
        }
    }

    public function testManyToManyHasRelationWithAliases3() {
        $component = new M2MTest();

        try {
            $rel = $component->getTable()->getRelation('RTC3');
            $this->pass();
        } catch(Doctrine_Exception $e) {
            $this->fail();
        }
        $this->assertTrue($rel instanceof Doctrine_Relation_Association);
        
        $this->assertTrue($component->RTC3 instanceof Doctrine_Collection);
    }


    public function testManyToManyHasRelationWithAliases() {
        $component = new M2MTest();

        try {
            $rel = $component->getTable()->getRelation('RTC1');
            $this->pass();
        } catch(Doctrine_Exception $e) {
            $this->fail();
        }
        $this->assertTrue($rel instanceof Doctrine_Relation_Association);
        
        $this->assertTrue($component->RTC1 instanceof Doctrine_Collection);
    }

    public function testManyToManyHasRelationWithAliases2() {
        $component = new M2MTest();

        try {
            $rel = $component->getTable()->getRelation('RTC2');
            $this->pass();
        } catch(Doctrine_Exception $e) {
            $this->fail();
        }
        $this->assertTrue($rel instanceof Doctrine_Relation_Association);
        
        $this->assertTrue($component->RTC1 instanceof Doctrine_Collection);
    }


    public function testManyToManyRelationSaving() {
        $component = new M2MTest();

        $component->RTC1[0]->name = '1';
        $component->RTC1[1]->name = '2';
        $component->name = '2';
        
        $count = $this->connection->count();

        $component->save();

        $this->assertEqual($this->connection->count(), ($count + 5));
        
        $this->assertEqual($component->RTC1->count(), 2);
        
        $component = $component->getTable()->find($component->id);
        
        $this->assertEqual($component->RTC1->count(), 2);

        // check that it doesn't matter saving the other M2M components as well

        $component->RTC2[0]->name = '1';
        $component->RTC2[1]->name = '2';

        $count = $this->connection->count();

        $component->save();

        $this->assertEqual($this->connection->count(), ($count + 4));

        $this->assertEqual($component->RTC2->count(), 2);

        $component = $component->getTable()->find($component->id);

        $this->assertEqual($component->RTC2->count(), 2);

    }

    public function testManyToManyRelationSaving2() {
        $component = new M2MTest();

        $component->RTC2[0]->name = '1';
        $component->RTC2[1]->name = '2';
        $component->name = '2';
        
        $count = $this->connection->count();

        $component->save();

        $this->assertEqual($this->connection->count(), ($count + 5));
        
        $this->assertEqual($component->RTC2->count(), 2);
        
        $component = $component->getTable()->find($component->id);

        $this->assertEqual($component->RTC2->count(), 2);

        // check that it doesn't matter saving the other M2M components as well

        $component->RTC1[0]->name = '1';
        $component->RTC1[1]->name = '2';

        $count = $this->connection->count();

        $component->save();

        $this->assertEqual($this->connection->count(), ($count + 3));
        
        $this->assertEqual($component->RTC1->count(), 2);
        
        $component = $component->getTable()->find($component->id);
        
        $this->assertEqual($component->RTC1->count(), 2);
    }
    
    public function testManyToManySimpleUpdate() {
        $component = $this->connection->getTable('M2MTest')->find(1);
        
        $this->assertEqual($component->name, 2);
        
        $component->name = 'changed name';
        
        $component->save();
        
        $this->assertEqual($component->name, 'changed name');
    }
}