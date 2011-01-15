<?php defined('SYSPATH') or die('No direct access allowed.');
/*
 * Bluebox Modular Telephony Software Library / Application
 *
 * The contents of this file are subject to the Mozilla Public License Version 1.1 (the 'License');
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/.
 *
 * Software distributed under the License is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, either
 * express or implied. See the License for the specific language governing rights and limitations under the License.
 *
 * The Original Code is Bluebox Telephony Configuration API and GUI Framework.
 * The Original Developer is the Initial Developer.
 * The Initial Developer of the Original Code is Darren Schreiber
 * All portions of the code written by the Initial Developer are Copyright © 2008-2009. All Rights Reserved.
 *
 * Contributor(s):
 *
 *
 */

/**
 * number.php - FreeSwitch Number driver
 * 
 * Creates the basic context and dialplan extensions for all phone numbers in the system, and calls the correct methods to add
 * their number-related tasks to the specific number being generated.
 *
 * @author Darren Schreiber <d@d-man.org>
 * @license MPL
 * @package TCAPI
 * @subpackage FreeSWITCH_Driver
 */
class FreeSwitch_Number_Driver extends FreeSwitch_Base_Driver
{
    /**
     * When a number is saved, we need to update any contexts that the number lives in
     */
    public static function set($obj)
    {
        // Go create the number related stuff for each context it is assigned to
        if ($obj->NumberContext)
        {
            $assignedContexts = array();
            
            foreach ($obj->NumberContext as $context)
            {
                // Add any "global" hooks that come before the processing of any numbers (this is per context)
                dialplan::start('context_' .$context['context_id']);

                FreeSwitch_NumberContext_Driver::set($context);

                // Add any "global" hooks that come after the processing of any numbers (this is per context)
                dialplan::end('context_' .$context['context_id']);

                $assignedContexts[] = $context['context_id'];
            }

            // Changed the operation of setting the pools that cause existings
            // pool records to be used, where we used to unset (delete) them, so
            // we need to ensure we dont orphan numbers when changing contexts
            $contexts = Doctrine::getTable('Context')->findAll();

            $xml = Telephony::getDriver()->xml;

            $xp = new DOMXPath($xml);

            foreach ($contexts as $context)
            {
                if (!in_array($context['context_id'], $assignedContexts))
                {
                    $search = sprintf('//document/section[@name="dialplan"]/context[@name="context_%s"]/extension[@name="%s"]', $context['context_id'], 'main_number_' .$obj['number_id']);

                    if($xp->query($search))
                    {
                        kohana::log('debug', 'FreeSWITCH -> REMOVING NUMBER ' .$obj['number'] .' (' .$obj['number_id'] .') FROM CONTEXT ' .$context['context_id']);

                        $xml->setXmlRoot($search);
                        
                        $xml->deleteNode();
                    }
                }
            }

            if ($obj['type'] == Number::TYPE_EXTERNAL AND !empty($obj->NumberContext[0]['context_id']))
            {
                kohana::log('debug', 'FreeSWITCH -> ADDING NUMBER ' .$obj['number'] .' (' .$obj['number_id'] .') TO NUMBER ROUTE');

                $xml = Freeswitch::setSection('number_route', $obj['number_id']);

                // Dialplans are a bit different - we don't want to keep anything that is currently in an extension, in the event it's totally changed
                $xml->deleteChildren();

                // Check what number they dialed
                $condition = '/condition[@field="destination_number"]{@expression="^' .$obj['number'] .'$"}';

                $xml->update($condition .'/action[@application="set"][@data="vm-operator-extension=' .$obj['number'] .'"]');

                $xml->update($condition. '/action[@application="transfer"]{@data="' .$obj['number'] .' XML context_' .$obj->NumberContext[0]['context_id'] .'"}');
            }
            else
            {
                kohana::log('debug', 'FreeSWITCH -> REMOVING NUMBER ' .$obj['number'] .' (' .$obj['number_id'] .') FROM NUMBER ROUTE');

                Freeswitch::setSection('number_route', $obj['number_id'])->deleteNode();
            }
        }
    }

    public static function delete($obj)
    {
        // Remove the number from all contexts that is belongs to
        if ($obj->NumberContext)
        {
            foreach ($obj->NumberContext as $context)
            {
                FreeSwitch_NumberContext_Driver::delete($context);
            }
        }
        
        Freeswitch::setSection('number_route', $obj['number_id'])->deleteNode();
    }
}