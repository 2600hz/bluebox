<?php
/**
 * @package FreePBX3
 * @subpackage Provisioner
 *
 */
class EndpointLine extends FreePbx_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_line_id', 'integer', null, array(
            'primary' => true,
            'autoincrement' => true
        ));
        $this->hasColumn('endpoint_id', 'integer', null);
        $this->hasColumn('device_id', 'integer', null, array(
            'unsigned' => true
        ));
        $this->hasColumn('line_appearance', 'integer', null);
        $this->hasColumn('parameters', 'array', null, array(
            'notnull' => true
        ));
    }
    public function setUp()
    {
        $this->hasOne('Endpoint', array(
            'local' => 'endpoint_id',
            'foreign' => 'endpoint_id'
        ));
        $this->hasOne('Device', array(
            'local' => 'device_id',
            'foreign' => 'device_id'
        ));
        $this->actAs('Timestampable');
    }
}
