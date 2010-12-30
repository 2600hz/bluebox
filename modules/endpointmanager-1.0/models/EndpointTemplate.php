<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointTemplate extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_template_id', 'integer', 4, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('endpoint_product_line_id', 'integer', 11, array('notnull' => TRUE, 'unsigned' => TRUE));
        $this->hasColumn('endpoint_model_id', 'integer', 10, array('notnull' => TRUE, 'unsigned' => TRUE));
        $this->hasColumn('name', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('custom_cfg_data', 'blob');
        $this->hasColumn('config_files_override', 'string');
    }

    public function setUp()
    {
        $this->hasOne('EndpointModel', array('local' => 'endpoint_model_id', 'foreign' => 'endpoint_model_id'));
        $this->hasOne('EndpointProductLine', array('local' => 'endpoint_product_line_id', 'foreign' => 'endpoint_product_line_id'));
    }
}