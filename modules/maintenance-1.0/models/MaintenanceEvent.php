<?php defined('SYSPATH') or die('No direct access allowed.');

class MaintenanceEvent extends Bluebox_Record
{
    /**
     * Sets the table name, and defines the table columns.
     */
    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('maintenance_event_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('last_run', 'integer', 11, array('unsigned' => true, 'default' => time()));
    }

    public function postSave()
    {
        kohana::log('debug', 'Starting a maintenance cycle');

        Event::run('bluebox.maintenance');
    }
}