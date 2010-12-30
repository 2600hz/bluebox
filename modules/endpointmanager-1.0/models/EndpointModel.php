<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointModel extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_model_id', 'integer', 11, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('endpoint_product_line_id', 'integer', 11, array('notnull' => TRUE, 'unsigned' => TRUE));
        $this->hasColumn('name', 'string', 25, array('notnull' => TRUE));
        $this->hasColumn('template_list', 'string', NULL, array('notnull' => TRUE));
        $this->hasColumn('template_data', 'blob', NULL, array('notnull' => TRUE));
        $this->hasColumn('enabled', 'integer', 4, array('default' => '1', 'notnull' => TRUE));
        $this->hasColumn('hidden', 'integer', 4, array('default' => '0', 'notnull' => TRUE));
    }

    public function setUp()
    {
        $this->hasOne('EndpointProductLine', array('local' => 'endpoint_product_line_id', 'foreign' => 'endpoint_product_line_id'));
        $this->hasMany('EndpointDevice', array('local' => 'endpoint_model_id', 'foreign' => 'endpoint_model_id'));
    }
}