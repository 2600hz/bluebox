<?php

/**
 * Doctrine_Ticket_673_TestCase
 *
 * @package     Doctrine
 * @author      Tamcy <7am.online@gmail.com>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */

class Doctrine_Ticket_673_TestCase extends Doctrine_UnitTestCase
{
    public function prepareData() 
    { }

    public function prepareTables()
    {
      $this->tables = array('T673_Student');
      parent::prepareTables();
    }

    public function testTicket()
    {
      $q = Doctrine_Query::create()
        ->update('T673_Student s')
        ->set('s.foo', 's.foo + 1')
        ->where('s.id = 2');
      
      $this->assertTrue(preg_match_all('/(s_foo)/', $q->getSqlQuery(), $m) === 2);
      $this->assertTrue(preg_match_all('/(s_id)/', $q->getSqlQuery(), $m) === 1);
      
      try {
        $q->execute();
        $this->pass();
      } catch (Exception $e) {
        $this->fail($e->__toString());
      }

      $q = Doctrine_Query::create()
        ->delete()
        ->from('T673_Student s')
        ->where('s.name = ? AND s.foo < ?', 'foo', 3);
      
      $this->assertTrue(preg_match_all('/(s_name)/', $q->getSqlQuery(), $m) === 1);
      $this->assertTrue(preg_match_all('/(s_foo)/', $q->getSqlQuery(), $m) === 1);

      try {
        $q->execute();
        $this->pass();
      } catch (Exception $e) {
        $this->fail($e->__toString());
      }
    }
}


class T673_Student extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('T673_Student_record');

    $this->hasColumn('s_id as id', 'varchar', 30, array (  'primary' => true,));
    $this->hasColumn('s_foo as foo', 'integer', 4, array ('notnull'=>true));
    $this->hasColumn('s_name as name', 'varchar', 50, array ());
  }
}
