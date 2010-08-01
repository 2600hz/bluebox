<?php
class FeatureCode extends Bluebox_Record
{
    public function construct()
    {
        if ( ! empty($this->registry) )
        {
            return;
        }

        $registry = array();

        $sections = FreeSwitch::getDialplanSections();

        foreach($sections as $section)
        {
            // no xml defined by default
            $registry[$section] = '';
        }

        $this->registry = $registry;
    }

    /**
    * Sets the table name, and defines the table columns.
    */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('feature_code_id', 'integer', 11, array('unsigned' => TRUE
                     ,'notnull' => TRUE
                     ,'primary' => TRUE
                     ,'autoincrement' => TRUE));
        $this->hasColumn('name', 'string', 80, array('notnull' => TRUE, 'minlength' => 2));
        $this->hasColumn('description', 'string', 512);
        // section-specific XML is stored in the $registry, provided by the GenericStructure behaviour
    }

    /**
    * Sets up relationships, behaviors, etc.
    */
    public function setUp()
    {
        $this->hasMany('FeatureCodeNumber as Number', array('local' => 'feature_code_id', 'foreign' => 'foreign_id', 'owningSide' => FALSE));

        // BEHAVIORS
        // Gives a generic $registry
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
    }
}
