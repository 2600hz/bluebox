<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointmanOuiList extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('id', 'integer', 4, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('oui', 'string', 30);
        $this->hasColumn('brand_id', 'integer', 11, array('notnull' => TRUE, 'unsigned' => TRUE));
        $this->hasColumn('custom', 'integer', 4, array('default' => '0', 'notnull' => TRUE));

        $this->unique('oui');

        $this->hasOne('EndpointmanBrandList', array('local' => 'brand_id', 'foreign' => 'id'));
    }
}