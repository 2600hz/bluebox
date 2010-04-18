<?php
///**
// * @package FreePBX3
// * @subpackage Provisioner
// *
// */
//class EndpointFile extends FreePbx_Record
//{
//    public function setTableDefinition()
//    {
//		$this->hasColumn('endpoint_file_id', 'integer', null, array('primary' => true, 'autoincrement' => true));
//		$this->hasColumn('endpoint_id', 'integer', null);
//                $this->hasColumn('directory', 'string', null);
//		$this->hasColumn('permissions', 'integer', null);
//    }
//
//	public function setUp()
//	{
//                $this->hasOne('Endpoint', array('local' => 'endpoint_id', 'foreign' => 'endpoint_id'));
//		$this->actAs('Timestampable');
//	}
//}
