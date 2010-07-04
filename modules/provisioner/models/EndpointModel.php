<?php
/**
 * @package Bluebox
 * @subpackage Provisioner
 *
 */
class EndpointModel extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_model_id', 'integer', null, array(
            'primary' => true,
            'autoincrement' => true
        ));
        $this->hasColumn('endpoint_vendor_id', 'integer', null);
        $this->hasColumn('description', 'string', 200);
        $this->hasColumn('line_count', 'integer', null, array(
            'default' => 1
        ));
    }
    public function setUp()
    {
        $this->hasMany('Endpoint', array(
            'local' => 'endpoint_model_id',
            'foreign' => 'endpoint_model_id'
        ));
        $this->hasOne('EndpointVendor', array(
            'local' => 'endpoint_vendor_id',
            'foreign' => 'endpoint_vendor_id',
            'onDelete' => 'CASCADE'
        ));
        $this->actAs('Timestampable');
        $this->actAs('SoftDelete');
    }
}
