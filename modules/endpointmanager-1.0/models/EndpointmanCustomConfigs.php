<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointmanCustomConfigs extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('id', 'integer', 11, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('product_id', 'integer', 11, array('unsigned' => TRUE, 'notnull' => TRUE));
        $this->hasColumn('name', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('original_name', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('data', 'blob');
    }

    public function setUp()
    {
        $this->hasOne('EndpointmanProductList', array('local' => 'product_id', 'foreign' => 'id'));
    }
}