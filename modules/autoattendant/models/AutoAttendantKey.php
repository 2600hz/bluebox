<?php
/**
 *
 * Store information about Auto Attendant Key mappings.
 *
 * TODO: Change this class. It doesn't allow multi-key IVRs.
 *
 * @author Michael Phillips
 * @package Bluebox
 * @subpackage AutoAttendant
 */
class AutoAttendantKey extends Bluebox_Record {

    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('auto_attendant_key_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('auto_attendant_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('auto_attendant_key', 'string', 32, array('default' => NULL));

        //ALTER TABLE `bluebox`.`auto_attendant_key` ADD COLUMN `destination` VARCHAR(32) NOT NULL AFTER `auto_attendant_key`;
        //$this->hasColumn('destination', 'string', 11, array('default' => NULL));
        
        $this->hasColumn('number_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
    }

    public function setUp()
    {
        // BEHAVIORS
        $this->actAs('TelephonyEnabled');
        $this->hasOne('AutoAttendant', array('local'   => 'auto_attendant_id', 'foreign' => 'auto_attendant_id', 'onDelete' => 'CASCADE'));
        $this->hasOne('Number', array('local'   => 'number_id', 'foreign' => 'number_id', 'onDelete' => 'CASCADE'));

        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, 'Number') or ($class == 'Number')) {
                $numberTable = Doctrine::getTable($class);
                $numberTable->bind(array('AutoAttendantKey', array('local' => 'number_id', 'foreign' => 'number_id')), Doctrine_Relation::MANY);
            }
        }
    }

}
