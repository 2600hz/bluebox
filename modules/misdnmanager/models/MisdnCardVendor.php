<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 * Copyright (C) 2005-2009, Darren Schreiber <d@d-man.org>
 *
 * Version: FPL 1.0 (a modified version of MPL 1.1)
 *
 * The contents of this file are subject to the FreePBX Public License Version
 * 1.0 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.freepbx.org/FPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is FreePBX Modular Telephony Software Library / Application
 *
 * The Initial Developer of the Original Code is
 * Darren Schreiber <d@d-man.org>
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * Darren Schreiber <d@d-man.org>
 *
 */

/**
 * MisdnCardVendor.php
 *
 * @author reto.haile <reto.haile@selmoni.ch>
 * @license CPAL-1.0
 * @package FreePBX3
 * @subpackage MisdnManager
 *
 */

class MisdnCardVendor extends FreePbx_Record {
    function setTableDefinition(){
        $this->hasColumn('card_vendor_id', 'integer', null, array('primary' => true, 'autoincrement' => true));
        $this->hasColumn('vendor', 'string', 100, array('notnull' => true, 'notblank' => true));
    }

    public function setUp() {
        /*
         * Relationships
         */
        $this->hasMany('MisdnCardModel', array('local' => 'card_vendor_id', 'foreign' => 'card_vendor_id'));
         
        /*
         * Behaviors
         */
        $this->actAs('Timestampable');      // Keep track of timestamps on records in this table
        $this->actAs('SoftDelete');         // Adds a deleted_at row. Instead of deleting the record, deleted_at is set to the actual date and time
    }
}
