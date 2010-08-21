<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointCustomConfig extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_custom_config_id', 'integer', 11, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('endpoint_product_line_id', 'integer', 11, array('unsigned' => TRUE, 'notnull' => TRUE));
        $this->hasColumn('name', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('original_name', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('data', 'blob');
    }

    public function setUp()
    {
        $this->hasOne('EndpointProductLine', array('local' => 'endpoint_product_line_id', 'foreign' => 'endpoint_product_line_id'));
    }
}