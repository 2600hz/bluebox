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
 *
 */

/**
 * TimeOfDay.php - Description of the plug-in model
 *
 * @author Michael Phillips
 * @license BSD
 * @package FreePBX
 * @subpackage TimeOfDay
 */
class TimeOfDay extends FreePbx_Record
{
    /*
     * Example:
     * <condition minute-of-day="540-1080"> (9am to 6pm EVERY day)
     * do something ...
     * </condition>
     */
	
	
    function setTableDefinition() 
    {
        $this->hasColumn('time_of_day_id', 'integer', 11, array(
                'primary' => true,
                'autoincrement' => true,
                'unsigned' => true
            )
        );
		
        $this->hasColumn('number_id', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('routes_to', 'integer', 11, array('unsigned' => true, 'notnull' => true));
        $this->hasColumn('year', 'string', 20, array('default' => NULL) ); // year = 4 digit year. Example year="2009"
        $this->hasColumn('yday', 'string', 20, array('default' => NULL) ); // yday = 1-365
        $this->hasColumn('week', 'string', 20, array('default' => NULL) ); // week = 1-52
        $this->hasColumn('mon', 'string', 20, array('default' => NULL) ); // mon = 1-12
        $this->hasColumn('mday', 'string', 20, array('default' => NULL) ); // mday = 1-31
        $this->hasColumn('wday', 'string', 20, array('default' => NULL) ); // wday = 1-7
        $this->hasColumn('yday', 'string', 20, array('default' => NULL) ); // yday = 1-365
        $this->hasColumn('hour', 'string', 20, array('default' => NULL) ); // hour = 0-23
        $this->hasColumn('minute', 'string', 20, array('default' => NULL) ); // yday = minute = 0-59
        $this->hasColumn('minute_of_day', 'string', 20, array('default' => NULL) ); // minute-of-day = 1-1440
		
        $this->hasColumn('enabled', 'boolean', array('default' => true));

    }

    public function setUp() 
    {
        /*
         * Relationships
         */
        $this->actAs('TelephonyEnabled');
        $this->actAs('Timestampable');

		
        $this->hasOne('Number', array('local' => 'number_id', 'foreign' => 'number_id'));
		
         // Add relation on the other side, too, including all extended models that may have already loaded

         // Load the number models 
         foreach (Doctrine::getLoadedModelFiles() as $model => $dir) {
             if (substr($model, strlen($model) - strlen('Number'), strlen('Number')) == 'Number') {
                    if (is_subclass_of($model, 'Number')) {
                        Doctrine::initializeModels($model);
                    }
             }
         }

        
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, 'Number') or ($class == 'Number')) {
                $numberTable = Doctrine::getTable($class);
                $numberTable->bind(array('TimeOfDay', array('local' => 'number_id', 'foreign' => 'number_id', 'cascade' => array('delete'))), Doctrine_Relation::MANY );
            }
        }
    }
}
