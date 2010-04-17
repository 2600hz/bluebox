<?php

class MisdnSetting extends FreePbx_Record {
    function setTableDefinition()
    {
        $this->hasColumn('id', 'integer', null, array('primary' => true));
        $this->hasColumn('dsp_debug', 'integer', 3, array('range' => array(0,255), 'default' => 0, 'notnull' => true));
        $this->hasColumn('dsp_poll', 'enum', null, array('values' => array(32, 64, 128, 256), 'default' => 128, 'notnull' => true));
        $this->hasColumn('dsp_dtmfthreshold', 'integer', 4, array('default' => 250));
        $this->hasColumn('dsp_options', 'integer', 4, array('default' => 0));
        $this->hasColumn('hfcmulti_debug', 'integer', 5, array('range' => array(0,33663), 'default' => 0, 'notnull' => true));
        $this->hasColumn('hfcmulti_pcm', 'integer', 3, array('default' => -1));
        $this->hasColumn('hfcmulti_poll', 'enum', null, array('values' => array(32, 64, 128, 256), 'default' => 128, 'notnull' => true));
        $this->hasColumn('devnode_user', 'string', 32, array('default' => 'root', 'notnull' => true));
        $this->hasColumn('devnode_group', 'string', 32, array('default' => 'root', 'notnull' => true));
        $this->hasColumn('devnode_mode', 'string', 3, array('default' => '644', 'regexp' => '/^[0-7]{3}$/'));
        $this->hasColumn('misdn_conf_file', 'string', 512, array('default' => '/etc/mISDN.conf', 'notnull' => true));
        $this->hasColumn('asterisk_misdn_conf_file', 'string', 512, array('default' => '/etc/asterisk/misdn.conf', 'notnull' => true));
    }
    
    
    function setUp(){
        $this->actAs('Timestampable');
        $this->actAs('SoftDelete');
    }
    
    
}