<?php
class OdbcMap extends Bluebox_Record {
    public function setTableDefinition()
    {
        $this->hasColumn('odbcmap_id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
        $this->hasColumn('odbc_id', 'integer', 11);
    }

    public function setUp()
    {
        $this->hasOne('Odbc', array('local' => 'odbc_id', 'foreign' => 'odbc_id'));
        
        // BEHAVIORS
        $this->actAs('Polymorphic');
        $this->actAs('TelephonyEnabled');
    }
}
