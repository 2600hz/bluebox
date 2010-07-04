<?php


class Doctrine_Ticket_741_TestCase extends Doctrine_UnitTestCase
{

    public function prepareData() 
    { }

    public function prepareTables()
    {
        $this->tables = array('Parent741', 'Child741');
        parent::prepareTables();
    }

    public function testTicket()
    {
        $moo = new Parent741();
        $moo->amount = 1000;
        $cow = new Child741();

        $moo->Cows[] = $cow;
        $cow->Moo = $moo;
        $moo->save();
        $this->assertEqual($moo->amount, 0);
    }

}

class Parent741 extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->hasColumn('id', 'integer', 4, array (
      'primary' => true,
      'autoincrement' => true,
      'notnull' => true,
    ));

    $this->hasColumn('amount', 'integer');
  }

  public function setUp()
  {
    $this->hasMany('Child741 as Cows', array('local' => 'id', 'foreign' => 'moo_id'));
  }
}

class Child741 extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->hasColumn('id', 'integer', 4, array (
      'primary' => true,
      'autoincrement' => true,
      'notnull' => true,
    ));

    $this->hasColumn('moo_id', 'integer');
  }

  public function setUp()
  {
    $this->hasOne('Parent741 as Moo', array('local' => 'moo_id', 'foreign' => 'id'));
  }

  public function postInsert($e)
  {
    $this->Moo->state('TDIRTY');
    //echo "State: ". $this->Moo->state() . " \t Amount: " . $this->Moo->amount . "\n";
    $this->Moo->amount = 0;
    //echo "State: ". $this->Moo->state() . " \t Amount: " . $this->Moo->amount . "\n";
    $this->Moo->save();
    //echo "State: ". $this->Moo->state() . " \t Amount: " . $this->Moo->amount . "\n";
    $this->Moo->refresh();
    //echo "State: ". $this->Moo->state() . " \t Amount: " . $this->Moo->amount . "\n";
    /*
      This outputs the following
      State: 6         Amount: 1000
      State: 6         Amount: 0
      State: 6         Amount: 0
      State: 3         Amount: 1000

    */
  }
}