<?php
/**
 * @package Bluebox
 * @subpackage Provisioner
 *
 */
class EndpointSetting extends Bluebox_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn('endpoint_setting_id', 'integer', null, array(
            'primary' => true,
            'autoincrement' => true
        ));
        /*
        *  Potentially this could be made on a per driver basis.  We will assess the community needs...
        * $this->hasColumn('endpoint_id', 'integer', null);
        */
        $this->hasColumn('chroot', 'boolean', FALSE);
        $this->hasColumn('provision_path', 'string', 512);
    }
    public function setUp()
    {
        $this->actAs('Timestampable');
    }
}
