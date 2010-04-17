<?php
class ValidatorTest_Person extends Doctrine_Record {
   public function setTableDefinition() {
      $this->hasColumn('identifier', 'integer', 4, array('notblank', 'unique'));
      $this->hasColumn('is_football_player', 'boolean');
   }
   
   public function setUp() {
      $this->hasOne('ValidatorTest_FootballPlayer', array(
          'local' => 'id', 'foreign' => 'person_id', 'onDelete' => 'CASCADE'
      ));
   }
}
