<?php defined('SYSPATH') or die('No direct access allowed.');

class EndpointDevice extends Bluebox_Record
{
    const EXT_NOTASSIGNED = 'Not Assigned';

    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_device_id', 'integer', 4, array('unsigned' => TRUE, 'notnull' => TRUE, 'primary' => TRUE,'autoincrement' => FALSE));
        $this->hasColumn('description', 'string', 20, array('notnull' => TRUE));
        $this->hasColumn('mac', 'string', 12);
        $this->hasColumn('endpoint_model_id', 'integer', 11, array('notnull' => TRUE, 'unsigned' => true));
        $this->hasColumn('endpoint_template_id', 'integer', 4, array('unsigned' => TRUE, 'notnull' => TRUE));
        //$this->hasColumn('ext', 'string', 15, array('default' => self::EXT_NOTASSIGNED, 'notnull' => TRUE));
        //$this->hasColumn('custom_cfg_data', 'blob', NULL, array('notnull' => TRUE, 'default' => ''));
        //$this->hasColumn('user_cfg_data', 'blob', NULL, array('notnull' => TRUE, 'default' => ''));
        //$this->hasColumn('config_files_override', 'string', NULL, array('notnull' => TRUE, 'default' => ''));

        $this->unique('mac');
    }

    public function setUp()
    {
        //$this->hasOne('EndpointTemplate', array('local' => 'endpoint_template_id', 'foreign' => 'endpoint_template_id'));
        $this->hasOne('EndpointModel', array('local' => 'endpoint_model_id', 'foreign' => 'endpoint_model_id'));

        $this->actAs('GenericStructure');
    }
}