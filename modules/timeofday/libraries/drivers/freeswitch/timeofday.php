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
 *
 */

/**
 * timeofday.php - FreeSwitch Time Of Day Confituration
 *
 * Allows for configuration of time of day routing for numbers
 *
 * @author Dale Hege
 * @license MPL
 * @package FreePBX3
 * @subpackage TimeOfDay
 */
class FreeSwitch_TimeOfDay_Driver extends FreeSwitch_Base_Driver {
    /**
     * Indicate we support FreeSWITCH
     */
    public static function set($obj)
    {
     	$xml = Telephony::getDriver()->xml;

        // Reference to our XML document, context and extension. Creates the extension & context if does not already exist
        foreach($obj->Number->NumberContext as $context) {

            // Does this number go anywhere?
            if ($obj->Number && ($obj->wday || $obj->minute_of_day)) {

                $xml = FreeSwitch::createExtension('timeofday_' . $obj->time_of_day_id, 'postroute', 'context_' . $context->context_id);
            
                // Dialplans are a bit different - we don't want to keep anything that is currently in an extension, in the event it's totally changed
                $xml->deleteChildren();
                $xml->update('/condition[@field="${freepbx_time_of_day_' . $obj->time_of_day_id . '}"][@expression="^$"]');
              
                $time_of_day_string = '';

                if($obj->wday){
                    $time_of_day_string .= '[@wday="' . $obj->wday .'"]';
                }
                if($obj->minute_of_day){
                    $time_of_day_string .= '[@minute-of-day="' . $obj->minute_of_day .'"]';
                }

                if($time_of_day_string) {
                    $xml->update('/condition' . $time_of_day_string);
                }

                // Check what number they dialed
                $xml->update('/condition[@field="destination_number"]{@expression="^' . $obj->Number->number . '$"}');

                $extensionRoot=$xml->getXmlRoot();
 
                $xml->setXmlRoot($xml->getXmlRoot() . '/condition[@field="destination_number"][@expression="^' . $obj->Number->number . '$"]');

                $xml->update('/action[@application="set"]{@data="freepbx_time_of_day_' . $obj->time_of_day_id . '=done"}');
                $route_to_number=Doctrine::getTable('Number')->findOneByNumberId($obj->routes_to);
                $xml->update('/action[@application="transfer"]{@data="' . $route_to_number->number . ' XML context_' . $context->context_id .'"}');
                $xml->setXmlRoot($extensionRoot);
            }
        }

    }
    public static function delete($obj)
    {
     	$xml = Telephony::getDriver()->xml;
        Kohana::log('debug', 'Deleting postroute_timeofday_' . $obj->time_of_day_id );

        $contexts = Doctrine::getTable('Context')->findAll();
        foreach($contexts as $context) {
            Kohana::log('debug', 'Deleting postroute_timeofday_' . $obj->time_of_day_id . ' in context_' . $context->context_id);
            $xml->setXmlRoot(sprintf('//document/section[@name="dialplan"]/context[@name="context_' . $context->context_id . '"]/extension[@name="%s"]', 'postroute_timeofday_' . $obj->time_of_day_id));
            $xml->deleteNode();
        }

    }

}
