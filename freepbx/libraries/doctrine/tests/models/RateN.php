<?php
class RateN extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('rates');
    $this->hasColumn('id', 'integer', 4, array('notnull' => true, 'primary' => true, 'autoincrement' => true));
    $this->hasColumn('policy_code', 'integer', 4, array (  'notnull' => true,  'notblank' => true,));
    $this->hasColumn('coverage_code', 'integer', 4, array (  'notnull' => true,  'notblank' => true,));
    $this->hasColumn('liability_code', 'integer', 4, array (  'notnull' => true,  'notblank' => true,));
    $this->hasColumn('total_rate', 'float', null, array (  'notnull' => true,  'notblank' => true,));
  }
  
  public function setUp()
  {
    $this->hasOne('PolicyCodeN', array('local' => 'policy_code', 'foreign' => 'code' ));
    $this->hasOne('CoverageCodeN', array('local' => 'coverage_code', 'foreign' => 'code' ));
    $this->hasOne('LiabilityCodeN', array('local' => 'liability_code', 'foreign' => 'code' ));
  }
}