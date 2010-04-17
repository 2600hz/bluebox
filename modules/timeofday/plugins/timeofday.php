<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * FreePBX Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is FreePBX Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer and Bandwidth, Inc. are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 * Michael Phillips
 *
 * @license MPL
 * @package FreePBX3
 * @subpackage TimeOfDay
 */



class TimeOfDay_Plugin extends FreePbx_Plugin
{
//    protected $preloadModels = array('TimeOfDay');
    
    public function add()
    {
    }
    
    public function edit()
    {

        $view = new View('timeofday/update');
        $view->tab = 'Add';
        $view->section = 'Time of Day Routing';


        $base = $this->getBaseModelObject();
		
        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base) {
            return FALSE;	// Nothing to do here.
        }

        if ( ! isset($base->TimeOfDay[0]->time_of_day_id)) {
            $base->TimeOfDay[] = new TimeOfDay();
        }

	$view->number_id = $base->number_id; // get number_id

        $timeOfDayCount = count($base->TimeOfDay->toArray());

        $from_minute = array();
        $to_minute = array();
        $from_hval = array();
        $from_mval = array();
        $from_pval = array();
        $to_hval = array();
        $to_mval = array();
        $to_pval = array();

        $view->timeOfDayCount = $timeOfDayCount;

        $at_least_one_working = 0;

        if($timeOfDayCount > 0) {
            foreach($base->TimeOfDay as $key => $timeOfDay) {
                if($timeOfDay->minute_of_day) {
                    list($from_minutes,$to_minutes) = explode('-',  $timeOfDay->minute_of_day);	

                    list($from_hval[$key], $from_mval[$key], $from_pval[$key])=TimeOfDayManager::getTimeFromMinute($from_minutes);
                    list($to_hval[$key], $to_mval[$key], $to_pval[$key])=TimeOfDayManager::getTimeFromMinute($to_minutes);
                    $from_minute[$key] = $from_minutes;
                    $to_minute[$key] = $to_minutes;
                    $at_least_one_working++;

                } else {
                    $from_hval[$key] = NULL;
                    $from_mval[$key] = NULL;
                    $from_pval[$key] = NULL;
                    $to_hval[$key] = NULL;
                    $to_mval[$key] = NULL;
                    $to_pval[$key] = NULL;
                    $from_minute[$key] = NULL;
                    $to_minute[$key] = NULL;
                }                    
            }                
            // Add an empty set of boxes so new entries can me made
            if($at_least_one_working) {
                $base->TimeOfDay[] = new TimeOfDay();

                $new_time_of_day_id=array_pop(array_keys($base->TimeOfDay->toArray()));
                $from_hval[$new_time_of_day_id] = NULL;
                $from_mval[$new_time_of_day_id] = NULL;
                $from_pval[$new_time_of_day_id] = NULL;
                $to_hval[$new_time_of_day_id] = NULL;
                $to_mval[$new_time_of_day_id] = NULL;
                $to_pval[$new_time_of_day_id] = NULL;
                $from_minute[$new_time_of_day_id] = NULL;
                $to_minute[$new_time_of_day_id] = NULL;
          
                $view->timeOfDayCount = $timeOfDayCount + 1;
            }
        } else {

            $view->timeOfDayCount = 1;

            $from_hval[0] = NULL;
            $from_mval[0] = NULL;
            $from_pval[0] = NULL;
            $to_hval[0] = NULL;
            $to_mval[0] = NULL;
            $to_pval[0] = NULL;
            $from_minute[0] = NULL;
            $to_minute[0] = NULL;
        }

        $view->from_hval = $from_hval;  
        $view->from_mval = $from_mval;
        $view->from_pval = $from_pval;

        $view->to_hval = $to_hval;  
        $view->to_mval = $to_mval;
        $view->to_pval = $to_pval;
        $view->from_minute = $from_minute;
        $view->to_minute = $to_minute;
		

        $view->timeofday = $base->TimeOfDay->toArray();
		
        $this->views[] = $view;
    }

    public function save()
    {
        $base = $this->getBaseModelObject();
		
        // While the field may be defined by an app or another module, it may not be populated! Double-check
        if (!$base) {
            return FALSE;	// Nothing to do here.
        }

        if($this->input->post()) {

            $form = $this->input->post('timeofday');

            $time_of_day_info = array();

            $time_of_day_save_count = 0;

            foreach($form as $form_entity) {

                $from_minute = TimeOfDayManager::getMinuteOfTheDay($form_entity['from_hr'], $form_entity['from_min'], $form_entity['from_pm']);
                $to_minute = TimeOfDayManager::getMinuteOfTheDay($form_entity['to_hr'], $form_entity['to_min'], $form_entity['to_pm']);
       
                if( empty($form_entity['routes_to'])) {
                    //Skip entries with no route_to
                } else {

                    if(isset($form_entity['wday'])) {
                        $wday =  implode(',', $form_entity['wday']);
                    } else {
                        $wday =  '';
                    }
                    $time_of_day_info[$time_of_day_save_count]['routes_to'] = $form_entity['routes_to'];
                    $time_of_day_info[$time_of_day_save_count]['wday'] = $wday;
                    $time_of_day_info[$time_of_day_save_count]['minute_of_day'] = $from_minute . '-' . $to_minute;
                    $time_of_day_info[$time_of_day_save_count]['number_id'] = $base->number_id;
                    $time_of_day_save_count++;
                }
            }
            $base->TimeOfDay[] = new TimeOfDay();
            $base->TimeOfDay->synchronizeWithArray($time_of_day_info);

        }

    }
    
	

}
