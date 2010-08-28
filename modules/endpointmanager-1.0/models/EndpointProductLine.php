<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointProductLine extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_product_line_id', 'integer', 11, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('endpoint_brand_id', 'integer', 11, array('unsigned' => TRUE, 'notnull' => TRUE));
        $this->hasColumn('long_name', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('short_name', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('cfg_dir', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('cfg_ver', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('hidden', 'integer', 4, array('default' => '0', 'notnull' => TRUE));
        $this->hasColumn('firmware_vers', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('firmware_files', 'string', NULL, array('notnull' => TRUE));
        $this->hasColumn('config_files', 'string');
        $this->hasColumn('special_cfgs', 'blob', NULL, array('notnull' => TRUE));
    }

    public function setUp()
    {
        $this->hasOne('EndpointBrand', array('local' => 'endpoint_brand_id', 'foreign' => 'endpoint_brand_id'));
        $this->hasMany('EndpointModel', array('local' => 'endpoint_product_line_id', 'foreign' => 'endpoint_product_line_id'));
    }
}