<?php defined('SYSPATH') or die('No direct access allowed.');

class NumberType extends Bluebox_Record
{
    const TYPE_NORMAL = 0;
    const TYPE_NOGROUP = 1;

    /**
     * Sets the table name, and defines table columns.
     */
    public function setTableDefinition()
    {
        // Don't add foreign key constraints because core number types will have no module association, and that's OK :-) We only use the package_id on install/remove
        $this->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL ^ Doctrine::EXPORT_CONSTRAINTS);

        // COLUMN DEFINITIONS
        $this->hasColumn('number_type_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('package_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('class', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('type', 'integer', 11, array('default' => NumberType::TYPE_NORMAL));
        $this->hasColumn('registry', 'array', 10000, array('default' => array()));
        $this->hasColumn('plugins', 'array', 10000, array('default' => array()));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        // RELATIONSHIPS
        $this->hasOne('Package', array('local' => 'package_id', 'foreign' => 'package_id'));    // A number endpoint must belong to a module

        // BEHAVIORS
        $this->actAs('Timestampable');
    }
}

