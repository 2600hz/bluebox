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
 * Card.php - Digital and analog telephony cards
 *
 * @author reto.haile <reto.haile@selmoni.ch>
 * @license CPAL-1.0
 * @package FreePBX3
 * @subpackage CardManager
 *
 */

class MisdnCard extends FreePbx_Record 
{
    function setTableDefinition()
    {
        $this->hasColumn('card_id', 'integer', null, array('primary' => true, 'autoincrement' => true));
        $this->hasColumn('pci_address', 'string', 9, array('notnull' => true,
                         'regexp' => '/[0-9a-f]{2}\:[0-9a-f]{2}\.[0-9a-f]{1}$/'));
        $this->hasColumn('card_model_id', 'integer', null, array('notnull' => true, 'notblank' => true));
        $this->hasColumn('description', 'string', 1024);
    }

    public function setUp() 
    {
        /*
         * Relationships
         */
        $this->hasOne('MisdnCardModel', array('local' => 'card_model_id', 'foreign'  => 'card_model_id'));
        $this->hasMany('MisdnCardSetting as Settings', array('local' => 'card_id', 'foreign' => 'card_id'));
        $this->hasMany('MisdnPort as Ports', array('local' => 'card_id', 'foreign' => 'card_id'));
         
        /*
         * Behaviors
         */
        $this->actAs('Timestampable');      // Keep track of timestamps on records in this table
        $this->actAs('SoftDelete');         // Ads a deleted_at row. Instead of deleting the record, deleted_at is set to the actual date and time
    }
}
