<?php
/**
 *
 * Store information about Auto Attendants.
 *
 * TODO: Change this class. It doesn't allow multi-key IVRs.
 *
 * @author Michael Phillips
 * @package FreePBX3
 * @subpackage AutoAttendant
 */
class AutoAttendant extends FreePbx_Record
{

    public function setTableDefinition()
    {
        // COLUMN DEFINITIONS
        $this->hasColumn('auto_attendant_id', 'integer', 11, array('unsigned' => true, 'notnull' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('user_id', 'integer', 11, array('unsigned' => true)); // Owner?  system wide?  Not sure how we want to hand out access....
        $this->hasColumn('name', 'string', 512, array('notblank' => true));
        $this->hasColumn('description', 'string',  4096);
        $this->hasColumn('timeout' , 'integer', 11, array('notblank' => true, 'default' => 10));
        $this->hasColumn('digit_timeout' , 'integer', 11, array('notblank' => true, 'default' => 2));
        $this->hasColumn('tts_string', 'blob');
        $this->hasColumn('file_id', 'integer', 11);
        $this->hasColumn('type', 'enum', null, array('values' => array('tts', 'audio')) );
        $this->hasColumn('extension_context_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('extension_digits', 'integer', 11, array('unsigned' => true, 'default' => 4));
    }


    public function setUp()
    {
        // BEHAVIORS
        $this->actAs('TelephonyEnabled');
        $this->hasMany('AutoAttendantKey', array('local'   => 'auto_attendant_id', 'foreign' => 'auto_attendant_id'));
        $this->actAs('MultiTenant');
    }

}
