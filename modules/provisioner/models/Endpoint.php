<?php
/**
 * @package FreePBX3
 * @subpackage Provisioner
 *
 */
class Endpoint extends FreePbx_Record
{
    public static $errors = array(
        'mac' => array(
            'required' => 'MAC is required.',
            'unique' => 'MAC is already in use.',
            'default' => 'Invalid MAC address.',
        ) ,
        'settings' => array(
            'default' => 'Unable to find driver.',
        ) ,
        'endpoint_model_id' => array(
            'default' => 'Model is required.',
        ) ,
    );
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_id', 'integer', null, array(
            'primary' => true,
            'autoincrement' => true
        ));
        $this->hasColumn('endpoint_vendor_id', 'integer', null);
        $this->hasColumn('endpoint_model_id', 'integer', null);
        $this->hasColumn('mac', 'string', 20, array(
            'unique' => true,
            'notnull' => true,
            'notblank' => true,
            'regexp' => '/^[0-9a-fA-F]{12}$/'
        ));
        $this->hasColumn('name', 'string', 100, array(
            'default' => ''
        ));
        $this->hasColumn('notes', 'string', 1024);
        $this->hasColumn('provision_user', 'string', 50);
        $this->hasColumn('provision_pwd', 'string', 50);
        $this->hasColumn('parameters', 'array', null, array(
            'notnull' => true
        ));
        $this->hasColumn('options', 'array', null, array(
            'notnull' => true
        ));
    }
    public function setUp()
    {
        $this->hasOne('EndpointModel', array(
            'local' => 'endpoint_model_id',
            'foreign' => 'endpoint_model_id'
        ));
        $this->hasMany('EndpointLine', array(
            'local' => 'endpoint_id',
            'foreign' => 'endpoint_id',
            'onDelete' => 'CASCADE'
        ));
        $this->actAs('Timestampable');
        //$this->actAs('SoftDelete');
        
    }
}
