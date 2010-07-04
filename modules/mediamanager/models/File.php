<?php
class File extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('file_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('user_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('name', 'string', 256, array('notblank' => true));
        $this->hasColumn('path', 'string', 256, array('notblank' => true));
        $this->hasColumn('size', 'integer', 11, array('notblank' => true));
        $this->hasColumn('type', 'string', 256, array('notblank' => true));
        $this->hasColumn('duration', 'decimal', 10, array('scale' => 2));
        $this->hasColumn('audio_bit_rate', 'integer', 11);
        $this->hasColumn('audio_sample_rate', 'integer', 11);
        $this->hasColumn('description', 'string', 512);
        $this->hasColumn('registry', 'array', 10000, array('default' => array()));
        $this->hasColumn('plugins', 'array', 10000, array('default' => array()));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        $this->hasOne('User', array('local' => 'user_id', 'foreign' => 'user_id', 'onDelete' => 'CASCADE'));

        $this->actAs('Timestampable');
    }
}
