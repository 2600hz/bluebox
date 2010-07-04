<?php
class FeatureCode extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
	public function setTableDefinition() {
            // COLUMN DEFINITIONS
            $this->hasColumn('feature_code_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
            $this->hasColumn('name', 'string', 80);
            $this->hasColumn('xml', 'string', 4096); // 4kb max xml size
            $this->hasColumn('description', 'string', 512);
	}

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp() {
        $this->hasOne('FeatureCodeNumber as Number', array('local' => 'feature_code_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));

        // BEHAVIORS
        //$this->actAs('Polymorphic');

        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
    }
}
