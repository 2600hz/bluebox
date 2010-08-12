<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointmanModelList extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('id', 'string', 11, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('brand', 'integer', 4, array('notnull' => TRUE));
        $this->hasColumn('model', 'string', 25, array('notnull' => TRUE));
        $this->hasColumn('template_list', 'string', NULL, array('notnull' => TRUE));
        $this->hasColumn('template_data', 'blob', NULL, array('notnull' => TRUE));
        $this->hasColumn('product_id', 'string', 11, array('notnull' => TRUE));
        $this->hasColumn('enabled', 'integer', 4, array('default' => '0', 'notnull' => TRUE));
        $this->hasColumn('hidden', 'integer', 4, array('default' => '0', 'notnull' => TRUE));
    }
}