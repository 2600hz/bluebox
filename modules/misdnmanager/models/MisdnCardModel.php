<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * MisdnCardModel.php
 *
 * @author reto.haile <reto.haile@selmoni.ch>
 * @license CPAL-1.0
 * @package FreePBX3
 * @subpackage CardManager
 *
 */

class MisdnCardModel extends FreePbx_Record
{
    function setTableDefinition()
    {
        $this->hasColumn('card_model_id', 'integer', null, array('primary' => true, 'autoincrement' => true));
        $this->hasColumn('card_vendor_id', 'integer', null, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('pci_subsys_id', 'string', 9, array('unique' => true, 'notnull' => true, 'notblank' => true, 'regexp' => '/^[0-9a-fA-F]{4}\:[0-9a-fA-F]{4}$/'));
        $this->hasColumn('model', 'string', 100, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('type', 'string', 10, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('portcount', 'integer', 1, array('notnull' => true, 'notblank' => true));
    }

    public function setUp()
    {
        /*
         * Relationships
         */
        $this->hasOne('MisdnCardVendor', array('local' => 'card_vendor_id', 'foreign' => 'card_vendor_id', 'onDelete' => 'CASCADE'));
//        $this->hasMany('MisdnCard', array('local' => 'card_model_id', 'foreign' => 'card_model_id'));
         
        /*
         * Behaviors
         */
        $this->actAs('Timestampable');      // Keep track of timestamps on records in this table
        $this->actAs('SoftDelete');         // Adds a deleted_at row. Instead of deleting the record, deleted_at is set to the actual date and time
    }
}
