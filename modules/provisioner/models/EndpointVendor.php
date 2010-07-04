<?php
/**
 * @package Bluebox
 * @subpackage Provisioner
 *
 */
class EndpointVendor extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_vendor_id', 'integer', null, array(
            'primary' => true,
            'autoincrement' => true
        ));
        $this->hasColumn('oui', 'string', 20);
        $this->hasColumn('vendor', 'string', 200);
        $this->hasColumn('description', 'string', 200);
        $this->hasColumn('driver', 'string', 200);
    }
    public function setUp()
    {
        $this->hasMany('EndpointModel', array(
            'local' => 'endpoint_vendor_id',
            'foreign' => 'endpoint_vendor_id'
        ));
        $this->actAs('Timestampable');
    }
}
