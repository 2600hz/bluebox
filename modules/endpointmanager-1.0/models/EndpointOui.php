<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointOui extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_oui_id', 'integer', 4, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('oui', 'string', 30);
        $this->hasColumn('endpoint_brand_id', 'integer', 11, array('notnull' => TRUE, 'unsigned' => TRUE));
        $this->hasColumn('custom', 'integer', 4, array('default' => '0', 'notnull' => TRUE));

        $this->unique('oui');
    }

    public function setUp()
    {
        $this->hasOne('EndpointBrand', array('local' => 'endpoint_brand_id', 'foreign' => 'endpoint_brand_id'));
    }
}