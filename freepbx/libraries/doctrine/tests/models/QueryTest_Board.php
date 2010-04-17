<?php
class QueryTest_Board extends Doctrine_Record
{
    /**
     * Initializes the table definition.
     */
    public function setTableDefinition()
    {
        $this->hasColumn('id', 'integer', 4, array('primary', 'autoincrement', 'notnull'));
        $this->hasColumn('categoryId as categoryId', 'integer', 4,
                array('notnull'));
        $this->hasColumn('name as name', 'string', 100,
                array('notnull', 'unique'));
        $this->hasColumn('lastEntryId as lastEntryId', 'integer', 4,
                array('default' => 0));
        $this->hasColumn('position as position', 'integer', 4,
                array('default' => 0, 'notnull'));
    }

    /**
     * Initializes the relations.
     */
    public function setUp()
    {
        $this->hasOne('QueryTest_Category as category', array(
            'local' => 'categoryId', 'foreign' => 'id'
        ));
        $this->hasOne('QueryTest_Entry as lastEntry', array(
            'local' => 'lastEntryId', 'foreign' => 'id', 'onDelete' => 'CASCADE'
        ));
    }
}
