<?php defined('SYSPATH') or die('No direct access allowed.');

class TTSEngine extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('tts_engine_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 255, array('notblank' => true));
        $this->hasColumn('description', 'string');
        $this->hasColumn('path', 'string', 512);
        $this->hasColumn('speakers', 'array');
    }

    /**
     * Sets up relationships, behaviors, etc.
     */
    public function setUp()
    {
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
    }

    public static function catalog()
    {
        $catalog = array();

        $records = Doctrine::getTable(__CLASS__)->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($records as $record)
        {
            foreach ($record['speakers'] as $speaker)
            {
                $catalog[$record['name']][$record['name'] .'/' .$speaker] = $speaker;
            }
        }

        return $catalog;
    }
}