<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Concept is a Brand of phones has...
 *     ... a product line which has ...
 *         ... a bunch of models.
 */

class EndpointBrand extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_brand_id', 'integer', 11, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('name', 'string', 255, array('notblank' => TRUE));
        $this->hasColumn('directory', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('cfg_ver', 'string', 255, array('notnull' => TRUE));
        $this->hasColumn('installed', 'integer', 4, array('default' => '1', 'notnull' => TRUE));
        $this->hasColumn('hidden', 'integer', 4, array('default' => '0', 'notnull' => TRUE));
    }

    public function setUp()
    {
        $this->hasMany('EndpointProductLine', array('local' => 'endpoint_brand_id', 'foreign' => 'endpoint_brand_id'));
    }
}