<?php defined('SYSPATH') or die('No direct access allowed.');

class CustomFeatureCode extends Bluebox_Record
{
    public function construct()
    {
        return (array) $this->registry;
    }

    /**
    * Sets the table name, and defines the table columns.
    */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('custom_feature_code_id', 'integer', 11, array('unsigned' => TRUE , 'notnull' => TRUE , 'primary' => TRUE, 'autoincrement' => TRUE));
        $this->hasColumn('name', 'string', 80, array('notnull' => TRUE, 'minlength' => 2));
        $this->hasColumn('description', 'string', 512);
	$this->hasColumn('dialplan_code','string',10000);
	$this->hasColumn('options','array', 10000, array('default' => array()));
	// options has the format:
	// array (
	//	array('field'=>'%CONTEXT%', 'question'=>'Context number:'),
	//	array('field'=>'%TIMEOFDAY%', 'question'=>'At what time (HH24:MI)')
	// );
	// All answers are (currently) collected in text fields. Drop-downs & other fields (e.g. 'context-dropdown', or 'sql-dropdown') may be added later
    }

    /**
    * Sets up relationships, behaviors, etc.
    */
    public function setUp()
    {
	$this->hasMany('FeatureCode as FeatureCodes', array('local' => 'custom_feature_code_id', 'foreign' => 'custom_feature_code_id', 'owningSide' => FALSE));

        // BEHAVIORS
        // Gives a generic $registry
        $this->actAs('GenericStructure');
        $this->actAs('MultiTenant');
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
    }
}
