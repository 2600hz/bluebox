<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Jon Blanton <jon@2600hz.com>
 * @license MPL
 * @package redbox-1.0
 */

class Redbox extends Bluebox_Record
{
    function setTableDefinition()
    {
        $this->hasColumn('redbox_id', 'integer', 11, array('primary' => true, 'unsigned' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 200, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('port1_label', 'string', 30, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('port2_label', 'string', 30, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('port3_label', 'string', 30, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('ports', 'array', 1000, array('default' => array(NULL)));
    }

    public function setUp()
    {
        $this->actAs('GenericStructure');
        $this->actAs('Timestampable');
        $this->actAs('TelephonyEnabled');
        $this->actAs('MultiTenant');
    }
}
