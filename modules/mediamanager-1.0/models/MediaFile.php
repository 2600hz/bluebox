<?php
class MediaFile extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('mediafile_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('user_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('description', 'string', 80, array('notblank' => true));
        $this->hasColumn('filename', 'string', 80, array('notblank' => true));
        $this->hasColumn('path', 'string', 512, array('notblank' => true));
        $this->hasColumn('category_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'default' => 0));
        //$this->hasColumn('size', 'integer', 11, array('notblank' => true));
        //$this->hasColumn('type', 'string', 256, array('notblank' => true));
        //$this->hasColumn('duration', 'decimal', 10, array('scale' => 2));
        //$this->hasColumn('audio_bit_rate', 'integer', 11);
        //$this->hasColumn('audio_sample_rate', 'integer', 11);
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        $this->hasOne('User', array('local' => 'user_id', 'foreign' => 'user_id', 'onDelete' => 'CASCADE'));
        $this->hasOne('MediaCategory', array('local' => 'category_id', 'foreign' => 'category_id'));

        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
    }

    public static function getSize($registry) {
        return (isset($registry['size']) ? $registry['size'] : 0);
    }

    public static function getType($registry) {
        return (isset($registry['type']) ? $registry['type'] : 'Unknown');
    }
    
    public static function getDuration($registry) {
        return (isset($registry['duration']) ? $registry['duration'] : '');
    }

    public static function getSampleRate($registry) {
        return (isset($registry['sample_rate']) ? $registry['sample_rate'] : 'Unknown');
    }

}
