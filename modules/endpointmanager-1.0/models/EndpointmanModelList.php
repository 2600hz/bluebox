<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointmanModelList extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('id', 'integer', 11, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('brand_id', 'integer', 11, array('notnull' => TRUE, 'unsigned' => TRUE));
        $this->hasColumn('name', 'string', 25, array('notnull' => TRUE));
        $this->hasColumn('template_list', 'string', NULL, array('notnull' => TRUE));
        $this->hasColumn('template_data', 'blob', NULL, array('notnull' => TRUE));
        $this->hasColumn('product_id', 'integer', 11, array('notnull' => TRUE, 'unsigned' => TRUE));
        $this->hasColumn('enabled', 'integer', 4, array('default' => '0', 'notnull' => TRUE));
        $this->hasColumn('hidden', 'integer', 4, array('default' => '0', 'notnull' => TRUE));
    }

    public function setUp()
    {
        $this->hasOne('EndpointmanBrandList', array('local' => 'brand_id', 'foreign' => 'id'));
        $this->hasOne('EndpointmanProductList', array('local' => 'product_id', 'foreign' => 'id'));
    }
}