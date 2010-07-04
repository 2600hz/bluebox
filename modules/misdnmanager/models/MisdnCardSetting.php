<?php

class MisdnCardSetting extends Bluebox_Record {
    function setTableDefinition()
    {
        $this->hasColumn('card_setting_id', 'integer', null, array('primary' => true, 'autoincrement' => true));
        $this->hasColumn('card_id', 'integer', null, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('option', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('value', 'string', 100);
    }
    
    
    function setUp(){
        $this->hasOne('MisdnCard', array('local' => 'card_id', 'foreign' => 'card_id', 'onDelete' => 'CASCADE'));
        
        $this->actAs('Timestampable');
        $this->actAs('SoftDelete');
    }
}