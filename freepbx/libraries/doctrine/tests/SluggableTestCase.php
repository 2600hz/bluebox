<?php
class Doctrine_Sluggable_TestCase extends Doctrine_UnitTestCase 
{    
    public function prepareTables()
    {
        $this->tables[] = "SluggableItem";
        $this->tables[] = "SluggableItem1";
        $this->tables[] = "SluggableItem2";
        $this->tables[] = "SluggableItem3";
        $this->tables[] = "SluggableItem4";
        $this->tables[] = "SluggableItem5";
        $this->tables[] = "SluggableItem6";
        $this->tables[] = "SluggableItem7";
        $this->tables[] = "SluggableItem8";
        $this->tables[] = "SluggableItem9";
        parent::prepareTables();
    }
    
    public function prepareData() 
    { }

    public function testSluggableWithNoFieldsNoGetUniqueSlugMethod()
    {
        $item = new SluggableItem();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'result-of-to-string');
    }
    
    public function testSSluggableWithNoFieldsOptionButGetUniqueSlugMethod()
    {
        $item = new SluggableItem1();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'result-of-to-get-unique-slug');
    }

    public function testSluggableWithFieldsOption()
    {
        parent::prepareTables();
        $item = new SluggableItem2();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
        $item = new SluggableItem2();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'my-item-1');
        $itemTable  = Doctrine::getTable('SluggableItem2');
        $this->assertTrue($index = $itemTable->getIndex('sluggable'));
        $this->assertEqual($index['type'], 'unique');
        $this->assertEqual($index['fields'], array('slug'));
    }
    
    public function testSluggableUniqueSlugOnUpdate()
    {
        parent::prepareTables();
        $item = new SluggableItem2();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
        $item = new SluggableItem2();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'my-item-1');
        $item->slug = 'New slug';
        $item->save();
        $this->assertEqual($item->slug, 'New slug');
    }
    
    public function testSluggableWithMultipleFieldsOption()
    {
        parent::prepareTables();
        $item = new SluggableItem4();
        $item->name = 'My item';
        $item->ref = 'My ref';
        $item->save();
        $this->assertEqual($item->slug, 'my-item-my-ref');
        $item = new SluggableItem4();
        $item->name = 'My item';
        $item->ref = 'My ref';
        $item->save();
        $this->assertEqual($item->slug, 'my-item-my-ref-1');
    }
    
    public function testSluggableWithFieldsAndNonUniqueOptions()
    {
        parent::prepareTables();
        $item = new SluggableItem3();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
        $item = new SluggableItem3();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
    }
    
    public function testSluggableWithUniqueByOption()
    {
        parent::prepareTables();
        $item = new SluggableItem5();
        $item->name = 'My item';
        $item->user_id = 1;
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
        $item = new SluggableItem5();
        $item->name = 'My item';
        $item->user_id = 2;
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
        $item = new SluggableItem5();
        $item->name = 'My item';
        $item->user_id = 1;
        $item->save();
        $this->assertEqual($item->slug, 'my-item-1');
        $itemTable  = Doctrine::getTable('SluggableItem5');
        $this->assertTrue($index = $itemTable->getIndex('sluggable'));
        $this->assertEqual($index['type'], 'unique');
        $this->assertEqual($index['fields'], array('slug', 'user_id'));
    }
    
    public function testSluggableWithUniqueByOptionAndNullValue()
    {
        parent::prepareTables();
        $item = new SluggableItem5();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
        $item = new SluggableItem5();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'my-item-1');
        $item = new SluggableItem5();
        $item->name = 'My item';
        $item->user_id = 1;
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
    }
    
    public function testSluggableWithMultipleUniqueByOption()
    {
        parent::prepareTables();
        $item = new SluggableItem6();
        $item->name = 'My item';
        $item->user_id = 1;
        $item->account_id = 1;
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
        $item = new SluggableItem6();
        $item->name = 'My item';
        $item->user_id = 1;
        $item->account_id = 2;
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
        $item = new SluggableItem6();
        $item->name = 'My item';
        $item->user_id = 1;
        $item->account_id = 2;
        $item->save();
        $this->assertEqual($item->slug, 'my-item-1');
    }
    
    public function testSluggableWithFieldsOptionAndNoIndex()
    {
        parent::prepareTables();
        $itemTable  = Doctrine::getTable('SluggableItem7');
        $this->assertFalse($itemTable->getIndex('sluggable'));
    }
    
    public function testSluggableUniqueSlugOnUpdateWithOptioncanUpdateSlug ()
    {
        parent::prepareTables();
        $item = new SluggableItem8();
        $item->name = 'My item';
        $item->save();
        $this->assertEqual($item->slug, 'my-item');
        $item1 = new SluggableItem8();
        $item1->name = 'My item';
        $item1->save();
        $this->assertEqual($item1->slug, 'my-item-1');
        $item1->slug = 'New slug';
        $item1->save();
        $this->assertEqual($item1->slug, 'new-slug');
        $item1->slug = 'My item';
        $item1->save();
        $this->assertEqual($item1->slug, 'my-item-1');
        $item1->name = 'New name';
        $item1->save();
        $this->assertEqual($item1->slug, 'new-name');
    }

    public function testSluggableWithSoftDeleteFailWithSameSlug()
    {
        Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);

        parent::prepareTables();
        $item0 = new SluggableItem9();
        $item0->name = 'test';
        $item0->save();
        $this->assertEqual($item0->slug, 'test');

        // Let's try to blow up Doctrine!
        $item0->delete(); // muhaha! I don't actually delete it!

        // We must have 0 as total of records
        
        $this->assertEqual(Doctrine_Query::create()->from('SluggableItem9')->count(), 0);

        $item1 = new SluggableItem9();
        $item1->name = 'test';
        $item1->save();
        $this->assertEqual($item1->slug, 'test-1');

        Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);
    }
}

// Model with no fields option and no getUniqueSlug, but __toString method
class SluggableItem extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('my_item');
        $this->hasColumn('name', 'string', 50);
    }

    public function setUp()
    {
        parent::setUp();
        $this->actAs('Sluggable', array('unique' => true));
    }

    public function __toString()
    {
        return 'result-of-to-string';
    }
}

// Model with no fields option but getUniqueSlug method
class SluggableItem1 extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('my_item1');
        $this->hasColumn('name', 'string', 50);
    }

    public function setUp()
    {
        parent::setUp();
        $this->actAs('Sluggable', array('unique' => true));
    }

    public function __toString()
    {
        return 'result-of-to-string';
    }

    public function getUniqueSlug()
    {
        return 'result-of-to-get-unique-slug';
    }
}


// Model with fields option
class SluggableItem2 extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('my_item2');
        $this->hasColumn('name', 'string', 50);
    }

    public function setUp()
    { 
        parent::setUp();
        $this->actAs('Sluggable', array('unique' => true,
                                        'fields' => array('name')));
    }
}

// Model with fields option and non unique option
class SluggableItem3 extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('my_item3');
        $this->hasColumn('name', 'string', 50);
    }

    public function setUp()
    { 
        parent::setUp();
        $this->actAs('Sluggable', array('unique' => false,
                                        'fields' => array('name')));
    }
}

// Model with multiple fields option
class SluggableItem4 extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('my_item4');
        $this->hasColumn('name', 'string', 50);
        $this->hasColumn('ref', 'string', 50);
    }

    public function setUp()
    { 
        parent::setUp();
        $this->actAs('Sluggable', array('unique' => true,
                                        'fields' => array('name', 'ref')));
    }
}

// Model with fields and  uniqueBy option
class SluggableItem5 extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('my_item5');
        $this->hasColumn('name', 'string', 50);
        $this->hasColumn('user_id', 'string', 50);
    }

    public function setUp()
    { 
        parent::setUp();
        $this->actAs('Sluggable',   array('fields'      => array('name'),
                                          'uniqueBy'    => array('user_id'),
                                          'unique'      => true
        ));
    }
}

// Model with fields and multiple uniqueBy option
class SluggableItem6 extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('my_item6');
        // Make sure this works the same with a column that is not named id
        $this->hasColumn('iid', 'integer', null, array('primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 50);
        $this->hasColumn('user_id', 'string', 50);
        $this->hasColumn('account_id', 'string', 50);
    }

    public function setUp()
    { 
        parent::setUp();
        $this->actAs('Sluggable',   array('fields'      => array('name'),
                                          'uniqueBy'    => array('user_id', 'account_id'),
                                          'unique'      => true
        ));
    }
}

// Model with fields option and no index
class SluggableItem7 extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('my_item2');
        $this->hasColumn('name', 'string', 50);
    }

    public function setUp()
    { 
        parent::setUp();
        $this->actAs('Sluggable', array('fields'        => array('name'),
                                        'uniqueIndex'   => false,
                                        'unique'        => true));
    }
}

// Model with fields option and canUpdateSlug option
class SluggableItem8 extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('my_item8');
        $this->hasColumn('name', 'string', 50);
    }

    public function setUp()
    { 
        parent::setUp();
        $this->actAs('Sluggable', array('unique'    => true,
                                        'fields'    => array('name'),
                                        'canUpdate' => true));
    }
}

// Model usage with mixing of SoftDelete
class SluggableItem9 extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('my_item9');
        $this->hasColumn('name', 'string', 50);
    }

    public function setUp()
    { 
        parent::setUp();

        $this->actAs('SoftDelete');
        $this->actAs('Sluggable', array('unique'      => true,
                                        'uniqueIndex' => true,
                                        'fields'      => array('name')));
    }
}