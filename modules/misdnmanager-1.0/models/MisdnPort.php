<?php

/**
 * MisdnPort.php - Represents one card port
 *
 * @author reto.haile <reto.haile@selmoni.ch>
 * @license CPAL-1.0
 * @package Bluebox
 * @subpackage MisdnManager
 *
 */
class MisdnPort extends Bluebox_Record
{
    public static $errors = array(
      'description' => array(
        'required'  => 'Please provide a short description what this port is used for.',
        'length'    => 'Max length is 64.'));


    public function setTableDefinition()
    {
        $this->hasColumn('port_id',     'integer',  11, array('primary' => true, 'autoincrement' => true, 'unsigned' => true));
        $this->hasColumn('card_id',     'integer',  11, array('notnull' => true));
        $this->hasColumn('number',      'integer',  2,  array('notnull' => true));
        $this->hasColumn('description', 'string',   64);
        $this->hasColumn('mode',        'enum',     4,  array('notnull' => true, 'values' => array('te', 'nt'), 'default' => 'te'));
        $this->hasColumn('link',        'enum',     8,  array('notnull' => true, 'values' => array('ptp', 'ptmp'), 'default' => 'ptp'));
        $this->hasColumn('masterclock', 'boolean',  1,  array('notnull' => true, 'default' => false));
        $this->hasColumn('capi',        'boolean',  1,  array('notnull' => true, 'default' => false));
        $this->hasColumn('optical',     'boolean',  1,  array('notnull' => true, 'default' => false));
        $this->hasColumn('los',         'boolean',  1,  array('notnull' => true, 'default' => false));
        $this->hasColumn('ais',         'boolean',  1,  array('notnull' => true, 'default' => false));
        $this->hasColumn('slip',        'boolean',  1,  array('notnull' => true, 'default' => false));
        $this->hasColumn('nocrc4',      'boolean',  1,  array('notnull' => true, 'default' => false));
    }


    public function setUp()
    {
        /*
         * Relationships
         */
        $this->hasOne('MisdnCard as Card', array('local' => 'card_id', 'foreign' => 'card_id', 'onDelete' => 'CASCADE'));
//        $this->hasMany('MisdnPortSetting as Settings', array('local' => 'port_id', 'foreign' => 'port_id'));

        /*
         * Behaviors
         */
        $this->actAs('Timestampable');
        $this->actAs('SoftDelete');
    }
}