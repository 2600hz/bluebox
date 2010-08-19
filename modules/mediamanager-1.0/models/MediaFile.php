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
        $this->hasColumn('account_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('description', 'string', 255, array('notblank' => true));
        $this->hasColumn('file', 'string', 255, array('notblank' => true, 'unique' => true));
        $this->hasColumn('path', 'string', 255, array('notblank' => true));
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        $this->hasOne('Account', array('local' => 'account_id', 'foreign' => 'account_id'));

        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
    }

    public static function getSize($ignore, $registry) {
        return number_format(round((isset($registry['size']) ? $registry['size'] : 0) / 1024, 2), 2) . 'KB';
    }

    public static function getType($ignore, $registry) {
        return (isset($registry['type']) ? $registry['type'] : 'Unknown');
    }
    
    public static function getLength($ignore, $registry) {
      if ( empty($registry) ) return 'Unknown';

      $minutes = floor($registry['length'] % 3600 / 60);
      $seconds = $registry['length'] % 60;
      $miliseconds = (($registry['length']) - floor($registry['length'])) * 100;

      return sprintf("%02d:%02d.%02d", $minutes, $seconds, $miliseconds);
    }

    public static function getSampleRate($ignore, $registry) {
        if (isset($registry['rates'])) {
            return implode(', ', $registry['rates']);
        } else
            return 'Unknown';
    }

    public static function getBaseName($field) {
        return basename($field);
    }

}
