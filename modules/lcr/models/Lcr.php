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
 * lcr.php - Description of the plug-in model
 *
 * @author Raymond Chandler <intralanman@gmail.com>
 * @license BSD
 * @package FreePBX
 * @subpackage LCR
 */
class Lcr extends FreePbx_Record {
    function setTableDefinition() {
        $this->hasColumn('id', 'integer', 11, array(
                'primary' => true,
                'autoincrement' => true,
                'unsigned' => true
            )
        );
        $this->hasColumn('digits', 'string', 16);
        $this->hasColumn('rate', 'float', 10, array('scale' => 6));
        $this->hasColumn('intrastate_rate', 'decimal', 10, array('scale' => 6));
        $this->hasColumn('intralata_rate', 'decimal', 10, array('scale' => 6));
        $this->hasColumn('carrier_id', 'integer', 11, array('unsigned' => true));
        $this->hasColumn('lead_strip', 'integer', 11);
        $this->hasColumn('trail_strip', 'integer', 11);
        $this->hasColumn('prefix', 'string', 16);
        $this->hasColumn('suffix', 'string', 16);
        $this->hasColumn('lcr_profile', 'string', 32, array('default' => 'default'));
        $this->hasColumn('date_start', 'date');
        $this->hasColumn('date_end', 'date');
        $this->hasColumn('quality', 'decimal', 10, array('scale' => 6));
        $this->hasColumn('reliability', 'decimal', 10, array('scale' => 6));
        $this->hasColumn('cid', 'string', 16);
        $this->hasColumn('enabled', 'integer', 1, array('default' => 1));

    //+-----------------+----------------------+------+-----+---------------------+----------------+
    //| Field           | Type                 | Null | Key | Default             | Extra          |
    //+-----------------+----------------------+------+-----+---------------------+----------------+
    //| id              | int(11)              | NO   | PRI | NULL                | auto_increment |
    //| digits          | varchar(15)          | YES  | MUL | NULL                |                |
    //| rate            | float(11,5) unsigned | NO   | MUL |                     |                |
    //| intrastate_rate | float(11,5) unsigned | NO   | MUL |                     |                |
    //| intralata_rate  | float(11,5) unsigned | NO   | MUL |                     |                |
    //| carrier_id      | int(11)              | NO   | MUL |                     |                |
    //| lead_strip      | int(11)              | NO   |     |                     |                |
    //| trail_strip     | int(11)              | NO   |     |                     |                |
    //| prefix          | varchar(16)          | NO   |     |                     |                |
    //| suffix          | varchar(16)          | NO   |     |                     |                |
    //| lcr_profile     | varchar(32)          | YES  | MUL | NULL                |                |
    //| date_start      | datetime             | NO   |     | 1970-01-01 00:00:00 |                |
    //| date_end        | datetime             | NO   |     | 2030-12-31 00:00:00 |                |
    //| quality         | float(10,6)          | NO   |     |                     |                |
    //| reliability     | float(10,6)          | NO   |     |                     |                |
    //| cid             | varchar(32)          | NO   |     |                     |                |
    //| enabled         | tinyint(1)           | NO   |     | 1                   |                |
    //+-----------------+----------------------+------+-----+---------------------+----------------+

    }

    public function setUp() {
        /*
         * Relationships
         */
        $this->hasOne('Provider', array('local'    => 'carrier_id',
            'foreign'  => 'provider_id')
        );
        $this->actAs('TelephonyEnabled');
    }
}
